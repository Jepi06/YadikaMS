<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LMS - SMK Yadika Soreang</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            ocean: {
              950: '#061325',
              900: '#0a1d37',
              850: '#0d2547',
              800: '#0f2f5a',
              700: '#15437f',
              600: '#1d5aa6'
            },
            sky: {
              400: '#38bdf8',
              500: '#0ea5e9',
              600: '#0284c7',
              50: '#f0f9ff',
              100: '#e0f2fe'
            },
            azure: {
              500: '#2563eb',
              600: '#1d4ed8'
            }
          },
          fontFamily: {
            sans: ['"Plus Jakarta Sans"', 'sans-serif'],
            display: ['"Space Grotesk"', 'sans-serif']
          }
        }
      }
    }
  </script>
  <style>
    html { scroll-behavior: smooth; }
    .glass-nav {
      background: rgba(255, 255, 255, 0.88);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
    }
    .hero-gradient {
      background: radial-gradient(120% 120% at 75% 20%, #1e40af 0%, #0d2547 48%, #061325 100%);
    }
    .badge-glow {
      box-shadow: 0 0 25px rgba(56, 189, 248, 0.35);
    }
    .glow-card {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .glow-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 20px 35px -10px rgba(13, 37, 71, 0.12), 0 0 0 1px rgba(14, 165, 233, 0.3);
    }
    .pattern-grid {
      background-size: 32px 32px;
      background-image:
        linear-gradient(to right, rgba(255, 255, 255, 0.05) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
    }
  </style>
</head>
<body class="bg-[#f6f9fc] font-sans text-slate-800 antialiased selection:bg-sky-500 selection:text-white">

  <!-- NAVBAR -->
  <header class="glass-nav sticky top-0 z-50 border-b border-slate-200/80 transition-all">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-20">
        <!-- Brand -->
        <a href="{{ route('lms') }}" class="flex items-center gap-3.5 group">
          <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-ocean-900 to-sky-600 flex items-center justify-center text-white shadow-md shadow-sky-900/20 group-hover:scale-105 transition-transform duration-300">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v7" />
            </svg>
          </div>
          <div>
            <div class="flex items-center gap-2">
              <span class="text-xl font-bold font-display tracking-tight text-ocean-950 group-hover:text-ocean-700 transition-colors">LMS Yadika</span>
              <span class="hidden sm:inline text-xs uppercase tracking-wider font-semibold px-2 py-0.5 rounded bg-sky-100 text-sky-700">Digital Hub</span>
            </div>
            <p class="text-xs text-slate-500 font-medium tracking-wide">SMK Yadika Soreang Kab. Bandung</p>
          </div>
        </a>

        <!-- Quick Nav Links -->
        <nav class="hidden md:flex items-center gap-8">
          <a href="#fitur" class="text-sm font-semibold text-slate-600 hover:text-sky-600 transition-colors">Fitur Unggulan</a>
          <a href="#alur-presensi" class="text-sm font-semibold text-slate-600 hover:text-sky-600 transition-colors">Presensi Barcode</a>
          <a href="#jurusan" class="text-sm font-semibold text-slate-600 hover:text-sky-600 transition-colors">Jurusan</a>
          <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold border border-emerald-200">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            Server Aktif (T.A 2024/2025)
          </span>
        </nav>

        <!-- Action CTA -->
        <div class="flex items-center gap-3">
          <a href="{{ route('lms.login') }}" class="relative inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-ocean-800 to-sky-600 hover:from-ocean-900 hover:to-sky-500 shadow-md shadow-sky-900/20 hover:shadow-lg hover:shadow-sky-600/30 transition-all duration-300 transform active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
            </svg>
            <span>Masuk Portal</span>
          </a>
        </div>
      </div>
    </div>
  </header>

  <!-- HERO SECTION -->
  <section class="hero-gradient relative text-white overflow-hidden pattern-grid pt-14 pb-20 lg:pt-20 lg:pb-28">
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-sky-500/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 right-10 w-[500px] h-[500px] bg-ocean-600/30 rounded-full blur-[110px] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">

        <!-- Left: Copywriting & CTA -->
        <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
          <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-sky-200 text-xs sm:text-sm font-semibold tracking-wide badge-glow">
            <span class="w-2 h-2 rounded-full bg-sky-400"></span>
            SMK Yadika Soreang — Terakreditasi "A"
          </div>

          <h1 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-extrabold font-display leading-[1.12] tracking-tight">
            Learning Management System <br class="hidden sm:inline">
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-300 via-sky-200 to-white">
              Cerdas, Cepat &amp; Terintegrasi
            </span>
          </h1>

          <p class="text-base sm:text-lg text-slate-200/90 font-normal leading-relaxed max-w-2xl mx-auto lg:mx-0">
            Satu ekosistem terpadu untuk mendistribusikan modul materi, pengumpulan tugas daring, dan presensi kelas berkecepatan tinggi — baik input manual maupun <span class="text-sky-300 font-semibold underline decoration-sky-400/50 underline-offset-4">Scan Barcode Dinamis</span> langsung dari instruktur.
          </p>

          <div class="pt-2 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
            <a href="{{ route('lms.login') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-white text-ocean-950 font-bold text-base shadow-xl shadow-ocean-950/40 hover:bg-sky-50 hover:text-ocean-900 transition-all duration-300 flex items-center justify-center gap-3 group">
              <span>Masuk ke LMS Sekarang</span>
              <svg class="w-5 h-5 text-sky-600 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
              </svg>
            </a>

            <a href="#fitur" class="w-full sm:w-auto px-6 py-3.5 rounded-xl bg-white/10 hover:bg-white/15 backdrop-blur-md border border-white/20 text-white font-semibold text-base transition-colors flex items-center justify-center gap-2">
              <svg class="w-5 h-5 text-sky-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>Pelajari Fitur</span>
            </a>
          </div>

          <div class="pt-6 border-t border-white/15 grid grid-cols-3 gap-4 text-center lg:text-left">
            <div>
              <div class="text-2xl sm:text-3xl font-extrabold text-white font-display">1.200+</div>
              <div class="text-xs text-sky-200/80 font-medium">Siswa &amp; Taruna Aktif</div>
            </div>
            <div>
              <div class="text-2xl sm:text-3xl font-extrabold text-white font-display">60+</div>
              <div class="text-xs text-sky-200/80 font-medium">Guru &amp; Mentor Ahli</div>
            </div>
            <div>
              <div class="text-2xl sm:text-3xl font-extrabold text-white font-display">100%</div>
              <div class="text-xs text-sky-200/80 font-medium">Daring &amp; Real-Time</div>
            </div>
          </div>
        </div>

        <!-- Right: Dashboard Preview Mockup -->
        <div class="lg:col-span-5 relative">
          <div class="relative mx-auto max-w-md lg:max-w-none">
            <div class="absolute -inset-1.5 bg-gradient-to-r from-sky-400 to-blue-600 rounded-3xl blur-xl opacity-40"></div>

            <div class="relative bg-ocean-900/90 border border-white/20 rounded-2xl p-6 backdrop-blur-xl shadow-2xl text-slate-100">

              <div class="flex items-center justify-between pb-4 mb-4 border-b border-white/10">
                <div class="flex items-center gap-2">
                  <div class="w-3 h-3 rounded-full bg-rose-500/80"></div>
                  <div class="w-3 h-3 rounded-full bg-amber-500/80"></div>
                  <div class="w-3 h-3 rounded-full bg-emerald-500/80"></div>
                  <span class="text-xs text-slate-300 font-mono ml-2">app.yadika-lms.id</span>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wider uppercase bg-sky-500/20 text-sky-300 border border-sky-400/30">Live Session</span>
              </div>

              <div class="bg-gradient-to-br from-ocean-800/90 to-ocean-800/40 border border-sky-400/20 rounded-xl p-4 mb-4">
                <div class="flex items-start justify-between">
                  <div class="space-y-1">
                    <span class="text-[11px] font-semibold tracking-wide uppercase px-2 py-0.5 rounded bg-sky-500/20 text-sky-300">PPLG &amp; Rekayasa Perangkat Lunak</span>
                    <h4 class="text-sm font-bold text-white">Pemrograman Web &amp; Basis Data (XII RPL 2)</h4>
                    <p class="text-xs text-slate-300">Guru: Pak Hendro Wibowo, M.Kom</p>
                  </div>
                  <span class="w-8 h-8 rounded-lg bg-sky-500/20 border border-sky-400/30 flex items-center justify-center text-sky-300 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                  </span>
                </div>
              </div>

              <div class="bg-white/5 border border-white/10 rounded-xl p-4 space-y-3">
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                    <span class="text-xs font-semibold text-white">Presensi Barcode Dibuka</span>
                  </div>
                  <span class="text-[11px] font-mono text-amber-300 bg-amber-500/10 px-2 py-0.5 rounded border border-amber-400/20">Sisa 08:42 Menit</span>
                </div>

                <div class="flex items-center gap-4 bg-ocean-950/60 p-3 rounded-lg border border-white/5">
                  <div class="w-16 h-16 bg-white rounded-lg p-1.5 flex items-center justify-center shadow-inner shrink-0">
                    <svg class="w-full h-full text-ocean-950" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M2 2h7v7H2V2zm2 2v3h3V4H4zm9-2h7v7h-7V2zm2 2v3h3V4h-3zM2 13h7v7H2v-7zm2 2v3h3v-3H4zm11 0h2v2h-2v-2zm-2 2h2v2h-2v-2zm4 0h2v2h-2v-2zm-2 2h2v2h-2v-2zm2-4h2v2h-2v-2zm-6 0h2v2h-2v-2zm4-6h2v2h-2V7zm-2 2h2v2h-2V9zm4 0h2v2h-2V9z"/>
                    </svg>
                  </div>
                  <div class="text-xs space-y-1">
                    <p class="font-medium text-slate-200">Buka kamera ponsel &amp; scan QR di depan kelas</p>
                    <div class="flex items-center gap-1.5 text-[11px] text-sky-300 font-semibold">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                      <span>32 dari 36 Siswa Sudah Presensi</span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="mt-4 flex items-center justify-between text-xs text-slate-300 pt-2 border-t border-white/10">
                <span class="flex items-center gap-1.5">
                  <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                  Tugas Baru: Proyek Rest API
                </span>
                <span class="text-sky-400 font-medium">Batas: Besok 23:59</span>
              </div>

            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- HIGHLIGHT BANNER -->
  <section class="relative -mt-8 z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-2xl shadow-xl shadow-slate-900/5 border border-slate-200/90 p-4 sm:p-6 grid grid-cols-1 md:grid-cols-3 gap-6 divide-y md:divide-y-0 md:divide-x divide-slate-100">

      <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center shrink-0">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        </div>
        <div>
          <h4 class="text-sm font-bold text-ocean-950">Materi PDF, Video &amp; Dokumen</h4>
          <p class="text-xs text-slate-500 mt-0.5">Akses modul pembelajaran kapan saja tanpa batas kuota lokal.</p>
        </div>
      </div>

      <div class="flex items-center gap-4 pt-4 md:pt-0 md:pl-6">
        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center shrink-0">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
        </div>
        <div>
          <h4 class="text-sm font-bold text-ocean-950">Dual Presensi (Scan &amp; Manual)</h4>
          <p class="text-xs text-slate-500 mt-0.5">Fleksibel: scan barcode di kelas atau entri manual bagi siswa izin/sakit.</p>
        </div>
      </div>

      <div class="flex items-center gap-4 pt-4 md:pt-0 md:pl-6">
        <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 border border-teal-100 flex items-center justify-center shrink-0">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </div>
        <div>
          <h4 class="text-sm font-bold text-ocean-950">Rekap Nilai Otomatis</h4>
          <p class="text-xs text-slate-500 mt-0.5">Ekspor daftar nilai dan persentase kehadiran langsung ke file Excel/PDF.</p>
        </div>
      </div>

    </div>
  </section>

  <!-- FITUR UTAMA -->
  <section id="fitur" class="py-20 lg:py-28 scroll-mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

      <div class="text-center max-w-2xl mx-auto mb-16 space-y-3">
        <span class="inline-block text-xs font-bold uppercase tracking-widest text-sky-600 bg-sky-50 px-3 py-1 rounded-full border border-sky-100">
          Fasilitas Digital Terlengkap
        </span>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-ocean-950 font-display tracking-tight">
          Fitur Utama untuk Mendukung Belajar Modern
        </h2>
        <p class="text-slate-600 text-sm sm:text-base">
          Dirancang khusus untuk kebutuhan guru dan siswa jurusan Rekayasa Perangkat Lunak (PPLG) serta seluruh program keahlian di SMK Yadika Soreang.
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

        <!-- Feature 1 -->
        <div class="glow-card bg-white rounded-2xl p-8 border border-slate-200/80 shadow-sm flex flex-col justify-between">
          <div>
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-ocean-900 to-ocean-700 text-white flex items-center justify-center mb-6 shadow-lg shadow-ocean-950/15">
              <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
              </svg>
            </div>
            <h3 class="text-xl font-bold text-ocean-950 mb-3 font-display">Materi &amp; Tugas Interaktif</h3>
            <p class="text-slate-600 text-sm leading-relaxed mb-6">
              Guru dapat mengunggah silabus, modul materi (PDF, PPT, link video), serta penugasan terstruktur per kelas. Siswa mengunggah dokumen jawaban secara online dengan timestamp otomatis.
            </p>
          </div>
          <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500 font-medium">
            <span class="flex items-center gap-1.5 text-sky-600 font-semibold">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
              Mendukung Multi-format File
            </span>
            <span class="text-slate-400">Mudah &amp; Aman</span>
          </div>
        </div>

        <!-- Feature 2 -->
        <div class="glow-card bg-white rounded-2xl p-8 border border-sky-200 shadow-sm relative overflow-hidden flex flex-col justify-between">
          <div class="absolute top-0 right-0 bg-gradient-to-l from-sky-500 to-ocean-700 text-white text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-bl-xl shadow-sm">
            Populer
          </div>
          <div>
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-sky-500 to-sky-400 text-white flex items-center justify-center mb-6 shadow-lg shadow-sky-500/25">
              <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
              </svg>
            </div>
            <h3 class="text-xl font-bold text-ocean-950 mb-3 font-display">Presensi Barcode &amp; Manual</h3>
            <p class="text-slate-600 text-sm leading-relaxed mb-6">
              Guru membuka sesi absensi dengan QR code yang berganti secara terenkripsi di proyektor. Siswa cukup melakukan pemindaian melalui ponsel untuk verifikasi hadir secara akurat tanpa titip absen.
            </p>
          </div>
          <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500 font-medium">
            <span class="flex items-center gap-1.5 text-sky-600 font-semibold">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
              Absen Selesai &lt; 5 Detik
            </span>
            <span class="text-slate-400">Anti Kecurangan</span>
          </div>
        </div>

        <!-- Feature 3 -->
        <div class="glow-card bg-white rounded-2xl p-8 border border-slate-200/80 shadow-sm flex flex-col justify-between">
          <div>
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-ocean-800 to-sky-600 text-white flex items-center justify-center mb-6 shadow-lg shadow-ocean-800/20">
              <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
            </div>
            <h3 class="text-xl font-bold text-ocean-950 mb-3 font-display">Rekap Nilai &amp; Analitik</h3>
            <p class="text-slate-600 text-sm leading-relaxed mb-6">
              Guru menilai tugas dengan rubrik yang transparan dan memantau persentase partisipasi kehadiran kelas secara terstruktur, siap diekspor untuk bahan rapor semesteran.
            </p>
          </div>
          <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500 font-medium">
            <span class="flex items-center gap-1.5 text-sky-600 font-semibold">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
              Ekspor Laporan Cepat
            </span>
            <span class="text-slate-400">Otomatisasi Penuh</span>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ALUR PRESENSI -->
  <section id="alur-presensi" class="py-16 bg-gradient-to-b from-white to-sky-50/50 border-y border-slate-200/60 scroll-mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
        <div class="lg:col-span-6 space-y-6">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-100 text-sky-800 text-xs font-bold uppercase tracking-wider">
            Inovasi Unggulan
          </div>
          <h2 class="text-2xl sm:text-3xl font-extrabold text-ocean-950 font-display">
            Alur Presensi Barcode Digital dalam 3 Langkah Praktis
          </h2>
          <p class="text-slate-600 text-sm leading-relaxed">
            Tidak ada lagi waktu belajar terbuang hanya untuk memanggil presensi satu per satu. Dengan sistem kode QR terenkripsi yang dibuat oleh guru, pencatatan hadir selesai dalam hitungan detik.
          </p>

          <div class="space-y-4 pt-2">
            <div class="flex items-start gap-4 p-4 rounded-xl bg-white border border-slate-200/80 shadow-sm">
              <div class="w-8 h-8 rounded-lg bg-ocean-900 text-white flex items-center justify-center font-bold text-sm shrink-0">1</div>
              <div>
                <h4 class="text-sm font-bold text-ocean-950">Guru Membuka Sesi Pertemuan</h4>
                <p class="text-xs text-slate-500 mt-1">Guru memilih mata pelajaran dan memproyeksikan Barcode dinamis di layar depan kelas.</p>
              </div>
            </div>

            <div class="flex items-start gap-4 p-4 rounded-xl bg-white border border-sky-200 shadow-sm">
              <div class="w-8 h-8 rounded-lg bg-sky-600 text-white flex items-center justify-center font-bold text-sm shrink-0">2</div>
              <div>
                <h4 class="text-sm font-bold text-ocean-950">Siswa Scan Barcode dari Ponsel</h4>
                <p class="text-xs text-slate-500 mt-1">Siswa masuk ke akun LMS masing-masing dan mengarahkan kamera scanner langsung.</p>
              </div>
            </div>

            <div class="flex items-start gap-4 p-4 rounded-xl bg-white border border-slate-200/80 shadow-sm">
              <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-sm shrink-0">3</div>
              <div>
                <h4 class="text-sm font-bold text-ocean-950">Kehadiran Langsung Tercatat</h4>
                <p class="text-xs text-slate-500 mt-1">Data siswa langsung berubah status menjadi 'Hadir'. Guru dapat mengubah siswa yang sakit/izin secara manual.</p>
              </div>
            </div>
          </div>
        </div>

        <div class="lg:col-span-6 flex justify-center">
          <div class="relative w-full max-w-md bg-gradient-to-tr from-ocean-950 to-ocean-800 text-white rounded-3xl p-8 shadow-2xl border border-sky-400/20 overflow-hidden">
            <div class="absolute -right-12 -bottom-12 w-48 h-48 bg-sky-500/20 rounded-full blur-2xl"></div>

            <div class="flex items-center justify-between pb-6 border-b border-white/10">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-sky-500/20 border border-sky-400/30 flex items-center justify-center text-sky-300">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <div>
                  <h4 class="text-sm font-bold text-white">Scanner Mobile LMS</h4>
                  <p class="text-[11px] text-sky-200">Kamera Terhubung</p>
                </div>
              </div>
              <span class="px-2.5 py-1 rounded bg-emerald-500/20 text-emerald-300 text-xs font-semibold">Tervalidasi GPS</span>
            </div>

            <div class="my-8 relative flex items-center justify-center py-6">
              <div class="w-44 h-44 border-2 border-dashed border-sky-400/80 rounded-2xl relative flex items-center justify-center bg-white/5">
                <div class="absolute -top-1 -left-1 w-4 h-4 border-t-2 border-l-2 border-sky-400"></div>
                <div class="absolute -top-1 -right-1 w-4 h-4 border-t-2 border-r-2 border-sky-400"></div>
                <div class="absolute -bottom-1 -left-1 w-4 h-4 border-b-2 border-l-2 border-sky-400"></div>
                <div class="absolute -bottom-1 -right-1 w-4 h-4 border-b-2 border-r-2 border-sky-400"></div>

                <div class="w-32 h-32 bg-white rounded-xl p-2 flex items-center justify-center shadow-lg">
                  <svg class="w-full h-full text-ocean-950" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M2 2h7v7H2V2zm2 2v3h3V4H4zm9-2h7v7h-7V2zm2 2v3h3V4h-3zM2 13h7v7H2v-7zm2 2v3h3v-3H4zm11 0h2v2h-2v-2zm-2 2h2v2h-2v-2zm4 0h2v2h-2v-2zm-2 2h2v2h-2v-2zm2-4h2v2h-2v-2zm-6 0h2v2h-2v-2zm4-6h2v2h-2V7zm-2 2h2v2h-2V9zm4 0h2v2h-2V9z"/>
                  </svg>
                </div>

                <div class="absolute inset-x-2 top-1/2 h-0.5 bg-gradient-to-r from-transparent via-sky-400 to-transparent shadow-[0_0_8px_#38bdf8]"></div>
              </div>
            </div>

            <div class="text-center space-y-1">
              <p class="text-xs text-sky-200">Arahkan lensa ke kode barcode guru pengampu</p>
              <p class="text-[11px] text-slate-400">Otomatis mendeteksi tanpa perlu klik tombol lagi</p>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- JURUSAN -->
  <section id="jurusan" class="py-20 scroll-mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center max-w-2xl mx-auto mb-14 space-y-2">
        <h3 class="text-2xl sm:text-3xl font-extrabold text-ocean-950 font-display">Tersedia untuk Semua Program Keahlian</h3>
        <p class="text-slate-600 text-sm">SMK Yadika Soreang memfasilitasi setiap kompetensi dengan modul digital yang relevan dengan industri.</p>
      </div>

      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm text-center hover:border-sky-300 transition-colors">
          <div class="w-12 h-12 mx-auto rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center font-bold mb-3 text-xs">PPLG</div>
          <h5 class="font-bold text-sm text-ocean-950">Rekayasa Perangkat Lunak</h5>
          <p class="text-xs text-slate-500 mt-1">Coding, Basis Data &amp; Web</p>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm text-center hover:border-sky-300 transition-colors">
          <div class="w-12 h-12 mx-auto rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold mb-3 text-xs">TJKT</div>
          <h5 class="font-bold text-sm text-ocean-950">Teknik Komputer &amp; Jaringan</h5>
          <p class="text-xs text-slate-500 mt-1">MikroTik, Server &amp; Fiber Optik</p>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm text-center hover:border-sky-300 transition-colors">
          <div class="w-12 h-12 mx-auto rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold mb-3 text-xs">AKL</div>
          <h5 class="font-bold text-sm text-ocean-950">Akuntansi Keuangan</h5>
          <p class="text-xs text-slate-500 mt-1">Spreadsheet &amp; Accurate</p>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm text-center hover:border-sky-300 transition-colors">
          <div class="w-12 h-12 mx-auto rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold mb-3 text-xs">MPLB</div>
          <h5 class="font-bold text-sm text-ocean-950">Manajemen Perkantoran</h5>
          <p class="text-xs text-slate-500 mt-1">Administrasi &amp; Komunikasi Bisnis</p>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
    <div class="bg-gradient-to-r from-ocean-950 via-ocean-900 to-sky-700 rounded-3xl p-8 sm:p-12 text-center text-white relative overflow-hidden shadow-2xl">
      <div class="relative z-10 max-w-2xl mx-auto space-y-5">
        <h3 class="text-2xl sm:text-4xl font-extrabold font-display">Siap Memulai Pembelajaran Hari Ini?</h3>
        <p class="text-sm sm:text-base text-sky-100">
          Gunakan akun siswa atau guru Anda untuk mengakses ruang kelas virtual, modul pelajaran, dan daftar presensi.
        </p>
        <div class="pt-2">
          <a href="{{ route('lms.login') }}" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-xl bg-white text-ocean-950 font-bold hover:bg-sky-50 shadow-lg hover:scale-105 transition-all">
            <span>Masuk ke Akun LMS</span>
            <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="bg-white border-t border-slate-200 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
        <div class="flex items-center gap-2.5">
          <div class="w-7 h-7 rounded-lg bg-ocean-900 text-white flex items-center justify-center text-xs font-bold">Y</div>
          <span class="text-sm font-bold text-ocean-950">SMK Yadika Soreang</span>
          <span class="text-slate-300">|</span>
          <span class="text-xs text-slate-500">Learning Management System</span>
        </div>
        <p class="text-xs text-slate-500">
          &copy; {{ date('Y') }} SMK Yadika Soreang. Hak Cipta Dilindungi.
        </p>
      </div>
    </div>
  </footer>

</body>
</html>