<x-filament-panels::page>
    <style>
        .mentari-school-wizard {
            width: 100%;
        }

        .mentari-school-wizard .fi-sc-wizard-header {
            overflow: hidden;
            border: 1px solid rgb(226 232 240 / .9);
            border-radius: .75rem;
            background:
                linear-gradient(135deg, rgb(240 249 255 / .95), rgb(255 255 255));
            box-shadow: 0 10px 26px rgb(15 23 42 / .07);
        }

        .mentari-school-wizard .fi-sc-wizard-header-step.fi-active {
            background:
                linear-gradient(135deg, rgb(14 165 233 / .14), rgb(255 255 255 / .6));
        }

        .mentari-school-wizard .fi-sc-wizard-header-step-btn {
            min-height: 4.75rem;
        }

        .mentari-school-wizard .fi-sc-wizard-header-step-label {
            font-weight: 800;
        }

        .mentari-school-form-section {
            overflow: hidden;
            border-color: rgb(226 232 240 / .95);
            background: rgb(255 255 255 / .96);
            box-shadow: 0 10px 26px rgb(15 23 42 / .06);
        }

        .mentari-school-form-section--identity {
            border-top: 3px solid rgb(14 165 233);
            background:
                linear-gradient(180deg, rgb(240 249 255 / .8), rgb(255 255 255 / .97) 38%);
        }

        .mentari-school-form-section--classrooms {
            border-top: 3px solid rgb(16 185 129);
            background:
                linear-gradient(180deg, rgb(236 253 245 / .78), rgb(255 255 255 / .97) 38%);
        }

        .mentari-school-form-section .fi-section-header-heading {
            font-size: .98rem;
            font-weight: 800;
        }

        .mentari-school-form-section .fi-section-header-description {
            font-size: .78rem;
            line-height: 1.4;
        }

        .mentari-school-form-section .fi-fo-repeater-item {
            border-color: rgb(203 213 225 / .88);
            background: rgb(255 255 255 / .9);
        }

        .mentari-school-wizard .fi-sc-wizard-footer {
            position: sticky;
            z-index: 20;
            bottom: .75rem;
            padding: .7rem;
            border: 1px solid rgb(226 232 240 / .88);
            border-radius: .75rem;
            background: rgb(255 255 255 / .9);
            box-shadow: 0 14px 28px rgb(15 23 42 / .12);
            backdrop-filter: blur(14px);
        }

        .mentari-school-wizard .fi-sc-wizard-footer .fi-btn {
            min-width: 7.25rem;
            justify-content: center;
        }

        .dark .mentari-school-wizard .fi-sc-wizard-header,
        .dark .mentari-school-form-section,
        .dark .mentari-school-form-section--identity,
        .dark .mentari-school-form-section--classrooms,
        .dark .mentari-school-form-section .fi-fo-repeater-item,
        .dark .mentari-school-wizard .fi-sc-wizard-footer {
            border-color: rgb(148 163 184 / .18);
            background: rgb(15 23 42 / .92);
        }

        @media (max-width: 640px) {
            .mentari-school-wizard .fi-sc-wizard-header-step-btn {
                min-height: auto;
            }

            .mentari-school-wizard .fi-sc-wizard-footer {
                bottom: .5rem;
            }
        }
    </style>

    {{ $this->table }}
</x-filament-panels::page>
