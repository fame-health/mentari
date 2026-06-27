<x-filament-panels::page>
    @php
        $currentStep = $this->getCurrentStep();
        $categories = $currentStep === 1 ? $this->getCategories() : collect();
        $selectedCategoryLabel = $this->getSelectedCategoryLabel();
    @endphp

    <style>
        .recommendation-flow {
            display: grid;
            gap: 1.25rem;
        }

        .recommendation-flow-steps {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .75rem;
        }

        .recommendation-flow-step {
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

        .dark .recommendation-flow-step {
            border-color: rgb(55 65 81);
            background: rgb(17 24 39);
            color: rgb(156 163 175);
        }

        .recommendation-flow-step.is-current {
            border-color: rgb(14 165 233);
            color: rgb(3 105 161);
            box-shadow: 0 0 0 1px rgb(14 165 233 / .14);
        }

        .dark .recommendation-flow-step.is-current {
            border-color: rgb(56 189 248);
            color: rgb(125 211 252);
        }

        .recommendation-flow-step.is-complete {
            border-color: rgb(134 239 172);
            color: rgb(21 128 61);
        }

        .dark .recommendation-flow-step.is-complete {
            border-color: rgb(22 101 52);
            color: rgb(134 239 172);
        }

        .recommendation-flow-step-number {
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

        .dark .recommendation-flow-step-number {
            background: rgb(31 41 55);
        }

        .recommendation-flow-step-number svg {
            width: 1rem;
            height: 1rem;
        }

        .recommendation-flow-step-copy {
            min-width: 0;
        }

        .recommendation-flow-step-title {
            display: block;
            overflow: hidden;
            font-size: .875rem;
            font-weight: 700;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .recommendation-flow-step-value {
            display: block;
            overflow: hidden;
            margin-top: .125rem;
            font-size: .75rem;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .recommendation-flow-heading {
            margin-bottom: 1rem;
        }

        .recommendation-flow-heading h2 {
            color: rgb(17 24 39);
            font-size: 1rem;
            font-weight: 700;
        }

        .dark .recommendation-flow-heading h2 {
            color: rgb(243 244 246);
        }

        .recommendation-flow-heading p {
            margin-top: .25rem;
            color: rgb(107 114 128);
            font-size: .875rem;
        }

        .dark .recommendation-flow-heading p {
            color: rgb(156 163 175);
        }

        .recommendation-flow-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .75rem;
        }

        .recommendation-flow-choice {
            display: flex;
            position: relative;
            width: 100%;
            min-height: 6.5rem;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem;
            overflow: hidden;
            border: 1px solid var(--recommendation-border, rgb(229 231 235));
            border-radius: .5rem;
            background: var(--recommendation-surface, rgb(255 255 255));
            color: rgb(17 24 39);
            text-align: left;
            box-shadow: 0 1px 2px rgb(15 23 42 / .05);
            transition: border-color 150ms ease, box-shadow 150ms ease, transform 150ms ease;
        }

        .recommendation-flow-choice::before {
            position: absolute;
            inset: 0 auto 0 0;
            width: .35rem;
            background: var(--recommendation-accent, rgb(14 165 233));
            content: "";
        }

        .recommendation-flow-choice:hover {
            border-color: var(--recommendation-accent, rgb(14 165 233));
            box-shadow: 0 10px 24px rgb(15 23 42 / .12);
            transform: translateY(-2px);
        }

        .recommendation-flow-choice:focus-visible {
            outline: 2px solid rgb(14 165 233);
            outline-offset: 2px;
        }

        .dark .recommendation-flow-choice {
            border-color: var(--recommendation-border-dark, rgb(55 65 81));
            background: var(--recommendation-surface-dark, rgb(17 24 39));
            color: rgb(243 244 246);
        }

        .recommendation-flow-choice--counseling-script {
            --recommendation-accent: rgb(14 165 233);
            --recommendation-border: rgb(186 230 253);
            --recommendation-border-dark: rgb(12 74 110);
            --recommendation-soft: rgb(224 242 254);
            --recommendation-surface: linear-gradient(135deg, rgb(240 249 255), rgb(255 255 255) 62%);
            --recommendation-surface-dark: linear-gradient(135deg, rgb(12 74 110 / .45), rgb(17 24 39) 62%);
        }

        .recommendation-flow-choice--dashboard-analysis {
            --recommendation-accent: rgb(217 70 239);
            --recommendation-border: rgb(245 208 254);
            --recommendation-border-dark: rgb(112 26 117);
            --recommendation-soft: rgb(250 232 255);
            --recommendation-surface: linear-gradient(135deg, rgb(253 244 255), rgb(255 255 255) 62%);
            --recommendation-surface-dark: linear-gradient(135deg, rgb(112 26 117 / .35), rgb(17 24 39) 62%);
        }

        .recommendation-flow-choice--relaxation {
            --recommendation-accent: rgb(16 185 129);
            --recommendation-border: rgb(167 243 208);
            --recommendation-border-dark: rgb(6 95 70);
            --recommendation-soft: rgb(209 250 229);
            --recommendation-surface: linear-gradient(135deg, rgb(236 253 245), rgb(255 255 255) 62%);
            --recommendation-surface-dark: linear-gradient(135deg, rgb(6 95 70 / .35), rgb(17 24 39) 62%);
        }

        .recommendation-flow-choice--reflection {
            --recommendation-accent: rgb(245 158 11);
            --recommendation-border: rgb(253 230 138);
            --recommendation-border-dark: rgb(146 64 14);
            --recommendation-soft: rgb(254 243 199);
            --recommendation-surface: linear-gradient(135deg, rgb(255 251 235), rgb(255 255 255) 62%);
            --recommendation-surface-dark: linear-gradient(135deg, rgb(146 64 14 / .35), rgb(17 24 39) 62%);
        }

        .recommendation-flow-choice--activity {
            --recommendation-accent: rgb(99 102 241);
            --recommendation-border: rgb(199 210 254);
            --recommendation-border-dark: rgb(67 56 202);
            --recommendation-soft: rgb(238 242 255);
            --recommendation-surface: linear-gradient(135deg, rgb(238 242 255), rgb(255 255 255) 62%);
            --recommendation-surface-dark: linear-gradient(135deg, rgb(67 56 202 / .35), rgb(17 24 39) 62%);
        }

        .recommendation-flow-choice-copy {
            min-width: 0;
        }

        .recommendation-flow-choice-title {
            display: block;
            font-size: .95rem;
            font-weight: 800;
            line-height: 1.4;
        }

        .recommendation-flow-choice-meta {
            display: block;
            margin-top: .375rem;
            color: rgb(107 114 128);
            font-size: .8rem;
        }

        .dark .recommendation-flow-choice-meta {
            color: rgb(156 163 175);
        }

        .recommendation-flow-choice-icon {
            display: grid;
            width: 2.25rem;
            height: 2.25rem;
            flex: 0 0 2.25rem;
            place-items: center;
            border-radius: .5rem;
            background: var(--recommendation-soft, rgb(240 249 255));
            color: var(--recommendation-accent, rgb(2 132 199));
            box-shadow: inset 0 0 0 1px rgb(255 255 255 / .7);
        }

        .dark .recommendation-flow-choice-icon {
            background: rgb(15 23 42 / .65);
            color: var(--recommendation-accent, rgb(125 211 252));
            box-shadow: inset 0 0 0 1px rgb(255 255 255 / .08);
        }

        .recommendation-flow-choice-icon svg {
            width: 1.2rem;
            height: 1.2rem;
        }

        .recommendation-flow-toolbar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
        }

        .recommendation-flow-selection {
            display: flex;
            min-width: 0;
            flex-wrap: wrap;
            gap: .5rem;
        }

        @media (max-width: 900px) {
            .recommendation-flow-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .recommendation-flow-steps,
            .recommendation-flow-grid {
                grid-template-columns: 1fr;
            }

            .recommendation-flow-step {
                padding: .75rem;
            }

            .recommendation-flow-step-value {
                white-space: normal;
            }
        }
    </style>

    <div class="recommendation-flow">
        <nav class="recommendation-flow-steps" aria-label="Tahapan memilih rekomendasi">
            @foreach ([
                1 => ['title' => 'Pilih Jenis Rekomendasi', 'value' => $currentStep > 1 ? $selectedCategoryLabel : 'Belum dipilih'],
                2 => ['title' => 'Daftar Rekomendasi', 'value' => $currentStep === 2 ? $this->getSelectedCategoryCount().' data' : 'Menunggu pilihan'],
            ] as $step => $content)
                <div @class([
                    'recommendation-flow-step',
                    'is-current' => $currentStep === $step,
                    'is-complete' => $currentStep > $step,
                ])>
                    <span class="recommendation-flow-step-number">
                        @if ($currentStep > $step)
                            <x-filament::icon icon="heroicon-o-check" />
                        @else
                            {{ $step }}
                        @endif
                    </span>
                    <span class="recommendation-flow-step-copy">
                        <span class="recommendation-flow-step-title">{{ $content['title'] }}</span>
                        <span class="recommendation-flow-step-value">{{ $content['value'] }}</span>
                    </span>
                </div>
            @endforeach
        </nav>

        @if ($currentStep === 1)
            <x-filament::section>
                <div class="recommendation-flow-heading">
                    <h2>1. Pilih Jenis Rekomendasi</h2>
                    <p>Pilih satu jenis untuk membuka daftar rekomendasi yang sesuai.</p>
                </div>

                <div class="recommendation-flow-grid">
                    @foreach ($categories as $category)
                        <button
                            type="button"
                            class="recommendation-flow-choice recommendation-flow-choice--{{ $category['tone'] }}"
                            wire:click="selectCategory(@js($category['value']))"
                        >
                            <span class="recommendation-flow-choice-copy">
                                <span class="recommendation-flow-choice-title">{{ $category['label'] }}</span>
                                <span class="recommendation-flow-choice-meta">
                                    {{ $category['count'] }} data · {{ $category['active_count'] }} aktif
                                </span>
                            </span>
                            <span class="recommendation-flow-choice-icon">
                                <x-filament::icon :icon="$category['icon']" />
                            </span>
                        </button>
                    @endforeach
                </div>
            </x-filament::section>
        @else
            <div class="recommendation-flow-toolbar">
                <div class="recommendation-flow-selection">
                    <x-filament::badge color="info" icon="heroicon-o-light-bulb">
                        {{ $selectedCategoryLabel }}
                    </x-filament::badge>
                    <x-filament::badge color="gray">
                        {{ $this->getSelectedCategoryCount() }} data
                    </x-filament::badge>
                </div>
                <x-filament::button
                    color="gray"
                    size="sm"
                    icon="heroicon-o-arrow-left"
                    wire:click="backToCategories"
                >
                    Ganti jenis
                </x-filament::button>
            </div>

            {{ $this->table }}
        @endif
    </div>
</x-filament-panels::page>
