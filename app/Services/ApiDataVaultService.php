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
     * Main Vault retrieval and persistence method.
     * 
     * If subscription is ACTIVE:
     *   1. Queries API via $apiCallback (with Cache::remember).
     *   2. Persists successful result into DB snapshot.
     *   3. If API fails, falls back gracefully to DB snapshot.
     * 
     * If subscription is EXPIRED (Archive Mode):
     *   1. Skips external API call completely.
     *   2. Retrieves snapshot from local DB using flexible matcher.
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
        $isLive = $this->isSubscriptionActive();

        // ══════════════════════════════════════════════════════════════
        // 1. LIVE MODE (Subscription Active)
        // ══════════════════════════════════════════════════════════════
        if ($isLive) {
            try {
                $result = Cache::remember($cacheKey, $cacheSeconds, function () use (
                    $projectId,
                    $media,
                    $endpointKey,
                    $sDate,
                    $eDate,
                    $apiCallback
                ) {
                    $res = $apiCallback();

                    // Only persist if result contains meaningful data
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
                            Log::warning("ApiDataVault: Failed saving snapshot to DB: {$dbErr->getMessage()}", [
                                'project_id' => $projectId,
                                'endpoint'   => $endpointKey,
                            ]);
                        }
                    }

                    return $res;
                });

                if ($result !== null && !empty($result)) {
                    return $result;
                }

                // If live callback returned empty array (e.g. bad API auth), fallback to snapshot
                $snapshot = $this->resolveSnapshotPayload($projectId, $media, $endpointKey, $sDate, $eDate);
                if ($snapshot !== null) {
                    return $snapshot;
                }

                return $result ?? [];
            } catch (\Throwable $apiErr) {
                Log::warning("ApiDataVault: Live API call failed ({$endpointKey}): {$apiErr->getMessage()} - falling back to DB.");

                // Graceful fallback to DB snapshot when live API is down
                $snapshot = $this->resolveSnapshotPayload($projectId, $media, $endpointKey, $sDate, $eDate);
                if ($snapshot !== null) {
                    Log::info("ApiDataVault: Served fallback DB snapshot for {$endpointKey} after API error.");
                    return $snapshot;
                }

                return [];
            }
        }

        // ══════════════════════════════════════════════════════════════
        // 2. ARCHIVE MODE (Subscription Inactive / Disconnected)
        // ══════════════════════════════════════════════════════════════
        Log::info("ApiDataVault: Serving from Archive DB (Subscription Inactive)", [
            'project_id' => $projectId,
            'media'      => $media,
            'endpoint'   => $endpointKey,
        ]);

        $snapshot = $this->resolveSnapshotPayload($projectId, $media, $endpointKey, $sDate, $eDate);
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
                    $itemDate = $item['date_created'] ?? $item['date_inserted_dt'] ?? $item['date'] ?? $item['created_at'] ?? '';
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
        string $endDate
    ): mixed {
        $sDate = Carbon::parse($startDate)->format('Y-m-d');
        $eDate = Carbon::parse($endDate)->format('Y-m-d');
        return $this->resolveSnapshotPayload($projectId, $media, $endpointKey, $sDate, $eDate);
    }
}
