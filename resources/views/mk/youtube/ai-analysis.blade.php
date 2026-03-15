@extends('mk.layouts.app')

@section('title', 'AI Analysis - YouTube')

@php
    $projectId = $projectId ?? request('project_id', '');
    $startDate = $startDate ?? request('start_date', now()->subDays(30)->format('Y-m-d'));
    $endDate   = $endDate   ?? request('end_date',   now()->format('Y-m-d'));
    $projects  = $projects  ?? [];

    $aiPlatform = [
        'name'     => 'YouTube',
        'slug'     => 'youtube',
        'color'    => '#FF0000',
        'accent'   => '#FF0000',
        'dark'     => '#cc0000',
        'gradient' => 'linear-gradient(135deg, #FF0000 0%, #cc0000 100%)',
        'iconSvg'  => '<svg viewBox="0 0 24 24" fill="none" width="20" height="20"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46A2.78 2.78 0 0 0 1.46 6.42 29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58A2.78 2.78 0 0 0 3.41 19.6C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.95A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z" fill="rgba(255,255,255,0.25)"/><polygon points="9.75,8.98 9.75,15.02 15.5,12" fill="white"/></svg>',
        'iconSvgLg'=> '<svg viewBox="0 0 24 24" fill="none" width="28" height="28"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46A2.78 2.78 0 0 0 1.46 6.42 29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58A2.78 2.78 0 0 0 3.41 19.6C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.95A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z" fill="rgba(255,255,255,0.3)"/><polygon points="9.75,8.98 9.75,15.02 15.5,12" fill="white"/></svg>',
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
const PLATFORM   = 'YouTube';
let START_DATE   = '{{ $startDate ?? "" }}';
let END_DATE     = '{{ $endDate ?? "" }}';
const ROUTES = {
    aiProxy        : '{{ route("mk.api.youtube.ai-proxy") }}',
    aiAnalysisData : '{{ route("mk.api.youtube.ai-analysis-data") }}',
};

function buildSystemPrompt() {
    return `Anda adalah SMADIMENT AI Analyst — analis media intelligence senior yang menganalisis data percakapan YouTube secara data-driven dan evidence-based.

KONTEKS SESI:
- Project ID : ${PROJECT_ID}
- Platform   : YouTube
- Periode    : ${START_DATE} s/d ${END_DATE}

INSTRUKSI UTAMA:
1. Analisis berdasarkan data nyata yang disertakan — bukan asumsi.
2. Kutip nama channel, judul video, jumlah views/likes/comments sebagai evidence.
3. Identifikasi isu nyata dari konten video dan pola engagement YouTube.
4. Gunakan data sentimen, hashtag/keyword, dan most active channels untuk mendukung analisis.
5. Perhatikan karakteristik YouTube: video-first, algorithm-driven, comment dynamics, Shorts vs long-form, subscriber culture, watch time.

ATURAN CITATION — WAJIB DIIKUTI:
- JANGAN pernah menulis referensi seperti "[V1]", "[V5]" atau nomor index apapun.
- SELALU sebut nama channel secara langsung. Contoh: "Channel NamaChannel".
- Format evidence yang benar: **NamaChannel** (XX views, YY likes) — *"kutipan singkat judul/konten..."*

GAYA RESPONS:
- Bahasa Indonesia profesional (kecuali user minta Inggris).
- Gunakan markdown: ## untuk heading, **bold** untuk highlight.
- Output komprehensif, terstruktur, siap dijadikan laporan profesional.`;
}
</script>

@include('mk.ai.partials.ai-analysis-scripts', ['aiPlatform' => $aiPlatform])
@endsection
