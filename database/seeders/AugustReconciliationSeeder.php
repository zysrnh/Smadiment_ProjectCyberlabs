<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProjectDailySentiment;
use App\Models\ProjectApiSnapshot;

class AugustReconciliationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Reconciles August 2026 data with official Drone Emprit monthly totals (Pak Syarif):
     * - Total Mentions: 275,638
     * - Mass Media (Online News): 113,344 (41.12%)
     * - Social Media: 162,294 (58.88%)
     */
    public function run(): void
    {
        $projectId = 16978;

        // 1. Reconcile project_daily_sentiments for August 2026
        // August 14 currently has 28084. Adjust by -32 mentions to make total exactly 275638.
        ProjectDailySentiment::where('project_id', $projectId)
            ->where('date', '2026-08-14')
            ->update([
                'positive' => 19137,
                'neutral'  => 4990,
                'negative' => 3925,
                'total'    => 28052,
            ]);

        $fullMonth = ProjectDailySentiment::where('project_id', $projectId)
            ->whereBetween('date', ['2026-08-01', '2026-08-31'])
            ->orderBy('date', 'asc')
            ->get();

        $startDate = '2026-08-01';
        $endDate   = '2026-08-31';

        // 2. mention_by_platform snapshot
        $mentionByPlat = [
            'platforms' => [
                ['media' => 'doc',       'label' => 'Mass Media',    'count' => 113344, 'category' => 'mass_media'],
                ['media' => 'twitter',   'label' => 'X (Twitter)',   'count' => 64918,  'category' => 'social_media'],
                ['media' => 'tiktok',    'label' => 'TikTok',        'count' => 42196,  'category' => 'social_media'],
                ['media' => 'instagram', 'label' => 'Instagram',     'count' => 25967,  'category' => 'social_media'],
                ['media' => 'youtube',   'label' => 'YouTube',       'count' => 17852,  'category' => 'social_media'],
                ['media' => 'facebook',  'label' => 'Facebook',      'count' => 11361,  'category' => 'social_media'],
            ],
            'mass_total'   => 113344,
            'social_total' => 162294,
            'grand_total'  => 275638,
        ];
        ProjectApiSnapshot::storeSnapshot($projectId, 'all', 'mention_by_platform', $startDate, $endDate, $mentionByPlat);

        // 3. snt_totals_all snapshot
        // Mass: 113344 (pos 71.7%: 81268, neu 20.7%: 23462, neg 7.6%: 8614)
        // Social: 162294 (pos 46.2%: 74980, neu 6.4%: 10387, neg 47.4%: 76927)
        $sntTotalsAll = [
            'totals' => [
                'pos' => 156248,
                'neu' => 33849,
                'neg' => 85541,
                'tot' => 275638,
            ],
            'by_media' => [
                ['key' => 'doc',       'label' => 'Mass Media',    'pos' => 81268, 'neu' => 23462, 'neg' => 8614],
                ['key' => 'twitter',   'label' => 'X / Twitter',   'pos' => 29992, 'neu' => 4155,  'neg' => 30771],
                ['key' => 'tiktok',    'label' => 'TikTok',        'pos' => 19495, 'neu' => 2701,  'neg' => 20000],
                ['key' => 'instagram', 'label' => 'Instagram',     'pos' => 11997, 'neu' => 1662,  'neg' => 12308],
                ['key' => 'youtube',   'label' => 'YouTube',       'pos' => 8248,  'neu' => 1143,  'neg' => 8461],
                ['key' => 'facebook',  'label' => 'Facebook',      'pos' => 5248,  'neu' => 726,   'neg' => 5387],
            ],
            'trend' => [],
        ];
        ProjectApiSnapshot::storeSnapshot($projectId, 'all', 'snt_totals_all', $startDate, $endDate, $sntTotalsAll);

        // 4. sentiment_by_media snapshot
        $sentimentByMedia = [
            'total_all'  => 275638,
            'media_data' => [
                [
                    'media'               => 'Mass Media',
                    'media_key'           => 'doc',
                    'positive'            => 81268,
                    'neutral'             => 23462,
                    'negative'            => 8614,
                    'total'               => 113344,
                    'positive_percentage' => 71.7,
                    'neutral_percentage'  => 20.7,
                    'negative_percentage' => 7.6,
                ],
                [
                    'media'               => 'X (Twitter)',
                    'media_key'           => 'twit',
                    'positive'            => 29992,
                    'neutral'             => 4155,
                    'negative'            => 30771,
                    'total'               => 64918,
                    'positive_percentage' => 46.2,
                    'neutral_percentage'  => 6.4,
                    'negative_percentage' => 47.4,
                ],
                [
                    'media'               => 'TikTok',
                    'media_key'           => 'tiktok',
                    'positive'            => 19495,
                    'neutral'             => 2701,
                    'negative'            => 20000,
                    'total'               => 42196,
                    'positive_percentage' => 46.2,
                    'neutral_percentage'  => 6.4,
                    'negative_percentage' => 47.4,
                ],
                [
                    'media'               => 'Instagram',
                    'media_key'           => 'ig',
                    'positive'            => 11997,
                    'neutral'             => 1662,
                    'negative'            => 12308,
                    'total'               => 25967,
                    'positive_percentage' => 46.2,
                    'neutral_percentage'  => 6.4,
                    'negative_percentage' => 47.4,
                ],
                [
                    'media'               => 'YouTube',
                    'media_key'           => 'yt',
                    'positive'            => 8248,
                    'neutral'             => 1143,
                    'negative'            => 8461,
                    'total'               => 17852,
                    'positive_percentage' => 46.2,
                    'neutral_percentage'  => 6.4,
                    'negative_percentage' => 47.4,
                ],
                [
                    'media'               => 'Facebook',
                    'media_key'           => 'fb',
                    'positive'            => 5248,
                    'neutral'             => 726,
                    'negative'            => 5387,
                    'total'               => 11361,
                    'positive_percentage' => 46.2,
                    'neutral_percentage'  => 6.4,
                    'negative_percentage' => 47.4,
                ],
            ],
        ];
        ProjectApiSnapshot::storeSnapshot($projectId, 'all', 'sentiment_by_media', $startDate, $endDate, $sentimentByMedia);

        // 5. trend_mentions snapshot (all 31 days of August)
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
            'doc'       => 0.41120,
            'twitter'   => 0.23552,
            'tiktok'    => 0.15308,
            'instagram' => 0.09421,
            'youtube'   => 0.06477,
            'facebook'  => 0.04122,
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
