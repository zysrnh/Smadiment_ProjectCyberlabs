<?php

namespace App\Http\Controllers\MK;

use App\Http\Controllers\Controller;
use App\Services\MediaKernelsClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrendingTopicController extends Controller
{
    private MediaKernelsClient $client;

    public function __construct(MediaKernelsClient $client)
    {
        $this->client = $client;
    }

    public function index(Request $request)
    {
        try {
            $endDate   = $request->query('end_date', now()->format('Y-m-d'));
            $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));

            return view('mk.trending-topic', [
                'startDate' => $startDate,
                'endDate'   => $endDate,
            ]);
        } catch (\Exception $e) {
            Log::error('Trending Topic Page Error', ['error' => $e->getMessage()]);
            return view('mk.trending-topic', [
                'startDate' => now()->subDays(6)->format('Y-m-d'),
                'endDate'   => now()->format('Y-m-d'),
            ]);
        }
    }

    public function getData(Request $request)
    {
        try {
            $startDate = $request->query('start_date');
            $endDate   = $request->query('end_date');
            $location  = $request->query('location', 'Indonesia');

            if (!$startDate || !$endDate) {
                return response()->json(['success' => false, 'error' => 'Missing required parameters: start_date, end_date'], 400);
            }

            $result = $this->client->twitterTrendingTopics($startDate, $endDate, 0, 23, $location);

            return response()->json(['success' => true, 'data' => $result]);
        } catch (\Exception $e) {
            Log::error('Trending Topic API error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => 'Failed to load trending topics'], 500);
        }
    }
}
