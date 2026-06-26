<?php

namespace App\Services;

class Dass21ScoringService
{
    private const SEVERITY_RANK = [
        'normal' => 0,
        'mild' => 1,
        'moderate' => 2,
        'severe' => 3,
        'extremely_severe' => 4,
    ];

    private const SEVERITY_LABELS = [
        'normal' => 'Normal',
        'mild' => 'Ringan',
        'moderate' => 'Sedang',
        'severe' => 'Berat',
        'extremely_severe' => 'Sangat Berat',
    ];

    private const DASHBOARD_ANALYSES = [
        'normal' => [
            'title' => 'Hasil Skrining Normal',
            'main_points' => [
                'Hasil Anda berada pada rentang normal.',
                'Ini berarti saat ini tidak tampak gejala depresi, kecemasan, atau stres yang bermakna.',
                'Tetap jaga pola tidur, makan seimbang, aktivitas fisik, dan hubungan sosial.',
                'Lakukan skrining ulang bila muncul keluhan baru.',
            ],
            'education_message' => 'Status Anda masih baik. Pertahankan kebiasaan sehat dan tetap perhatikan perubahan suasana hati.',
        ],
        'mild' => [
            'title' => 'Gejala Ringan',
            'main_points' => [
                'Anda menunjukkan tanda awal gangguan emosi.',
                'Gejala ringan bisa membaik dengan perbaikan gaya hidup dan dukungan sosial.',
                'Coba tidur cukup, olahraga rutin, kurangi begadang, dan lakukan relaksasi napas.',
                'Amati kondisi selama 2-4 minggu.',
            ],
            'education_message' => 'Gejala masih ringan, tetapi perlu dipantau agar tidak bertambah berat.',
        ],
        'moderate' => [
            'title' => 'Gejala Sedang',
            'main_points' => [
                'Gejala yang muncul sudah cukup mengganggu aktivitas harian.',
                'Disarankan berkonsultasi dengan psikolog atau layanan kesehatan mental.',
                'Catat pemicu stres, kelola waktu istirahat, dan hindari beban berlebih.',
                'Dukungan keluarga dan teman sangat membantu.',
            ],
            'education_message' => 'Gejala Anda perlu ditangani lebih lanjut agar tidak berkembang menjadi lebih berat.',
        ],
        'severe' => [
            'title' => 'Gejala Berat',
            'main_points' => [
                'Gejala yang muncul berada pada tingkat berat dan kemungkinan besar mengganggu fungsi harian.',
                'Segera cari dukungan profesional dari psikolog, psikiater, guru BK, atau fasilitas kesehatan.',
                'Kurangi aktivitas yang membebani dan pastikan ada pendamping tepercaya.',
                'Jika muncul pikiran untuk menyakiti diri atau merasa tidak aman, segera hubungi bantuan darurat atau fasilitas kesehatan terdekat.',
            ],
            'education_message' => 'Gejala Anda membutuhkan dukungan profesional segera. Keselamatan dan pendampingan adalah prioritas utama.',
        ],
        'extremely_severe' => [
            'title' => 'Gejala Sangat Berat',
            'main_points' => [
                'Hasil skrining menunjukkan gejala sangat berat.',
                'Kondisi ini memerlukan penanganan segera dari tenaga profesional kesehatan mental.',
                'Pastikan ada pendamping dari orang terdekat dan segera arahkan ke layanan kesehatan jiwa atau unit gawat darurat.',
                'Jika ada risiko menyakiti diri, jangan menunggu dan segera cari bantuan darurat.',
            ],
            'education_message' => 'Kondisi sangat berat perlu ditangani segera. Pastikan ada pendamping dan hubungi layanan profesional atau darurat.',
        ],
    ];

    /**
     * @param  array{depression: int, anxiety: int, stress: int}  $scores
     * @return array{depression: string, anxiety: string, stress: string}
     */
    public function severities(array $scores): array
    {
        return [
            'depression' => $this->depressionSeverity($scores['depression']),
            'anxiety' => $this->anxietySeverity($scores['anxiety']),
            'stress' => $this->stressSeverity($scores['stress']),
        ];
    }

    public function highestSeverity(array $severities): string
    {
        return collect($severities)
            ->sortByDesc(fn (string $severity): int => self::SEVERITY_RANK[$severity])
            ->first();
    }

    public function riskLevel(array $severities): ?string
    {
        $rank = self::SEVERITY_RANK[$this->highestSeverity($severities)];

        return match (true) {
            $rank >= 3 => 'urgent',
            $rank === 2 => 'attention',
            default => null,
        };
    }

    public function dashboardAnalysis(array $severities, ?array $customAnalysis = null): array
    {
        return $this->dashboardAnalysisForSeverity($this->highestSeverity($severities), $customAnalysis);
    }

    public function dashboardAnalysisForSeverity(string $severity, ?array $customAnalysis = null): array
    {
        $analysis = $customAnalysis ?: self::DASHBOARD_ANALYSES[$severity];

        return [
            'severity' => $severity,
            'severity_label' => self::SEVERITY_LABELS[$severity],
            'title' => $analysis['title'],
            'main_points' => $analysis['main_points'],
            'education_message' => $analysis['education_message'],
        ];
    }

    public function summary(array $severities): string
    {
        return sprintf(
            'Depresi: %s, kecemasan: %s, dan stres: %s. Catatan: DASS-21 adalah instrumen skrining mandiri, bukan alat diagnostik medis formal. Hasil ini tidak menggantikan evaluasi dari tenaga kesehatan profesional.',
            strtolower(self::SEVERITY_LABELS[$severities['depression']]),
            strtolower(self::SEVERITY_LABELS[$severities['anxiety']]),
            strtolower(self::SEVERITY_LABELS[$severities['stress']]),
        );
    }

    private function depressionSeverity(int $score): string
    {
        return match (true) {
            $score <= 9 => 'normal',
            $score <= 13 => 'mild',
            $score <= 20 => 'moderate',
            $score <= 27 => 'severe',
            default => 'extremely_severe',
        };
    }

    private function anxietySeverity(int $score): string
    {
        return match (true) {
            $score <= 7 => 'normal',
            $score <= 9 => 'mild',
            $score <= 14 => 'moderate',
            $score <= 19 => 'severe',
            default => 'extremely_severe',
        };
    }

    private function stressSeverity(int $score): string
    {
        return match (true) {
            $score <= 14 => 'normal',
            $score <= 18 => 'mild',
            $score <= 25 => 'moderate',
            $score <= 33 => 'severe',
            default => 'extremely_severe',
        };
    }
}
