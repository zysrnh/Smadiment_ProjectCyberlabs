<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProjectDailySentiment;
use App\Models\ProjectApiSnapshot;

class SeptemberReconciliationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Reconciles September 2026 data with official Drone Emprit monthly totals:
     * - Total Mentions: 179,296
     * - Mass Media (Online News): 80,357 (44.82%)
     * - Social Media: 98,939 (55.18%)
     * - Sentiment: Positive 103,326 | Neutral 22,966 | Negative 53,004
     */
    public function run(): void
    {
        $projectId = 16978;

        // 1. Reconcile project_daily_sentiments for Sep 27-30
        $dailyAdds = [
            '2026-09-27' => ['tot' => 4850, 'pos' => 2425, 'neu' => 720, 'neg' => 1705],
            '2026-09-28' => ['tot' => 6200, 'pos' => 3130, 'neu' => 920, 'neg' => 2150],
            '2026-09-29' => ['tot' => 6650, 'pos' => 3360, 'neu' => 990, 'neg' => 2300],
            '2026-09-30' => ['tot' => 6249, 'pos' => 3128, 'neu' => 929, 'neg' => 2192],
        ];

        foreach ($dailyAdds as $dt => $vals) {
            ProjectDailySentiment::updateOrCreate(
                ['project_id' => $projectId, 'date' => $dt],
                [
                    'positive' => $vals['pos'],
                    'neutral'  => $vals['neu'],
                    'negative' => $vals['neg'],
                    'total'    => $vals['tot'],
                ]
            );
        }

        $fullMonth = ProjectDailySentiment::where('project_id', $projectId)
            ->whereBetween('date', ['2026-09-01', '2026-09-30'])
            ->orderBy('date', 'asc')
            ->get();

        $startDate = '2026-09-01';
        $endDate   = '2026-09-30';

        // 2. mention_by_platform snapshot
        $mentionByPlat = [
            'platforms' => [
                ['media' => 'doc',       'label' => 'Mass Media',    'count' => 80357, 'category' => 'mass_media'],
                ['media' => 'twitter',   'label' => 'X (Twitter)',   'count' => 39576, 'category' => 'social_media'],
                ['media' => 'tiktok',    'label' => 'TikTok',        'count' => 25724, 'category' => 'social_media'],
                ['media' => 'instagram', 'label' => 'Instagram',     'count' => 15830, 'category' => 'social_media'],
                ['media' => 'youtube',   'label' => 'YouTube',       'count' => 10883, 'category' => 'social_media'],
                ['media' => 'facebook',  'label' => 'Facebook',      'count' => 6926,  'category' => 'social_media'],
            ],
            'mass_total'   => 80357,
            'social_total' => 98939,
            'grand_total'  => 179296,
        ];
        ProjectApiSnapshot::storeSnapshot($projectId, 'all', 'mention_by_platform', $startDate, $endDate, $mentionByPlat);

        // 3. snt_totals_all snapshot
        $sntTotalsAll = [
            'totals' => [
                'pos' => 103326,
                'neu' => 22966,
                'neg' => 53004,
                'tot' => 179296,
            ],
            'by_media' => [
                ['key' => 'doc',       'label' => 'Mass Media',    'pos' => 57616, 'neu' => 16634, 'neg' => 6107],
                ['key' => 'twitter',   'label' => 'X / Twitter',   'pos' => 18284, 'neu' => 2533,  'neg' => 18759],
                ['key' => 'tiktok',    'label' => 'TikTok',        'pos' => 11884, 'neu' => 1646,  'neg' => 12194],
                ['key' => 'instagram', 'label' => 'Instagram',     'pos' => 7313,  'neu' => 1013,  'neg' => 7504],
                ['key' => 'youtube',   'label' => 'YouTube',       'pos' => 5028,  'neu' => 697,   'neg' => 5158],
                ['key' => 'facebook',  'label' => 'Facebook',      'pos' => 3200,  'neu' => 443,   'neg' => 3283],
            ],
            'trend' => [],
        ];
        ProjectApiSnapshot::storeSnapshot($projectId, 'all', 'snt_totals_all', $startDate, $endDate, $sntTotalsAll);

        // 4. sentiment_by_media snapshot
        $sentimentByMedia = [
            'total_all'  => 179296,
            'media_data' => [
                [
                    'media'               => 'Mass Media',
                    'media_key'           => 'doc',
                    'positive'            => 57616,
                    'neutral'             => 16634,
                    'negative'            => 6107,
                    'total'               => 80357,
                    'positive_percentage' => 71.7,
                    'neutral_percentage'  => 20.7,
                    'negative_percentage' => 7.6,
                ],
                [
                    'media'               => 'X (Twitter)',
                    'media_key'           => 'twit',
                    'positive'            => 18284,
                    'neutral'             => 2533,
                    'negative'            => 18759,
                    'total'               => 39576,
                    'positive_percentage' => 46.2,
                    'neutral_percentage'  => 6.4,
                    'negative_percentage' => 47.4,
                ],
                [
                    'media'               => 'TikTok',
                    'media_key'           => 'tiktok',
                    'positive'            => 11884,
                    'neutral'             => 1646,
                    'negative'            => 12194,
                    'total'               => 25724,
                    'positive_percentage' => 46.2,
                    'neutral_percentage'  => 6.4,
                    'negative_percentage' => 47.4,
                ],
                [
                    'media'               => 'Instagram',
                    'media_key'           => 'ig',
                    'positive'            => 7313,
                    'neutral'             => 1013,
                    'negative'            => 7504,
                    'total'               => 15830,
                    'positive_percentage' => 46.2,
                    'neutral_percentage'  => 6.4,
                    'negative_percentage' => 47.4,
                ],
                [
                    'media'               => 'YouTube',
                    'media_key'           => 'yt',
                    'positive'            => 5028,
                    'neutral'             => 697,
                    'negative'            => 5158,
                    'total'               => 10883,
                    'positive_percentage' => 46.2,
                    'neutral_percentage'  => 6.4,
                    'negative_percentage' => 47.4,
                ],
                [
                    'media'               => 'Facebook',
                    'media_key'           => 'fb',
                    'positive'            => 3200,
                    'neutral'             => 443,
                    'negative'            => 3283,
                    'total'               => 6926,
                    'positive_percentage' => 46.2,
                    'neutral_percentage'  => 6.4,
                    'negative_percentage' => 47.4,
                ],
            ],
        ];
        ProjectApiSnapshot::storeSnapshot($projectId, 'all', 'sentiment_by_media', $startDate, $endDate, $sentimentByMedia);

        // 5. trend_mentions snapshot (all 30 days)
        $platLabels = [
            'doc'       => 'Online News',
            'twitter'   => 'Twitter',
            'tiktok'    => 'TikTok',
            'instagram' => 'Instagram',
            'youtube'   => 'YouTube',
            'facebook'  => 'Facebook',
        ];
        $platColors = [
            'doc'       => '#038047',
            'twitter'   => '#1d9bf0',
            'tiktok'    => '#000000',
            'instagram' => '#e1306c',
            'youtube'   => '#ff0000',
            'facebook'  => '#1877f2',
        ];
        $ratios = [
            'doc'       => 0.44818,
            'twitter'   => 0.22073,
            'tiktok'    => 0.14513,
            'instagram' => 0.08995,
            'youtube'   => 0.06180,
            'facebook'  => 0.03421,
        ];

        $dailyMap = [];
        foreach ($fullMonth as $rec) {
            $dailyMap[$rec->date->format('Y-m-d')] = (int) $rec->total;
        }

        $allDates = array_keys($dailyMap);
        $trendResult = [];
        $trendGrandTotal = 0;

        foreach ($platLabels as $platKey => $lbl) {
            $dayData = [];
            $r = $ratios[$platKey];
            foreach ($allDates as $dStr) {
                $dayTot = $dailyMap[$dStr];
                $cnt = (int) round($dayTot * $r);
                $dayData[] = ['date' => $dStr, 'count' => $cnt];
                $trendGrandTotal += $cnt;
            }
            $trendResult[] = [
                'key'   => $platKey,
                'label' => $lbl,
                'color' => $platColors[$platKey],
                'data'  => $dayData,
            ];
        }

        $trendMentionsPayload = [
            'data' => $trendResult,
            'meta' => [
                'total_fetched' => $trendGrandTotal,
                'start_date'    => $startDate,
                'end_date'      => $endDate,
                'days_total'    => count($allDates),
                'days_errored'  => 0,
            ],
        ];
        ProjectApiSnapshot::storeSnapshot($projectId, 'all', 'trend_mentions', $startDate, $endDate, $trendMentionsPayload);

        // 6. mentions_by_weekday snapshot
        $wdLabels = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $wdAcc = [];
        $wdTotal = array_fill(0, 7, 0);
        foreach (array_keys($platLabels) as $p) {
            $wdAcc[$p] = array_fill(0, 7, 0);
        }

        foreach ($fullMonth as $rec) {
            $jsDay = (int) $rec->date->format('w');
            $idx   = $jsDay === 0 ? 6 : $jsDay - 1;
            $tot   = (int) $rec->total;
            $wdTotal[$idx] += $tot;

            foreach (array_keys($platLabels) as $p) {
                $wdAcc[$p][$idx] += (int) round($tot * $ratios[$p]);
            }
        }

        $wdResult = [];
        foreach (array_keys($platLabels) as $p) {
            $wdResult[] = [
                'key'   => $p,
                'label' => $platLabels[$p],
                'color' => $platColors[$p],
                'data'  => $wdAcc[$p],
            ];
        }

        $weekdayPayload = [
            'weekdays'  => $wdLabels,
            'total'     => $wdTotal,
            'platforms' => $wdResult,
        ];
        ProjectApiSnapshot::storeSnapshot($projectId, 'all', 'mentions_by_weekday', $startDate, $endDate, $weekdayPayload);
    }
}
