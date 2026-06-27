<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="MENTARI adalah platform pendamping kesehatan mental siswa dengan mood harian, screening DASS-21, edukasi, komunitas sekolah, dan dashboard admin.">

    <title>MENTARI - Platform Dukungan Kesehatan Mental Siswa</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f7f4ef] font-sans text-[#18201b] antialiased">
    <main>
        <section
            class="relative isolate overflow-hidden bg-cover bg-center text-white"
            style="background-image: linear-gradient(90deg, rgba(11, 26, 22, 0.88) 0%, rgba(11, 26, 22, 0.68) 48%, rgba(11, 26, 22, 0.38) 100%), url('{{ asset('images/mentari-hero.png') }}');"
        >
            <header class="mx-auto flex w-full max-w-7xl items-center justify-between px-5 py-5">
                <a href="/" class="inline-flex items-center gap-3 font-semibold">
                    <span class="grid size-10 place-items-center rounded-md bg-[#f8c24f] text-[#1c231e] shadow-sm">
                        <span class="size-4 rounded-full bg-[#e94f67]"></span>
                    </span>
                    <span class="text-lg">MENTARI</span>
                </a>

                <nav class="hidden items-center gap-7 text-sm font-medium text-white/82 md:flex">
                    <a href="#fitur" class="transition hover:text-white">Fitur</a>
                    <a href="#alur" class="transition hover:text-white">Alur</a>
                    <a href="#admin" class="transition hover:text-white">Dashboard</a>
                    <a href="#api" class="transition hover:text-white">API</a>
                </nav>

                <a href="/admin" class="inline-flex items-center justify-center rounded-md bg-white px-4 py-2.5 text-sm font-semibold text-[#18201b] shadow-sm transition hover:bg-[#f8c24f]">
                    Masuk admin
                </a>
            </header>

            <div class="mx-auto flex min-h-[74svh] max-w-7xl items-center px-5 pb-16 pt-14">
                <div class="max-w-3xl">
                    <p class="mb-5 inline-flex items-center gap-2 rounded-md bg-white/12 px-3 py-1.5 text-sm font-medium text-white ring-1 ring-white/20">
                        <span class="size-2 rounded-full bg-[#f8c24f]"></span>
                        Screening awal, bukan diagnosis klinis
                    </p>

                    <h1 class="max-w-2xl text-5xl font-bold leading-[1.05]">
                        MENTARI
                    </h1>

                    <p class="mt-6 max-w-2xl text-lg leading-8 text-white/88">
                        Platform dukungan kesehatan mental siswa untuk check-in mood harian, screening DASS-21, rekomendasi self-care, konten edukasi, komunitas sekolah, dan monitoring admin.
                    </p>

                    <div class="mt-8 flex w-full flex-col gap-3 sm:w-auto sm:flex-row">
                        <a href="/admin" class="inline-flex w-full items-center justify-center rounded-md bg-[#f8c24f] px-5 py-3 text-sm font-semibold text-[#18201b] shadow-lg shadow-black/20 transition hover:bg-[#ffd56d] sm:w-auto">
                            Buka dashboard admin
                        </a>
                        <a href="#fitur" class="inline-flex w-full items-center justify-center rounded-md border border-white/35 bg-white/10 px-5 py-3 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/18 sm:w-auto">
                            Lihat fitur utama
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section class="border-y border-[#e0d8ca] bg-white">
            <div class="mx-auto grid max-w-7xl divide-y divide-[#e0d8ca] px-5 sm:grid-cols-3 sm:divide-x sm:divide-y-0">
                <div class="py-6">
                    <p class="text-3xl font-bold text-[#e94f67]">21</p>
                    <p class="mt-1 text-sm text-[#566058]">Pertanyaan DASS-21 untuk screening awal siswa.</p>
                </div>
                <div class="py-6 sm:px-8">
                    <p class="text-3xl font-bold text-[#1d7f70]">24/7</p>
                    <p class="mt-1 text-sm text-[#566058]">Mood check-in dan riwayat aktivitas harian.</p>
                </div>
                <div class="py-6 sm:px-8">
                    <p class="text-3xl font-bold text-[#cc7a00]">1</p>
                    <p class="mt-1 text-sm text-[#566058]">Dashboard terpadu untuk sekolah dan konselor.</p>
                </div>
            </div>
        </section>

        <section id="fitur" class="bg-[#f7f4ef] py-20">
            <div class="mx-auto max-w-7xl px-5">
                <div class="max-w-2xl">
                    <p class="text-sm font-semibold uppercase text-[#e94f67]">Fitur utama</p>
                    <h2 class="mt-3 text-3xl font-bold leading-tight text-[#18201b]">Dibuat untuk siswa, guru BK, dan pengelola sekolah.</h2>
                    <p class="mt-4 text-base leading-7 text-[#566058]">
                        MENTARI menyatukan pencatatan emosi, edukasi, rekomendasi personal, dan pemantauan risiko dalam satu alur yang mudah dipakai.
                    </p>
                </div>

                <div class="mt-10 grid gap-5 md:grid-cols-2 lg:grid-cols-4">
                    <article class="rounded-lg border border-[#e0d8ca] bg-white p-6 shadow-sm">
                        <div class="mb-5 h-1.5 w-16 rounded-full bg-[#f8c24f]"></div>
                        <h3 class="text-lg font-semibold text-[#18201b]">Mood harian</h3>
                        <p class="mt-3 text-sm leading-6 text-[#566058]">Siswa mencatat mood, melihat riwayat, dan menjaga streak aktivitas harian.</p>
                    </article>

                    <article class="rounded-lg border border-[#e0d8ca] bg-white p-6 shadow-sm">
                        <div class="mb-5 h-1.5 w-16 rounded-full bg-[#e94f67]"></div>
                        <h3 class="text-lg font-semibold text-[#18201b]">Screening DASS-21</h3>
                        <p class="mt-3 text-sm leading-6 text-[#566058]">Skor depresi, kecemasan, dan stres dihitung otomatis sebagai bahan deteksi awal.</p>
                    </article>

                    <article class="rounded-lg border border-[#e0d8ca] bg-white p-6 shadow-sm">
                        <div class="mb-5 h-1.5 w-16 rounded-full bg-[#1d7f70]"></div>
                        <h3 class="text-lg font-semibold text-[#18201b]">Konten & rekomendasi</h3>
                        <p class="mt-3 text-sm leading-6 text-[#566058]">Materi edukasi dan skrip konseling disesuaikan dengan tingkat kebutuhan siswa.</p>
                    </article>

                    <article class="rounded-lg border border-[#e0d8ca] bg-white p-6 shadow-sm">
                        <div class="mb-5 h-1.5 w-16 rounded-full bg-[#cc7a00]"></div>
                        <h3 class="text-lg font-semibold text-[#18201b]">Komunitas sekolah</h3>
                        <p class="mt-3 text-sm leading-6 text-[#566058]">Ruang berbagi antar siswa dengan pengelolaan konten dari lingkungan sekolah.</p>
                    </article>
                </div>
            </div>
        </section>

        <section id="alur" class="bg-white py-20">
            <div class="mx-auto max-w-7xl px-5">
                <div class="grid gap-12 lg:grid-cols-[0.85fr_1.15fr] lg:items-start">
                    <div>
                        <p class="text-sm font-semibold uppercase text-[#1d7f70]">Alur dukungan</p>
                        <h2 class="mt-3 text-3xl font-bold leading-tight text-[#18201b]">Sinyal siswa masuk, sekolah bisa merespons lebih cepat.</h2>
                        <p class="mt-4 text-base leading-7 text-[#566058]">
                            Data yang dikirim dari aplikasi Android diproses oleh backend Laravel, lalu ditampilkan di Filament agar admin dapat memantau tren dan alert.
                        </p>
                    </div>

                    <div class="grid gap-4">
                        <div class="rounded-lg border border-[#e0d8ca] bg-[#fbfaf7] p-5">
                            <p class="text-sm font-semibold text-[#e94f67]">01. Siswa check-in</p>
                            <p class="mt-2 text-sm leading-6 text-[#566058]">Mood harian, aktivitas, dan profil siswa diperbarui melalui REST API.</p>
                        </div>
                        <div class="rounded-lg border border-[#e0d8ca] bg-[#fbfaf7] p-5">
                            <p class="text-sm font-semibold text-[#cc7a00]">02. Sistem menghitung risiko</p>
                            <p class="mt-2 text-sm leading-6 text-[#566058]">Hasil screening dipetakan menjadi severity, analisis dashboard, rekomendasi, dan alert.</p>
                        </div>
                        <div class="rounded-lg border border-[#e0d8ca] bg-[#fbfaf7] p-5">
                            <p class="text-sm font-semibold text-[#1d7f70]">03. Admin memantau</p>
                            <p class="mt-2 text-sm leading-6 text-[#566058]">Dashboard menampilkan statistik, tren mood, distribusi risiko, dan daftar alert terbaru.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="admin" class="bg-[#18201b] py-20 text-white">
            <div class="mx-auto grid max-w-7xl gap-10 px-5 lg:grid-cols-[0.95fr_1.05fr] lg:items-center">
                <div>
                    <p class="text-sm font-semibold uppercase text-[#f8c24f]">Dashboard admin</p>
                    <h2 class="mt-3 text-3xl font-bold leading-tight">Kontrol operasional untuk sekolah dan tim pendamping.</h2>
                    <p class="mt-4 text-base leading-7 text-white/75">
                        Admin dapat mengelola sekolah, pengguna, mood, screening, konten edukasi, rekomendasi, komunitas, dan risk alert dari panel Filament.
                    </p>

                    <div class="mt-7 flex flex-col gap-3 sm:flex-row">
                        <a href="/admin" class="inline-flex w-full items-center justify-center rounded-md bg-[#f8c24f] px-5 py-3 text-sm font-semibold text-[#18201b] transition hover:bg-[#ffd56d] sm:w-auto">
                            Login admin
                        </a>
                        <a href="#api" class="inline-flex w-full items-center justify-center rounded-md border border-white/20 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10 sm:w-auto">
                            Cek endpoint API
                        </a>
                    </div>
                </div>

                <div class="rounded-lg border border-white/12 bg-white p-5 text-[#18201b] shadow-2xl shadow-black/30">
                    <div class="flex items-center justify-between border-b border-[#e0d8ca] pb-4">
                        <div>
                            <p class="text-sm font-semibold">Ringkasan sekolah</p>
                            <p class="text-xs text-[#566058]">Monitoring siswa hari ini</p>
                        </div>
                        <span class="rounded-md bg-[#e9f6f2] px-3 py-1 text-xs font-semibold text-[#1d7f70]">Aktif</span>
                    </div>

                    <div class="grid gap-3 py-5 sm:grid-cols-3">
                        <div class="border-l-4 border-[#e94f67] bg-[#fff6f7] p-4">
                            <p class="text-2xl font-bold">18</p>
                            <p class="mt-1 text-xs text-[#566058]">Alert perlu tindak lanjut</p>
                        </div>
                        <div class="border-l-4 border-[#f8c24f] bg-[#fffaf0] p-4">
                            <p class="text-2xl font-bold">63%</p>
                            <p class="mt-1 text-xs text-[#566058]">Check-in mood minggu ini</p>
                        </div>
                        <div class="border-l-4 border-[#1d7f70] bg-[#f1faf7] p-4">
                            <p class="text-2xl font-bold">5</p>
                            <p class="mt-1 text-xs text-[#566058]">Konten edukasi aktif</p>
                        </div>
                    </div>

                    <div class="space-y-3 border-t border-[#e0d8ca] pt-4">
                        <div class="flex items-center justify-between gap-4 text-sm">
                            <span class="font-medium">Tren mood stabil</span>
                            <span class="h-2 w-36 rounded-full bg-[#e0d8ca]"><span class="block h-2 w-24 rounded-full bg-[#1d7f70]"></span></span>
                        </div>
                        <div class="flex items-center justify-between gap-4 text-sm">
                            <span class="font-medium">Screening selesai</span>
                            <span class="h-2 w-36 rounded-full bg-[#e0d8ca]"><span class="block h-2 w-28 rounded-full bg-[#f8c24f]"></span></span>
                        </div>
                        <div class="flex items-center justify-between gap-4 text-sm">
                            <span class="font-medium">Alert dibaca</span>
                            <span class="h-2 w-36 rounded-full bg-[#e0d8ca]"><span class="block h-2 w-20 rounded-full bg-[#e94f67]"></span></span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="api" class="bg-[#f7f4ef] py-20">
            <div class="mx-auto max-w-7xl px-5">
                <div class="grid gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:items-start">
                    <div>
                        <p class="text-sm font-semibold uppercase text-[#cc7a00]">REST API Android</p>
                        <h2 class="mt-3 text-3xl font-bold leading-tight text-[#18201b]">Endpoint siap dipakai aplikasi mobile.</h2>
                        <p class="mt-4 text-base leading-7 text-[#566058]">
                            Backend memakai Laravel Sanctum untuk autentikasi bearer token dan menyediakan data sekolah, dashboard siswa, mood, screening, edukasi, rekomendasi, komunitas, dan alert.
                        </p>
                    </div>

                    <div class="overflow-hidden rounded-lg border border-[#d8cfbf] bg-[#101713] text-sm shadow-sm">
                        <div class="flex items-center gap-2 border-b border-white/10 px-5 py-3">
                            <span class="size-2.5 rounded-full bg-[#e94f67]"></span>
                            <span class="size-2.5 rounded-full bg-[#f8c24f]"></span>
                            <span class="size-2.5 rounded-full bg-[#1d7f70]"></span>
                        </div>
                        <div class="space-y-3 p-5 font-mono text-[#e6efe8]">
                            <p><span class="text-[#f8c24f]">Base URL</span> http://127.0.0.1:8000/api/v1</p>
                            <p><span class="text-[#1d7f70]">GET</span> /schools</p>
                            <p><span class="text-[#1d7f70]">POST</span> /auth/login</p>
                            <p><span class="text-[#1d7f70]">GET</span> /dashboard</p>
                            <p><span class="text-[#1d7f70]">POST</span> /screening/results</p>
                            <p><span class="text-[#1d7f70]">GET</span> /risk-alerts</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-[#e0d8ca] bg-white">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-5 py-8 text-sm text-[#566058] md:flex-row md:items-center md:justify-between">
            <p class="font-semibold text-[#18201b]">MENTARI</p>
            <p>Dashboard: <a class="font-medium text-[#e94f67] hover:underline" href="/admin">http://127.0.0.1:8000/admin</a></p>
            <p>API: <span class="font-medium text-[#18201b]">http://127.0.0.1:8000/api/v1</span></p>
        </div>
    </footer>
</body>
</html>
