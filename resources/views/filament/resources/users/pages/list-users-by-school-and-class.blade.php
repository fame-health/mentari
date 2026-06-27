<x-filament-panels::page>
    @php
        $currentStep = $this->getCurrentStep();
        $schools = $currentStep === 1 ? $this->getSchools() : collect();
        $levels = $currentStep === 2 ? $this->getLevels() : collect();
    @endphp

    <style>
        .user-flow {
            display: grid;
            gap: 1.25rem;
        }

        .user-flow-steps {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .75rem;
        }

        .user-flow-step {
            display: flex;
            min-width: 0;
            align-items: center;
            gap: .75rem;
            padding: .875rem 1rem;
            border: 1px solid rgb(229 231 235);
            border-radius: .5rem;
            background: rgb(255 255 255);
            color: rgb(107 114 128);
        }

        .dark .user-flow-step {
            border-color: rgb(55 65 81);
            background: rgb(17 24 39);
            color: rgb(156 163 175);
        }

        .user-flow-step.is-current {
            border-color: rgb(14 165 233);
            color: rgb(3 105 161);
            box-shadow: 0 0 0 1px rgb(14 165 233 / .14);
        }

        .dark .user-flow-step.is-current {
            border-color: rgb(56 189 248);
            color: rgb(125 211 252);
        }

        .user-flow-step.is-complete {
            border-color: rgb(134 239 172);
            color: rgb(21 128 61);
        }

        .dark .user-flow-step.is-complete {
            border-color: rgb(22 101 52);
            color: rgb(134 239 172);
        }

        .user-flow-step-number {
            display: grid;
            width: 2rem;
            height: 2rem;
            flex: 0 0 2rem;
            place-items: center;
            border-radius: 9999px;
            background: rgb(243 244 246);
            color: currentColor;
            font-size: .875rem;
            font-weight: 700;
        }

        .user-flow-step-number svg {
            width: 1rem;
            height: 1rem;
        }

        .dark .user-flow-step-number {
            background: rgb(31 41 55);
        }

        .user-flow-step-copy {
            min-width: 0;
        }

        .user-flow-step-title {
            display: block;
            overflow: hidden;
            font-size: .875rem;
            font-weight: 700;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .user-flow-step-value {
            display: block;
            overflow: hidden;
            margin-top: .125rem;
            font-size: .75rem;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .user-flow-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .75rem;
        }

        .user-flow-choice {
            display: flex;
            position: relative;
            width: 100%;
            min-height: 6.25rem;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem;
            overflow: hidden;
            border: 1px solid var(--user-flow-border, rgb(229 231 235));
            border-radius: .5rem;
            background: var(--user-flow-surface, rgb(255 255 255));
            color: rgb(17 24 39);
            text-align: left;
            box-shadow: 0 1px 2px rgb(15 23 42 / .05);
            transition: border-color 150ms ease, box-shadow 150ms ease, transform 150ms ease;
        }

        .user-flow-choice::before {
            position: absolute;
            inset: 0 auto 0 0;
            width: .35rem;
            background: var(--user-flow-accent, rgb(14 165 233));
            content: "";
        }

        .user-flow-choice:hover {
            border-color: var(--user-flow-accent, rgb(14 165 233));
            box-shadow: 0 10px 24px rgb(15 23 42 / .12);
            transform: translateY(-2px);
        }

        .user-flow-choice:focus-visible {
            outline: 2px solid rgb(14 165 233);
            outline-offset: 2px;
        }

        .dark .user-flow-choice {
            border-color: var(--user-flow-border-dark, rgb(55 65 81));
            background: var(--user-flow-surface-dark, rgb(17 24 39));
            color: rgb(243 244 246);
        }

        .user-flow-choice--sky {
            --user-flow-accent: rgb(14 165 233);
            --user-flow-border: rgb(186 230 253);
            --user-flow-border-dark: rgb(12 74 110);
            --user-flow-soft: rgb(224 242 254);
            --user-flow-surface: linear-gradient(135deg, rgb(240 249 255), rgb(255 255 255) 62%);
            --user-flow-surface-dark: linear-gradient(135deg, rgb(12 74 110 / .45), rgb(17 24 39) 62%);
        }

        .user-flow-choice--emerald {
            --user-flow-accent: rgb(16 185 129);
            --user-flow-border: rgb(167 243 208);
            --user-flow-border-dark: rgb(6 95 70);
            --user-flow-soft: rgb(209 250 229);
            --user-flow-surface: linear-gradient(135deg, rgb(236 253 245), rgb(255 255 255) 62%);
            --user-flow-surface-dark: linear-gradient(135deg, rgb(6 95 70 / .35), rgb(17 24 39) 62%);
        }

        .user-flow-choice--fuchsia {
            --user-flow-accent: rgb(217 70 239);
            --user-flow-border: rgb(245 208 254);
            --user-flow-border-dark: rgb(112 26 117);
            --user-flow-soft: rgb(250 232 255);
            --user-flow-surface: linear-gradient(135deg, rgb(253 244 255), rgb(255 255 255) 62%);
            --user-flow-surface-dark: linear-gradient(135deg, rgb(112 26 117 / .35), rgb(17 24 39) 62%);
        }

        .user-flow-choice--amber {
            --user-flow-accent: rgb(245 158 11);
            --user-flow-border: rgb(253 230 138);
            --user-flow-border-dark: rgb(146 64 14);
            --user-flow-soft: rgb(254 243 199);
            --user-flow-surface: linear-gradient(135deg, rgb(255 251 235), rgb(255 255 255) 62%);
            --user-flow-surface-dark: linear-gradient(135deg, rgb(146 64 14 / .35), rgb(17 24 39) 62%);
        }

        .user-flow-choice--indigo {
            --user-flow-accent: rgb(99 102 241);
            --user-flow-border: rgb(199 210 254);
            --user-flow-border-dark: rgb(67 56 202);
            --user-flow-soft: rgb(238 242 255);
            --user-flow-surface: linear-gradient(135deg, rgb(238 242 255), rgb(255 255 255) 62%);
            --user-flow-surface-dark: linear-gradient(135deg, rgb(67 56 202 / .35), rgb(17 24 39) 62%);
        }

        .user-flow-choice--slate {
            --user-flow-accent: rgb(100 116 139);
            --user-flow-border: rgb(203 213 225);
            --user-flow-border-dark: rgb(71 85 105);
            --user-flow-soft: rgb(241 245 249);
            --user-flow-surface: linear-gradient(135deg, rgb(248 250 252), rgb(255 255 255) 62%);
            --user-flow-surface-dark: linear-gradient(135deg, rgb(71 85 105 / .35), rgb(17 24 39) 62%);
        }

        .user-flow-choice-copy {
            min-width: 0;
        }

        .user-flow-choice-title {
            display: block;
            font-size: .95rem;
            font-weight: 800;
            line-height: 1.4;
        }

        .user-flow-choice-meta {
            display: block;
            margin-top: .375rem;
            color: rgb(107 114 128);
            font-size: .8rem;
        }

        .dark .user-flow-choice-meta {
            color: rgb(156 163 175);
        }

        .user-flow-choice-icon {
            display: grid;
            width: 2.25rem;
            height: 2.25rem;
            flex: 0 0 2.25rem;
            place-items: center;
            border-radius: .5rem;
            background: var(--user-flow-soft, rgb(240 249 255));
            color: var(--user-flow-accent, rgb(2 132 199));
            box-shadow: inset 0 0 0 1px rgb(255 255 255 / .7);
        }

        .dark .user-flow-choice-icon {
            background: rgb(15 23 42 / .65);
            color: var(--user-flow-accent, rgb(125 211 252));
            box-shadow: inset 0 0 0 1px rgb(255 255 255 / .08);
        }

        .user-flow-choice-icon svg {
            width: 1.2rem;
            height: 1.2rem;
        }

        .user-flow-heading {
            margin-bottom: 1rem;
        }

        .user-flow-heading h2 {
            color: rgb(17 24 39);
            font-size: 1rem;
            font-weight: 700;
        }

        .dark .user-flow-heading h2 {
            color: rgb(243 244 246);
        }

        .user-flow-heading p {
            margin-top: .25rem;
            color: rgb(107 114 128);
            font-size: .875rem;
        }

        .dark .user-flow-heading p {
            color: rgb(156 163 175);
        }

        .user-flow-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
        }

        .user-flow-selection {
            display: flex;
            min-width: 0;
            flex-wrap: wrap;
            gap: .5rem;
        }

        .user-flow-empty {
            padding: 2.5rem 1rem;
            border: 1px dashed rgb(209 213 219);
            border-radius: .5rem;
            color: rgb(107 114 128);
            text-align: center;
        }

        .dark .user-flow-empty {
            border-color: rgb(75 85 99);
            color: rgb(156 163 175);
        }

        @media (max-width: 900px) {
            .user-flow-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .user-flow-steps,
            .user-flow-grid {
                grid-template-columns: 1fr;
            }

            .user-flow-step {
                padding: .75rem;
            }

            .user-flow-step-value {
                white-space: normal;
            }
        }
    </style>

    <div class="user-flow">
        <nav class="user-flow-steps" aria-label="Tahapan memilih pengguna">
            @foreach ([
                1 => ['title' => 'Pilih Sekolah', 'value' => $currentStep > 1 ? $this->getSelectedSchoolLabel() : 'Belum dipilih'],
                2 => ['title' => 'Pilih Kelas', 'value' => $currentStep >= 3 ? $this->getSelectedLevelLabel() : 'Belum dipilih'],
                3 => ['title' => 'Daftar Pengguna', 'value' => $currentStep === 3 ? 'Hasil ditampilkan' : 'Menunggu pilihan'],
            ] as $step => $content)
                <div @class([
                    'user-flow-step',
                    'is-current' => $currentStep === $step,
                    'is-complete' => $currentStep > $step,
                ])>
                    <span class="user-flow-step-number">
                        @if ($currentStep > $step)
                            <x-filament::icon icon="heroicon-o-check" />
                        @else
                            {{ $step }}
                        @endif
                    </span>
                    <span class="user-flow-step-copy">
                        <span class="user-flow-step-title">{{ $content['title'] }}</span>
                        <span class="user-flow-step-value">{{ $content['value'] }}</span>
                    </span>
                </div>
            @endforeach
        </nav>

        @if ($currentStep === 1)
            <x-filament::section>
                <div class="user-flow-heading">
                    <h2>1. Pilih Sekolah</h2>
                    <p>Pilih sekolah untuk melihat kelas yang tersedia.</p>
                </div>

                @if ($schools->isEmpty())
                    <div class="user-flow-empty">Belum ada sekolah yang dapat dipilih.</div>
                @else
                    <div class="user-flow-grid">
                        @foreach ($schools as $school)
                            @php
                                $schoolTone = $school['id'] === 0
                                    ? 'slate'
                                    : ['sky', 'emerald', 'fuchsia', 'amber', 'indigo'][$loop->index % 5];
                            @endphp

                            <button
                                type="button"
                                class="user-flow-choice user-flow-choice--{{ $schoolTone }}"
                                wire:click="selectSchool({{ $school['id'] }})"
                            >
                                <span class="user-flow-choice-copy">
                                    <span class="user-flow-choice-title">{{ $school['name'] }}</span>
                                    <span class="user-flow-choice-meta">
                                        {{ $school['code'] }} · {{ $school['users_count'] }} pengguna
                                    </span>
                                </span>
                                <span class="user-flow-choice-icon">
                                    <x-filament::icon icon="heroicon-o-building-office-2" />
                                </span>
                            </button>
                        @endforeach
                    </div>
                @endif
            </x-filament::section>
        @elseif ($currentStep === 2)
            <x-filament::section>
                <div class="user-flow-toolbar user-flow-heading">
                    <div>
                        <h2>2. Pilih Kelas</h2>
                        <p>{{ $this->getSelectedSchoolLabel() }} · pilih kelas untuk membuka daftar pengguna.</p>
                    </div>
                    <x-filament::button
                        color="gray"
                        icon="heroicon-o-arrow-left"
                        wire:click="backToSchools"
                    >
                        Ganti sekolah
                    </x-filament::button>
                </div>

                @if ($levels->isEmpty())
                    <div class="user-flow-empty">Belum ada pengguna atau kelas pada sekolah ini.</div>
                @else
                    <div class="user-flow-grid">
                        @foreach ($levels as $level)
                            @php
                                $levelTone = $level['value'] === '__empty__'
                                    ? 'slate'
                                    : ['emerald', 'sky', 'indigo', 'amber', 'fuchsia'][$loop->index % 5];
                            @endphp

                            <button
                                type="button"
                                class="user-flow-choice user-flow-choice--{{ $levelTone }}"
                                wire:click="selectLevel(@js($level['value']))"
                            >
                                <span class="user-flow-choice-copy">
                                    <span class="user-flow-choice-title">{{ $level['label'] }}</span>
                                    <span class="user-flow-choice-meta">{{ $level['count'] }} pengguna</span>
                                </span>
                                <span class="user-flow-choice-icon">
                                    <x-filament::icon icon="heroicon-o-academic-cap" />
                                </span>
                            </button>
                        @endforeach
                    </div>
                @endif
            </x-filament::section>
        @else
            <div class="user-flow-toolbar">
                <div class="user-flow-selection">
                    <x-filament::badge color="info" icon="heroicon-o-building-office-2">
                        {{ $this->getSelectedSchoolLabel() }}
                    </x-filament::badge>
                    <x-filament::badge color="success" icon="heroicon-o-academic-cap">
                        {{ $this->getSelectedLevelLabel() }}
                    </x-filament::badge>
                </div>
                <div class="user-flow-selection">
                    <x-filament::button
                        color="gray"
                        size="sm"
                        icon="heroicon-o-arrow-left"
                        wire:click="backToLevels"
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
