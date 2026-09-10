@extends('mk.layouts.app')

@section('title', 'AI Analysis - Instagram')

@php
    $projectId = $projectId ?? request('project_id', '');
    $startDate = $startDate ?? request('start_date', now()->subDays(30)->format('Y-m-d'));
    $endDate   = $endDate   ?? request('end_date',   now()->format('Y-m-d'));
    $projects  = $projects  ?? [];

    $aiPlatform = [
        'name'     => 'Instagram',
        'slug'     => 'instagram',
        'color'    => '#e6683c',
        'accent'   => '#e6683c',
        'dark'     => '#c13584',
        'gradient' => 'linear-gradient(135deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%)',
        'iconSvg'  => '<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" width="18" height="18"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>',
        'iconSvgLg'=> '<svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" width="28" height="28"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>',
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
const PLATFORM   = 'Instagram';
let START_DATE   = '{{ $startDate ?? "" }}';
let END_DATE     = '{{ $endDate ?? "" }}';
const ROUTES = {
    aiProxy        : '{{ route("mk.api.instagram.ai-proxy") }}',
    aiAnalysisData : '{{ route("mk.api.instagram.ai-analysis-data") }}',
};

function buildSystemPrompt() {
    return `Anda adalah SMADIMENT AI Analyst — analis media intelligence senior yang menganalisis data percakapan Instagram secara data-driven dan evidence-based.

KONTEKS SESI:
- Project ID : ${PROJECT_ID}
- Platform   : Instagram
- Periode    : ${START_DATE} s/d ${END_DATE}

INSTRUKSI UTAMA:
1. Analisis berdasarkan data nyata yang disertakan — bukan asumsi.
2. Kutip nama akun (@username), konten caption, jumlah likes/comments sebagai evidence.
3. Identifikasi isu nyata dari konten post dan pola engagement Instagram.
4. Gunakan data sentimen, hashtag, dan most active accounts untuk mendukung analisis.
5. Perhatikan karakteristik Instagram: visual-first, hashtag culture, influencer ecosystem, Reels virality, dan Stories.

ATURAN KELENGKAPAN LAPORAN (ANTI-CUTOFF) — WAJIB:
- Anda WAJIB menyelesaikan SELURUH struktur laporan dari Executive Summary, Isu Utama, hingga Rekomendasi/Penutup sampai tuntas.
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
- JANGAN pernah menulis referensi seperti "Post [1]", "[P5]" atau nomor index apapun.
- SELALU sebut nama akun secara langsung. Contoh: "@namaakun".
- Format evidence yang benar: **@NamaAkun** (XX likes, YY comments) — *"kutipan singkat caption..."*

GAYA RESPONS:
- Bahasa Indonesia profesional (kecuali user minta Inggris).
- Gunakan markdown: ## untuk heading, **bold** untuk highlight.
- Output komprehensif, terstruktur, siap dijadikan laporan profesional.`;
}
</script>

@include('mk.ai.partials.ai-analysis-scripts', ['aiPlatform' => $aiPlatform])
@endsection
