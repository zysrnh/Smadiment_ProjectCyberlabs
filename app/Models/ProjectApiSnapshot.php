<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ProjectApiSnapshot extends Model
{
    use HasFactory;

    protected $table = 'project_api_snapshots';

    protected $fillable = [
        'project_id',
        'media',
        'endpoint_key',
        'start_date',
        'end_date',
        'payload',
        'synced_at',
    ];

    protected $casts = [
        'project_id' => 'integer',
        'start_date' => 'date:Y-m-d',
        'end_date'   => 'date:Y-m-d',
        'synced_at'  => 'datetime',
    ];

    /**
     * Store or update a snapshot in database atomically.
     *
     * @param int $projectId
     * @param string $media
     * @param string $endpointKey
     * @param string $startDate (Y-m-d)
     * @param string $endDate (Y-m-d)
     * @param mixed $payload
     * @return self
     */
    public static function storeSnapshot(
        int $projectId,
        string $media,
        string $endpointKey,
        string $startDate,
        string $endDate,
        mixed $payload
    ): self {
        $encodedPayload = is_string($payload) ? $payload : json_encode($payload);

        return static::updateOrCreate(
            [
                'project_id'   => $projectId,
                'media'        => strtolower($media),
                'endpoint_key' => $endpointKey,
                'start_date'   => Carbon::parse($startDate)->format('Y-m-d'),
                'end_date'     => Carbon::parse($endDate)->format('Y-m-d'),
            ],
            [
                'payload'   => $encodedPayload,
                'synced_at' => now(),
            ]
        );
    }

    /**
     * Retrieve payload for a given query from snapshot.
     *
     * @param int $projectId
     * @param string $media
     * @param string $endpointKey
     * @param string $startDate (Y-m-d)
     * @param string $endDate (Y-m-d)
     * @return mixed|null
     */
    public static function getSnapshot(
        int $projectId,
        string $media,
        string $endpointKey,
        string $startDate,
        string $endDate
    ): mixed {
        $record = static::where('project_id', $projectId)
            ->where('media', strtolower($media))
            ->where('endpoint_key', $endpointKey)
            ->where('start_date', Carbon::parse($startDate)->format('Y-m-d'))
            ->where('end_date', Carbon::parse($endDate)->format('Y-m-d'))
            ->first();

        if (!$record || empty($record->payload)) {
            return null;
        }

        $decoded = json_decode($record->payload, true);
        return $decoded !== null ? $decoded : $record->payload;
    }

    /**
     * Smart flexible snapshot resolver for offline resilience:
     * 1. Exact match (start_date & end_date)
     * 2. Containing date range (e.g. searching 2026-09-05 inside 2026-09-01 s/d 2026-09-26)
     * 3. Latest snapshot for the endpoint
     * 4. Family prefix match (e.g. news_mentions_0_1200 covers news_mentions_0_500)
     */
    public static function findSnapshotForQuery(
        int $projectId,
        string $media,
        string $endpointKey,
        string $startDate,
        string $endDate
    ): mixed {
        $sDate = Carbon::parse($startDate)->format('Y-m-d');
        $eDate = Carbon::parse($endDate)->format('Y-m-d');
        $media = strtolower($media);

        // 1. Exact Match
        $record = static::where('project_id', $projectId)
            ->where('media', $media)
            ->where('endpoint_key', $endpointKey)
            ->where('start_date', $sDate)
            ->where('end_date', $eDate)
            ->first();

        // 2. Containing Match (snapshot covers the requested period)
        if (!$record) {
            $record = static::where('project_id', $projectId)
                ->where('media', $media)
                ->where('endpoint_key', $endpointKey)
                ->where('start_date', '<=', $sDate)
                ->where('end_date', '>=', $eDate)
                ->orderBy('synced_at', 'desc')
                ->first();
        }

        // 3. Fallback to latest snapshot of exact endpoint_key
        if (!$record) {
            $record = static::where('project_id', $projectId)
                ->where('media', $media)
                ->where('endpoint_key', $endpointKey)
                ->orderBy('end_date', 'desc')
                ->orderBy('synced_at', 'desc')
                ->first();
        }

        // 4. Family prefix fallback (e.g. news_mentions_*, articles_*)
        if (!$record) {
            $prefix = null;
            if (str_starts_with($endpointKey, 'news_mentions_')) {
                $prefix = 'news_mentions_%';
            } elseif (str_starts_with($endpointKey, 'articles_')) {
                $prefix = 'articles_%';
            }

            if ($prefix) {
                $record = static::where('project_id', $projectId)
                    ->where('endpoint_key', 'like', $prefix)
                    ->orderBy('synced_at', 'desc')
                    ->first();
            }
        }

        if (!$record || empty($record->payload)) {
            return null;
        }

        $decoded = json_decode($record->payload, true);
        return $decoded !== null ? $decoded : $record->payload;
    }

    /**
     * Fallback lookup: get latest snapshot for endpoint even if date differs slightly.
     *
     * @param int $projectId
     * @param string $media
     * @param string $endpointKey
     * @return self|null
     */
    public static function findLatestSnapshot(
        int $projectId,
        string $media,
        string $endpointKey
    ): ?self {
        return static::where('project_id', $projectId)
            ->where('media', strtolower($media))
            ->where('endpoint_key', $endpointKey)
            ->orderBy('end_date', 'desc')
            ->orderBy('synced_at', 'desc')
            ->first();
    }
}
