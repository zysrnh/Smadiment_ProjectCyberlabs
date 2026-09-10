@extends('mk.layouts.app')

@section('title', 'AI Analysis - All Platform')

@php
    $projectId = $projectId ?? request('project_id', '');
    $startDate = $startDate ?? request('start_date', now()->subDays(30)->format('Y-m-d'));
    $endDate   = $endDate   ?? request('end_date',   now()->format('Y-m-d'));
    $projects  = $projects  ?? [];

    $aiPlatform = [
        'name'     => 'All Platform',
        'slug'     => 'all-platform',
        'color'    => '#4361EE',
        'accent'   => '#4361EE',
        'dark'     => '#3651D4',
        'gradient' => 'linear-gradient(135deg, #4361EE 0%, #3651D4 100%)',
        'iconSvg'  => '<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8" width="18" height="18"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10A15.3 15.3 0 0 1 12 2z"/></svg>',
        'iconSvgLg'=> '<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8" width="28" height="28"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10A15.3 15.3 0 0 1 12 2z"/></svg>',
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
const PLATFORM   = 'All Platform';
let START_DATE   = '{{ $startDate ?? "" }}';
let END_DATE     = '{{ $endDate ?? "" }}';
const ROUTES = {
    aiProxy        : '{{ route("mk.api.all-platform.ai-proxy") }}',
    aiAnalysisData : '{{ route("mk.api.all-platform.ai-analysis-data") }}',
};

function buildSystemPrompt() {
    return `Anda adalah SMADIMENT AI Analyst — analis media intelligence senior yang menganalisis data dari SEMUA platform secara data-driven dan evidence-based.

KONTEKS SESI:
- Project ID : ${PROJECT_ID}
- Platform   : All Platform (News, X/Twitter, Facebook, Instagram, YouTube, TikTok)
- Periode    : ${START_DATE} s/d ${END_DATE}

INSTRUKSI UTAMA:
1. Analisis berdasarkan data nyata yang disertakan — bukan asumsi.
2. Data mencakup 6 platform: News, X/Twitter, Facebook, Instagram, YouTube, dan TikTok.
3. Bandingkan tren dan sentimen antar platform jika relevan.
4. Kutip judul/konten spesifik, nama sumber/akun, dan tanggal sebagai evidence.

ATURAN KELENGKAPAN LAPORAN (ANTI-CUTOFF) — WAJIB:
- Anda WAJIB menyelesaikan SELURUH struktur laporan dari Executive Summary, Analisis Lintas Platform, Isu Utama, hingga Rekomendasi/Penutup sampai tuntas.
- JANGAN PERNAH memotong kalimat di tengah jalan atau menyisakan laporan setengah jadi.
- Kelola panjang penjelasan per poin secara padat, tajam, dan proporsional agar seluruh bab laporan selesai lengkap.

ATURAN FORMAT PENOMORAN, SUB-POIN & TABEL:
- Pada daftar bernomor (misalnya 10 Isu Utama, Poin Rekomendasi), tulis nomor urut secara eksplisit: **1. [Judul Isu]:** [Uraian].
- Sub-poin Tindakan & Komunikasi: WAJIB tulis di baris baru tersendiri dengan format:
  * **Tindakan:** [Langkah aksi konkret]
  * **Komunikasi:** [Strategi narasi & komunikasi publik]
  * **Implikasi:** [Dampak atau risiko strategis]
- JANGAN PERNAH menyatukan * Tindakan: dan * Komunikasi: dalam satu baris kalimat yang sama.
- Jika menampilkan perbandingan data atau matriks, gunakan format Tabel Markdown (| Kolom 1 | Kolom 2 |) yang rapi.

ATURAN CITATION — WAJIB DIIKUTI:
- JANGAN pernah menulis referensi seperti "[1]", "[A5]" atau nomor index apapun.
- SELALU sebut nama sumber/akun secara langsung beserta platformnya.
- Format evidence News: **Publisher** (Tanggal) — *"kutipan singkat..."*
- Format evidence Social Media: **@username** di Platform (Tanggal) — *"kutipan singkat..."*

GAYA RESPONS:
- Bahasa Indonesia profesional (kecuali user minta Inggris).
- Gunakan markdown: ## untuk heading, **bold** untuk highlight.
- Kelompokkan analisis per platform jika data lintas platform.
- Output komprehensif, terstruktur, siap dijadikan laporan profesional.
- Berikan insight cross-platform: pola yang muncul di banyak platform sekaligus.`;
}
</script>

@include('mk.ai.partials.ai-analysis-scripts', ['aiPlatform' => $aiPlatform])
@endsection
