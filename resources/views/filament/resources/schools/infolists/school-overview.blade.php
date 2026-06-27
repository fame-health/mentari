@php
    $classrooms = $record->classrooms()->get();
    $activeClassrooms = $classrooms->where('is_active', true);
    $inactiveClassrooms = $classrooms->where('is_active', false);

    $studentsCount = (int) ($record->getAttribute('students_count')
        ?? $record->users()->where('role', 'student')->count());
    $counselorsCount = (int) ($record->getAttribute('counselors_count')
        ?? $record->users()->where('role', 'counselor')->count());
    $screeningCount = (int) ($record->getAttribute('screening_results_count')
        ?? $record->screeningResults()->count());
    $activeAlertCount = (int) ($record->getAttribute('active_risk_alerts_count')
        ?? $record->riskAlerts()->whereNull('dismissed_at')->count());

    $stats = [
        [
            'label' => 'Siswa',
            'value' => $studentsCount,
            'description' => 'Terdaftar',
            'icon' => 'heroicon-o-users',
            'tone' => 'sky',
        ],
        [
            'label' => 'Konselor',
            'value' => $counselorsCount,
            'description' => 'Pendamping',
            'icon' => 'heroicon-o-user-group',
            'tone' => 'emerald',
        ],
        [
            'label' => 'Screening',
            'value' => $screeningCount,
            'description' => 'Selesai',
            'icon' => 'heroicon-o-clipboard-document-check',
            'tone' => 'violet',
        ],
        [
            'label' => 'Alert aktif',
            'value' => $activeAlertCount,
            'description' => $activeAlertCount > 0 ? 'Perlu dicek' : 'Aman',
            'icon' => $activeAlertCount > 0 ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-check-circle',
            'tone' => $activeAlertCount > 0 ? 'rose' : 'amber',
        ],
    ];

    $createdAt = $record->created_at?->format('d M Y, H:i') ?? '-';
    $updatedAt = $record->updated_at?->diffForHumans() ?? '-';
    $deletedAt = $record->deleted_at?->format('d M Y, H:i');
@endphp

<style>
    .mentari-school-view {
        display: grid;
        gap: 1rem;
    }

    .mentari-school-view__hero {
        overflow: hidden;
        border: 1px solid rgb(186 230 253 / .95);
        border-radius: .5rem;
        background:
            linear-gradient(135deg, rgb(14 165 233 / .14), rgb(255 255 255 / .96) 48%),
            linear-gradient(90deg, rgb(16 185 129 / .12), transparent 58%);
        box-shadow: 0 16px 36px rgb(15 23 42 / .08);
    }

    .mentari-school-view__hero-inner {
        display: flex;
        gap: 1rem;
        align-items: flex-start;
        justify-content: space-between;
        padding: 1rem;
    }

    .mentari-school-view__identity {
        min-width: 0;
        display: flex;
        gap: .875rem;
        align-items: flex-start;
    }

    .mentari-school-view__avatar {
        display: grid;
        width: 3.25rem;
        height: 3.25rem;
        flex: none;
        place-items: center;
        border-radius: .5rem;
        background: rgb(14 165 233);
        color: white;
        box-shadow: 0 10px 20px rgb(14 165 233 / .28);
    }

    .mentari-school-view__avatar .fi-icon {
        width: 1.55rem;
        height: 1.55rem;
    }

    .mentari-school-view__eyebrow {
        margin-bottom: .2rem;
        font-size: .72rem;
        font-weight: 800;
        color: rgb(2 132 199);
        text-transform: uppercase;
    }

    .mentari-school-view__title {
        margin: 0;
        color: rgb(15 23 42);
        font-size: 1.25rem;
        font-weight: 850;
        line-height: 1.2;
    }

    .mentari-school-view__address {
        display: flex;
        gap: .4rem;
        align-items: flex-start;
        max-width: 42rem;
        margin-top: .45rem;
        color: rgb(71 85 105);
        font-size: .86rem;
        line-height: 1.5;
    }

    .mentari-school-view__address .fi-icon {
        width: 1rem;
        height: 1rem;
        margin-top: .16rem;
        color: rgb(14 165 233);
    }

    .mentari-school-view__badges {
        display: flex;
        flex-wrap: wrap;
        gap: .45rem;
        justify-content: flex-end;
    }

    .mentari-school-view__code {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        max-width: 100%;
        border: 1px solid rgb(125 211 252 / .88);
        border-radius: .5rem;
        background: rgb(240 249 255 / .96);
        padding: .42rem .6rem;
        color: rgb(3 105 161);
        font-size: .74rem;
        font-weight: 800;
    }

    .mentari-school-view__code .fi-icon {
        width: .95rem;
        height: .95rem;
    }

    .mentari-school-view__status {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        border-radius: .5rem;
        padding: .42rem .6rem;
        font-size: .74rem;
        font-weight: 800;
    }

    .mentari-school-view__status--active {
        border: 1px solid rgb(167 243 208 / .95);
        background: rgb(236 253 245 / .98);
        color: rgb(4 120 87);
    }

    .mentari-school-view__status--deleted {
        border: 1px solid rgb(254 205 211 / .95);
        background: rgb(255 241 242 / .98);
        color: rgb(190 18 60);
    }

    .mentari-school-view__status .fi-icon {
        width: .95rem;
        height: .95rem;
    }

    .mentari-school-view__stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: .65rem;
    }

    .mentari-school-view__stat {
        display: flex;
        gap: .75rem;
        align-items: center;
        min-width: 0;
        min-height: 5.1rem;
        border: 1px solid rgb(226 232 240 / .95);
        border-radius: .5rem;
        background:
            linear-gradient(180deg, rgb(248 250 252 / .96), rgb(255 255 255));
        padding: .82rem;
        box-shadow: 0 8px 18px rgb(15 23 42 / .045);
    }

    .mentari-school-view__stat-icon {
        display: grid;
        width: 2.35rem;
        height: 2.35rem;
        flex: none;
        place-items: center;
        border-radius: .5rem;
    }

    .mentari-school-view__stat-icon .fi-icon {
        width: 1.18rem;
        height: 1.18rem;
    }

    .mentari-school-view__stat-value {
        color: rgb(15 23 42);
        font-size: 1.25rem;
        font-weight: 850;
        line-height: 1;
    }

    .mentari-school-view__stat-label {
        margin-top: .22rem;
        color: rgb(51 65 85);
        font-size: .8rem;
        font-weight: 800;
    }

    .mentari-school-view__stat-description {
        margin-top: .08rem;
        color: rgb(100 116 139);
        font-size: .72rem;
    }

    .mentari-school-view__stat--sky .mentari-school-view__stat-icon {
        background: rgb(224 242 254);
        color: rgb(2 132 199);
    }

    .mentari-school-view__stat--emerald .mentari-school-view__stat-icon {
        background: rgb(209 250 229);
        color: rgb(5 150 105);
    }

    .mentari-school-view__stat--violet .mentari-school-view__stat-icon {
        background: rgb(237 233 254);
        color: rgb(124 58 237);
    }

    .mentari-school-view__stat--rose .mentari-school-view__stat-icon {
        background: rgb(255 228 230);
        color: rgb(225 29 72);
    }

    .mentari-school-view__stat--amber .mentari-school-view__stat-icon {
        background: rgb(254 243 199);
        color: rgb(217 119 6);
    }

    .mentari-school-view__grid {
        display: grid;
        grid-template-columns: minmax(0, 1.35fr) minmax(17rem, .65fr);
        gap: .85rem;
    }

    .mentari-school-view__panel {
        border: 1px solid rgb(226 232 240 / .96);
        border-radius: .5rem;
        background: rgb(255 255 255 / .98);
        padding: .95rem;
        box-shadow: 0 8px 18px rgb(15 23 42 / .045);
    }

    .mentari-school-view__panel--classes {
        border-top: 3px solid rgb(16 185 129);
        background:
            linear-gradient(180deg, rgb(236 253 245 / .74), rgb(255 255 255) 44%);
    }

    .mentari-school-view__panel--history {
        border-top: 3px solid rgb(245 158 11);
        background:
            linear-gradient(180deg, rgb(255 251 235 / .82), rgb(255 255 255) 44%);
    }

    .mentari-school-view__panel-heading {
        display: flex;
        gap: .55rem;
        align-items: center;
        color: rgb(15 23 42);
        font-size: .92rem;
        font-weight: 850;
    }

    .mentari-school-view__panel-heading .fi-icon {
        width: 1rem;
        height: 1rem;
    }

    .mentari-school-view__panel-copy {
        margin-top: .22rem;
        color: rgb(100 116 139);
        font-size: .76rem;
        line-height: 1.45;
    }

    .mentari-school-view__class-list {
        display: flex;
        flex-wrap: wrap;
        gap: .48rem;
        margin-top: .8rem;
    }

    .mentari-school-view__class-chip {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        border: 1px solid rgb(167 243 208 / .95);
        border-radius: .5rem;
        background: rgb(236 253 245 / .98);
        padding: .42rem .58rem;
        color: rgb(5 95 70);
        font-size: .75rem;
        font-weight: 800;
    }

    .mentari-school-view__class-chip--inactive {
        border-color: rgb(203 213 225 / .96);
        background: rgb(248 250 252 / .98);
        color: rgb(71 85 105);
    }

    .mentari-school-view__class-chip .fi-icon {
        width: .9rem;
        height: .9rem;
    }

    .mentari-school-view__empty {
        margin-top: .8rem;
        border: 1px dashed rgb(148 163 184 / .8);
        border-radius: .5rem;
        background: rgb(248 250 252 / .85);
        padding: .8rem;
        color: rgb(100 116 139);
        font-size: .8rem;
    }

    .mentari-school-view__timeline {
        display: grid;
        gap: .68rem;
        margin-top: .8rem;
    }

    .mentari-school-view__timeline-item {
        display: grid;
        grid-template-columns: 1.75rem minmax(0, 1fr);
        gap: .6rem;
        align-items: start;
    }

    .mentari-school-view__timeline-icon {
        display: grid;
        width: 1.75rem;
        height: 1.75rem;
        place-items: center;
        border-radius: .5rem;
        background: rgb(254 243 199);
        color: rgb(217 119 6);
    }

    .mentari-school-view__timeline-icon .fi-icon {
        width: .95rem;
        height: .95rem;
    }

    .mentari-school-view__timeline-label {
        color: rgb(51 65 85);
        font-size: .75rem;
        font-weight: 800;
    }

    .mentari-school-view__timeline-value {
        margin-top: .08rem;
        color: rgb(15 23 42);
        font-size: .82rem;
        font-weight: 650;
        line-height: 1.35;
    }

    .dark .mentari-school-view__hero,
    .dark .mentari-school-view__stat,
    .dark .mentari-school-view__panel {
        border-color: rgb(148 163 184 / .18);
        background: rgb(15 23 42 / .92);
    }

    .dark .mentari-school-view__title,
    .dark .mentari-school-view__stat-value,
    .dark .mentari-school-view__panel-heading,
    .dark .mentari-school-view__timeline-value {
        color: rgb(248 250 252);
    }

    .dark .mentari-school-view__address,
    .dark .mentari-school-view__stat-description,
    .dark .mentari-school-view__panel-copy {
        color: rgb(203 213 225);
    }

    @media (max-width: 920px) {
        .mentari-school-view__stats,
        .mentari-school-view__grid {
            grid-template-columns: 1fr 1fr;
        }

        .mentari-school-view__panel--classes {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 640px) {
        .mentari-school-view__hero-inner {
            display: grid;
        }

        .mentari-school-view__badges {
            justify-content: flex-start;
        }

        .mentari-school-view__stats,
        .mentari-school-view__grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="mentari-school-view">
    <section class="mentari-school-view__hero">
        <div class="mentari-school-view__hero-inner">
            <div class="mentari-school-view__identity">
                <div class="mentari-school-view__avatar">
                    <x-filament::icon icon="heroicon-o-building-office-2" />
                </div>

                <div>
                    <div class="mentari-school-view__eyebrow">Profil sekolah</div>
                    <h3 class="mentari-school-view__title">{{ $record->name }}</h3>
                    <div class="mentari-school-view__address">
                        <x-filament::icon icon="heroicon-o-map-pin" />
                        <span>{{ $record->address ?: 'Alamat belum diisi.' }}</span>
                    </div>
                </div>
            </div>

            <div class="mentari-school-view__badges">
                <div class="mentari-school-view__code">
                    <x-filament::icon icon="heroicon-o-key" />
                    <span>{{ $record->code ?: 'Kode dibuat otomatis' }}</span>
                </div>

                <div @class([
                    'mentari-school-view__status',
                    'mentari-school-view__status--deleted' => $record->trashed(),
                    'mentari-school-view__status--active' => ! $record->trashed(),
                ])>
                    <x-filament::icon :icon="$record->trashed() ? 'heroicon-o-trash' : 'heroicon-o-check-circle'" />
                    <span>{{ $record->trashed() ? 'Terhapus' : 'Aktif' }}</span>
                </div>
            </div>
        </div>
    </section>

    <section class="mentari-school-view__stats" aria-label="Statistik sekolah">
        @foreach ($stats as $stat)
            <article class="mentari-school-view__stat mentari-school-view__stat--{{ $stat['tone'] }}">
                <div class="mentari-school-view__stat-icon">
                    <x-filament::icon :icon="$stat['icon']" />
                </div>

                <div>
                    <div class="mentari-school-view__stat-value">{{ number_format($stat['value'], 0, ',', '.') }}</div>
                    <div class="mentari-school-view__stat-label">{{ $stat['label'] }}</div>
                    <div class="mentari-school-view__stat-description">{{ $stat['description'] }}</div>
                </div>
            </article>
        @endforeach
    </section>

    <section class="mentari-school-view__grid">
        <article class="mentari-school-view__panel mentari-school-view__panel--classes">
            <div class="mentari-school-view__panel-heading">
                <x-filament::icon icon="heroicon-o-academic-cap" />
                <span>Daftar kelas</span>
            </div>
            <p class="mentari-school-view__panel-copy">
                {{ $activeClassrooms->count() }} kelas aktif dari {{ $classrooms->count() }} kelas yang tersimpan.
            </p>

            @if ($classrooms->isNotEmpty())
                <div class="mentari-school-view__class-list">
                    @foreach ($activeClassrooms as $classroom)
                        <span class="mentari-school-view__class-chip">
                            <x-filament::icon icon="heroicon-o-check-circle" />
                            {{ $classroom->name }}
                        </span>
                    @endforeach

                    @foreach ($inactiveClassrooms as $classroom)
                        <span class="mentari-school-view__class-chip mentari-school-view__class-chip--inactive">
                            <x-filament::icon icon="heroicon-o-pause-circle" />
                            {{ $classroom->name }}
                        </span>
                    @endforeach
                </div>
            @else
                <div class="mentari-school-view__empty">
                    Belum ada kelas. Gunakan tombol Kelola kelas untuk menambahkan kelas sekolah ini.
                </div>
            @endif
        </article>

        <article class="mentari-school-view__panel mentari-school-view__panel--history">
            <div class="mentari-school-view__panel-heading">
                <x-filament::icon icon="heroicon-o-clock" />
                <span>Riwayat data</span>
            </div>
            <p class="mentari-school-view__panel-copy">Waktu pembuatan dan pembaruan terakhir data sekolah.</p>

            <div class="mentari-school-view__timeline">
                <div class="mentari-school-view__timeline-item">
                    <div class="mentari-school-view__timeline-icon">
                        <x-filament::icon icon="heroicon-o-plus-circle" />
                    </div>
                    <div>
                        <div class="mentari-school-view__timeline-label">Dibuat</div>
                        <div class="mentari-school-view__timeline-value">{{ $createdAt }}</div>
                    </div>
                </div>

                <div class="mentari-school-view__timeline-item">
                    <div class="mentari-school-view__timeline-icon">
                        <x-filament::icon icon="heroicon-o-arrow-path" />
                    </div>
                    <div>
                        <div class="mentari-school-view__timeline-label">Terakhir diperbarui</div>
                        <div class="mentari-school-view__timeline-value">{{ $updatedAt }}</div>
                    </div>
                </div>

                @if ($record->trashed())
                    <div class="mentari-school-view__timeline-item">
                        <div class="mentari-school-view__timeline-icon">
                            <x-filament::icon icon="heroicon-o-trash" />
                        </div>
                        <div>
                            <div class="mentari-school-view__timeline-label">Dihapus</div>
                            <div class="mentari-school-view__timeline-value">{{ $deletedAt }}</div>
                        </div>
                    </div>
                @endif
            </div>
        </article>
    </section>
</div>
