<?php

namespace App\Http\Controllers;

use App\Services\MediaKernelsClient;
use Illuminate\Http\Request;

class MkProjectsController extends Controller
{
    /**
     * Ubah response sentiment API jadi format rapi
     */
    private function normalizeSentimentTotal(array $raw): array
    {
        $src = $raw['data'] ?? $raw;

        return [
            'positive' => (int) ($src['positive'] ?? $src['pos'] ?? $src['1'] ?? 0),
            'neutral'  => (int) ($src['neutral']  ?? $src['neu'] ?? $src['0'] ?? 0),
            'negative' => (int) ($src['negative'] ?? $src['neg'] ?? $src['-1'] ?? 0),
        ];
    }

    /**
     * Normalize Age/Gender/Type data untuk chart
     * Format API: [{"age_group": "<=18", "post_freq": "2553", ...}, ...]
     */
    private function normalizeChartData(array $raw, string $labelKey = 'age_group', string $valueKey = 'post_freq'): array
    {
        $data = $raw['data'] ?? $raw;
        
        if (empty($data) || !is_array($data)) {
            return ['labels' => [], 'values' => []];
        }

        $labels = [];
        $values = [];

        foreach ($data as $item) {
            if (is_array($item)) {
                $label = $item[$labelKey] ?? 'Unknown';
                $value = (int) ($item[$valueKey] ?? 0);
                
                $labels[] = $label;
                $values[] = $value;
            }
        }

        return [
            'labels' => $labels,
            'values' => $values
        ];
    }

    /**
     * Normalize Categories data
     * Format bisa beda-beda tergantung endpoint
     */
    private function normalizeCategoriesData(array $raw): array
    {
        $data = $raw['data'] ?? $raw;
        
        if (empty($data) || !is_array($data)) {
            return ['labels' => [], 'values' => []];
        }

        $labels = [];
        $values = [];

        foreach ($data as $key => $item) {
            if (is_array($item)) {
                // Format: {"category_name": {...}, ...}
                $label = $item['name'] ?? $item['category'] ?? $key;
                $value = (int) ($item['total'] ?? $item['count'] ?? $item['value'] ?? 0);
            } else {
                // Format: {"category_name": 123, ...}
                $label = $key;
                $value = (int) $item;
            }
            
            $labels[] = $label;
            $values[] = $value;
        }

        return [
            'labels' => $labels,
            'values' => $values
        ];
    }

    /**
     * Ubah geo API ke list rapi
     */
    private function normalizeGeoRows(array $raw): array
    {
        $src = $raw['data'] ?? $raw;
        $rows = [];

        foreach ($src as $k => $v) {
            if (is_numeric($v)) {
                $rows[] = ['name' => (string)$k, 'count' => (int)$v];
            } elseif (is_array($v)) {
                $rows[] = [
                    'name' => $v['name'] ?? $k,
                    'count' => (int)($v['count'] ?? $v['total'] ?? 0),
                ];
            }
        }

        usort($rows, fn($a, $b) => $b['count'] <=> $a['count']);
        return array_slice($rows, 0, 10);
    }

    /**
     * Normalize Estimated Reach data
     * Format: {"data": [{"date": "2024-12-01", "reach": 12345}, ...]}
     */
    private function normalizeEstReachData(array $raw): array
    {
        $data = $raw['data'] ?? $raw;
        
        if (empty($data) || !is_array($data)) {
            return ['labels' => [], 'values' => []];
        }

        $labels = [];
        $values = [];

        foreach ($data as $item) {
            if (is_array($item)) {
                $label = $item['date'] ?? $item['time'] ?? 'Unknown';
                $value = (int) ($item['reach'] ?? $item['est_reach'] ?? $item['value'] ?? 0);
                
                $labels[] = $label;
                $values[] = $value;
            }
        }

        return [
            'labels' => $labels,
            'values' => $values
        ];
    }

    /**
     * Normalize Shared URL Frequency
     * Format: {"data": [{"url": "...", "freq": 123}, ...]}
     */
    private function normalizeSharedUrlData(array $raw): array
    {
        $data = $raw['data'] ?? $raw;
        
        if (empty($data) || !is_array($data)) {
            return [];
        }

        $rows = [];
        foreach ($data as $item) {
            if (is_array($item)) {
                $rows[] = [
                    'url' => $item['url'] ?? $item['link'] ?? 'Unknown',
                    'freq' => (int) ($item['freq'] ?? $item['frequency'] ?? $item['count'] ?? 0),
                ];
            }
        }

        // Sort by frequency descending
        usort($rows, fn($a, $b) => $b['freq'] <=> $a['freq']);
        return array_slice($rows, 0, 10);
    }

    /**
     * Normalize Most Active Users
     * Format: {"data": [{"username": "...", "post_count": 123}, ...]}
     */
    private function normalizeMostActiveUsers(array $raw): array
    {
        $data = $raw['data'] ?? $raw;
        
        if (empty($data) || !is_array($data)) {
            return [];
        }

        $rows = [];
        foreach ($data as $item) {
            if (is_array($item)) {
                $rows[] = [
                    'username' => $item['username'] ?? $item['user'] ?? $item['name'] ?? 'Unknown',
                    'count' => (int) ($item['post_count'] ?? $item['posts'] ?? $item['count'] ?? 0),
                ];
            }
        }

        // Sort by count descending
        usort($rows, fn($a, $b) => $b['count'] <=> $a['count']);
        return array_slice($rows, 0, 10);
    }

    /**
     * HALAMAN UTAMA
     */
    public function index(Request $request, MediaKernelsClient $mk)
    {
        /** ===============================
         * 1️⃣ AMBIL PROJECT LIST
         * =============================== */
        $start  = (int) $request->query('start', 0);
        $limit  = (int) $request->query('limit', 20);

        $rawProjects = $mk->listProjects($start, $limit);
        $projects    = array_values($rawProjects);

        /** ===============================
         * 2️⃣ PARAM FILTER
         * =============================== */
        $projectId = $request->query('project_id') ?? ($projects[0]['id'] ?? null);

        $startDate = $request->query('start_date', now()->subDay()->toDateString());
        $endDate   = $request->query('end_date', now()->toDateString());
        $startTime = (int) $request->query('start_time', 0);
        $endTime   = (int) $request->query('end_time', 23);
        $media     = $request->query('media', 'twit');
        $sentiment = (int) $request->query('sentiment', 1);

        /** ===============================
         * 3️⃣ DEFAULT DATA (BIAR GA ERROR)
         * =============================== */
        $sentimentRaw  = [];
        $sentimentNorm = ['positive' => 0, 'neutral' => 0, 'negative' => 0];

        $geoRaw   = [];
        $geoRows  = [];

        $ageRaw    = [];
        $ageChart  = ['labels' => [], 'values' => []];
        
        $genderRaw = [];
        $genderChart = ['labels' => [], 'values' => []];

        $authorsTypeRaw = [];
        $authorsTypeChart = ['labels' => [], 'values' => []];
        
        $categoriesRaw  = [];
        $categoriesChart = ['labels' => [], 'values' => []];

        // 🆕 NEW API DATA
        $estReachRaw = [];
        $estReachChart = ['labels' => [], 'values' => []];

        $geoUserRaw = [];
        $geoUserRows = [];

        $getSentimentRaw = [];
        $getSentimentChart = ['labels' => [], 'values' => []];

        $sharedUrlRaw = [];
        $sharedUrlRows = [];

        $activeUsersRaw = [];
        $activeUsersRows = [];

        /** ===============================
         * 4️⃣ CONSUME API (INI INTINYA)
         * =============================== */
        if ($projectId) {

            // 🔹 Sentiment
            $sentimentRaw = $mk->sentimentTotal(
                $projectId,
                $startDate,
                $endDate,
                $startTime,
                $endTime
            );
            $sentimentNorm = $this->normalizeSentimentTotal($sentimentRaw);

            // 🔹 Geo
            $geoRaw = $mk->geoTwitterUserSentiment(
                $projectId,
                $media,
                $startDate,
                $endDate,
                $startTime,
                $endTime,
                $sentiment
            );
            $geoRows = $this->normalizeGeoRows($geoRaw);

            // 🔹 AGE
            $ageRaw = $mk->authorsAge(
                $projectId,
                $media,
                $startDate,
                $endDate,
                $startTime,
                $endTime
            );
            $ageChart = $this->normalizeChartData($ageRaw, 'age_group', 'post_freq');

            // 🔹 GENDER
            $genderRaw = $mk->authorsGender(
                $projectId,
                $media,
                $startDate,
                $endDate,
                $startTime,
                $endTime
            );
            $genderChart = $this->normalizeChartData($genderRaw, 'gender', 'post_freq');

            // 🔹 AUTHORS TYPE
            $authorsTypeRaw = $mk->authorsType(
                $projectId,
                $media,
                $startDate,
                $endDate,
                $startTime,
                $endTime
            );
            $authorsTypeChart = $this->normalizeChartData($authorsTypeRaw, 'is_organization', 'post_freq');

            // 🔹 CATEGORIES
            $categoriesRaw = $mk->categories(
                $projectId,
                'all',
                $startDate,
                $endDate,
                $startTime,
                $endTime
            );
            $categoriesChart = $this->normalizeCategoriesData($categoriesRaw);

            // 🔥 NEW: ESTIMATED REACH
            $estReachRaw = $mk->estReach(
                $projectId,
                $media,
                $startDate,
                $endDate,
                $startTime,
                $endTime,
                'all'
            );
            $estReachChart = $this->normalizeEstReachData($estReachRaw);

            // 🔥 NEW: GEO TWITTER USER
            $geoUserRaw = $mk->geoTwitterUser(
                $projectId,
                $media,
                $startDate,
                $endDate,
                $startTime,
                $endTime
            );
            $geoUserRows = $this->normalizeGeoRows($geoUserRaw);

            // 🔥 NEW: GET SENTIMENT (detailed)
            $getSentimentRaw = $mk->getSentiment(
                $projectId,
                $media,
                $startDate,
                $endDate,
                $startTime,
                $endTime
            );
            // Normalize sentiment detail as chart
            $getSentimentChart = $this->normalizeSentimentTotal($getSentimentRaw);

            // 🔥 NEW: SHARED URL FREQUENCY
            $sharedUrlRaw = $mk->sharedUrlFreq(
                $projectId,
                $startDate,
                $endDate
            );
            $sharedUrlRows = $this->normalizeSharedUrlData($sharedUrlRaw);

            // 🔥 NEW: MOST ACTIVE USERS
            $activeUsersRaw = $mk->mostActiveUsers(
                $projectId,
                $startDate,
                $endDate,
                $startTime,
                $endTime
            );
            $activeUsersRows = $this->normalizeMostActiveUsers($activeUsersRaw);
        }

        /** ===============================
         * 5️⃣ KIRIM KE VIEW
         * =============================== */
        return view('mk.projects', [
            'raw' => $rawProjects,
            'projects' => $projects,

            'projectId' => $projectId,
            'params' => compact(
                'startDate',
                'endDate',
                'startTime',
                'endTime',
                'media',
                'sentiment'
            ),

            'sentimentRaw' => $sentimentRaw,
            'sentimentNorm' => $sentimentNorm,

            'geoRaw' => $geoRaw,
            'geoRows' => $geoRows,

            'ageRaw' => $ageRaw,
            'ageChart' => $ageChart,
            
            'genderRaw' => $genderRaw,
            'genderChart' => $genderChart,
            
            'authorsTypeRaw' => $authorsTypeRaw,
            'authorsTypeChart' => $authorsTypeChart,
            
            'categoriesRaw' => $categoriesRaw,
            'categoriesChart' => $categoriesChart,

            // 🆕 NEW API DATA
            'estReachRaw' => $estReachRaw,
            'estReachChart' => $estReachChart,

            'geoUserRaw' => $geoUserRaw,
            'geoUserRows' => $geoUserRows,

            'getSentimentRaw' => $getSentimentRaw,
            'getSentimentChart' => $getSentimentChart,

            'sharedUrlRaw' => $sharedUrlRaw,
            'sharedUrlRows' => $sharedUrlRows,

            'activeUsersRaw' => $activeUsersRaw,
            'activeUsersRows' => $activeUsersRows,

            'start' => $start,
            'limit' => $limit,
        ]);
    }
}