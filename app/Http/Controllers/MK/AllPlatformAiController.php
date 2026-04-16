<?php

namespace App\Http\Controllers\MK;

use App\Http\Controllers\Controller;
use App\Services\MK\PlatformDataService;
use App\Services\MediaKernelsClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * AllPlatformAiController (refactored)
 *
 * Responsibilities:
 *  1. Resolve the list of project IDs the authenticated user may access.
 *  2. Delegate all heavy lifting to PlatformDataService.
 *  3. Return a clean JSON response.
 *  4. Proxy Gemini AI requests.
 *
 * It contains NO platform-specific fetch logic — that lives entirely in
 * PlatformDataService.
 */
class AllPlatformAiController extends Controller
{
    public function __construct(
        private readonly PlatformDataService $platformService,
        private readonly MediaKernelsClient  $client,
    ) {}

    // =========================================================================
    // PAGE
    // =========================================================================

    public function page(Request $request)
    {
        try {
            $projects  = $this->getAssignedProjects();
            $projectId = $request->query('project_id');

            // Auto-redirect to first project when none is selected.
            if (!$projectId && count($projects) > 0) {
                $projectId = $projects[0]['id'] ?? null;
                if ($projectId) {
                    return redirect()->route('mk.all-ai-analysis', [
                        'project_id' => $projectId,
                        'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                        'end_date'   => $request->query('end_date',   now()->format('Y-m-d')),
                    ]);
                }
            }

            return view('mk.ai.all-platform')->with([
                'projectId' => $projectId,
                'startDate' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                'endDate'   => $request->query('end_date',   now()->format('Y-m-d')),
                'projects'  => $projects,
            ]);

        } catch (\Throwable $e) {
            Log::error('AllPlatformAi page error', ['error' => $e->getMessage()]);

            return view('mk.ai.all-platform')->with([
                'projectId' => null,
                'startDate' => now()->subDays(6)->format('Y-m-d'),
                'endDate'   => now()->format('Y-m-d'),
                'projects'  => [],
            ]);
        }
    }

    // =========================================================================
    // DATA — multi-project aggregation endpoint
    // =========================================================================

    /**
     * GET /mk/api/all-ai/data
     *
     * Query params:
     *   project_id   – optional; if omitted, all assigned projects are used.
     *   project_ids  – optional comma-separated list of IDs.
     *   start_date
     *   end_date
     */
    public function data(Request $request): JsonResponse
    {
        try {
            $startDate = $request->query('start_date');
            $endDate   = $request->query('end_date');

            $projectIds = $this->resolveProjectIds($request);

            if (empty($projectIds)) {
                return response()->json(['success' => false, 'error' => 'No accessible projects found.'], 400);
            }

            // Delegate entirely to the service layer.
            $result = $this->platformService->aggregateAll($projectIds, $startDate, $endDate);

            return response()->json([
                'success' => true,
                'data'    => [
                    'summary'  => $result['summary'],
                    'projects' => $result['projects'],
                    'dataset'  => $result['dataset'],

                    // Pre-built text dataset for the AI prompt (backward-compat).
                    'text_dataset' => $this->buildTextDataset($result),
                ],
            ]);

        } catch (\Throwable $e) {
            Log::error('AllPlatformAi data error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // =========================================================================
    // PROXY — Gemini AI
    // =========================================================================

    public function proxy(Request $request): JsonResponse
    {
        try {
            $apiKey = env('GEMINI_API_KEY');
            if (!$apiKey) {
                return response()->json(['error' => 'GEMINI_API_KEY is not configured.'], 500);
            }

            $messages  = $request->input('messages', []);
            $system    = $request->input('system', '');
            $maxTokens = (int) $request->input('max_tokens', 8192);

            if (empty($messages)) {
                return response()->json(['error' => 'messages must not be empty.'], 400);
            }

            [$text, $usedModel] = $this->callGemini($apiKey, $messages, $system, $maxTokens);

            if (empty($text)) {
                return response()->json(['error' => 'All Gemini models are unavailable or quota exhausted.'], 429);
            }

            return response()->json([
                'content' => [['type' => 'text', 'text' => $text]],
                'model'   => $usedModel,
            ]);

        } catch (\Throwable $e) {
            Log::error('AllPlatformAi proxy error', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Resolve the list of project IDs to aggregate.
     *
     * Priority:
     *  1. `project_ids` (comma-separated) query param.
     *  2. `project_id` (single) query param.
     *  3. All projects assigned to the authenticated user.
     */
    private function resolveProjectIds(Request $request): array
    {
        // Multiple IDs supplied explicitly.
        if ($request->has('project_ids')) {
            $ids = array_filter(array_map('trim', explode(',', $request->query('project_ids'))));
            if (!empty($ids)) return $this->filterAssigned($ids);
        }

        // Single ID supplied.
        if ($projectId = $request->query('project_id')) {
            return $this->filterAssigned([$projectId]);
        }

        // Fall back to ALL assigned projects.
        return array_column($this->getAssignedProjects(), 'id');
    }

    /**
     * Return only IDs the current user is actually assigned to.
     */
    private function filterAssigned(array $ids): array
    {
        $assigned = array_column($this->getAssignedProjects(), 'id');
        return array_values(array_intersect($ids, $assigned));
    }

    /**
     * Load all projects assigned to the authenticated user.
     */
    private function getAssignedProjects(): array
    {
        $user               = Auth::user();
        $assignedProjectIds = $user->assignedProjectIds();
        $rawProjects        = $this->client->listProjects(0, 100);

        return array_values(array_filter(
            array_values($rawProjects),
            fn ($p) => in_array($p['id'] ?? null, $assignedProjectIds)
        ));
    }

    /**
     * Build a human-readable text block from the aggregated result.
     * Used as context for the Gemini AI prompt.
     */
    private function buildTextDataset(array $result): string
    {
        $s     = $result['summary'];
        $lines = [];

        $lines[] = '=== AGGREGATED MULTI-PLATFORM DATASET ===';
        $lines[] = "Projects: {$s['project_count']}";
        $lines[] = "Total Mentions: {$s['total_mentions']}";
        $lines[] = "Sentiment — Positive: {$s['pct_positive']}% ({$s['total_positive']}) | "
                 . "Negative: {$s['pct_negative']}% ({$s['total_negative']}) | "
                 . "Neutral: {$s['pct_neutral']}% ({$s['total_neutral']})";
        $lines[] = '';

        // Per-project summary.
        foreach ($result['projects'] as $proj) {
            $pid  = $proj['project_id'];
            $cnt  = implode(', ', array_map(
                fn ($k, $v) => "{$k}={$v}",
                array_keys($proj['counts']),
                array_values($proj['counts'])
            ));
            $pos  = $proj['sentiment']['positive'];
            $neg  = $proj['sentiment']['negative'];
            $neu  = $proj['sentiment']['neutral'];
            $lines[] = "[Project {$pid}] Items: {$cnt} | Pos:{$pos} Neg:{$neg} Neu:{$neu}";
        }

        $lines[] = '';

        // Flat dataset — grouped by platform for readability.
        $byPlatform = [];
        foreach ($result['dataset'] as $item) {
            $byPlatform[$item['platform']][] = $item;
        }

        foreach ($byPlatform as $platform => $items) {
            $lines[] = "--- " . strtoupper($platform) . " (" . count($items) . " items) ---";
            foreach ($items as $i => $item) {
                $m       = $item['metrics'];
                $metrics = "likes={$m['likes']} views={$m['views']} comments={$m['comments']} shares={$m['shares']}";
                $lines[] = "[" . ($i + 1) . "] @{$item['author']} | {$item['date']} | {$item['sentiment']} | {$metrics}";
                if ($item['content']) $lines[] = "   \"{$item['content']}\"";
            }
            $lines[] = '';
        }

        $lines[] = '=== END DATASET ===';

        return implode("\n", $lines);
    }

    /**
     * Call Gemini API with a model fallback chain.
     *
     * @return array{string, string} [responseText, modelUsed]
     */
    private function callGemini(string $apiKey, array $messages, string $system, int $maxTokens): array
    {
        // Build Gemini-compatible contents array.
        $contents   = [];
        $firstAdded = false;

        foreach ($messages as $msg) {
            $role    = $msg['role'] === 'assistant' ? 'model' : 'user';
            $content = $msg['content'];

            // Prepend system prompt to the first user message.
            if (!$firstAdded && $role === 'user' && $system !== '') {
                $content    = $system . "\n\n---\n\n" . $content;
                $firstAdded = true;
            }

            $contents[] = ['role' => $role, 'parts' => [['text' => $content]]];
        }

        $models = [
            'gemini-2.5-flash',
            'gemini-2.5-pro',
            'gemini-2.0-flash',
            'gemini-2.0-flash-lite',
            'gemini-flash-latest',
        ];

        foreach ($models as $model) {
            $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

            try {
                $response = Http::withHeaders(['Content-Type' => 'application/json'])
                    ->timeout(60)
                    ->post($endpoint, [
                        'contents'         => $contents,
                        'generationConfig' => [
                            'maxOutputTokens' => $maxTokens,
                            'temperature'     => 0.7,
                        ],
                    ]);

                if (in_array($response->status(), [404, 429])) {
                    Log::warning("Gemini {$model} skipped", ['status' => $response->status()]);
                    continue;
                }

                if ($response->failed()) {
                    Log::warning("Gemini {$model} failed", ['status' => $response->status()]);
                    continue;
                }

                $text = $response->json('candidates.0.content.parts.0.text', '');
                if ($text !== '') {
                    Log::info("Gemini OK", ['model' => $model]);
                    return [$text, $model];
                }

            } catch (\Throwable $e) {
                Log::warning("Gemini {$model} exception", ['error' => $e->getMessage()]);
            }
        }

        return ['', ''];
    }
}