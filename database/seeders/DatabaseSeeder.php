<?php

namespace Database\Seeders;

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

        User::updateOrCreate(
            ['email' => 'admin@mentari.test'],
            [
                'school_id' => $school->id,
                'name' => 'Admin Mentari',
                'password' => 'Mentari123!',
                'role' => 'admin',
                'avatar_initial' => 'A',
                'email_verified_at' => now(),
            ],
        );

        User::updateOrCreate(
            ['email' => 'siswa@mentari.test'],
            [
                'school_id' => $school->id,
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
    }
}
