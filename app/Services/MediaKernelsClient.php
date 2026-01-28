<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MediaKernelsClient
{
    private function baseUrl(): string
    {
        return rtrim(config('services.mediakernels.base_url'), '/');
    }

    private function username(): string
    {
        return (string) config('services.mediakernels.username');
    }

    private function password(): string
    {
        return (string) config('services.mediakernels.password');
    }

    private function parseJson($res): array
    {
        // Kadang response JSON tapi kebaca string, jadi paksa decode dari body
        $json = $res->json();
        if (is_array($json)) return $json;

        $body = $res->body();
        $decoded = json_decode($body, true);
        return is_array($decoded) ? $decoded : ['raw' => $body];
    }

    public function authorsAge(
        string $projectId,
        string $media,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23,
        string $sort = 'post_freq desc'
    ): array {
        $token = $this->getToken();

        $res = Http::timeout(30)->acceptJson()->get(
            $this->baseUrl() . '/authors_age/',
            [
                'project_id' => $projectId,
                'media'      => $media,
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'start_time' => $startTime,
                'end_time'   => $endTime,
                'sort'       => $sort,
                'is_cache'   => 'true',
                'token'      => $token,
            ]
        );

        $res->throw();
        return $this->parseJson($res);
    }

    public function authorsGender(
        string $projectId,
        string $media,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23,
        string $sort = 'post_freq desc'
    ): array {
        $token = $this->getToken();

        $res = Http::timeout(30)->acceptJson()->get(
            $this->baseUrl() . '/authors_gender/',
            [
                'project_id' => $projectId,
                'media'      => $media,
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'start_time' => $startTime,
                'end_time'   => $endTime,
                'sort'       => $sort,
                'is_cache'   => 'true',
                'field'      => 'gender',
                'token'      => $token,
            ]
        );

        $res->throw();
        return $this->parseJson($res);
    }


    public function getToken(): string
    {
        return Cache::remember('mk:token', now()->addMinutes(50), function () {
            if (!$this->username() || !$this->password()) {
                throw new \RuntimeException("ENV MEDIAKERNELS_USERNAME/PASSWORD belum di-set.");
            }

            $res = Http::timeout(30)
                ->acceptJson()
                ->get($this->baseUrl() . '/token/', [
                    'username' => $this->username(),
                    'password' => $this->password(),
                    'for_api' => 'true',
                    'for_chatbot' => 'false',
                ]);

            $res->throw();

            $json = $this->parseJson($res);
            $token = $json['token'] ?? null;

            if (!$token) {
                throw new \RuntimeException("Token tidak ditemukan pada response /token/");
            }

            return $token;
        });
    }

    public function listProjects(int $start = 0, int $limit = 20): array
    {
        $token = $this->getToken();

        $res = Http::timeout(30)->acceptJson()->get($this->baseUrl() . '/projects', [
            'start' => $start,
            'limit' => $limit,
            'sort' => 'id desc',
            'client' => '',
            'is_cache' => 'true',
            'token' => $token,
        ]);

        if ($res->status() === 401) {
            Cache::forget('mk:token');
            $token = $this->getToken();

            $res = Http::timeout(30)->acceptJson()->get($this->baseUrl() . '/projects', [
                'start' => $start,
                'limit' => $limit,
                'sort' => 'id desc',
                'client' => '',
                'is_cache' => 'true',
                'token' => $token,
            ]);
        }

        $res->throw();
        return $this->parseJson($res);
    }

    // ✅ 1) sentiment_total
    public function sentimentTotal(string $projectId, string $startDate, string $endDate, int $startTime = 0, int $endTime = 23): array
    {
        $token = $this->getToken();

        $res = Http::timeout(30)->acceptJson()->get($this->baseUrl() . '/sentiment_total/', [
            'project_id' => $projectId,
            'start_date' => $startDate,
            'start_time' => $startTime,
            'end_date'   => $endDate,
            'end_time'   => $endTime,
            'token'      => $token,
        ]);

        $res->throw();
        return $this->parseJson($res);
    }

    // ✅ 2) get_geo_twitter_user_sentiment
    public function geoTwitterUserSentiment(
        string $projectId,
        string $media,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23,
        int $sentiment = 1
    ): array {
        $token = $this->getToken();

        $res = Http::timeout(30)->acceptJson()->get($this->baseUrl() . '/get_geo_twitter_user_sentiment/', [
            'project_id' => $projectId,
            'media'      => $media,
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'start_time' => $startTime,
            'end_time'   => $endTime,
            'sentiment'  => $sentiment,
            'format'     => 'json',
            'token'      => $token,
        ]);

        $res->throw();
        return $this->parseJson($res);
    }
    
    // 🔥 3) project_stats
    public function projectStats(
        string $projectId,
        string $media,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23,
        string $types = 'volumetotal'
    ): array {
        $token = $this->getToken();

        $res = Http::timeout(30)->acceptJson()->get(
            $this->baseUrl() . '/project_stats/',
            [
                'project_id' => $projectId,
                'media'      => $media,
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'start_time' => $startTime,
                'end_time'   => $endTime,
                'types'      => $types,
                'is_cache'   => 'true',
                'token'      => $token,
            ]
        );

        $res->throw();
        return $this->parseJson($res);
    }
    
    // 🔥 authors_type
    public function authorsType(
        string $projectId,
        string $media,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23,
        string $sort = 'post_freq desc'
    ): array {
        $token = $this->getToken();

        $res = Http::timeout(30)->acceptJson()->get(
            $this->baseUrl() . '/authors_type/',
            [
                'project_id' => $projectId,
                'media'      => $media,
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'start_time' => $startTime,
                'end_time'   => $endTime,
                'sort'       => $sort,
                'field'      => 'is_organization',
                'is_cache'   => 'true',
                'token'      => $token,
            ]
        );

        $res->throw();
        return $this->parseJson($res);
    }
    
    // 🔥 categories
    public function categories(
        string $projectId,
        string $media,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23
    ): array {
        $token = $this->getToken();

        $res = Http::timeout(30)->acceptJson()->get(
            $this->baseUrl() . '/categories/',
            [
                'project_id'   => $projectId,
                'media'        => $media,
                'start_date'   => $startDate,
                'end_date'     => $endDate,
                'start_time'   => $startTime,
                'end_time'     => $endTime,
                'localities'   => '',
                'group_ids'    => '',
                'category_ids' => '',
                'token'        => $token,
            ]
        );

        $res->throw();
        return $this->parseJson($res);
    }

    // 🔥 NEW: est_reach - TIMEOUT 60s karena endpoint berat
    public function estReach(
        string $projectId,
        string $media,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23,
        string $type = 'all'
    ): array {
        try {
            $token = $this->getToken();

            $res = Http::timeout(60)->acceptJson()->get(
                $this->baseUrl() . '/est_reach/',
                [
                    'project_id' => $projectId,
                    'media'      => $media,
                    'start_date' => $startDate,
                    'end_date'   => $endDate,
                    'start_time' => $startTime,
                    'end_time'   => $endTime,
                    'type'       => $type,
                    'token'      => $token,
                    'is_cache'   => 'true',
                ]
            );

            $res->throw();
            return $this->parseJson($res);
        } catch (\Exception $e) {
            Log::warning('est_reach API timeout or error', ['error' => $e->getMessage()]);
            return ['data' => []]; // Return empty on error
        }
    }

    // 🔥 NEW: get_geo_twitter_user
    public function geoTwitterUser(
        string $projectId,
        string $media,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23
    ): array {
        try {
            $token = $this->getToken();

            $res = Http::timeout(60)->acceptJson()->get(
                $this->baseUrl() . '/get_geo_twitter_user/',
                [
                    'project_id' => $projectId,
                    'media'      => $media,
                    'start_date' => $startDate,
                    'end_date'   => $endDate,
                    'start_time' => $startTime,
                    'end_time'   => $endTime,
                    'format'     => 'json',
                    'token'      => $token,
                ]
            );

            $res->throw();
            return $this->parseJson($res);
        } catch (\Exception $e) {
            Log::warning('geoTwitterUser API timeout or error', ['error' => $e->getMessage()]);
            return ['data' => []];
        }
    }

    // 🔥 NEW: get_sentiment
    public function getSentiment(
        string $projectId,
        string $media,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23
    ): array {
        try {
            $token = $this->getToken();

            $res = Http::timeout(60)->acceptJson()->get(
                $this->baseUrl() . '/get_sentiment/',
                [
                    'project_id'              => $projectId,
                    'media'                   => $media,
                    'start_date'              => $startDate,
                    'end_date'                => $endDate,
                    'start_time'              => $startTime,
                    'end_time'                => $endTime,
                    'skip_docs'               => '1',
                    'with_training_totals'    => '0',
                    'with_relevancy_totals'   => '0',
                    'with_sentiments_bymedia' => '0',
                    'token'                   => $token,
                ]
            );

            $res->throw();
            return $this->parseJson($res);
        } catch (\Exception $e) {
            Log::warning('getSentiment API timeout or error', ['error' => $e->getMessage()]);
            return ['data' => []];
        }
    }

    // 🔥 NEW: get_shared_url_freq
    public function sharedUrlFreq(
        string $projectId,
        string $startDate,
        string $endDate
    ): array {
        try {
            $token = $this->getToken();

            $res = Http::timeout(60)->acceptJson()->get(
                $this->baseUrl() . '/get_shared_url_freq/',
                [
                    'project_id' => $projectId,
                    'start_date' => $startDate,
                    'end_date'   => $endDate,
                    'token'      => $token,
                ]
            );

            $res->throw();
            return $this->parseJson($res);
        } catch (\Exception $e) {
            Log::warning('sharedUrlFreq API timeout or error', ['error' => $e->getMessage()]);
            return ['data' => []];
        }
    }

    // 🔥 NEW: most_active_users - TIMEOUT 60s karena ini yang error
    public function mostActiveUsers(
        string $projectId,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23
    ): array {
        try {
            $token = $this->getToken();

            $res = Http::timeout(60)->acceptJson()->get(
                $this->baseUrl() . '/most_active_users/',
                [
                    'project_id' => $projectId,
                    'start_date' => $startDate,
                    'end_date'   => $endDate,
                    'start_time' => $startTime,
                    'end_time'   => $endTime,
                    'token'      => $token,
                ]
            );

            $res->throw();
            return $this->parseJson($res);
        } catch (\Exception $e) {
            Log::warning('mostActiveUsers API timeout or error', ['error' => $e->getMessage()]);
            return ['data' => []]; // Return empty array instead of throwing error
        }
    }
}