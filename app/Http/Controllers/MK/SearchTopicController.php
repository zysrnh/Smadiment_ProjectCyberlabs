<?php

namespace App\Http\Controllers\MK;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SearchTopicController extends Controller
{
    public function index(Request $request)
    {
        $endDate   = $request->query('end_date', now()->format('Y-m-d'));
        $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));
        $keyword   = $request->query('keyword', '');

        return view('mk.search-topic', [
            'startDate' => $startDate,
            'endDate'   => $endDate,
            'keyword'   => $keyword,
        ]);
    }
}
