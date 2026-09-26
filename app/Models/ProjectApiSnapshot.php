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
