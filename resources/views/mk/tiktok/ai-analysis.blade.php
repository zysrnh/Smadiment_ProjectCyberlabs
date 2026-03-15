@extends('mk.layouts.app')

@section('title', 'AI Analysis - TikTok')

@php
    $projectId = $projectId ?? request('project_id', '');
    $startDate = $startDate ?? request('start_date', now()->subDays(30)->format('Y-m-d'));
    $endDate   = $endDate   ?? request('end_date',   now()->format('Y-m-d'));
    $projects  = $projects  ?? [];

    $aiPlatform = [
        'name'     => 'TikTok',
        'slug'     => 'tiktok',
        'color'    => '#010101',
        'accent'   => '#EE1D52',
        'dark'     => '#010101',
        'gradient' => 'linear-gradient(135deg, #EE1D52 0%, #69C9D0 100%)',
        'iconSvg'  => '<svg viewBox="0 0 24 24" fill="white" width="18" height="18"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.27 6.27 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V8.69a8.16 8.16 0 0 0 4.77 1.52V6.74a4.85 4.85 0 0 1-1-.05z"/></svg>',
        'iconSvgLg'=> '<svg viewBox="0 0 24 24" fill="white" width="28" height="28"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.27 6.27 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V8.69a8.16 8.16 0 0 0 4.77 1.52V6.74a4.85 4.85 0 0 1-1-.05z"/></svg>',
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
const PLATFORM   = 'TikTok';
let START_DATE   = '{{ $startDate ?? "" }}';
let END_DATE     = '{{ $endDate ?? "" }}';
const ROUTES = {
    aiProxy        : '{{ route("mk.api.tiktok.ai-proxy") }}',
    aiAnalysisData : '{{ route("mk.api.tiktok.ai-analysis-data") }}',
};

function buildSystemPrompt() {
    return `Anda adalah SMADIMENT AI Analyst — analis media intelligence senior yang menganalisis data percakapan TikTok secara data-driven dan evidence-based.

KONTEKS SESI:
- Project ID : ${PROJECT_ID}
- Platform   : TikTok
- Periode    : ${START_DATE} s/d ${END_DATE}

INSTRUKSI UTAMA:
1. Analisis berdasarkan data nyata yang disertakan — bukan asumsi.
2. Kutip nama creator (@username), caption video, jumlah views/likes/comments/shares sebagai evidence.
3. Identifikasi isu nyata dari konten video dan pola engagement TikTok.
4. Gunakan data sentimen, hashtag, dan most active creators untuk mendukung analisis.
5. Perhatikan karakteristik TikTok: short-form video, For You Page (FYP) algorithm, duet & stitch culture, trending sounds, creator economy, hashtag challenges.

ATURAN CITATION — WAJIB DIIKUTI:
- JANGAN pernah menulis referensi seperti "Post [1]", "[T5]" atau nomor index apapun.
- SELALU sebut nama creator secara langsung. Contoh: "@NamaCreator".
- SELALU sertakan kutipan singkat caption atau konten yang relevan dalam tanda kutip.
- Format evidence yang benar: **@NamaCreator** (XX views, YY likes) — *"kutipan singkat konten..."*
- Jika nama creator tidak tersedia dalam data, tulis: "creator tidak diketahui" — jangan tulis nomor index.

GAYA RESPONS:
- Bahasa Indonesia profesional (kecuali user minta Inggris).
- Gunakan markdown: ## untuk heading, **bold** untuk highlight.
- Setiap insight harus actionable dan didukung referensi spesifik dari data.
- Output komprehensif, terstruktur, siap dijadikan laporan profesional.`;
}
</script>

@include('mk.ai.partials.ai-analysis-scripts', ['aiPlatform' => $aiPlatform])
@endsection
