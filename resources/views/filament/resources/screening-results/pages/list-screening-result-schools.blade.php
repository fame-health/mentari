<x-filament-panels::page>
    @php
        $currentStep = $this->getCurrentStep();
        $schools = $currentStep === 1 ? $this->getSchools() : collect();
        $classrooms = $currentStep === 2 ? $this->getClassroomOptions() : collect();
    @endphp

    <style>
        .screening-flow {
            display: grid;
            gap: 1.25rem;
        }

        .screening-flow-steps,
        .screening-flow-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .75rem;
        }

        .screening-flow-step,
        .screening-flow-choice {
            border: 1px solid rgb(226 232 240);
            border-radius: .5rem;
            background: rgb(255 255 255);
            box-shadow: 0 8px 20px rgb(15 23 42 / .055);
        }

        .dark .screening-flow-step,
        .dark .screening-flow-choice {
            border-color: rgb(51 65 85);
            background: rgb(15 23 42);
        }

        .screening-flow-step {
            display: flex;
            min-width: 0;
            align-items: center;
            gap: .75rem;
            padding: .85rem 1rem;
            color: rgb(100 116 139);
        }

        .screening-flow-step.is-current {
            border-color: rgb(14 165 233);
            color: rgb(3 105 161);
            box-shadow: 0 0 0 1px rgb(14 165 233 / .15);
        }

        .screening-flow-step.is-complete {
            border-color: rgb(52 211 153);
            color: rgb(4 120 87);
        }

        .dark .screening-flow-step.is-current {
            border-color: rgb(56 189 248);
            color: rgb(125 211 252);
        }

        .dark .screening-flow-step.is-complete {
            border-color: rgb(16 185 129);
            color: rgb(110 231 183);
        }

        .screening-flow-step-number {
            display: grid;
            width: 2rem;
            height: 2rem;
            flex: 0 0 2rem;
            place-items: center;
            border-radius: 999px;
            background: rgb(241 245 249);
            font-size: .82rem;
            font-weight: 800;
        }

        .dark .screening-flow-step-number {
            background: rgb(30 41 59);
        }

        .screening-flow-step-copy {
            min-width: 0;
        }

        .screening-flow-step-title,
        .screening-flow-step-value {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .screening-flow-step-title {
            color: rgb(15 23 42);
            font-size: .86rem;
            font-weight: 800;
        }

        .dark .screening-flow-step-title {
            color: rgb(248 250 252);
        }

        .screening-flow-step-value {
            margin-top: .1rem;
            font-size: .74rem;
        }

        .screening-flow-heading {
            margin-bottom: 1rem;
        }

        .screening-flow-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
        }

        .screening-flow-heading h2 {
            color: rgb(15 23 42);
            font-size: 1rem;
            font-weight: 800;
        }

        .dark .screening-flow-heading h2 {
            color: rgb(248 250 252);
        }

        .screening-flow-heading p {
            margin-top: .25rem;
            color: rgb(100 116 139);
            font-size: .86rem;
        }

        .dark .screening-flow-heading p {
            color: rgb(148 163 184);
        }

        .screening-flow-choice {
            --screening-flow-accent: rgb(14 165 233);
            display: flex;
            position: relative;
            width: 100%;
            min-height: 6.5rem;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            overflow: hidden;
            padding: 1rem;
            color: rgb(15 23 42);
            text-align: left;
            transition: border-color 160ms ease, box-shadow 160ms ease, transform 160ms ease;
        }

        .screening-flow-choice::before {
            position: absolute;
            inset: 0 auto 0 0;
            width: .35rem;
            background: var(--screening-flow-accent);
            content: "";
        }

        .screening-flow-choice:hover {
            border-color: var(--screening-flow-accent);
            box-shadow: 0 16px 30px rgb(15 23 42 / .12);
            transform: translateY(-2px);
        }

        .screening-flow-choice:focus-visible {
            outline: 2px solid var(--screening-flow-accent);
            outline-offset: 2px;
        }

        .dark .screening-flow-choice {
            color: rgb(248 250 252);
        }

        .screening-flow-choice--emerald {
            --screening-flow-accent: rgb(16 185 129);
            background: linear-gradient(135deg, rgb(236 253 245), rgb(255 255 255) 64%);
        }

        .screening-flow-choice--sky {
            --screening-flow-accent: rgb(14 165 233);
            background: linear-gradient(135deg, rgb(240 249 255), rgb(255 255 255) 64%);
        }

        .screening-flow-choice--indigo {
            --screening-flow-accent: rgb(99 102 241);
            background: linear-gradient(135deg, rgb(238 242 255), rgb(255 255 255) 64%);
        }

        .screening-flow-choice--amber {
            --screening-flow-accent: rgb(245 158 11);
            background: linear-gradient(135deg, rgb(255 251 235), rgb(255 255 255) 64%);
        }

        .screening-flow-choice--fuchsia {
            --screening-flow-accent: rgb(217 70 239);
            background: linear-gradient(135deg, rgb(253 244 255), rgb(255 255 255) 64%);
        }

        .dark .screening-flow-choice--emerald,
        .dark .screening-flow-choice--sky,
        .dark .screening-flow-choice--indigo,
        .dark .screening-flow-choice--amber,
        .dark .screening-flow-choice--fuchsia {
            background: rgb(15 23 42);
        }

        .screening-flow-choice-copy {
            min-width: 0;
        }

        .screening-flow-choice-title {
            display: block;
            font-size: .96rem;
            font-weight: 800;
            line-height: 1.35;
        }

        .screening-flow-choice-meta,
        .screening-flow-choice-description {
            display: block;
            color: rgb(100 116 139);
            font-size: .78rem;
            line-height: 1.35;
        }

        .screening-flow-choice-meta {
            margin-top: .35rem;
            font-weight: 700;
        }

        .screening-flow-choice-description {
            margin-top: .15rem;
        }

        .dark .screening-flow-choice-meta,
        .dark .screening-flow-choice-description {
            color: rgb(148 163 184);
        }

        .screening-flow-choice-icon {
            display: grid;
            width: 2.35rem;
            height: 2.35rem;
            flex: 0 0 2.35rem;
            place-items: center;
            border-radius: .5rem;
            background: rgb(255 255 255 / .78);
            color: var(--screening-flow-accent);
            box-shadow: inset 0 0 0 1px rgb(255 255 255 / .84);
        }

        .dark .screening-flow-choice-icon {
            background: rgb(30 41 59);
            box-shadow: inset 0 0 0 1px rgb(255 255 255 / .08);
        }

        .screening-flow-choice-icon svg,
        .screening-flow-step-number svg {
            width: 1.1rem;
            height: 1.1rem;
        }

        .screening-flow-selection {
            display: flex;
            min-width: 0;
            flex-wrap: wrap;
            gap: .5rem;
        }

        .screening-flow-empty {
            border: 1px dashed rgb(203 213 225);
            border-radius: .5rem;
            padding: 2rem 1rem;
            color: rgb(100 116 139);
            text-align: center;
        }

        .dark .screening-flow-empty {
            border-color: rgb(71 85 105);
            color: rgb(148 163 184);
        }

        @media (max-width: 960px) {
            .screening-flow-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .screening-flow-steps,
            .screening-flow-grid {
                grid-template-columns: 1fr;
            }

            .screening-flow-step {
                padding: .75rem;
            }

            .screening-flow-step-value {
                white-space: normal;
            }
        }
    </style>

    <div class="screening-flow">
        <nav class="screening-flow-steps" aria-label="Tahapan melihat hasil screening">
            @foreach ([
                1 => ['title' => 'Pilih Sekolah', 'value' => $currentStep > 1 ? $this->getSelectedSchoolLabel() : 'Belum dipilih'],
                2 => ['title' => 'Pilih Kelas', 'value' => $currentStep === 3 ? $this->getSelectedClassroomLabel() : 'Belum dipilih'],
                3 => ['title' => 'Data Screening', 'value' => $currentStep === 3 ? 'Hasil ditampilkan' : 'Menunggu pilihan'],
            ] as $step => $content)
                <div @class([
                    'screening-flow-step',
                    'is-current' => $currentStep === $step,
                    'is-complete' => $currentStep > $step,
                ])>
                    <span class="screening-flow-step-number">
                        @if ($currentStep > $step)
                            <x-filament::icon icon="heroicon-o-check" />
                        @else
                            {{ $step }}
                        @endif
                    </span>
                    <span class="screening-flow-step-copy">
                        <span class="screening-flow-step-title">{{ $content['title'] }}</span>
                        <span class="screening-flow-step-value">{{ $content['value'] }}</span>
                    </span>
                </div>
            @endforeach
        </nav>

        @if ($currentStep === 1)
            <x-filament::section>
                <div class="screening-flow-heading">
                    <h2>1. Pilih Sekolah</h2>
                    <p>Pilih sekolah yang ingin dilihat hasil screening siswanya.</p>
                </div>

                @if ($schools->isEmpty())
                    <div class="screening-flow-empty">Belum ada sekolah yang dapat dipilih.</div>
                @else
                    <div class="screening-flow-grid">
                        @foreach ($schools as $school)
                            @php
                                $schoolTone = ['sky', 'emerald', 'indigo', 'amber', 'fuchsia'][$loop->index % 5];
                            @endphp

                            <button
                                type="button"
                                class="screening-flow-choice screening-flow-choice--{{ $schoolTone }}"
                                wire:click="selectSchool({{ $school['id'] }})"
                            >
                                <span class="screening-flow-choice-copy">
                                    <span class="screening-flow-choice-title">{{ $school['name'] }}</span>
                                    <span class="screening-flow-choice-meta">
                                        {{ $school['code'] }} - {{ $school['students_count'] }} siswa
                                    </span>
                                    <span class="screening-flow-choice-description">
                                        {{ $school['screening_results_count'] }} hasil screening
                                    </span>
                                </span>
                                <span class="screening-flow-choice-icon">
                                    <x-filament::icon icon="heroicon-o-building-office-2" />
                                </span>
                            </button>
                        @endforeach
                    </div>
                @endif
            </x-filament::section>
        @elseif ($currentStep === 2)
            <x-filament::section>
                <div class="screening-flow-toolbar screening-flow-heading">
                    <div>
                        <h2>2. Pilih Kelas</h2>
                        <p>{{ $this->getSelectedSchoolLabel() }} - pilih semua kelas atau kelas tertentu.</p>
                    </div>
                    <x-filament::button
                        color="gray"
                        icon="heroicon-o-arrow-left"
                        wire:click="backToSchools"
                    >
                        Ganti sekolah
                    </x-filament::button>
                </div>

                @if ($classrooms->isEmpty())
                    <div class="screening-flow-empty">Belum ada kelas pada sekolah ini.</div>
                @else
                    <div class="screening-flow-grid">
                        @foreach ($classrooms as $classroom)
                            <button
                                type="button"
                                class="screening-flow-choice screening-flow-choice--{{ $classroom['tone'] }}"
                                wire:click="selectClassroom(@js($classroom['value']))"
                            >
                                <span class="screening-flow-choice-copy">
                                    <span class="screening-flow-choice-title">{{ $classroom['label'] }}</span>
                                    <span class="screening-flow-choice-meta">
                                        {{ $classroom['students_count'] }} siswa - {{ $classroom['screening_results_count'] }} hasil screening
                                    </span>
                                    <span class="screening-flow-choice-description">{{ $classroom['description'] }}</span>
                                </span>
                                <span class="screening-flow-choice-icon">
                                    <x-filament::icon :icon="$classroom['icon']" />
                                </span>
                            </button>
                        @endforeach
                    </div>
                @endif
            </x-filament::section>
        @else
            <div class="screening-flow-toolbar">
                <div class="screening-flow-selection">
                    <x-filament::badge color="info" icon="heroicon-o-building-office-2">
                        {{ $this->getSelectedSchoolLabel() }}
                    </x-filament::badge>
                    <x-filament::badge color="success" icon="heroicon-o-academic-cap">
                        {{ $this->getSelectedClassroomLabel() }}
                    </x-filament::badge>
                </div>

                <div class="screening-flow-selection">
                    <x-filament::button
                        tag="a"
                        :href="$this->getAnalysisUrl()"
                        color="primary"
                        size="sm"
                        icon="heroicon-o-chart-bar-square"
                    >
                        Buka diagram analisis
                    </x-filament::button>
                    <x-filament::button
                        color="gray"
                        size="sm"
                        icon="heroicon-o-arrow-left"
                        wire:click="backToClassrooms"
                    >
                        Ganti kelas
                    </x-filament::button>
                    <x-filament::button
                        color="gray"
                        size="sm"
                        icon="heroicon-o-building-office-2"
                        wire:click="backToSchools"
                    >
                        Ganti sekolah
                    </x-filament::button>
                </div>
            </div>

            {{ $this->table }}
        @endif
    </div>
</x-filament-panels::page>
