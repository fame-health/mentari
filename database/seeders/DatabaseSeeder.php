<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\EducationCategory;
use App\Models\EducationContent;
use App\Models\MoodOption;
use App\Models\Recommendation;
use App\Models\School;
use App\Models\ScreeningQuestion;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::updateOrCreate(
            ['code' => 'MENTARI-DEMO'],
            ['name' => 'Sekolah Demo Mentari', 'address' => 'Indonesia'],
        );

        $classrooms = collect(['VII', 'VIII', 'IX', 'X', 'XI', 'XII'])
            ->mapWithKeys(fn (string $name, int $index): array => [
                $name => Classroom::updateOrCreate(
                    ['school_id' => $school->id, 'name' => $name],
                    ['sort_order' => $index + 1, 'is_active' => true],
                ),
            ]);

        User::updateOrCreate(
            ['email' => 'admin@mentari.test'],
            [
                'school_id' => $school->id,
                'classroom_id' => null,
                'name' => 'Admin Mentari',
                'password' => 'Mentari123!',
                'role' => 'admin',
                'level' => null,
                'avatar_initial' => 'A',
                'email_verified_at' => now(),
            ],
        );

        User::updateOrCreate(
            ['email' => 'siswa@mentari.test'],
            [
                'school_id' => $school->id,
                'classroom_id' => $classrooms['X']->id,
                'name' => 'Siswa Demo',
                'password' => 'Mentari123!',
                'role' => 'student',
                'level' => 'X',
                'avatar_initial' => 'S',
                'email_verified_at' => now(),
            ],
        );

        $moods = [
            ['key' => 'very_sad', 'emoji' => '😢', 'label' => 'Sangat Sedih', 'description' => 'Hari terasa sangat berat.', 'color' => '#7C83FD', 'score' => 1, 'sort_order' => 1],
            ['key' => 'sad', 'emoji' => '🙁', 'label' => 'Sedih', 'description' => 'Sedang merasa kurang baik.', 'color' => '#72A0C1', 'score' => 2, 'sort_order' => 2],
            ['key' => 'neutral', 'emoji' => '😐', 'label' => 'Biasa Saja', 'description' => 'Perasaan cukup netral.', 'color' => '#F2C94C', 'score' => 3, 'sort_order' => 3],
            ['key' => 'good', 'emoji' => '🙂', 'label' => 'Baik', 'description' => 'Hari berjalan cukup baik.', 'color' => '#6FCF97', 'score' => 4, 'sort_order' => 4],
            ['key' => 'great', 'emoji' => '😄', 'label' => 'Sangat Baik', 'description' => 'Merasa bersemangat dan bahagia.', 'color' => '#FF8EAA', 'score' => 5, 'sort_order' => 5],
        ];

        foreach ($moods as $mood) {
            MoodOption::updateOrCreate(['key' => $mood['key']], [...$mood, 'is_active' => true]);
        }

        $questionScales = [
            1 => 'stress', 2 => 'anxiety', 3 => 'depression', 4 => 'anxiety', 5 => 'depression',
            6 => 'stress', 7 => 'anxiety', 8 => 'stress', 9 => 'anxiety', 10 => 'depression',
            11 => 'stress', 12 => 'stress', 13 => 'depression', 14 => 'stress', 15 => 'anxiety',
            16 => 'depression', 17 => 'depression', 18 => 'stress', 19 => 'anxiety', 20 => 'anxiety',
            21 => 'depression',
        ];

        $questionTexts = [
            1 => 'Saya sulit menenangkan diri setelah sesuatu membuat saya kesal.',
            2 => 'Saya menyadari mulut saya terasa kering.',
            3 => 'Saya merasa sulit merasakan hal-hal positif.',
            4 => 'Saya mengalami kesulitan bernapas tanpa aktivitas fisik yang berat.',
            5 => 'Saya merasa sulit memulai sesuatu.',
            6 => 'Saya cenderung bereaksi berlebihan terhadap situasi.',
            7 => 'Saya merasakan tubuh gemetar.',
            8 => 'Saya merasa banyak menggunakan energi karena cemas.',
            9 => 'Saya khawatir akan panik dan mempermalukan diri.',
            10 => 'Saya merasa tidak memiliki sesuatu yang dinantikan.',
            11 => 'Saya merasa mudah gelisah.',
            12 => 'Saya sulit untuk rileks.',
            13 => 'Saya merasa sedih dan murung.',
            14 => 'Saya tidak sabar ketika sesuatu menghambat aktivitas saya.',
            15 => 'Saya merasa hampir panik.',
            16 => 'Saya tidak dapat merasa antusias terhadap apa pun.',
            17 => 'Saya merasa diri saya tidak berharga.',
            18 => 'Saya merasa mudah tersinggung.',
            19 => 'Saya menyadari detak jantung tanpa aktivitas fisik.',
            20 => 'Saya merasa takut tanpa alasan yang jelas.',
            21 => 'Saya merasa hidup tidak berarti.',
        ];

        foreach ($questionScales as $number => $scale) {
            ScreeningQuestion::updateOrCreate(
                ['number' => $number],
                [
                    'scale' => $scale,
                    'text' => $questionTexts[$number],
                    'sort_order' => $number,
                    'is_active' => true,
                ],
            );
        }

        $category = EducationCategory::updateOrCreate(
            ['slug' => 'kesehatan-mental'],
            [
                'title' => 'Kesehatan Mental',
                'description' => 'Materi dasar untuk mengenali dan menjaga kesehatan mental.',
                'sort_order' => 1,
                'is_active' => true,
            ],
        );

        EducationContent::updateOrCreate(
            ['education_category_id' => $category->id, 'title' => 'Mengenali Stres dan Cara Mengelolanya'],
            [
                'type' => 'article',
                'read_time_minutes' => 5,
                'read_time_label' => '5 menit baca',
                'summary' => 'Pelajari tanda-tanda stres dan langkah sederhana yang dapat dilakukan sehari-hari.',
                'body' => "Stres adalah respons tubuh terhadap tekanan. Cobalah berhenti sejenak, atur napas, tidur cukup, bergerak, dan bicarakan perasaan dengan orang yang tepercaya.\n\nJika stres terus mengganggu kegiatan sehari-hari, mintalah bantuan guru BK atau tenaga profesional.",
                'accent_color' => '#F97316',
                'published_at' => now(),
                'is_active' => true,
            ],
        );

        $recommendations = [
            ['title' => 'Latihan napas 4-4', 'category' => 'relaxation', 'description' => 'Tarik napas selama 4 detik dan hembuskan selama 4 detik. Ulangi perlahan.', 'duration_minutes' => 3, 'duration_label' => '3 menit', 'priority' => 'high', 'accent_color' => '#60A5FA'],
            ['title' => 'Jurnal singkat', 'category' => 'reflection', 'description' => 'Tuliskan apa yang kamu rasakan dan satu hal kecil yang dapat kamu lakukan hari ini.', 'duration_minutes' => 10, 'duration_label' => '10 menit', 'priority' => 'medium', 'accent_color' => '#A78BFA'],
            ['title' => 'Berjalan ringan', 'category' => 'activity', 'description' => 'Berjalanlah di tempat aman sambil memperhatikan napas dan lingkungan sekitar.', 'duration_minutes' => 15, 'duration_label' => '15 menit', 'priority' => 'medium', 'accent_color' => '#34D399'],
        ];

        foreach ($recommendations as $recommendation) {
            Recommendation::updateOrCreate(
                ['title' => $recommendation['title']],
                [...$recommendation, 'is_active' => true],
            );
        }

        $counselingScripts = [
            [
                'title' => 'Skrip konseling - Normal',
                'severity' => 'normal',
                'description' => 'Berdasarkan hasil skrining, kondisi Anda masih dalam batas normal. Ini kabar baik, tetapi tetap penting menjaga pola hidup sehat, tidur cukup, dan aktivitas fisik. Bila nanti muncul keluhan seperti sulit tidur, cemas berlebihan, atau sedih berkepanjangan, silakan lakukan skrining ulang atau berkonsultasi.',
                'accent_color' => '#22C55E',
            ],
            [
                'title' => 'Skrip konseling - Ringan',
                'severity' => 'mild',
                'description' => 'Hasil Anda menunjukkan gejala ringan. Biasanya kondisi ini bisa membaik dengan istirahat cukup, aktivitas fisik teratur, dan teknik relaksasi sederhana. Saya sarankan Anda memantau kondisi selama beberapa minggu. Bila keluhan tidak membaik atau justru meningkat, sebaiknya konsultasi lebih lanjut.',
                'accent_color' => '#38BDF8',
            ],
            [
                'title' => 'Skrip konseling - Sedang',
                'severity' => 'moderate',
                'description' => 'Hasil skrining menunjukkan gejala sedang, yang berarti kondisi ini sudah mulai mengganggu aktivitas sehari-hari. Saya menyarankan Anda untuk berkonsultasi dengan psikolog atau layanan kesehatan mental agar mendapat penanganan yang sesuai. Sementara itu, coba kurangi beban, jaga pola tidur, dan catat situasi yang memicu stres.',
                'accent_color' => '#F59E0B',
            ],
            [
                'title' => 'Skrip konseling - Berat',
                'severity' => 'severe',
                'description' => 'Hasil Anda menunjukkan gejala berat. Kondisi ini perlu evaluasi profesional segera karena dapat berdampak besar pada fungsi harian. Saya menyarankan Anda segera menghubungi psikolog, psikiater, atau fasilitas kesehatan terdekat. Jika ada pikiran untuk menyakiti diri, segera cari bantuan darurat.',
                'accent_color' => '#EF4444',
            ],
            [
                'title' => 'Skrip konseling - Sangat Berat',
                'severity' => 'extremely_severe',
                'description' => 'Hasil skrining menunjukkan kondisi sangat berat dan memerlukan penanganan segera. Keselamatan Anda adalah hal utama, jadi jangan menunda untuk mendapatkan bantuan profesional. Mohon segera dibawa atau diarahkan ke layanan kesehatan jiwa atau unit gawat darurat, dan pastikan ada pendamping dari orang terdekat.',
                'accent_color' => '#B91C1C',
            ],
        ];

        foreach ($counselingScripts as $script) {
            Recommendation::updateOrCreate(
                [
                    'category' => Recommendation::COUNSELING_SCRIPT_CATEGORY,
                    'severity' => $script['severity'],
                ],
                [
                    ...$script,
                    'category' => Recommendation::COUNSELING_SCRIPT_CATEGORY,
                    'duration_minutes' => null,
                    'duration_label' => 'Skrip singkat',
                    'priority' => 'personalized',
                    'is_active' => true,
                ],
            );
        }

        $dashboardAnalyses = [
            [
                'title' => 'Hasil Skrining Normal',
                'severity' => 'normal',
                'main_points' => [
                    'Hasil Anda berada pada rentang normal.',
                    'Ini berarti saat ini tidak tampak gejala depresi, kecemasan, atau stres yang bermakna.',
                    'Tetap jaga pola tidur, makan seimbang, aktivitas fisik, dan hubungan sosial.',
                    'Lakukan skrining ulang bila muncul keluhan baru.',
                ],
                'education_message' => 'Status Anda masih baik. Pertahankan kebiasaan sehat dan tetap perhatikan perubahan suasana hati.',
                'accent_color' => '#22C55E',
            ],
            [
                'title' => 'Gejala Ringan',
                'severity' => 'mild',
                'main_points' => [
                    'Anda menunjukkan tanda awal gangguan emosi.',
                    'Gejala ringan bisa membaik dengan perbaikan gaya hidup dan dukungan sosial.',
                    'Coba tidur cukup, olahraga rutin, kurangi begadang, dan lakukan relaksasi napas.',
                    'Amati kondisi selama 2-4 minggu.',
                ],
                'education_message' => 'Gejala masih ringan, tetapi perlu dipantau agar tidak bertambah berat.',
                'accent_color' => '#38BDF8',
            ],
            [
                'title' => 'Gejala Sedang',
                'severity' => 'moderate',
                'main_points' => [
                    'Gejala yang muncul sudah cukup mengganggu aktivitas harian.',
                    'Disarankan berkonsultasi dengan psikolog atau layanan kesehatan mental.',
                    'Catat pemicu stres, kelola waktu istirahat, dan hindari beban berlebih.',
                    'Dukungan keluarga dan teman sangat membantu.',
                ],
                'education_message' => 'Gejala Anda perlu ditangani lebih lanjut agar tidak berkembang menjadi lebih berat.',
                'accent_color' => '#F59E0B',
            ],
            [
                'title' => 'Gejala Berat',
                'severity' => 'severe',
                'main_points' => [
                    'Gejala yang muncul berada pada tingkat berat dan kemungkinan besar mengganggu fungsi harian.',
                    'Segera cari dukungan profesional dari psikolog, psikiater, guru BK, atau fasilitas kesehatan.',
                    'Kurangi aktivitas yang membebani dan pastikan ada pendamping tepercaya.',
                    'Jika muncul pikiran untuk menyakiti diri atau merasa tidak aman, segera hubungi bantuan darurat atau fasilitas kesehatan terdekat.',
                ],
                'education_message' => 'Gejala Anda membutuhkan dukungan profesional segera. Keselamatan dan pendampingan adalah prioritas utama.',
                'accent_color' => '#EF4444',
            ],
            [
                'title' => 'Gejala Sangat Berat',
                'severity' => 'extremely_severe',
                'main_points' => [
                    'Hasil skrining menunjukkan gejala sangat berat.',
                    'Kondisi ini memerlukan penanganan segera dari tenaga profesional kesehatan mental.',
                    'Pastikan ada pendamping dari orang terdekat dan segera arahkan ke layanan kesehatan jiwa atau unit gawat darurat.',
                    'Jika ada risiko menyakiti diri, jangan menunggu dan segera cari bantuan darurat.',
                ],
                'education_message' => 'Kondisi sangat berat perlu ditangani segera. Pastikan ada pendamping dan hubungi layanan profesional atau darurat.',
                'accent_color' => '#B91C1C',
            ],
        ];

        foreach ($dashboardAnalyses as $analysis) {
            Recommendation::updateOrCreate(
                [
                    'category' => Recommendation::DASHBOARD_ANALYSIS_CATEGORY,
                    'severity' => $analysis['severity'],
                ],
                [
                    ...$analysis,
                    'category' => Recommendation::DASHBOARD_ANALYSIS_CATEGORY,
                    'description' => implode("\n", $analysis['main_points']),
                    'duration_minutes' => null,
                    'duration_label' => 'Analisis dashboard',
                    'priority' => 'personalized',
                    'is_active' => true,
                ],
            );
        }
    }
}
