@extends('mk.layouts.app')

@section('title', 'AI Analysis - News')

@php
    $projectId = $projectId ?? request('project_id', '');
    $startDate = $startDate ?? request('start_date', now()->subDays(30)->format('Y-m-d'));
    $endDate   = $endDate   ?? request('end_date',   now()->format('Y-m-d'));
    $projects  = $projects  ?? [];

    $aiPlatform = [
        'name'     => 'News',
        'slug'     => 'news',
        'color'    => '#038047',
        'accent'   => '#038047',
        'dark'     => '#026738',
        'gradient' => 'linear-gradient(135deg, #038047 0%, #026738 100%)',
        'iconSvg'  => '<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8" width="18" height="18"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8z"/></svg>',
        'iconSvgLg'=> '<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8" width="28" height="28"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8z"/></svg>',
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
const PLATFORM   = 'News';
let START_DATE   = '{{ $startDate ?? "" }}';
let END_DATE     = '{{ $endDate ?? "" }}';
const ROUTES = {
    aiProxy        : '{{ route("mk.api.news.ai-proxy") }}',
    aiAnalysisData : '{{ route("mk.api.news.ai-analysis-data") }}',
};

function buildSystemPrompt() {
    return `Anda adalah SMADIMENT AI Analyst — analis media intelligence senior yang menganalisis data pemberitaan online secara data-driven dan evidence-based.

KONTEKS SESI:
- Project ID : ${PROJECT_ID}
- Platform   : News (Online News Media / Portal Berita)
- Periode    : ${START_DATE} s/d ${END_DATE}

INSTRUKSI UTAMA:
1. Analisis berdasarkan data nyata yang disertakan — bukan asumsi.
2. Kutip judul artikel spesifik, nama publisher, dan tanggal sebagai evidence.
3. Identifikasi isu nyata dari judul dan konten artikel yang tersedia.
4. Jika artikel memuat quote/kutipan narasumber, gunakan sebagai evidence langsung.
5. Sentimen dari setiap artikel sudah tersedia — gunakan untuk analisis distribusi.

ATURAN CITATION — WAJIB DIIKUTI:
- JANGAN pernah menulis referensi seperti "Artikel [1]", "[A5]" atau nomor index apapun.
- SELALU sebut nama publisher secara langsung. Contoh: "Kompas", "Tempo", "Detik".
- Format evidence yang benar: **Publisher** (Tanggal) — *"kutipan singkat judul/konten..."*

GAYA RESPONS:
- Bahasa Indonesia profesional (kecuali user minta Inggris).
- Gunakan markdown: ## untuk heading, **bold** untuk highlight.
- Output komprehensif, terstruktur, siap dijadikan laporan profesional.`;
}
</script>

@include('mk.ai.partials.ai-analysis-scripts', ['aiPlatform' => $aiPlatform])
@endsection
