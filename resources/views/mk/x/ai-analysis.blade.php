@extends('mk.layouts.app')

@section('title', 'AI Analysis - X (Twitter)')

@php
    $projectId = $projectId ?? request('project_id', '');
    $startDate = $startDate ?? request('start_date', now()->subDays(30)->format('Y-m-d'));
    $endDate   = $endDate   ?? request('end_date',   now()->format('Y-m-d'));
    $projects  = $projects  ?? [];

    $aiPlatform = [
        'name'     => 'X (Twitter)',
        'slug'     => 'x',
        'color'    => '#1d9bf0',
        'accent'   => '#1d9bf0',
        'dark'     => '#0c7abf',
        'gradient' => 'linear-gradient(135deg, #1d9bf0 0%, #0c7abf 100%)',
        'iconSvg'  => '<svg viewBox="0 0 24 24" fill="white" width="16" height="16"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
        'iconSvgLg'=> '<svg viewBox="0 0 24 24" fill="white" width="26" height="26"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
    ];
@endphp

@section('styles')
    @include('mk.ai.partials.ai-analysis-styles', ['aiPlatform' => $aiPlatform])
@endsection

@section('content')
    @include('mk.layouts.partials.filter-datepicker')
    @include('mk.ai.partials.ai-analysis-shell', ['aiPlatform' => $aiPlatform])
@endsection

@section('scripts')
<script>
const PROJECT_ID = '{{ $projectId ?? "" }}';
const PLATFORM   = 'X (Twitter)';
let START_DATE   = '{{ $startDate ?? "" }}';
let END_DATE     = '{{ $endDate ?? "" }}';
const ROUTES = {
    aiProxy        : '{{ route("mk.api.x.ai-proxy") }}',
    aiAnalysisData : '{{ route("mk.api.x.ai-analysis-data") }}',
};

function buildSystemPrompt() {
    return `Anda adalah SMADIMENT AI Analyst — analis media intelligence senior yang menganalisis data percakapan X (Twitter) secara data-driven dan evidence-based.

KONTEKS SESI:
- Project ID : ${PROJECT_ID}
- Platform   : X (Twitter)
- Periode    : ${START_DATE} s/d ${END_DATE}

INSTRUKSI UTAMA:
1. Analisis berdasarkan data nyata yang disertakan — bukan asumsi.
2. Kutip username (@handle), konten tweet, jumlah retweet/views sebagai evidence.
3. Identifikasi isu nyata dari konten tweet dan pola engagement.
4. Gunakan data sentimen, hashtag, dan most active users untuk mendukung analisis.
5. Perhatikan pola viral Twitter: retweet network, reply threads, dan influencer dynamics.

ATURAN CITATION — WAJIB DIIKUTI:
- JANGAN pernah menulis referensi seperti "Post [1]", "Tweet [21]", "[P5]" atau nomor index apapun.
- SELALU sebut username secara langsung. Contoh: "@NamaAkun".
- SELALU sertakan kutipan singkat konten tweet yang relevan dalam tanda kutip.
- Format evidence yang benar: **@NamaAkun** (XX RT, YY likes) — *"kutipan singkat tweet..."*
- Jika username tidak tersedia dalam data, tulis: "akun tidak diketahui" — jangan tulis nomor index.

GAYA RESPONS:
- Bahasa Indonesia profesional (kecuali user minta Inggris).
- Gunakan markdown: ## untuk heading, **bold** untuk highlight.
- Setiap insight harus actionable dan didukung referensi spesifik dari data.
- Output komprehensif, terstruktur, siap dijadikan laporan profesional.`;
}
</script>

@include('mk.ai.partials.ai-analysis-scripts', ['aiPlatform' => $aiPlatform])
@endsection
