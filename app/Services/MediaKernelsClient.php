<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MediaKernelsClient
{
    // ── Cache TTL default (menit) ─── ubah sesuai kebutuhan
    // data real-time → 5 | laporan harian → 30
    private int $cacheTtl = 10;
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
        $json = $res->json();
        if (is_array($json)) return $json;

        $body = $res->body();
        $decoded = json_decode($body, true);
        return is_array($decoded) ? $decoded : ['raw' => $body];
    }

    // ─────────────────────────────────────────────────────
    // GLOBAL CACHE HELPERS
    // ─────────────────────────────────────────────────────

    /**
     * Buat cache key dari endpoint + params (tanpa token).
     */
    private function _cacheKey(string $endpoint, array $params): string
    {
        unset($params['token']);
        ksort($params);
        return 'mk:resp:' . $endpoint . ':' . md5(json_encode($params));
    }

    /**
     * Wrapper cache universal untuk semua API call.
     *
     * Cara pakai di method manapun:
     *   return $this->_cached('/endpoint/', $params, function($p) {
     *       return Http::timeout(30)->acceptJson()
     *           ->get($this->baseUrl().'/endpoint/', $p)->throw()->json();
     *   });
     *
     * @param int|null $ttlMinutes  null = pakai $this->cacheTtl
     */
    private function _cached(string $endpoint, array $params, callable $fetcher, ?int $ttlMinutes = null): array
    {
        $key = $this->_cacheKey($endpoint, $params);
        $ttl = now()->addMinutes($ttlMinutes ?? $this->cacheTtl);

        return Cache::remember($key, $ttl, function () use ($params, $fetcher) {
            $result = $fetcher($params);
            return is_array($result) ? $result : [];
        });
    }

    /**
     * Hapus semua cache MK response (panggil setelah user ganti filter/project).
     */
    public function bustCache(): void
    {
        // Untuk Redis/Memcached: bisa pakai Cache::tags(['mk:resp'])->flush();
        // Untuk file cache (default): flush semua cache app.
        // Atau lebih aman: biarkan TTL expire sendiri.
        Log::info('MediaKernelsClient: manual cache bust triggered');
        // Cache::flush(); // ← uncomment jika perlu hard reset
    }

    // ─────────────────────────────────────────────────────
    // AUTH
    // ─────────────────────────────────────────────────────

    public function getToken(): string
    {
        return Cache::remember('mk:token', now()->addMinutes(50), function () {
            if (!$this->username() || !$this->password()) {
                throw new \RuntimeException("ENV MEDIAKERNELS_USERNAME/PASSWORD belum di-set.");
            }

            $res = Http::timeout(30)
                ->acceptJson()
                ->get($this->baseUrl() . '/token/', [
                    'username'    => $this->username(),
                    'password'    => $this->password(),
                    'for_api'     => 'true',
                    'for_chatbot' => 'false',
                ]);

            $res->throw();

            $json  = $this->parseJson($res);
            $token = $json['token'] ?? null;

            Log::info('MediaKernels /token/ response', ['response' => $json]);

            if (!$token) {
                Log::error('Token not found in response', [
                    'available_keys' => array_keys($json),
                    'full_response'  => $json,
                ]);
                throw new \RuntimeException("Token tidak ditemukan pada response /token/");
            }

            return $token;
        });
    }

    // ─────────────────────────────────────────────────────
    // PROJECTS
    // ─────────────────────────────────────────────────────

    public function listProjects(int $start = 0, int $limit = 20): array
    {
        $token = $this->getToken();

        $res = Http::timeout(30)->acceptJson()->get($this->baseUrl() . '/projects', [
            'start'    => $start,
            'limit'    => $limit,
            'sort'     => 'id desc',
            'client'   => '',
            'is_cache' => 'true',
            'token'    => $token,
        ]);

        if ($res->status() === 401) {
            Cache::forget('mk:token');
            $token = $this->getToken();

            $res = Http::timeout(30)->acceptJson()->get($this->baseUrl() . '/projects', [
                'start'    => $start,
                'limit'    => $limit,
                'sort'     => 'id desc',
                'client'   => '',
                'is_cache' => 'true',
                'token'    => $token,
            ]);
        }

        $res->throw();
        return $this->parseJson($res);
    }

    // ─────────────────────────────────────────────────────
    // AUTHORS DEMOGRAPHICS
    // ─────────────────────────────────────────────────────

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

    // ─────────────────────────────────────────────────────
    // SENTIMENT
    // ─────────────────────────────────────────────────────

    public function sentimentTotal(
        string $projectId,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23
    ): array {
        $params = [
            'project_id' => $projectId, 'start_date' => $startDate,
            'end_date' => $endDate, 'start_time' => $startTime, 'end_time' => $endTime,
        ];
        return $this->_cached('/sentiment_total/', $params, function ($p) {
            $p['token'] = $this->getToken();
            $res = Http::timeout(30)->acceptJson()->get($this->baseUrl() . '/sentiment_total/', $p);
            $res->throw();
            return $this->parseJson($res);
        });
    }

    public function sentimentMedia(
        string $projectId,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23
    ): array {
        try {
            $token = $this->getToken();

            $res = Http::timeout(60)->acceptJson()->get(
                $this->baseUrl() . '/sentiment_media/',
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

            $json = $this->parseJson($res);

            Log::info('sentimentMedia API response', [
                'has_all'        => isset($json['all']),
                'has_bymedia'    => isset($json['bymedia']),
                'top_level_keys' => array_keys($json),
            ]);

            return $json;

        } catch (\Exception $e) {
            Log::error('sentimentMedia API error', [
                'error'      => $e->getMessage(),
                'project_id' => $projectId,
            ]);
            return ['all' => 0, 'bymedia' => []];
        }
    }

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

    // ─────────────────────────────────────────────────────
    // GEOGRAPHIC
    // ─────────────────────────────────────────────────────

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

    public function topAuthorLocation(
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
                $this->baseUrl() . '/top_author_location/',
                [
                    'project_id' => $projectId,
                    'media'      => $media,
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
            Log::warning('topAuthorLocation API error', ['error' => $e->getMessage()]);
            return ['data' => []];
        }
    }

    // ─────────────────────────────────────────────────────
    // PROJECT STATS & DATA SOURCE
    // ─────────────────────────────────────────────────────

    public function projectStats(
        string $projectId,
        string $media,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23,
        string $types = 'volumetotal'
    ): array {
        $params = [
            'project_id' => $projectId, 'media' => $media,
            'start_date' => $startDate, 'end_date' => $endDate,
            'start_time' => $startTime, 'end_time' => $endTime,
            'types' => $types, 'is_cache' => 'true',
        ];
        return $this->_cached('/project_stats/', $params, function ($p) {
            $p['token'] = $this->getToken();
            $res = Http::timeout(30)->acceptJson()->get($this->baseUrl() . '/project_stats/', $p);
            $res->throw();
            return $this->parseJson($res);
        });
    }

    public function totalUsers(
        string $projectId,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23
    ): array {
        try {
            $token = $this->getToken();

            $res = Http::timeout(60)->acceptJson()->get(
                $this->baseUrl() . '/total_users/',
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

            $json = $this->parseJson($res);

            Log::info('totalUsers API response', [
                'has_data'       => isset($json['data']),
                'top_level_keys' => array_keys($json),
            ]);

            return $json;

        } catch (\Exception $e) {
            Log::error('totalUsers API error', [
                'error'      => $e->getMessage(),
                'project_id' => $projectId,
            ]);
            return ['data' => []];
        }
    }

    public function totalAuthors(
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
                $this->baseUrl() . '/total_authors/',
                [
                    'project_id' => $projectId,
                    'media'      => $media,
                    'start_date' => $startDate,
                    'end_date'   => $endDate,
                    'start_time' => $startTime,
                    'end_time'   => $endTime,
                    'token'      => $token,
                ]
            );

            $res->throw();

            $json = $this->parseJson($res);

            Log::info('totalAuthors API response', [
                'has_data'       => isset($json['data']),
                'top_level_keys' => array_keys($json),
            ]);

            return $json;

        } catch (\Exception $e) {
            Log::error('totalAuthors API error', [
                'error'      => $e->getMessage(),
                'project_id' => $projectId,
            ]);
            return ['data' => []];
        }
    }

    public function volumeTotal(
        string $projectId,
        string $media,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23,
        bool $isCache = true
    ): array {
        $params = [
            'project_id' => $projectId, 'media' => $media,
            'start_date' => $startDate, 'end_date' => $endDate,
            'start_time' => $startTime, 'end_time' => $endTime,
            'is_cache'   => $isCache ? 'true' : 'false',
        ];
        return $this->_cached('/volume_total/', $params, function ($p) {
            try {
                $p['token'] = $this->getToken();
                $res = Http::timeout(60)->acceptJson()->get($this->baseUrl() . '/volume_total/', $p);
                $res->throw();
                $json = $this->parseJson($res);
                Log::info('volumeTotal API response', ['has_data' => isset($json['data']), 'top_level_keys' => array_keys($json)]);
                return $json;
            } catch (\Exception $e) {
                Log::error('volumeTotal API error', ['error' => $e->getMessage()]);
                return ['data' => []];
            }
        });
    }

    public function trendsTotal(
        string $projectId,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23
    ): array {
        try {
            $token = $this->getToken();

            $res = Http::timeout(60)->acceptJson()->get(
                $this->baseUrl() . '/trends_total/',
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

            $json = $this->parseJson($res);

            Log::info('trendsTotal API raw response', [
                'response'       => $json,
                'top_level_keys' => array_keys($json),
            ]);

            $transformedData = [];

            foreach ($json as $datetime => $mediaCounts) {
                if (!is_array($mediaCounts)) continue;

                $date = date('Y-m-d', strtotime($datetime));

                foreach ($mediaCounts as $mediaType => $count) {
                    $keyword = strtoupper($mediaType);
                    $found   = false;

                    foreach ($transformedData as &$trend) {
                        if ($trend['keyword'] === $keyword) {
                            $trend['data'][] = ['date' => $date, 'count' => (int) $count];
                            $found = true;
                            break;
                        }
                    }

                    if (!$found) {
                        $transformedData[] = [
                            'keyword' => $keyword,
                            'data'    => [['date' => $date, 'count' => (int) $count]],
                        ];
                    }
                }
            }

            Log::info('trendsTotal transformed data', [
                'count'  => count($transformedData),
                'sample' => array_slice($transformedData, 0, 2, true),
            ]);

            return ['data' => $transformedData];

        } catch (\Exception $e) {
            Log::error('trendsTotal API error', [
                'error'      => $e->getMessage(),
                'project_id' => $projectId,
            ]);
            return ['data' => []];
        }
    }

    // ─────────────────────────────────────────────────────
    // CONTENT & ENGAGEMENT
    // ─────────────────────────────────────────────────────

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
            return ['data' => []];
        }
    }

    public function mentions(
        string $projectId,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23,
        bool $withContent = true,
        int $start = 0,
        int $rows = 10
    ): array {
        try {
            $token = $this->getToken();

            $res = Http::timeout(60)->acceptJson()->get(
                $this->baseUrl() . '/mentions/',
                [
                    'project_id'   => $projectId,
                    'start_date'   => $startDate,
                    'end_date'     => $endDate,
                    'start_time'   => $startTime,
                    'end_time'     => $endTime,
                    'with_content' => $withContent ? 'true' : 'false',
                    'start'        => $start,
                    'rows'         => $rows,
                    'token'        => $token,
                ]
            );

            $res->throw();
            return $this->parseJson($res);
        } catch (\Exception $e) {
            Log::warning('mentions API error', ['error' => $e->getMessage()]);
            return ['data' => []];
        }
    }

    public function getUserMentions(
        string $projectId,
        string $startDate,
        string $endDate,
        string $username,
        int $startTime = 0,
        int $endTime = 23,
        int $start = 0,
        int $batchSize = 200
    ): array {
        try {
            $token = $this->getToken();
            $userMentions = [];
            $apiStart = $start;
            $maxResults = 50;
            $maxScans   = 10;
            $scanCount  = 0;

            while (count($userMentions) < $maxResults && $scanCount < $maxScans) {
                $res = Http::timeout(60)->acceptJson()->get(
                    $this->baseUrl() . '/mentions/',
                    [
                        'project_id'   => $projectId,
                        'start_date'   => $startDate,
                        'end_date'     => $endDate,
                        'start_time'   => 0,
                        'end_time'     => 23,
                        'with_content' => 'true',
                        'start'        => $apiStart,
                        'rows'         => $batchSize,
                        'token'        => $token,
                    ]
                );

                $res->throw();
                $batch = $this->parseJson($res);

                if (empty($batch) || !is_array($batch)) break;

                foreach ($batch as $mention) {
                    $scrName = strtolower($mention['author_scr_name'] ?? '');
                    if ($scrName === strtolower($username)) {
                        $userMentions[] = $mention;
                    }
                }

                $scanCount++;
                if (count($batch) < $batchSize) break;
                $apiStart += $batchSize;
            }

            usort($userMentions, function($a, $b) {
                return strtotime($b['date_created'] ?? '0') - strtotime($a['date_created'] ?? '0');
            });

            $hasMore = count($userMentions) >= $maxResults && $scanCount >= $maxScans;

            Log::info('getUserMentions', [
                'username'       => $username,
                'scans'          => $scanCount,
                'total_found'    => count($userMentions),
                'has_more'       => $hasMore,
                'next_api_start' => $apiStart,
            ]);

            return [
                'mentions'       => array_slice($userMentions, 0, $maxResults),
                'has_more'       => $hasMore,
                'next_api_start' => $apiStart,
            ];

        } catch (\Exception $e) {
            Log::error('getUserMentions error', ['error' => $e->getMessage()]);
            return ['mentions' => [], 'has_more' => false, 'next_api_start' => 0];
        }
    }

    public function postWithLocation(
        int $projectId,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23,
        int $start = 0,
        int $rows = 100
    ): array {
        try {
            $token = $this->getToken();

            $res = Http::timeout(60)->acceptJson()->get(
                $this->baseUrl() . '/posts/',
                [
                    'project_id' => $projectId,
                    'start_date' => $startDate,
                    'end_date'   => $endDate,
                    'start_time' => $startTime,
                    'end_time'   => $endTime,
                    'localities' => '',
                    'media'      => 'all',
                    'start'      => $start,
                    'rows'       => $rows,
                    'token'      => $token,
                ]
            );

            $res->throw();
            return $this->parseJson($res);
        } catch (\Exception $e) {
            Log::warning('postWithLocation API error', ['error' => $e->getMessage()]);
            return [];
        }
    }

    // ─────────────────────────────────────────────────────
    // HASHTAGS & TOPICS
    // ─────────────────────────────────────────────────────

   public function topHashtags(
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
            $this->baseUrl() . '/top_hashtags/',
            [
                'project_id' => $projectId,
                'media'      => $media,
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'start_time' => $startTime,
                'end_time'   => $endTime,
                'token'      => $token,
            ]
        );

        Log::info('Top Hashtags HTTP status', [
            'status'     => $res->status(),
            'project_id' => $projectId,
        ]);

        $json = $this->parseJson($res);

        Log::info('Top Hashtags API response', [
            'project_id' => $projectId,
            'is_array'   => is_array($json),
            'data_count' => is_array($json) ? count($json) : 0,
            'keys'       => is_array($json) ? array_keys($json) : 'not_array',
            'first_item' => is_array($json) && count($json) > 0 ? $json[array_keys($json)[0]] : null,
        ]);

        $res->throw();
        return $json;

    } catch (\Exception $e) {
        Log::error('topHashtags API error', [
            'error'      => $e->getMessage(),
            'project_id' => $projectId,
            'class'      => get_class($e),
            'line'       => $e->getLine(),
            'file'       => $e->getFile(),
        ]);
        return ['data' => []];
    }
}

    public function twitterTrendingTopics(
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23,
        string $location = 'Indonesia',
        string $topics = ''
    ): array {
        try {
            $token = $this->getToken();

            $res = Http::timeout(60)->acceptJson()->get(
                $this->baseUrl() . '/twitter_trending_topics/',
                [
                    'start_date' => $startDate,
                    'end_date'   => $endDate,
                    'start_time' => $startTime,
                    'end_time'   => $endTime,
                    'location'   => $location,
                    'topics'     => $topics,
                    'token'      => $token,
                ]
            );

            $res->throw();

            $json = $this->parseJson($res);

            Log::info('twitterTrendingTopics API response', [
                'count'  => is_array($json) ? count($json) : 0,
                'sample' => is_array($json) && count($json) > 0 ? array_keys($json)[0] : null,
            ]);

            return $json;

        } catch (\Exception $e) {
            Log::error('twitterTrendingTopics API error', [
                'error'      => $e->getMessage(),
                'start_date' => $startDate,
            ]);
            return [];
        }
    }

    public function topicMap(
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
                $this->baseUrl() . '/topic_map/',
                [
                    'project_id' => $projectId,
                    'media'      => $media,
                    'start_date' => $startDate,
                    'end_date'   => $endDate,
                    'start_time' => $startTime,
                    'end_time'   => $endTime,
                    'token'      => $token,
                ]
            );

            $res->throw();

            $json = $this->parseJson($res);

            Log::info('topicMap API response', [
                'total_topics' => count($json),
                'sample'       => array_slice($json, 0, 5, true),
            ]);

            return $json;

        } catch (\Exception $e) {
            Log::error('topicMap API error', [
                'error'      => $e->getMessage(),
                'project_id' => $projectId,
            ]);
            return [];
        }
    }

    public function recentTopics(
        string $level = 'internasional',
        int $size = 5
    ): array {
        try {
            $token = $this->getToken();

            $res = Http::timeout(30)->acceptJson()->get(
                $this->baseUrl() . '/recenttopics/',
                [
                    'level' => $level,
                    'size'  => $size,
                    'token' => $token,
                ]
            );

            $res->throw();
            return $this->parseJson($res);
        } catch (\Exception $e) {
            Log::warning('recentTopics API timeout or error', ['error' => $e->getMessage()]);
            return ['data' => []];
        }
    }

    // ─────────────────────────────────────────────────────
    // PUBLISHER & INFLUENCERS
    // ─────────────────────────────────────────────────────

    public function publisherStats(
        string $projectId,
        string $media,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23,
        int $rows = 100,
        bool $includePagerank = true
    ): array {
        try {
            $token = $this->getToken();

            $params = [
                'project_id' => $projectId,
                'media'      => $media,
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'start_time' => $startTime,
                'end_time'   => $endTime,
                'rows'       => $rows,
                'token'      => $token,
            ];

            if ($includePagerank) {
                $params['pagerank'] = '';
            }

            $res = Http::timeout(60)->acceptJson()->get(
                $this->baseUrl() . '/publisher_stats/',
                $params
            );

            $res->throw();

            $json = $this->parseJson($res);

            Log::info('publisherStats raw response', [
                'has_data'       => isset($json['data']),
                'has_article'    => isset($json['article']),
                'top_level_keys' => array_keys($json),
                'sample_data'    => array_slice($json, 0, 2, true),
            ]);

            return $json;

        } catch (\Exception $e) {
            Log::error('publisherStats API error', [
                'error'  => $e->getMessage(),
                'trace'  => $e->getTraceAsString(),
                'params' => $params ?? [],
            ]);
            return ['data' => [], 'article' => []];
        }
    }


    public function sharedUrlFreq(
        string $projectId,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23
    ): array {
        try {
            $token = $this->getToken();

            $res = Http::timeout(60)->acceptJson()->get(
                $this->baseUrl() . '/get_shared_url_freq/',
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

            $json = $this->parseJson($res);

            Log::info('sharedUrlFreq API response', [
                'status'      => $json['status'] ?? 'unknown',
                'total_items' => isset($json['data']) ? count($json['data']) : 0,
            ]);

            return $json;

        } catch (\Exception $e) {
            Log::warning('sharedUrlFreq API error', ['error' => $e->getMessage()]);
            return ['status' => 'error', 'data' => []];
        }
    }
   public function recentTopicsHybrid(
        string $level = 'internasional',
        int $size = 5
    ): array {
        Log::info('=== HYBRID recentTopics CALLED ===', [
            'level' => $level,
            'size' => $size,
        ]);

        // Try v2 first
        try {
            Log::info('Attempting v2 endpoint...');
            
            $token = $this->getToken();
            $url = $this->baseUrl() . '/recenttopics_v2/';
            
            $res = Http::timeout(60)->acceptJson()->get($url, [
                'level' => $level,
                'size'  => $size,
                'token' => $token,
            ]);

            Log::info('v2 HTTP Response', [
                'status_code' => $res->status(),
                'success' => $res->successful(),
            ]);

            $res->throw();
            $json = $this->parseJson($res);

            Log::info('v2 Raw Response', [
                'status' => $json['status'] ?? 'not_set',
                'has_current_issue' => isset($json['current_issue']),
                'response_sample' => json_encode($json, JSON_PRETTY_PRINT),
            ]);

            // Check if v2 has valid data
            if (isset($json['status']) && $json['status'] === 'success' && isset($json['current_issue'][$level])) {
                $count = count($json['current_issue'][$level]);
                Log::info('✅ v2 SUCCESS - Using v2 data', [
                    'level' => $level,
                    'count' => $count,
                ]);
                
                // Transform v2 structure to standardized format
                $transformed = [
                    'daftar_isu' => [],
                    'api_version' => 'v2',
                    'status' => 'success',
                ];

                foreach ($json['current_issue'][$level] as $issue) {
                    $transformed['daftar_isu'][] = [
                        'judul' => $issue['issue_title'] ?? 'Untitled',
                        'deskripsi' => '', // v2 doesn't have description
                        'referensi' => isset($issue['urls']) && count($issue['urls']) > 0 ? $issue['urls'][0] : null,
                        'urls' => $issue['urls'] ?? [],
                    ];
                }

                return $transformed;
            }

            // v2 returned error or empty data
            Log::warning('⚠️ v2 returned error/empty, falling back to v1', [
                'v2_status' => $json['status'] ?? 'unknown',
                'v2_message' => $json['message'] ?? 'no message',
            ]);
            
            throw new \Exception('v2 returned error or empty data, fallback to v1');

        } catch (\Exception $e) {
            Log::warning('v2 failed, attempting v1 fallback', [
                'v2_error' => $e->getMessage(),
            ]);

            // Fallback to v1
            try {
                Log::info('Attempting v1 endpoint...');
                
                $token = $this->getToken();
                $url = $this->baseUrl() . '/recenttopics/';
                
                $res = Http::timeout(60)->acceptJson()->get($url, [
                    'level' => $level,
                    'size'  => $size,
                    'token' => $token,
                ]);

                Log::info('v1 HTTP Response', [
                    'status_code' => $res->status(),
                    'success' => $res->successful(),
                ]);

                $res->throw();
                $json = $this->parseJson($res);

                Log::info('v1 Raw Response', [
                    'has_daftar_isu' => isset($json['daftar_isu']),
                    'count' => isset($json['daftar_isu']) ? count($json['daftar_isu']) : 0,
                    'response_sample' => json_encode($json, JSON_PRETTY_PRINT),
                ]);

                // Check if v1 has valid data
                if (isset($json['daftar_isu']) && is_array($json['daftar_isu']) && count($json['daftar_isu']) > 0) {
                    Log::info('✅ v1 SUCCESS - Using v1 data', [
                        'level' => $level,
                        'count' => count($json['daftar_isu']),
                    ]);
                    
                    $json['api_version'] = 'v1';
                    $json['status'] = 'success';
                    return $json;
                }

                // v1 also empty
                Log::warning('⚠️ v1 also returned empty data');
                throw new \Exception('v1 returned empty data');

            } catch (\Exception $e1) {
                Log::error('❌ Both v2 and v1 failed', [
                    'v2_error' => $e->getMessage(),
                    'v1_error' => $e1->getMessage(),
                ]);

                return [
                    'daftar_isu' => [],
                    'api_version' => 'error',
                    'status' => 'error',
                    'message' => 'Both v2 and v1 endpoints failed',
                ];
            }
        }
    }
public function wordCloud(
    string $projectId,
    string $startDate,
    int $startTime = 0,
    string $endDate,
    int $endTime = 23,
    string $sentiment = '2'
): array {
    try {
        $token = $this->getToken();

        $res = Http::timeout(60)->acceptJson()->get(
            $this->baseUrl() . '/wordcloud/',
            [
                'project_id' => $projectId,
                'start_date' => $startDate,
                'start_time' => $startTime,
                'end_date'   => $endDate,
                'end_time'   => $endTime,
                'sentiment'  => $sentiment,
                'token'      => $token,
            ]
        );

        $res->throw();

        $json = $this->parseJson($res);

        Log::info('wordCloud API response', [
            'status'        => $json['status'] ?? 'unknown',
            'has_data'      => isset($json['data']),
            'has_phrases'   => isset($json['data']['phrases']),
            'phrases_count' => isset($json['data']['phrases']) ? count($json['data']['phrases']) : 0,
            'numrows'       => $json['numrows'] ?? 0,
        ]);

        return $json;

    } catch (\Exception $e) {
        Log::error('wordCloud API error', [
            'error'      => $e->getMessage(),
            'project_id' => $projectId,
            'sentiment'  => $sentiment,
        ]);
        return [
            'status' => 'error',
            'data'   => ['phrases' => [], 'sites' => []],
            'numrows' => 0,
        ];
    }
}
public function topPublisher(
    string $projectId,
    string $startDate,
    string $endDate,
    int $startTime = 0,
    int $endTime = 23,
    int $rows = 100,
    string $newsType = 'article'
): array {
    try {
        $token = $this->getToken();

        $res = Http::timeout(60)->acceptJson()->get(
            $this->baseUrl() . '/top_publisher/',
            [
                'project_id' => $projectId,
                'start_date' => $startDate,
                'start_time' => $startTime,
                'end_date'   => $endDate,
                'end_time'   => $endTime,
                'rows'       => $rows,
                'news_type'  => $newsType,
                'token'      => $token,
            ]
        );

        $res->throw();

        $json = $this->parseJson($res);

        Log::info('topPublisher API response', [
            'total_publishers' => count($json),
            'top_3'            => array_slice(array_keys($json), 0, 3),
        ]);

        return $json;

    } catch (\Exception $e) {
        Log::error('topPublisher API error', [
            'error'      => $e->getMessage(),
            'project_id' => $projectId,
        ]);
        return [];
    }
}


public function articles(
    string $projectId,
    string $media = 'doc',
    string $startDate,
    string $endDate,
    int $startTime = 0,
    int $endTime = 23,
    int $start = 0,
    int $rows = 100,
    bool $withQuotes = true
): array {
    try {
        $token = $this->getToken();

        $res = Http::timeout(60)->acceptJson()->get(
            $this->baseUrl() . '/articles/',
            [
                'project_id'  => $projectId,
                'media'       => $media,
                'start_date'  => $startDate,
                'start_time'  => $startTime,
                'end_date'    => $endDate,
                'end_time'    => $endTime,
                'start'       => $start,
                'rows'        => $rows,
                'with_quotes' => $withQuotes ? 'true' : 'false',
                'token'       => $token,
            ]
        );

        $res->throw();

        $json = $this->parseJson($res);

        Log::info('articles API response', [
            'total_articles' => count($json),
            'has_quotes'     => $withQuotes,
            'media_type'     => $media,
        ]);

        return $json;

    } catch (\Exception $e) {
        Log::error('articles API error', [
            'error'      => $e->getMessage(),
            'project_id' => $projectId,
        ]);
        return [];
    }
}


public function fbTopStatus(
    string $projectId,
    string $startDate,
    string $endDate,
    int $startTime = 0,
    int $endTime = 23,
    int $rows = 100,
    string $sub = 'fblike'
): array {
    try {
        $token = $this->getToken();

        $res = Http::timeout(60)->acceptJson()->get(
            $this->baseUrl() . '/fb_top_status/',
            [
                'project_id' => $projectId,
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'start_time' => $startTime,
                'end_time'   => $endTime,
                'rows'       => $rows,
                'sub'        => $sub,
                'is_cache'   => 'true',
                'token'      => $token,
            ]
        );

        $res->throw();
        return $this->parseJson($res);
    } catch (\Exception $e) {
        Log::warning('fbTopStatus API error', ['error' => $e->getMessage()]);
        return [];
    }
}


// ─────────────────────────────────────────────────────
// GEOGRAPHIC - FACEBOOK
// ─────────────────────────────────────────────────────

public function geoUserFacebook(
    string $projectId,
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
                'media'      => 'fb',
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'start_time' => $startTime,
                'end_time'   => $endTime,
                'format'     => 'json',
                'token'      => $token,
            ]
        );

        $res->throw();

        $json = $this->parseJson($res);

        if (isset($json['data']) && is_array($json['data'])) {
            $json['data'] = array_values(array_filter($json['data'], function ($item) {
                $itemMedia = strtolower($item['media'] ?? $item['source'] ?? $item['tcode'] ?? '');
                if (!$itemMedia) return true;
                return str_contains($itemMedia, 'fb');
            }));
        }

        return $json;
    } catch (\Exception $e) {
        Log::warning('geoUserFacebook API error', ['error' => $e->getMessage()]);
        return ['data' => []];
    }
}

public function geoSentimentFacebook(
    string $projectId,
    string $startDate,
    string $endDate,
    int $startTime = 0,
    int $endTime = 23,
    int $sentiment = 1
): array {
    try {
        $token = $this->getToken();

        $res = Http::timeout(60)->acceptJson()->get(
            $this->baseUrl() . '/get_geo_twitter_user_sentiment/',
            [
                'project_id' => $projectId,
                'media'      => 'fb',
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'start_time' => $startTime,
                'end_time'   => $endTime,
                'sentiment'  => $sentiment,
                'format'     => 'json',
                'token'      => $token,
            ]
        );

        $res->throw();

        $json = $this->parseJson($res);

        if (isset($json['data']) && is_array($json['data'])) {
            $json['data'] = array_values(array_filter($json['data'], function ($item) {
                $itemMedia = strtolower($item['media'] ?? $item['source'] ?? $item['tcode'] ?? '');
                if (!$itemMedia) return true;
                return str_contains($itemMedia, 'fb');
            }));
        }

        return $json;
    } catch (\Exception $e) {
        Log::warning('geoSentimentFacebook API error', ['error' => $e->getMessage()]);
        return ['data' => []];
    }
}

public function topLocationsFacebook(
    string $projectId,
    string $startDate,
    string $endDate,
    int $startTime = 0,
    int $endTime = 23
): array {
    try {
        $token = $this->getToken();

        $res = Http::timeout(60)->acceptJson()->get(
            $this->baseUrl() . '/top_author_location/',
            [
                'project_id' => $projectId,
                'media'      => 'fb',
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'start_time' => $startTime,
                'end_time'   => $endTime,
                'token'      => $token,
            ]
        );

        $res->throw();

        $json = $this->parseJson($res);

        $items = isset($json['data']) ? $json['data'] : (is_array($json) ? $json : []);
        $filtered = array_values(array_filter($items, function ($item) {
            $itemMedia = strtolower($item['media'] ?? $item['source'] ?? '');
            if (!$itemMedia) return true;
            return str_contains($itemMedia, 'fb');
        }));

        return isset($json['data']) ? array_merge($json, ['data' => $filtered]) : $filtered;

    } catch (\Exception $e) {
        Log::warning('topLocationsFacebook API error', ['error' => $e->getMessage()]);
        return ['data' => []];
    }
}
public function tiktokTopStatus(
    string $projectId,
    string $startDate,
    string $endDate,
    int $startTime = 0,
    int $endTime = 23,
    int $rows = 1000,
    string $sub = 'postbylike'
): array {
    try {
        $token = $this->getToken();

        $res = Http::timeout(60)->acceptJson()->get(
            $this->baseUrl() . '/tiktok_top_status/',
            [
                'project_id' => $projectId,
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'start_time' => $startTime,
                'end_time'   => $endTime,
                'rows'       => $rows,
                'sub'        => $sub,
                'token'      => $token,
            ]
        );

        $res->throw();

        $json = $this->parseJson($res);

        Log::info('tiktokTopStatus API response', [
            'project_id'    => $projectId,
            'sub'           => $sub,
            'total_items'   => is_array($json) ? count($json) : 0,
            'first_item'    => is_array($json) && count($json) > 0 ? array_slice($json[0], 0, 5) : null,
        ]);

        return is_array($json) ? $json : [];

    } catch (\Exception $e) {
        Log::error('tiktokTopStatus API error', [
            'error'      => $e->getMessage(),
            'project_id' => $projectId,
        ]);
        return [];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// INSTAGRAM TOP STATUS
// API: GET /ig_top_status/
// Params: project_id, rows, start_date, end_date, start_time, end_time, sub, token
// sub options: postbylike | postbycomment | postbyview
// ─────────────────────────────────────────────────────────────────────────────
public function igTopStatus(
    string $projectId,
    string $startDate,
    string $endDate,
    int $startTime = 0,
    int $endTime = 23,
    int $rows = 100,
    string $sub = 'postbylike'
): array {
    try {
        $token = $this->getToken();

        $res = Http::timeout(60)->acceptJson()->get(
            $this->baseUrl() . '/ig_top_status/',
            [
                'project_id' => $projectId,
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'start_time' => $startTime,
                'end_time'   => $endTime,
                'rows'       => $rows,
                'sub'        => $sub,
                'token'      => $token,
            ]
        );

        $res->throw();

        $json = $this->parseJson($res);

        Log::info('igTopStatus API response', [
            'project_id'  => $projectId,
            'sub'         => $sub,
            'total_items' => is_array($json) ? count($json) : 0,
        ]);

        return is_array($json) ? $json : [];

    } catch (\Exception $e) {
        Log::error('igTopStatus API error', [
            'error'      => $e->getMessage(),
            'project_id' => $projectId,
        ]);
        return [];
    }
}
public function ytbTopStatus(
    string $projectId,
    string $startDate,
    string $endDate,
    int $startTime = 0,
    int $endTime = 23,
    int $rows = 100,
    string $sub = 'postbyview'
): array {
    try {
        $token = $this->getToken();

        $res = Http::timeout(60)->acceptJson()->get(
            $this->baseUrl() . '/yt_top_status/',
            [
                'project_id' => $projectId,
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'start_time' => $startTime,
                'end_time'   => $endTime,
                'rows'       => $rows,
                'sub'        => $sub,
                'token'      => $token,
            ]
        );

        $res->throw();

        $json = $this->parseJson($res);

        Log::info('ytbTopStatus API response', [
            'project_id'  => $projectId,
            'sub'         => $sub,
            'total_items' => is_array($json) ? count($json) : 0,
            'first_item'  => is_array($json) && count($json) > 0 ? array_slice($json[0], 0, 5) : null,
        ]);

        return is_array($json) ? $json : [];

    } catch (\Exception $e) {
        Log::error('ytbTopStatus API error', [
            'error'      => $e->getMessage(),
            'project_id' => $projectId,
        ]);
        return [];
    }
}




















public function compareProjects(
    array $projectIds,
    string $startDate,
    string $endDate,
    string $types = 'volumetotal',
    int $startTime = 0,
    int $endTime = 23,
    bool $isCache = true
): array {
    $projectIdsStr = implode(',', $projectIds);

    try {
        $token = $this->getToken();

        $params = [
            'project_ids' => $projectIdsStr,
            'start_date'  => $startDate,
            'start_time'  => $startTime,
            'end_date'    => $endDate,
            'end_time'    => $endTime,
            'types'       => $types,
            'is_cache'    => $isCache ? 'true' : 'false',
            'token'       => $token,
        ];

        $res = Http::timeout(60)->acceptJson()->get(
            $this->baseUrl() . '/compare_projects/',
            $params
        );

        $res->throw();

        $json = $this->parseJson($res);

        Log::info('compareProjects API response', [
            'types'       => $types,
            'project_ids' => $projectIdsStr,
            'keys'        => is_array($json) ? array_keys($json) : gettype($json),
            'sample'      => is_array($json) ? array_slice($json, 0, 2, true) : $json,
        ]);

        return is_array($json) ? $json : [];

    } catch (\Exception $e) {
        Log::error('compareProjects API error', [
            'error'       => $e->getMessage(),
            'project_ids' => $projectIdsStr,
            'types'       => $types,
        ]);
        return [];
    }
}

public function trendsHour(
    string $projectId,
    string $startDate,
    string $endDate,
    int $startTime = 0,
    int $endTime = 23
): array {
    try {
        $token = $this->getToken();

        $res = Http::timeout(60)->acceptJson()->get(
            $this->baseUrl() . '/trends_total/',
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

        $json = $this->parseJson($res);

        Log::info('trendsHour raw response keys sample', [
            'sample_keys' => array_slice(array_keys($json), 0, 5),
        ]);

        // Kita perlu raw json SEBELUM di-transform ke per-hari
        // Cek apakah key-nya mengandung jam (misal "2026-03-01 14:00:00")
        $hourAcc = [];     // [mediaKey][0..23] = count
        $hasHourData = false;

        foreach ($json as $datetime => $mediaCounts) {
            if (!is_array($mediaCounts)) continue;

            // Coba extract jam dari datetime key
            $hour = null;
            if (strlen($datetime) > 10) {
                // Format: "2026-03-01 14:00:00" atau "2026-03-01T14:00:00"
                $ts = strtotime($datetime);
                if ($ts !== false) {
                    $hour = (int) date('H', $ts);
                    $hasHourData = true;
                }
            }

            // Kalau tidak ada jam info, skip (tidak bisa group by hour)
            if ($hour === null) continue;

            foreach ($mediaCounts as $mediaType => $count) {
                $key = strtolower($mediaType);
                if (!isset($hourAcc[$key])) {
                    $hourAcc[$key] = array_fill(0, 24, 0);
                }
                $hourAcc[$key][$hour] += (int) $count;
            }
        }

        Log::info('trendsHour processed', [
            'has_hour_data' => $hasHourData,
            'platforms'     => array_keys($hourAcc),
        ]);

        return [
            'has_hour_data' => $hasHourData,
            'data'          => $hourAcc,
        ];

    } catch (\Exception $e) {
        Log::error('trendsHour API error', [
            'error'      => $e->getMessage(),
            'project_id' => $projectId,
        ]);
        return ['has_hour_data' => false, 'data' => []];
    }
}

public function mentionKanalPeriodeHour(
    string $projectId,
    string $startDate,
    string $endDate
): array {
    try {
        $token = $this->getToken();

        // URL pattern dari Drone Emprit:
        // /widget/projectmentionkanalperiode/{project_id}/start_date/{start_date}/end_date/{end_date}/media//periode/hour
        $url = $this->baseUrl()
            . '/widget/projectmentionkanalperiode/' . $projectId
            . '/start_date/' . $startDate
            . '/end_date/' . $endDate
            . '/media//periode/hour';

        $res = Http::timeout(60)->acceptJson()->get($url, [
            'token' => $token,
        ]);

        $res->throw();

        $json = $this->parseJson($res);

        Log::info('mentionKanalPeriodeHour response', [
            'project_id' => $projectId,
            'type'       => gettype($json),
            'count'      => is_array($json) ? count($json) : 0,
            'sample'     => is_array($json) ? array_slice($json, 0, 2) : $json,
        ]);

        return is_array($json) ? $json : [];

    } catch (\Exception $e) {
        Log::error('mentionKanalPeriodeHour API error', [
            'error'      => $e->getMessage(),
            'project_id' => $projectId,
        ]);
        return [];
    }
}

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

        $json = $this->parseJson($res);

        Log::info('mostActiveUsers API response', [
            'project_id'  => $projectId,
            'total_items' => is_array($json) ? count($json) : 0,
            'keys'        => is_array($json) ? array_keys($json) : [],
        ]);

        return is_array($json) ? $json : [];

    } catch (\Exception $e) {
        Log::warning('mostActiveUsers API timeout or error', ['error' => $e->getMessage()]);
        return ['data' => []];
    }
}

/**
 * Get all posts/activity by a specific Twitter user
 * Scans /mentions/ endpoint with larger batch and more iterations
 */
public function getUserPosts(
    string $projectId,
    string $startDate,
    string $endDate,
    string $username,
    int $maxResults = 200,
    int $batchSize  = 500,
    int $maxScans   = 20,
    int $startFrom  = 0
): array {
    try {
        $token      = $this->getToken();
        $userPosts  = [];
        $apiStart   = $startFrom;
        $scanCount  = 0;
        $totalScanned = 0;

        while (count($userPosts) < $maxResults && $scanCount < $maxScans) {
            $res = Http::timeout(90)->acceptJson()->get(
                $this->baseUrl() . '/mentions/',
                [
                    'project_id'   => $projectId,
                    'start_date'   => $startDate,
                    'end_date'     => $endDate,
                    'start_time'   => 0,
                    'end_time'     => 23,
                    'with_content' => 'true',
                    'start'        => $apiStart,
                    'rows'         => $batchSize,
                    'token'        => $token,
                ]
            );

            $res->throw();
            $batch = $this->parseJson($res);

            if (empty($batch) || !is_array($batch)) break;

            $batchCount = count($batch);
            $totalScanned += $batchCount;

            foreach ($batch as $post) {
                $scrName = strtolower(
                    $post['author_scr_name'] ?? $post['name'] ?? ''
                );

                if ($scrName === strtolower($username)) {
                    $userPosts[] = $post;
                }
            }

            $scanCount++;

            // Berhenti jika batch lebih kecil dari yang diminta (sudah habis)
            if ($batchCount < $batchSize) break;

            $apiStart += $batchSize;
        }

        // Sort by date terbaru
        usort($userPosts, function ($a, $b) {
            return strtotime($b['date_created'] ?? '0')
                 - strtotime($a['date_created'] ?? '0');
        });

        $hasMore = (count($userPosts) >= $maxResults) || 
                   ($scanCount >= $maxScans && count($userPosts) > 0);

        Log::info('getUserPosts', [
            'username'      => $username,
            'scans'         => $scanCount,
            'total_scanned' => $totalScanned,
            'found'         => count($userPosts),
            'has_more'      => $hasMore,
            'next_start'    => $apiStart,
        ]);

        return [
            'posts'          => array_slice($userPosts, 0, $maxResults),
            'has_more'       => $hasMore,
            'next_api_start' => $apiStart,
            'total_scanned'  => $totalScanned,
        ];

    } catch (\Exception $e) {
        Log::error('getUserPosts error', ['error' => $e->getMessage()]);
        return [
            'posts'          => [],
            'has_more'       => false,
            'next_api_start' => 0,
            'total_scanned'  => 0,
        ];
    }
}

public function mostStatus(
        string $projectId,
        string $media,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23,
        int $rows = 100,
        string $sub = 'postbyview'
    ): array {
        try {
            $token = $this->getToken();

            $res = Http::timeout(60)->acceptJson()->get(
                $this->baseUrl() . '/most_status/',
                [
                    'project_id' => $projectId,
                    'media'      => $media,
                    'start_date' => $startDate,
                    'end_date'   => $endDate,
                    'start_time' => $startTime,
                    'end_time'   => $endTime,
                    'rows'       => $rows,
                    'sub'        => $sub,
                    'is_cache'   => 'true',
                    'token'      => $token,
                ]
            );

            $res->throw();

            $json = $this->parseJson($res);

            // Extract array — MK API may return raw [] or {data:[]}
            $items = $json;
            if (isset($json['data']) && is_array($json['data'])) {
                $items = $json['data'];
            }
            if (!is_array($items)) {
                $items = [];
            }
            // Re-index and filter out non-array entries (e.g. "Welcome to Drone Emprit API")
            $items = array_values(array_filter($items, 'is_array'));

            Log::info('mostStatus API response', [
                'project_id'  => $projectId,
                'media'       => $media,
                'sub'         => $sub,
                'total_items' => count($items),
            ]);

            return $items;

        } catch (\Exception $e) {
            Log::error('mostStatus API error', [
                'error'      => $e->getMessage(),
                'project_id' => $projectId,
                'media'      => $media,
                'sub'        => $sub,
            ]);
            return [];
        }
    }

    // ─────────────────────────────────────────────────────
    // MOST RETWEETS
    // API: GET /most_retweets/
    // ─────────────────────────────────────────────────────

    public function mostRetweets(
        string $projectId,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23,
        int $rows = 100
    ): array {
        try {
            $token = $this->getToken();

            $res = Http::timeout(60)->acceptJson()->get(
                $this->baseUrl() . '/most_retweets/',
                [
                    'project_id' => $projectId,
                    'start_date' => $startDate,
                    'end_date'   => $endDate,
                    'start_time' => $startTime,
                    'end_time'   => $endTime,
                    'rows'       => $rows,
                    'is_cache'   => 'true',
                    'token'      => $token,
                ]
            );

            $res->throw();

            $json = $this->parseJson($res);

            Log::info('mostRetweets API response', [
                'project_id'  => $projectId,
                'total_items' => is_array($json) ? count($json) : 0,
                'sample_keys' => is_array($json) && count($json) > 0 ? array_keys(is_array($json[0] ?? null) ? $json[0] : []) : [],
                'sample_item' => is_array($json) && count($json) > 0 ? array_slice(is_array($json[0] ?? null) ? $json[0] : [], 0, 20) : [],
            ]);

            return is_array($json) ? $json : [];

        } catch (\Exception $e) {
            Log::error('mostRetweets API error', [
                'error'      => $e->getMessage(),
                'project_id' => $projectId,
            ]);
            return [];
        }
    }

    public function userMostEngaged(
        string $projectId,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23,
        int $rows = 200,
        string $sub = 'rt'   // 'rt' = By Collected Mentions | 'rt_all' = By Total Retweets
    ): array {
        try {
            $token = $this->getToken();

            $res = Http::timeout(60)->acceptJson()->get(
                $this->baseUrl() . '/user_most_engaged/',
                [
                    'project_id' => $projectId,
                    'start_date' => $startDate,
                    'end_date'   => $endDate,
                    'start_time' => $startTime,
                    'end_time'   => $endTime,
                    'rows'       => $rows,
                    'media'      => 'twitter',
                    'sub'        => $sub,
                    'token'      => $token,
                ]
            );

            $res->throw();

            $json = $this->parseJson($res);

            Log::info('userMostEngaged API response', [
                'project_id'  => $projectId,
                'sub'         => $sub,
                'type'        => gettype($json),
                'count'       => is_array($json) ? count($json) : 0,
                'sample_keys' => is_array($json) && count($json) > 0
                    ? array_keys(is_array($json[0] ?? null) ? $json[0] : $json)
                    : [],
                'sample'      => is_array($json) && count($json) > 0
                    ? array_slice(is_array($json[0] ?? null) ? $json[0] : $json, 0, 8)
                    : [],
            ]);

            return is_array($json) ? $json : [];

        } catch (\Exception $e) {
            Log::error('userMostEngaged API error', [
                'error'      => $e->getMessage(),
                'project_id' => $projectId,
                'sub'        => $sub,
            ]);
            return [];
        }
    }


 public function topInfluencers(
        string $projectId,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23,
        string $localities = '',
        int $rows = 200
    ): array {
        try {
            $token = $this->getToken();

            $res = Http::connectTimeout(30)->timeout(120)->retry(2, 3000)->acceptJson()->get(
                $this->baseUrl() . '/top_influencers/',
                [
                    'project_id' => $projectId,
                    'start_date' => $startDate,
                    'end_date'   => $endDate,
                    'start_time' => $startTime,
                    'end_time'   => $endTime,
                    'localities' => $localities,
                    'token'      => $token,
                ]
            );

            $res->throw();

            $json = $this->parseJson($res);

            Log::info('topInfluencers API response', [
                'project_id' => $projectId,
                'count'      => is_array($json) ? count($json) : 0,
                'sample'     => is_array($json) && count($json) > 0
                    ? array_slice($json[0], 0, 4)
                    : [],
            ]);

            return is_array($json) ? $json : [];

        } catch (\Exception $e) {
            Log::error('topInfluencers API error', [
                'error'      => $e->getMessage(),
                'project_id' => $projectId,
            ]);
            return [];
        }
    }

public function tiktokTopStatusAll(
    string $projectId,
    string $startDate,
    string $endDate,
    int $startTime = 0,
    int $endTime = 23,
    int $maxRows = 100,
    string $sub = 'postbylike'
): array {
    try {
        $token = $this->getToken();

        $res = Http::timeout(60)->acceptJson()->get(
            $this->baseUrl() . '/tiktok_top_status/',
            [
                'project_id' => $projectId,
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'start_time' => $startTime,
                'end_time'   => $endTime,
                'rows'       => $maxRows,
                'sub'        => $sub,
                'is_cache'   => 'false',
                'token'      => $token,
            ]
        );

        $res->throw();
        $json = $this->parseJson($res);

        Log::info('tiktokTopStatusAll result', [
            'total' => is_array($json) ? count($json) : 0,
        ]);

        return is_array($json) ? $json : [];

    } catch (\Exception $e) {
           Log::error('tiktokTopStatusAll error', ['error' => $e->getMessage()]);
        return [];
    }
}
}