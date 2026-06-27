<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="MENTARI adalah platform pendamping kesehatan mental siswa dengan mood harian, screening DASS-21, edukasi, komunitas sekolah, dan dashboard admin.">

    <title>MENTARI - Platform Dukungan Kesehatan Mental Siswa</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .gradient-text {
            background: linear-gradient(135deg, #ec4899 0%, #6366f1 50%, #3b82f6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .glow-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glow-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -15px rgba(236, 72, 153, 0.15), 0 20px 40px -15px rgba(59, 130, 246, 0.15);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.6);
        }
        .glass-nav {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.4);
        }
        .mesh-gradient {
            background-color: #ffffff;
            background-image: 
                radial-gradient(at 10% 20%, rgba(236, 72, 153, 0.08) 0px, transparent 50%),
                radial-gradient(at 90% 10%, rgba(59, 130, 246, 0.08) 0px, transparent 50%),
                radial-gradient(at 50% 80%, rgba(99, 102, 241, 0.05) 0px, transparent 50%);
        }
    </style>
</head>
<body class="mesh-gradient font-sans text-slate-800 antialiased min-h-screen selection:bg-pink-200 selection:text-pink-900 overflow-x-hidden w-full">

    <!-- Glowing Background blobs for futuristic tech vibe -->
    <div class="absolute top-0 left-1/4 -z-10 size-96 rounded-full bg-pink-300/20 blur-3xl filter animate-pulse" style="animation-duration: 8s;"></div>
    <div class="absolute top-40 right-1/4 -z-10 size-[450px] rounded-full bg-blue-300/20 blur-3xl filter animate-pulse" style="animation-duration: 12s;"></div>

    <main class="relative">
        <!-- HEADER / NAVIGATION -->
        <header class="sticky top-0 z-50 glass-nav transition-all duration-300">
            <div class="mx-auto flex w-full max-w-7xl items-center justify-between px-6 py-4">
                <a href="/" class="inline-flex items-center gap-3 font-bold group">
                    <div class="relative flex size-10 items-center justify-center rounded-xl bg-gradient-to-tr from-pink-500 to-indigo-600 text-white shadow-md shadow-indigo-200 transition group-hover:scale-105">
                        <!-- Custom futuristic logo icon -->
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707-.707M12 7a5 5 0 100 10 5 5 0 000-10z" />
                        </svg>
                        <span class="absolute -top-0.5 -right-0.5 size-2.5 rounded-full bg-pink-400 ring-2 ring-white"></span>
                    </div>
                    <span class="text-xl tracking-tight font-extrabold bg-gradient-to-r from-slate-900 to-indigo-950 bg-clip-text text-transparent group-hover:opacity-95 transition">MENTARI</span>
                </a>

                <nav class="hidden items-center gap-8 text-sm font-semibold text-slate-600 md:flex">
                    <a href="#fitur" class="transition hover:text-pink-500">Fitur</a>
                    <a href="#alur" class="transition hover:text-pink-500">Alur</a>
                    <a href="#sekolah" class="transition hover:text-pink-500">Sekolah</a>
                    <a href="#admin" class="transition hover:text-indigo-600">Dashboard</a>
                    <a href="#api" class="transition hover:text-indigo-600">API</a>
                </nav>

                <div class="flex items-center gap-3">
                    <a href="/admin" class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-pink-500 to-indigo-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-indigo-500/20 transition duration-300 hover:opacity-95 hover:shadow-indigo-500/35 hover:-translate-y-0.5">
                        Masuk Admin
                    </a>
                </div>
            </div>
        </header>

        <!-- HERO SECTION -->
        <section class="relative overflow-hidden pt-12 pb-24 lg:pt-20">
            <div class="mx-auto max-w-7xl px-6">
                <div class="grid gap-12 lg:grid-cols-12 lg:items-center">
                    
                    <!-- Left Column (Text & CTAs) -->
                    <div class="lg:col-span-7 space-y-6 text-left relative z-10">
                        <div class="inline-flex items-center gap-2 rounded-full bg-pink-50 px-4 py-1.5 text-xs font-semibold text-pink-600 ring-1 ring-pink-500/10">
                            <span class="relative flex size-2">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-pink-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full size-2 bg-pink-500"></span>
                            </span>
                            Screening Awal & Dukungan Kesehatan Mental
                        </div>

                        <h1 class="text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl lg:text-6xl leading-[1.1]">
                            Menyinari Jiwa,<br/>
                            <span class="gradient-text">Menggapai Potensi Terbaik</span>
                        </h1>

                        <p class="max-w-2xl text-lg leading-relaxed text-slate-600">
                            MENTARI hadir sebagai platform digital pendamping kesehatan mental siswa. Mudahkan check-in mood harian, screening mandiri DASS-21, akses self-care pintar, dan hubungkan siswa dengan konselor sekolah secara real-time.
                        </p>

                        <div class="flex flex-col gap-4 sm:flex-row pt-2">
                            <a href="/admin" class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-pink-500 to-indigo-600 px-6 py-4 text-base font-bold text-white shadow-xl shadow-indigo-500/25 transition duration-300 hover:opacity-95 hover:-translate-y-0.5">
                                Buka Dashboard Admin
                                <svg class="ml-2 size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </a>
                            <a href="#fitur" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white/70 backdrop-blur-md px-6 py-4 text-base font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:border-slate-300 hover:-translate-y-0.5">
                                Lihat Fitur Utama
                            </a>
                        </div>
                    </div>

                    <!-- Right Column (Hero Image Graphic - Constrained Height) -->
                    <div class="lg:col-span-5 relative">
                        <div class="absolute -inset-2 rounded-3xl bg-gradient-to-tr from-pink-500 to-indigo-500 opacity-25 blur-2xl"></div>
                        <div class="relative overflow-hidden rounded-2xl border border-slate-200/50 shadow-2xl transition duration-500 hover:scale-[1.02] max-h-[340px] sm:max-h-[380px] lg:max-h-[420px] w-full">
                            <img src="{{ asset('images/mentari-hero-kids.png') }}" alt="Pendampingan Kesehatan Mental Remaja MENTARI" class="w-full h-[320px] sm:h-[380px] lg:h-[420px] object-cover object-top block">
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- STATS SECTION -->
        <section class="border-y border-slate-100 bg-white/60 backdrop-blur-md relative z-10">
            <div class="mx-auto max-w-7xl px-6 py-12">
                <div class="grid gap-8 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-slate-100">
                    <div class="flex items-start gap-4 py-4 sm:py-0 sm:px-6">
                        <div class="size-12 rounded-xl bg-pink-50 flex items-center justify-center text-pink-500 shrink-0 shadow-sm">
                            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-4xl font-extrabold text-slate-900">21</p>
                            <p class="mt-1.5 text-sm font-semibold text-slate-500 uppercase tracking-wider">Pertanyaan DASS-21</p>
                            <p class="mt-0.5 text-xs text-slate-400">Screening terstandarisasi untuk mendeteksi kecemasan, stres & depresi.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-4 py-6 sm:py-0 sm:px-8">
                        <div class="size-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-500 shrink-0 shadow-sm">
                            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-4xl font-extrabold text-slate-900">24/7</p>
                            <p class="mt-1.5 text-sm font-semibold text-slate-500 uppercase tracking-wider">Check-in Mood</p>
                            <p class="mt-0.5 text-xs text-slate-400">Pantau kestabilan emosi siswa kapan saja secara berkala.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 py-4 sm:py-0 sm:px-8">
                        <div class="size-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 shrink-0 shadow-sm">
                            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-4xl font-extrabold text-slate-900">1</p>
                            <p class="mt-1.5 text-sm font-semibold text-slate-500 uppercase tracking-wider">Dashboard Terpadu</p>
                            <p class="mt-0.5 text-xs text-slate-400">Satu panel kendali untuk konselor sekolah, guru BK, & dinas terkait.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FEATURES SECTION -->
        <section id="fitur" class="py-24 relative overflow-hidden">
            <div class="mx-auto max-w-7xl px-6">
                
                <!-- Section Header -->
                <div class="max-w-3xl mb-16">
                    <p class="text-xs font-bold uppercase tracking-widest text-pink-500">Fitur Utama</p>
                    <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-5xl leading-tight">
                        Didesain Khusus untuk Ekosistem Sekolah yang Responsif.
                    </h2>
                    <p class="mt-4 text-lg text-slate-500 leading-relaxed">
                        MENTARI menyatukan pencatatan emosi harian, deteksi dini klinis, materi self-care terkurasi, dan perlindungan siswa dari risiko mental dalam satu alur kerja modern.
                    </p>
                </div>

                <!-- Feature Grid -->
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4 xl:gap-8">
                    
                    <!-- Card 1 -->
                    <article class="glow-hover glass-card rounded-2xl p-8 shadow-sm transition hover:border-pink-200">
                        <div class="size-12 rounded-xl bg-pink-50 flex items-center justify-center text-pink-500 mb-6 shadow-sm">
                            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900">Mood Harian</h3>
                        <p class="mt-3 text-sm leading-relaxed text-slate-500">
                            Siswa dapat mencatat emosi harian dengan emoticons interaktif, memantau grafik kestabilan, serta menjaga streak keaktifan pendampingan.
                        </p>
                    </article>

                    <!-- Card 2 -->
                    <article class="glow-hover glass-card rounded-2xl p-8 shadow-sm transition hover:border-indigo-200">
                        <div class="size-12 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-500 mb-6 shadow-sm">
                            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900">Screening DASS-21</h3>
                        <p class="mt-3 text-sm leading-relaxed text-slate-500">
                            Kuesioner psikologi klinis DASS-21 yang diisi siswa akan dianalisis instan oleh sistem guna memetakan tingkat depresi, kecemasan, dan stres.
                        </p>
                    </article>

                    <!-- Card 3 -->
                    <article class="glow-hover glass-card rounded-2xl p-8 shadow-sm transition hover:border-blue-200">
                        <div class="size-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 mb-6 shadow-sm">
                            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.754 18.477 18.147 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900">Edukasi & Rekomendasi</h3>
                        <p class="mt-3 text-sm leading-relaxed text-slate-500">
                            Sajikan artikel kesehatan mental, audio meditatif, dan panduan konseling mandiri yang disesuaikan secara dinamis dengan hasil screening siswa.
                        </p>
                    </article>

                    <!-- Card 4 -->
                    <article class="glow-hover glass-card rounded-2xl p-8 shadow-sm transition hover:border-pink-200">
                        <div class="size-12 rounded-xl bg-rose-50 flex items-center justify-center text-rose-500 mb-6 shadow-sm">
                            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900">Komunitas Sekolah</h3>
                        <p class="mt-3 text-sm leading-relaxed text-slate-500">
                            Ruang berbagi pesan anonim positif dan aman di bawah moderasi ketat pihak sekolah, guna memupuk rasa saling peduli di kalangan siswa.
                        </p>
                    </article>

                </div>
            </div>
        </section>

        <!-- ALUR DUKUNGAN (TIMELINE) SECTION -->
        <section id="alur" class="py-24 bg-white/70 backdrop-blur-md relative z-10 border-y border-slate-100">
            <div class="mx-auto max-w-7xl px-6">
                <div class="grid gap-12 lg:grid-cols-12 lg:items-start">
                    
                    <!-- Text Info Left -->
                    <div class="lg:col-span-5 space-y-6">
                        <p class="text-xs font-bold uppercase tracking-widest text-pink-500">Alur Pendampingan</p>
                        <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl leading-tight">
                            Deteksi Dini dan Respon Cepat Sekolah.
                        </h2>
                        <p class="text-base text-slate-500 leading-relaxed">
                            Data status emosional siswa yang dikirim secara privat melalui aplikasi Android terproses instan di Laravel backend. Guru BK atau konselor dapat langsung merespons alert jika ditemukan risiko tingkat tinggi.
                        </p>
                        <div class="relative py-4 pr-10">
                            <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-pink-500/10 to-indigo-500/10 blur-xl"></div>
                            <div class="relative bg-white/80 p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4">
                                <span class="flex size-3 shrink-0 rounded-full bg-emerald-500"></span>
                                <p class="text-xs text-slate-600 font-medium leading-relaxed">Sistem enkripsi menjamin privasi data screening siswa tetap terjaga dan hanya dapat diakses oleh konselor sekolah yang terotorisasi.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Steps Timeline Right -->
                    <div class="lg:col-span-7 space-y-8 relative before:absolute before:left-6 before:top-2 before:bottom-2 before:w-[2px] before:bg-slate-100">
                        
                        <!-- Step 1 -->
                        <div class="relative pl-14 group">
                            <div class="absolute left-3 top-1.5 flex size-7 items-center justify-center rounded-full bg-white ring-4 ring-pink-50 text-xs font-bold text-pink-500 transition group-hover:scale-110 shadow-sm">
                                01
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 group-hover:text-pink-500 transition">Siswa Melakukan Check-in</h3>
                            <p class="mt-2 text-sm leading-relaxed text-slate-500">
                                Melalui aplikasi mobile Android, siswa memperbarui mood harian mereka, mengisi screening DASS-21 berkala, dan mengeksplorasi modul edukasi self-care.
                            </p>
                        </div>

                        <!-- Step 2 -->
                        <div class="relative pl-14 group">
                            <div class="absolute left-3 top-1.5 flex size-7 items-center justify-center rounded-full bg-white ring-4 ring-indigo-50 text-xs font-bold text-indigo-500 transition group-hover:scale-110 shadow-sm">
                                02
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 group-hover:text-indigo-600 transition">Sistem Menganalisis Tingkat Risiko</h3>
                            <p class="mt-2 text-sm leading-relaxed text-slate-500">
                                Kecerdasan platform memetakan hasil tes, melacak riwayat emosi siswa, menyusun rekomendasi konseling, dan memicu alert darurat untuk tingkat stres berat.
                            </p>
                        </div>

                        <!-- Step 3 -->
                        <div class="relative pl-14 group">
                            <div class="absolute left-3 top-1.5 flex size-7 items-center justify-center rounded-full bg-white ring-4 ring-blue-50 text-xs font-bold text-blue-500 transition group-hover:scale-110 shadow-sm">
                                03
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 group-hover:text-blue-600 transition">Konselor Memberikan Tindakan</h3>
                            <p class="mt-2 text-sm leading-relaxed text-slate-500">
                                Tim bimbingan konseling memantau grafik dashboard, menghubungi siswa berisiko secara privat, dan menjadwalkan konseling tatap muka jika diperlukan.
                            </p>
                        </div>

                    </div>

                </div>
            </div>
        </section>

        <!-- ADMIN DASHBOARD SHOWCASE SECTION -->
        <section id="admin" class="py-24 relative overflow-hidden bg-slate-950 text-white">
            <div class="absolute top-0 right-0 w-[500px] h-[500px] rounded-full bg-indigo-500/10 blur-[120px] -z-10"></div>
            <div class="absolute bottom-0 left-0 w-[500px] h-[500px] rounded-full bg-pink-500/10 blur-[120px] -z-10"></div>

            <div class="mx-auto max-w-7xl px-6">
                <div class="grid gap-16 lg:grid-cols-12 lg:items-center">
                    
                    <!-- Left info -->
                    <div class="lg:col-span-5 space-y-6">
                        <span class="inline-flex items-center gap-2 rounded-full bg-indigo-500/10 px-4 py-1.5 text-xs font-semibold text-indigo-400 ring-1 ring-indigo-400/20">
                            Dashboard Konselor
                        </span>
                        <h2 class="text-3xl font-extrabold tracking-tight sm:text-5xl leading-tight">
                            Kontrol Penuh Di Tangan Konselor Sekolah.
                        </h2>
                        <p class="text-slate-400 leading-relaxed text-base">
                            Panel admin bertenaga Filament memudahkan pengelolaan data sekolah, analisis statistik tren mood siswa, evaluasi tingkat screening, publikasi konten, serta penanganan notifikasi alert berisiko.
                        </p>
                        <div class="flex flex-col gap-4 sm:flex-row pt-2">
                            <a href="/admin" class="inline-flex items-center justify-center rounded-xl bg-pink-500 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-pink-500/20 transition hover:bg-pink-600 hover:-translate-y-0.5">
                                Akses Dashboard Admin
                            </a>
                            <a href="#api" class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/5 hover:bg-white/10 px-5 py-3 text-sm font-bold transition hover:-translate-y-0.5">
                                Pelajari REST API
                            </a>
                        </div>
                    </div>

                    <!-- Dashboard mockup UI Right -->
                    <div class="lg:col-span-7">
                        <div class="relative overflow-hidden rounded-2xl border border-white/10 bg-slate-900 p-6 shadow-2xl">
                            <!-- Window Bar -->
                            <div class="flex items-center justify-between border-b border-white/5 pb-4 mb-6">
                                <div class="flex items-center gap-4">
                                    <div class="flex gap-1.5">
                                        <span class="size-3 rounded-full bg-pink-500"></span>
                                        <span class="size-3 rounded-full bg-indigo-500"></span>
                                        <span class="size-3 rounded-full bg-blue-500"></span>
                                    </div>
                                    <span class="text-xs text-slate-500 font-mono">mentari_dashboard_v1.0</span>
                                </div>
                                <span class="rounded-full bg-pink-500/10 px-3 py-1 text-[10px] font-bold text-pink-400 uppercase tracking-wider">Live Monitoring</span>
                            </div>

                            <!-- Dashboard Stats Grid -->
                            <div class="grid gap-4 sm:grid-cols-3 mb-6">
                                <div class="rounded-xl border border-white/5 bg-slate-950/40 p-4">
                                    <p class="text-xs font-semibold text-slate-500">Alert Mendesak</p>
                                    <div class="flex items-baseline gap-2 mt-1">
                                        <p class="text-3xl font-extrabold text-pink-500">18</p>
                                        <span class="text-[10px] font-semibold text-pink-400 bg-pink-500/10 px-1.5 py-0.5 rounded">Tindak Lanjut</span>
                                    </div>
                                </div>
                                
                                <div class="rounded-xl border border-white/5 bg-slate-950/40 p-4">
                                    <p class="text-xs font-semibold text-slate-500">Check-in Mood</p>
                                    <div class="flex items-baseline gap-2 mt-1">
                                        <p class="text-3xl font-extrabold text-indigo-400">63%</p>
                                        <span class="text-[10px] font-semibold text-indigo-400 bg-indigo-500/10 px-1.5 py-0.5 rounded">Minggu Ini</span>
                                    </div>
                                </div>

                                <div class="rounded-xl border border-white/5 bg-slate-950/40 p-4">
                                    <p class="text-xs font-semibold text-slate-500">Konten Self-Care</p>
                                    <div class="flex items-baseline gap-2 mt-1">
                                        <p class="text-3xl font-extrabold text-blue-400">5</p>
                                        <span class="text-[10px] font-semibold text-blue-400 bg-blue-500/10 px-1.5 py-0.5 rounded">Edukasi Aktif</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Progress List / Student Metrics -->
                            <div class="space-y-4 rounded-xl border border-white/5 bg-slate-950/20 p-4">
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between text-xs font-bold">
                                        <span class="text-slate-400">Tingkat Kestabilan Emosi</span>
                                        <span class="text-indigo-400">68% Stabil</span>
                                    </div>
                                    <div class="h-2 w-full rounded-full bg-slate-800 overflow-hidden">
                                        <div class="h-full rounded-full bg-gradient-to-r from-pink-500 to-indigo-500" style="width: 68%"></div>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <div class="flex items-center justify-between text-xs font-bold">
                                        <span class="text-slate-400">Screening DASS-21 Selesai</span>
                                        <span class="text-pink-400">82% Siswa</span>
                                    </div>
                                    <div class="h-2 w-full rounded-full bg-slate-800 overflow-hidden">
                                        <div class="h-full rounded-full bg-gradient-to-r from-pink-500 to-blue-500" style="width: 82%"></div>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <div class="flex items-center justify-between text-xs font-bold">
                                        <span class="text-slate-400">Notifikasi Alert Terselesaikan</span>
                                        <span class="text-emerald-400">91% Penanganan</span>
                                    </div>
                                    <div class="h-2 w-full rounded-full bg-slate-800 overflow-hidden">
                                        <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-400" style="width: 91%"></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- REST API DOCUMENTATION SECTION -->
        <section id="api" class="py-24 relative overflow-hidden">
            <div class="mx-auto max-w-7xl px-6">
                <div class="grid gap-12 lg:grid-cols-12 lg:items-center">
                    
                    <!-- Text Left -->
                    <div class="lg:col-span-5 space-y-6">
                        <span class="inline-flex items-center gap-2 rounded-full bg-pink-50 px-4 py-1.5 text-xs font-semibold text-pink-600 ring-1 ring-pink-500/10">
                            Akses Pengembang
                        </span>
                        <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl leading-tight">
                            REST API Siap Integrasi Mobile App.
                        </h2>
                        <p class="text-slate-500 leading-relaxed text-base">
                            Backend MENTARI menyediakan API endpoint lengkap untuk pertukaran data aplikasi Android/iOS dengan dukungan Laravel Sanctum bearer token authentication.
                        </p>
                        
                        <div class="space-y-3 pt-2">
                            <div class="flex items-center gap-3 text-sm font-semibold text-slate-700">
                                <span class="flex size-2 rounded-full bg-pink-500"></span>
                                Autentikasi Keamanan Bearer Token
                            </div>
                            <div class="flex items-center gap-3 text-sm font-semibold text-slate-700">
                                <span class="flex size-2 rounded-full bg-indigo-500"></span>
                                Format Output Data Terstruktur JSON
                            </div>
                            <div class="flex items-center gap-3 text-sm font-semibold text-slate-700">
                                <span class="flex size-2 rounded-full bg-blue-500"></span>
                                Respon Latency Rendah & Aman
                            </div>
                        </div>
                    </div>

                    <!-- Code / IDE Panel Right -->
                    <div class="lg:col-span-7">
                        <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-slate-900 text-slate-300 shadow-xl font-mono text-sm">
                            
                            <!-- Window header -->
                            <div class="flex items-center justify-between border-b border-slate-800 bg-slate-950 px-5 py-3.5">
                                <div class="flex items-center gap-2">
                                    <span class="size-3 rounded-full bg-pink-500"></span>
                                    <span class="size-3 rounded-full bg-yellow-500"></span>
                                    <span class="size-3 rounded-full bg-green-500"></span>
                                </div>
                                <span class="text-xs text-slate-500">api_endpoints.sh</span>
                            </div>

                            <!-- Code space -->
                            <div class="p-6 space-y-4 overflow-x-auto">
                                <div>
                                    <span class="text-pink-400">BASE_URL</span>
                                    <span class="text-slate-400">=</span>
                                    <span class="text-emerald-400">"http://127.0.0.1:8000/api/v1"</span>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-center gap-4">
                                        <span class="rounded bg-indigo-500/10 px-2 py-0.5 text-xs font-bold text-indigo-400">GET</span>
                                        <span class="text-slate-400">/schools</span>
                                        <span class="text-slate-500 text-xs ml-auto"># Ambil daftar sekolah terdaftar</span>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <span class="rounded bg-pink-500/10 px-2 py-0.5 text-xs font-bold text-pink-400">POST</span>
                                        <span class="text-slate-400">/auth/login</span>
                                        <span class="text-slate-500 text-xs ml-auto"># Login siswa & peroleh token</span>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <span class="rounded bg-indigo-500/10 px-2 py-0.5 text-xs font-bold text-indigo-400">GET</span>
                                        <span class="text-slate-400">/dashboard</span>
                                        <span class="text-slate-500 text-xs ml-auto"># Ambil ringkasan status siswa</span>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <span class="rounded bg-pink-500/10 px-2 py-0.5 text-xs font-bold text-pink-400">POST</span>
                                        <span class="text-slate-400">/screening/results</span>
                                        <span class="text-slate-500 text-xs ml-auto"># Kirim data jawaban DASS-21</span>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <span class="rounded bg-indigo-500/10 px-2 py-0.5 text-xs font-bold text-indigo-400">GET</span>
                                        <span class="text-slate-400">/risk-alerts</span>
                                        <span class="text-slate-500 text-xs ml-auto"># Peroleh riwayat peringatan stres</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </main>

    <!-- FOOTER -->
    <footer class="border-t border-slate-100 bg-white/70 backdrop-blur-md py-12 relative z-10">
        <div class="mx-auto max-w-7xl px-6">
            <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between text-sm text-slate-500">
                <div class="flex items-center gap-3">
                    <div class="flex size-8 items-center justify-center rounded-lg bg-gradient-to-tr from-pink-500 to-indigo-600 text-white font-black text-sm shadow-md shadow-indigo-100">
                        M
                    </div>
                    <span class="font-extrabold text-slate-900 tracking-tight">MENTARI</span>
                </div>
                <div class="flex flex-col md:flex-row gap-4 md:gap-8">
                    <p>Dashboard: <a class="font-bold text-pink-500 hover:underline" href="/admin">http://127.0.0.1:8000/admin</a></p>
                    <p>API Base: <span class="font-bold text-slate-800">http://127.0.0.1:8000/api/v1</span></p>
                </div>
                <p class="text-xs text-slate-400">&copy; {{ date('Y') }} MENTARI. Platform Kesehatan Mental & Dukungan Siswa.</p>
            </div>
        </div>
    </footer>
</body>
</html>
