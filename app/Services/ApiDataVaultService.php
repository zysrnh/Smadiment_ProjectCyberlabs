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
     *   2. Retrieves latest available snapshot from local DB.
     *
     * @param int $projectId
     * @param string $media (e.g., 'twitter', 'facebook', 'instagram', 'youtube', 'tiktok', 'news', 'all')
     * @param string $endpointKey (e.g., 'volume_total', 'sentiment_total', 'word_cloud', 'topic_mentions')
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
                return Cache::remember($cacheKey, $cacheSeconds, function () use (
                    $projectId,
                    $media,
                    $endpointKey,
                    $sDate,
                    $eDate,
                    $apiCallback
                ) {
                    $result = $apiCallback();

                    // Only persist if result contains meaningful data
                    if ($result !== null && !empty($result)) {
                        try {
                            ProjectApiSnapshot::storeSnapshot(
                                $projectId,
                                $media,
                                $endpointKey,
                                $sDate,
                                $eDate,
                                $result
                            );
                        } catch (\Throwable $dbErr) {
                            Log::warning("ApiDataVault: Failed saving snapshot to DB: {$dbErr->getMessage()}", [
                                'project_id' => $projectId,
                                'endpoint'   => $endpointKey,
                            ]);
                        }
                    }

                    return $result;
                });
            } catch (\Throwable $apiErr) {
                Log::error("ApiDataVault: Live API call failed ({$endpointKey}): {$apiErr->getMessage()}", [
                    'project_id' => $projectId,
                    'media'      => $media,
                ]);

                // Graceful fallback to DB snapshot when live API is down
                $snapshot = ProjectApiSnapshot::getSnapshot($projectId, $media, $endpointKey, $sDate, $eDate);
                if ($snapshot !== null) {
                    Log::info("ApiDataVault: Served fallback DB snapshot for {$endpointKey} after API error.");
                    return $snapshot;
                }

                throw $apiErr;
            }
        }

        // ══════════════════════════════════════════════════════════════
        // 2. ARCHIVE MODE (Subscription Expired / Offline)
        // ══════════════════════════════════════════════════════════════
        Log::info("ApiDataVault: Serving from Archive DB (Subscription Inactive)", [
            'project_id' => $projectId,
            'media'      => $media,
            'endpoint'   => $endpointKey,
        ]);

        $snapshot = ProjectApiSnapshot::getSnapshot($projectId, $media, $endpointKey, $sDate, $eDate);

        if ($snapshot !== null) {
            return $snapshot;
        }

        // Nearest available snapshot fallback if exact date is missing
        $latest = ProjectApiSnapshot::findLatestSnapshot($projectId, $media, $endpointKey);
        if ($latest && $latest->payload) {
            $decoded = json_decode($latest->payload, true);
            return $decoded !== null ? $decoded : $latest->payload;
        }

        return null;
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
        return ProjectApiSnapshot::getSnapshot($projectId, $media, $endpointKey, $startDate, $endDate);
    }
}
