@extends('mk.layouts.app')

@section('title', 'AI Analysis - Facebook')

@php
    $projectId = $projectId ?? request('project_id', '');
    $startDate = $startDate ?? request('start_date', now()->subDays(30)->format('Y-m-d'));
    $endDate   = $endDate   ?? request('end_date',   now()->format('Y-m-d'));
    $projects  = $projects  ?? [];

    $aiPlatform = [
        'name'     => 'Facebook',
        'slug'     => 'facebook',
        'color'    => '#1877f2',
        'accent'   => '#1877f2',
        'dark'     => '#1260cc',
        'gradient' => 'linear-gradient(135deg, #1877f2 0%, #1260cc 100%)',
        'iconSvg'  => '<svg viewBox="0 0 24 24" fill="white" width="18" height="18"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
        'iconSvgLg'=> '<svg viewBox="0 0 24 24" fill="white" width="28" height="28"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
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
const PLATFORM   = 'Facebook';
let START_DATE   = '{{ $startDate ?? "" }}';
let END_DATE     = '{{ $endDate ?? "" }}';
const ROUTES = {
    aiProxy        : '{{ route("mk.api.facebook.ai-proxy") }}',
    aiAnalysisData : '{{ route("mk.api.facebook.ai-analysis-data") }}',
};

function buildSystemPrompt() {
    return `Anda adalah SMADIMENT AI Analyst — analis media intelligence senior yang menganalisis data percakapan Facebook secara data-driven dan evidence-based.

KONTEKS SESI:
- Project ID : ${PROJECT_ID}
- Platform   : Facebook
- Periode    : ${START_DATE} s/d ${END_DATE}

INSTRUKSI UTAMA:
1. Analisis berdasarkan data nyata yang disertakan — bukan asumsi.
2. Kutip nama akun/halaman, konten post, jumlah likes/shares/comments sebagai evidence.
3. Identifikasi isu nyata dari konten post dan pola engagement Facebook.
4. Gunakan data sentimen, hashtag, dan most active users untuk mendukung analisis.
5. Perhatikan pola viral Facebook: share networks, comment threads, dan page vs personal account dynamics.

ATURAN CITATION — WAJIB DIIKUTI:
- JANGAN pernah menulis referensi seperti "Post [1]", "Post [21]", "[P5]" atau nomor index apapun.
- SELALU sebut nama akun/halaman secara langsung. Contoh: "@NamaAkun" atau "Halaman XYZ".
- SELALU sertakan kutipan singkat konten post yang relevan dalam tanda kutip.
- Format evidence yang benar: **@NamaAkun** (XX likes, YY shares) — *"kutipan singkat konten..."*
- Jika nama akun tidak tersedia dalam data, tulis: "akun tidak diketahui" — jangan tulis nomor index.

GAYA RESPONS:
- Bahasa Indonesia profesional (kecuali user minta Inggris).
- Gunakan markdown: ## untuk heading, **bold** untuk highlight.
- Setiap insight harus actionable dan didukung referensi spesifik dari data.
- Output komprehensif, terstruktur, siap dijadikan laporan profesional.`;
}
</script>

@include('mk.ai.partials.ai-analysis-scripts', ['aiPlatform' => $aiPlatform])
@endsection
