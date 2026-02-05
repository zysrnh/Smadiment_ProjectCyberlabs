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
            
            Log::info('MediaKernels /token/ response', ['response' => $json]);
            
            $token = $json['token'] ?? null;

            if (!$token) {
                Log::error('Token not found in response', [
                    'available_keys' => array_keys($json),
                    'full_response' => $json
                ]);
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

    // 🔥 NEW: sentiment_media - Get sentiment breakdown by media type
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
                'has_all' => isset($json['all']),
                'has_bymedia' => isset($json['bymedia']),
                'top_level_keys' => array_keys($json),
            ]);
            
            return $json;
            
        } catch (\Exception $e) {
            Log::error('sentimentMedia API error', [
                'error' => $e->getMessage(),
                'project_id' => $projectId,
            ]);
            return ['all' => 0, 'bymedia' => []];
        }
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
            return ['data' => []];
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
            return ['data' => []];
        }
    }

    // 🔥 NEW: most_retweets
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

    // 🔥 FIXED: publisher_stats dengan logging lebih detail
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
                'has_data' => isset($json['data']),
                'has_article' => isset($json['article']),
                'top_level_keys' => array_keys($json),
                'sample_data' => array_slice($json, 0, 2, true)
            ]);
            
            return $json;
            
        } catch (\Exception $e) {
            Log::error('publisherStats API error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'params' => $params ?? []
            ]);
            return ['data' => [], 'article' => []];
        }
    }

    // 🔥 NEW: recent_topics
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

    // 🔥 NEW: top_hashtags
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

            $res->throw();
            return $this->parseJson($res);
        } catch (\Exception $e) {
            Log::warning('topHashtags API error', ['error' => $e->getMessage()]);
            return ['data' => []];
        }
    }

    // 🔥 NEW: mentions
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

    // 🔥 DATA SOURCE API #1: total_users
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
                'has_data' => isset($json['data']),
                'top_level_keys' => array_keys($json),
            ]);
            
            return $json;
            
        } catch (\Exception $e) {
            Log::error('totalUsers API error', [
                'error' => $e->getMessage(),
                'project_id' => $projectId,
            ]);
            return ['data' => []];
        }
    }

    // 🔥 DATA SOURCE API #2: total_authors
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
                'has_data' => isset($json['data']),
                'top_level_keys' => array_keys($json),
            ]);
            
            return $json;
            
        } catch (\Exception $e) {
            Log::error('totalAuthors API error', [
                'error' => $e->getMessage(),
                'project_id' => $projectId,
            ]);
            return ['data' => []];
        }
    }

    // 🔥 DATA SOURCE API #3: volume_total
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
                'has_data' => isset($json['data']),
                'top_level_keys' => array_keys($json),
            ]);
            
            return $json;
            
        } catch (\Exception $e) {
            Log::error('volumeTotal API error', [
                'error' => $e->getMessage(),
                'project_id' => $projectId,
            ]);
            return ['data' => []];
        }
    }

    // 🔥 DATA SOURCE API #4: trends_total - FIXED dengan transformasi data
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
                'response' => $json,
                'top_level_keys' => array_keys($json),
            ]);
            
            $transformedData = [];
            
            foreach ($json as $datetime => $mediaCounts) {
                if (!is_array($mediaCounts)) continue;
                
                $date = date('Y-m-d', strtotime($datetime));
                
                foreach ($mediaCounts as $mediaType => $count) {
                    $keyword = strtoupper($mediaType);
                    
                    $found = false;
                    foreach ($transformedData as &$trend) {
                        if ($trend['keyword'] === $keyword) {
                            $trend['data'][] = [
                                'date' => $date,
                                'count' => (int)$count
                            ];
                            $found = true;
                            break;
                        }
                    }
                    
                    if (!$found) {
                        $transformedData[] = [
                            'keyword' => $keyword,
                            'data' => [
                                [
                                    'date' => $date,
                                    'count' => (int)$count
                                ]
                            ]
                        ];
                    }
                }
            }
            
            Log::info('trendsTotal transformed data', [
                'count' => count($transformedData),
                'sample' => array_slice($transformedData, 0, 2, true)
            ]);
            
            return ['data' => $transformedData];
            
        } catch (\Exception $e) {
            Log::error('trendsTotal API error', [
                'error' => $e->getMessage(),
                'project_id' => $projectId,
            ]);
            return ['data' => []];
        }
    }
    // 🔥 NEW: topic_map
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
            'sample' => array_slice($json, 0, 5, true)
        ]);
        
        return $json;
        
    } catch (\Exception $e) {
        Log::error('topicMap API error', [
            'error' => $e->getMessage(),
            'project_id' => $projectId,
        ]);
        return [];
    }
}
}