<?php

namespace App\Services;

use App\Models\ProjectApiSnapshot;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ApiDataVaultService
{
    /**
     * Check if the current user has an active subscription/trial.
     *
     * @param User|null $user
     * @return bool
     */
    public function isSubscriptionActive(?User $user = null): bool
    {
        $user = $user ?? Auth::user();

        if (!$user) {
            return true; // Default fallback if running in CLI or guest
        }

        if (method_exists($user, 'isTrialActive')) {
            return $user->isTrialActive();
        }

        return true;
    }

    /**
     * Main Vault retrieval and persistence method (DB-First).
     * 
     * 1. Checks Cache first for instant response.
     * 2. Checks DB Snapshot next. If snapshot exists, returns DB data immediately.
     * 3. Hits live API callback ONLY if DB snapshot is missing, and saves result to DB snapshot + Cache.
     *
     * @param int $projectId
     * @param string $media (e.g., 'twitter', 'facebook', 'instagram', 'youtube', 'tiktok', 'news', 'all', 'doc')
     * @param string $endpointKey (e.g., 'volume_total', 'sentiment_total', 'word_cloud', 'news_mentions_0_500')
     * @param string $startDate (Y-m-d)
     * @param string $endDate (Y-m-d)
     * @param \Closure $apiCallback Function executing the real API call
     * @param int $cacheSeconds Cache duration in seconds (default: 30 minutes)
     * @return mixed
     */
    public function remember(
        int $projectId,
        string $media,
        string $endpointKey,
        string $startDate,
        string $endDate,
        \Closure $apiCallback,
        int $cacheSeconds = 1800
    ): mixed {
        $sDate = Carbon::parse($startDate)->format('Y-m-d');
        $eDate = Carbon::parse($endDate)->format('Y-m-d');
        $media = strtolower($media);

        $cacheKey = "vault_{$projectId}_{$media}_{$endpointKey}_{$sDate}_{$eDate}";

        // 1. Ultra-fast Cache Check
        if (Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);
            if ($cached !== null && !empty($cached)) {
                return $cached;
            }
        }

        // 2. DB-First: Return local DB snapshot immediately if available
        $snapshot = $this->resolveSnapshotPayload($projectId, $media, $endpointKey, $sDate, $eDate);
        if ($snapshot !== null && !empty($snapshot)) {
            Cache::put($cacheKey, $snapshot, $cacheSeconds);
            return $snapshot;
        }

        // 3. Fallback: Hit Live API if DB snapshot does not exist yet
        try {
            $res = $apiCallback();

            if ($res !== null && !empty($res)) {
                try {
                    ProjectApiSnapshot::storeSnapshot(
                        $projectId,
                        $media,
                        $endpointKey,
                        $sDate,
                        $eDate,
                        $res
                    );
                } catch (\Throwable $dbErr) {
                    Log::warning("ApiDataVault: Failed saving snapshot to DB: " . $dbErr->getMessage(), [
                        'project_id' => $projectId,
                        'endpoint'   => $endpointKey,
                    ]);
                }
                Cache::put($cacheKey, $res, $cacheSeconds);
                return $res;
            }
        } catch (\Throwable $apiErr) {
            Log::warning("ApiDataVault: Live API call failed ({$endpointKey}): " . $apiErr->getMessage());
        }

        return $snapshot ?? [];
    }

    /**
     * Resolve snapshot payload with smart filtering & date fallback.
     */
    private function resolveSnapshotPayload(
        int $projectId,
        string $media,
        string $endpointKey,
        string $sDate,
        string $eDate
    ): mixed {
        $data = ProjectApiSnapshot::findSnapshotForQuery($projectId, $media, $endpointKey, $sDate, $eDate);

        if ($data === null) {
            return null;
        }

        // If data is a list of mentions/articles and a specific day was queried
        if (is_array($data) && !empty($data) && isset($data[0]) && is_array($data[0])) {
            if ($sDate === $eDate) {
                $filtered = [];
                foreach ($data as $item) {
                    $itemDate = $item['orig_date'] ?? $item['date_created'] ?? $item['date_inserted_dt'] ?? $item['date'] ?? $item['created_at'] ?? '';
                    if (str_starts_with($itemDate, $sDate) || str_contains($itemDate, $sDate)) {
                        $filtered[] = $item;
                    }
                }

                if (!empty($filtered)) {
                    return $filtered;
                }

                // If exact date has no raw records in sample, return top slice of available items
                return array_slice($data, 0, 100);
            }
        }

        return $data;
    }

    /**
     * Manually save a snapshot into the vault.
     */
    public function saveSnapshot(
        int $projectId,
        string $media,
        string $endpointKey,
        string $startDate,
        string $endDate,
        mixed $payload
    ): ProjectApiSnapshot {
        return ProjectApiSnapshot::storeSnapshot($projectId, $media, $endpointKey, $startDate, $endDate, $payload);
    }

    /**
     * Directly retrieve a snapshot from the vault without calling API.
     */
    public function getSnapshot(
        int $projectId,
        string $media,
        string $endpointKey,
        string $startDate,
        string $endtime
    ): mixed {
        $sDate = Carbon::parse($startDate)->format('Y-m-d');
        $eDate = Carbon::parse($endtime)->format('Y-m-d');
        return $this->resolveSnapshotPayload($projectId, $media, $endpointKey, $sDate, $eDate);
    }
}
