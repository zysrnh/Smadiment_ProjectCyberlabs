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
        $json = $res->json();
        if (is_array($json)) return $json;

        $body = $res->body();
        $decoded = json_decode($body, true);
        return is_array($decoded) ? $decoded : ['raw' => $body];
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
        try {
            $token = $this->getToken();

            $res = Http::timeout(60)->acceptJson()->get(
                $this->baseUrl() . '/volume_total/',
                [
                    'project_id' => $projectId,
                    'media'      => $media,
                    'start_date' => $startDate,
                    'end_date'   => $endDate,
                    'start_time' => $startTime,
                    'end_time'   => $endTime,
                    'is_cache'   => $isCache ? 'true' : 'false',
                    'token'      => $token,
                ]
            );

            $res->throw();

            $json = $this->parseJson($res);

            Log::info('volumeTotal API response', [
                'has_data'       => isset($json['data']),
                'top_level_keys' => array_keys($json),
            ]);

            return $json;

        } catch (\Exception $e) {
            Log::error('volumeTotal API error', [
                'error'      => $e->getMessage(),
                'project_id' => $projectId,
            ]);
            return ['data' => []];
        }
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
        int $rows = 50
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
                    'with_content' => 'true',
                    'start'        => 0,
                    'rows'         => 100,
                    'token'        => $token,
                ]
            );

            $res->throw();

            $json = $this->parseJson($res);

            Log::info('getUserMentions raw API response', [
                'username'      => $username,
                'total_results' => count($json),
            ]);

            $userMentions = [];
            foreach ($json as $mention) {
                $authorScreenName = $mention['author_scr_name'] ?? '';

                if (strtolower($authorScreenName) === strtolower($username)) {
                    $userMentions[] = $mention;
                    if (count($userMentions) >= $rows) break;
                }
            }

            Log::info('getUserMentions filtered results', [
                'username'       => $username,
                'filtered_count' => count($userMentions),
            ]);

            return $userMentions;

        } catch (\Exception $e) {
            Log::error('getUserMentions API error', [
                'error'    => $e->getMessage(),
                'username' => $username,
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
            return $this->parseJson($res);
        } catch (\Exception $e) {
            Log::warning('mostActiveUsers API timeout or error', ['error' => $e->getMessage()]);
            return ['data' => []];
        }
    }

    public function mostRetweets(
        string $projectId,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23
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
                    'token'      => $token,
                ]
            );

            $res->throw();
            return $this->parseJson($res);
        } catch (\Exception $e) {
            Log::warning('mostRetweets API timeout or error', ['error' => $e->getMessage()]);
            return ['data' => []];
        }
    }

    public function mostStatus(
        string $projectId,
        string $media,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23,
        string $mentionType = 'view_all'
    ): array {
        try {
            $token = $this->getToken();

            $res = Http::timeout(60)->acceptJson()->get(
                $this->baseUrl() . '/twitter_most_status/',
                [
                    'project_id'   => $projectId,
                    'media'        => $media,
                    'start_date'   => $startDate,
                    'end_date'     => $endDate,
                    'start_time'   => $startTime,
                    'end_time'     => $endTime,
                    'mention_type' => $mentionType,
                    'token'        => $token,
                ]
            );

            $res->throw();
            return $this->parseJson($res);
        } catch (\Exception $e) {
            Log::warning('mostStatus API error', ['error' => $e->getMessage()]);
            return [];
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

    public function topInfluencers(
        string $projectId,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23
    ): array {
        try {
            $token = $this->getToken();

            $res = Http::timeout(60)->acceptJson()->get(
                $this->baseUrl() . '/top_influencers/',
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
            Log::warning('topInfluencers API error', ['error' => $e->getMessage()]);
            return ['data' => []];
        }
    }

    // ─────────────────────────────────────────────────────
    // SHARED URLS
    // ─────────────────────────────────────────────────────

    /**
     * Get most frequently shared URLs.
     *
     * API: GET /get_shared_url_freq/
     * Response: { "status": "success", "data": [ { "url": "...", "freq": "18", "hostname": "..." }, ... ] }
     */
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






















}