<x-filament-panels::page>
    @php
        $currentStep = $this->getCurrentStep();
        $schools = $currentStep === 1 ? $this->getSchools() : collect();
        $classrooms = $currentStep === 2 ? $this->getClassroomOptions() : collect();

        if ($currentStep === 3) {
            $summary = $this->getScopeSummary();
            $combinedAnalysis = $this->getCombinedAnalysis();
            $schoolOverview = $this->getSchoolOverview();
            $classroomCharts = $this->getClassroomCharts();
            $studentCharts = $this->getStudentCharts();
            $streakChart = $studentCharts->sortByDesc('streak_days')->values();
            $severityDistribution = $this->getSeverityDistribution();
            $severityMax = max(1, $severityDistribution
                ->flatMap(fn (array $row): array => [$row['depression'], $row['anxiety'], $row['stress']])
                ->max() ?? 1);
        }
    @endphp

    <style>
        .analysis-flow {
            --analysis-rose: 236 72 153;
            --analysis-sky: 14 165 233;
            --analysis-emerald: 16 185 129;
            --analysis-amber: 245 158 11;
            --analysis-violet: 139 92 246;
            --analysis-teal: 20 184 166;
            display: grid;
            gap: 1rem;
        }

        .analysis-flow *,
        .analysis-flow *::before,
        .analysis-flow *::after {
            box-sizing: border-box;
        }

        .analysis-steps,
        .analysis-choice-grid,
        .analysis-stat-grid,
        .analysis-dashboard-grid,
        .analysis-class-grid {
            display: grid;
            gap: .75rem;
        }

        .analysis-steps {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .analysis-step,
        .analysis-choice,
        .analysis-stat,
        .analysis-panel,
        .analysis-class-panel,
        .analysis-student-row,
        .analysis-command {
            border: 1px solid rgb(226 232 240 / .9);
            border-radius: .5rem;
            background: rgb(255 255 255 / .96);
            box-shadow: 0 10px 28px rgb(15 23 42 / .06);
        }

        .dark .analysis-step,
        .dark .analysis-choice,
        .dark .analysis-stat,
        .dark .analysis-panel,
        .dark .analysis-class-panel,
        .dark .analysis-student-row,
        .dark .analysis-command {
            border-color: rgb(148 163 184 / .16);
            background: rgb(15 23 42 / .9);
            box-shadow: 0 12px 30px rgb(2 6 23 / .24);
        }

        .analysis-step {
            display: flex;
            min-width: 0;
            align-items: center;
            gap: .75rem;
            padding: .8rem .9rem;
            color: rgb(100 116 139);
            transition: border-color 160ms ease, box-shadow 160ms ease, transform 160ms ease;
        }

        .analysis-step.is-current {
            border-color: rgb(var(--analysis-sky) / .48);
            color: rgb(3 105 161);
            box-shadow:
                0 12px 26px rgb(var(--analysis-sky) / .1),
                inset 3px 0 0 rgb(var(--analysis-sky));
        }

        .analysis-step.is-complete {
            border-color: rgb(var(--analysis-emerald) / .4);
            color: rgb(4 120 87);
            box-shadow:
                0 12px 26px rgb(var(--analysis-emerald) / .08),
                inset 3px 0 0 rgb(var(--analysis-emerald));
        }

        .dark .analysis-step.is-current {
            color: rgb(125 211 252);
        }

        .dark .analysis-step.is-complete {
            color: rgb(110 231 183);
        }

        .analysis-step-number {
            display: grid;
            width: 2rem;
            height: 2rem;
            flex: 0 0 2rem;
            place-items: center;
            border-radius: .5rem;
            background: rgb(241 245 249);
            color: inherit;
            font-size: .82rem;
            font-weight: 850;
        }

        .dark .analysis-step-number {
            background: rgb(30 41 59);
        }

        .analysis-step-copy,
        .analysis-choice-copy,
        .analysis-student-copy {
            min-width: 0;
        }

        .analysis-step-title,
        .analysis-step-value {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .analysis-step-title {
            color: rgb(15 23 42);
            font-size: .84rem;
            font-weight: 850;
        }

        .dark .analysis-step-title {
            color: rgb(248 250 252);
        }

        .analysis-step-value {
            margin-top: .1rem;
            font-size: .73rem;
            line-height: 1.25;
        }

        .analysis-stage {
            display: grid;
            gap: 1rem;
        }

        .analysis-stage-head,
        .analysis-section-heading {
            display: flex;
            min-width: 0;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
        }

        .analysis-section-heading {
            margin-bottom: .9rem;
        }

        .analysis-stage-head h2,
        .analysis-section-heading h2,
        .analysis-command-title {
            color: rgb(15 23 42);
            font-size: 1rem;
            font-weight: 850;
            line-height: 1.25;
        }

        .dark .analysis-stage-head h2,
        .dark .analysis-section-heading h2,
        .dark .analysis-command-title {
            color: rgb(248 250 252);
        }

        .analysis-stage-head p,
        .analysis-section-heading p,
        .analysis-command-copy {
            margin-top: .25rem;
            max-width: 48rem;
            color: rgb(100 116 139);
            font-size: .82rem;
            line-height: 1.5;
        }

        .dark .analysis-stage-head p,
        .dark .analysis-section-heading p,
        .dark .analysis-command-copy {
            color: rgb(148 163 184);
        }

        .analysis-eyebrow,
        .analysis-heading-count {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            border-radius: 999px;
            padding: .22rem .55rem;
            background: rgb(var(--analysis-rose) / .1);
            color: rgb(190 24 93);
            font-size: .68rem;
            font-weight: 850;
            line-height: 1;
            text-transform: uppercase;
        }

        .analysis-heading-count {
            flex: 0 0 auto;
            background: rgb(var(--analysis-sky) / .1);
            color: rgb(3 105 161);
            text-transform: none;
        }

        .dark .analysis-eyebrow {
            background: rgb(var(--analysis-rose) / .18);
            color: rgb(249 168 212);
        }

        .dark .analysis-heading-count {
            background: rgb(var(--analysis-sky) / .18);
            color: rgb(125 211 252);
        }

        .analysis-choice-grid {
            grid-template-columns: repeat(auto-fit, minmax(16rem, 1fr));
        }

        .analysis-choice {
            --analysis-tone: var(--analysis-sky);
            --analysis-tone-soft: 240 249 255;
            position: relative;
            display: flex;
            width: 100%;
            min-height: 7.4rem;
            align-items: stretch;
            justify-content: space-between;
            gap: .9rem;
            overflow: hidden;
            padding: .95rem;
            color: rgb(15 23 42);
            text-align: left;
            background:
                linear-gradient(135deg, rgb(var(--analysis-tone-soft) / .96), rgb(255 255 255 / .98) 62%);
            transition: border-color 160ms ease, box-shadow 160ms ease, transform 160ms ease;
        }

        .analysis-choice::before {
            position: absolute;
            inset: 0 auto 0 0;
            width: .28rem;
            background: rgb(var(--analysis-tone));
            content: "";
        }

        .analysis-choice:hover {
            border-color: rgb(var(--analysis-tone) / .48);
            box-shadow: 0 16px 34px rgb(var(--analysis-tone) / .13);
            transform: translateY(-2px);
        }

        .analysis-choice:focus-visible {
            outline: 2px solid rgb(var(--analysis-tone) / .44);
            outline-offset: 2px;
        }

        .dark .analysis-choice {
            color: rgb(248 250 252);
            background: linear-gradient(135deg, rgb(var(--analysis-tone) / .16), rgb(15 23 42 / .94) 56%);
        }

        .analysis-choice--emerald {
            --analysis-tone: var(--analysis-emerald);
            --analysis-tone-soft: 236 253 245;
        }

        .analysis-choice--sky {
            --analysis-tone: var(--analysis-sky);
            --analysis-tone-soft: 240 249 255;
        }

        .analysis-choice--indigo {
            --analysis-tone: 99 102 241;
            --analysis-tone-soft: 238 242 255;
        }

        .analysis-choice--amber {
            --analysis-tone: var(--analysis-amber);
            --analysis-tone-soft: 255 251 235;
        }

        .analysis-choice--fuchsia {
            --analysis-tone: var(--analysis-rose);
            --analysis-tone-soft: 253 242 248;
        }

        .analysis-choice-title {
            display: block;
            color: inherit;
            font-size: .98rem;
            font-weight: 850;
            line-height: 1.3;
        }

        .analysis-choice-description {
            display: block;
            margin-top: .4rem;
            color: rgb(100 116 139);
            font-size: .77rem;
            line-height: 1.35;
        }

        .dark .analysis-choice-description {
            color: rgb(148 163 184);
        }

        .analysis-choice-metrics {
            display: flex;
            flex-wrap: wrap;
            gap: .35rem;
            margin-top: .65rem;
        }

        .analysis-choice-metrics span,
        .analysis-selection-chip,
        .analysis-student-metric,
        .analysis-table-kicker {
            display: inline-flex;
            align-items: center;
            min-height: 1.5rem;
            border-radius: 999px;
            padding: .18rem .48rem;
            background: rgb(255 255 255 / .82);
            color: rgb(71 85 105);
            font-size: .7rem;
            font-weight: 750;
            line-height: 1.15;
            box-shadow: inset 0 0 0 1px rgb(226 232 240 / .82);
        }

        .dark .analysis-choice-metrics span,
        .dark .analysis-selection-chip,
        .dark .analysis-student-metric,
        .dark .analysis-table-kicker {
            background: rgb(15 23 42 / .72);
            color: rgb(203 213 225);
            box-shadow: inset 0 0 0 1px rgb(148 163 184 / .18);
        }

        .analysis-choice-icon {
            display: grid;
            width: 2.55rem;
            height: 2.55rem;
            flex: 0 0 2.55rem;
            place-items: center;
            align-self: flex-start;
            border-radius: .5rem;
            background: rgb(255 255 255 / .9);
            color: rgb(var(--analysis-tone));
            box-shadow:
                0 8px 16px rgb(var(--analysis-tone) / .12),
                inset 0 0 0 1px rgb(255 255 255 / .8);
        }

        .dark .analysis-choice-icon {
            background: rgb(30 41 59 / .88);
            box-shadow: inset 0 0 0 1px rgb(255 255 255 / .08);
        }

        .analysis-choice-icon svg,
        .analysis-step-number svg,
        .analysis-stat-icon svg,
        .analysis-command-icon svg {
            width: 1.1rem;
            height: 1.1rem;
        }

        .analysis-toolbar,
        .analysis-actions,
        .analysis-selection {
            display: flex;
            min-width: 0;
            flex-wrap: wrap;
            gap: .5rem;
        }

        .analysis-toolbar,
        .analysis-command {
            align-items: center;
            justify-content: space-between;
        }

        .analysis-command {
            display: flex;
            gap: .9rem;
            padding: .9rem;
            background:
                linear-gradient(115deg, rgb(255 255 255 / .98), rgb(253 242 248 / .72), rgb(240 249 255 / .82));
        }

        .dark .analysis-command {
            background: linear-gradient(115deg, rgb(15 23 42 / .94), rgb(131 24 67 / .18), rgb(12 74 110 / .2));
        }

        .analysis-command-main {
            display: flex;
            min-width: 0;
            align-items: center;
            gap: .75rem;
        }

        .analysis-command-icon,
        .analysis-stat-icon {
            display: grid;
            width: 2.35rem;
            height: 2.35rem;
            flex: 0 0 auto;
            place-items: center;
            border-radius: .5rem;
            background: rgb(var(--analysis-command-color, var(--analysis-rose)) / .12);
            color: rgb(var(--analysis-command-color, var(--analysis-rose)));
        }

        .analysis-selection {
            margin-top: .45rem;
        }

        .analysis-stat-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .analysis-stat {
            --analysis-stat-color: var(--analysis-sky);
            display: grid;
            min-height: 8rem;
            gap: .75rem;
            overflow: hidden;
            padding: .9rem;
            background:
                linear-gradient(150deg, rgb(255 255 255 / .98), rgb(var(--analysis-stat-color) / .08));
            box-shadow:
                0 10px 28px rgb(15 23 42 / .055),
                inset 3px 0 0 rgb(var(--analysis-stat-color));
        }

        .dark .analysis-stat {
            background: linear-gradient(150deg, rgb(15 23 42 / .94), rgb(var(--analysis-stat-color) / .14));
        }

        .analysis-stat--students {
            --analysis-stat-color: var(--analysis-rose);
        }

        .analysis-stat--streak {
            --analysis-stat-color: var(--analysis-amber);
        }

        .analysis-stat--mood {
            --analysis-stat-color: var(--analysis-emerald);
        }

        .analysis-stat--screening {
            --analysis-stat-color: var(--analysis-sky);
        }

        .analysis-stat-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .65rem;
        }

        .analysis-stat-icon {
            --analysis-command-color: var(--analysis-stat-color);
            width: 2rem;
            height: 2rem;
        }

        .analysis-stat-label {
            color: rgb(71 85 105);
            font-size: .72rem;
            font-weight: 850;
            line-height: 1.2;
            text-align: right;
            text-transform: uppercase;
        }

        .dark .analysis-stat-label {
            color: rgb(203 213 225);
        }

        .analysis-stat-value {
            color: rgb(var(--analysis-stat-color));
            font-size: 1.8rem;
            font-weight: 900;
            line-height: 1;
        }

        .analysis-stat-note {
            margin-top: .25rem;
            color: rgb(100 116 139);
            font-size: .76rem;
            line-height: 1.35;
        }

        .dark .analysis-stat-note {
            color: rgb(148 163 184);
        }

        .analysis-stat-meter,
        .analysis-bar-track,
        .analysis-severity-line {
            display: block;
            overflow: hidden;
            border-radius: 999px;
            background: rgb(226 232 240);
        }

        .dark .analysis-stat-meter,
        .dark .analysis-bar-track,
        .dark .analysis-severity-line {
            background: rgb(30 41 59);
        }

        .analysis-stat-meter {
            height: .42rem;
        }

        .analysis-stat-meter i,
        .analysis-bar-fill,
        .analysis-severity-line i {
            display: block;
            width: var(--analysis-width, 0%);
            height: 100%;
            border-radius: inherit;
            background: var(--analysis-color, rgb(var(--analysis-stat-color)));
        }

        .analysis-dashboard-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            align-items: stretch;
        }

        .analysis-panel {
            --analysis-panel-color: var(--analysis-sky);
            --analysis-panel-soft: 240 249 255;
            overflow: hidden;
            padding: .95rem;
            border-top: 3px solid rgb(var(--analysis-panel-color));
            background:
                linear-gradient(180deg, rgb(var(--analysis-panel-soft) / .72), rgb(255 255 255 / .98) 38%);
        }

        .dark .analysis-panel {
            background: linear-gradient(180deg, rgb(var(--analysis-panel-color) / .14), rgb(15 23 42 / .92) 38%);
        }

        .analysis-panel--rose {
            --analysis-panel-color: var(--analysis-rose);
            --analysis-panel-soft: 253 242 248;
        }

        .analysis-panel--sky {
            --analysis-panel-color: var(--analysis-sky);
            --analysis-panel-soft: 240 249 255;
        }

        .analysis-panel--emerald {
            --analysis-panel-color: var(--analysis-emerald);
            --analysis-panel-soft: 236 253 245;
        }

        .analysis-panel--amber {
            --analysis-panel-color: var(--analysis-amber);
            --analysis-panel-soft: 255 251 235;
        }

        .analysis-panel--violet {
            --analysis-panel-color: var(--analysis-violet);
            --analysis-panel-soft: 245 243 255;
        }

        .analysis-panel--teal {
            --analysis-panel-color: var(--analysis-teal);
            --analysis-panel-soft: 240 253 250;
        }

        .analysis-scroll-stack,
        .analysis-bar-list,
        .analysis-school-bars,
        .analysis-class-bars,
        .analysis-mini-bars,
        .analysis-severity-chart {
            display: grid;
            gap: .72rem;
        }

        .analysis-scroll-stack {
            max-height: 24rem;
            overflow: auto;
            padding-right: .15rem;
        }

        .analysis-bar-row {
            display: grid;
            gap: .35rem;
        }

        .analysis-bar-top {
            display: flex;
            min-width: 0;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            color: rgb(15 23 42);
            font-size: .8rem;
            font-weight: 780;
        }

        .dark .analysis-bar-top {
            color: rgb(248 250 252);
        }

        .analysis-bar-label {
            display: flex;
            min-width: 0;
            align-items: center;
            gap: .45rem;
        }

        .analysis-bar-label > span:last-child {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .analysis-bar-rank {
            display: grid;
            width: 1.45rem;
            height: 1.45rem;
            flex: 0 0 auto;
            place-items: center;
            border-radius: .45rem;
            background: rgb(255 255 255 / .86);
            color: rgb(71 85 105);
            font-size: .68rem;
            font-weight: 850;
            box-shadow: inset 0 0 0 1px rgb(226 232 240 / .9);
        }

        .dark .analysis-bar-rank {
            background: rgb(15 23 42 / .8);
            color: rgb(203 213 225);
            box-shadow: inset 0 0 0 1px rgb(148 163 184 / .18);
        }

        .analysis-bar-value {
            flex: 0 0 auto;
            color: rgb(100 116 139);
            font-size: .76rem;
            font-weight: 850;
        }

        .dark .analysis-bar-value {
            color: rgb(148 163 184);
        }

        .analysis-bar-track {
            height: .62rem;
        }

        .analysis-class-grid {
            grid-template-columns: repeat(auto-fit, minmax(16rem, 1fr));
        }

        .analysis-class-panel {
            --analysis-class-color: var(--analysis-sky);
            display: grid;
            gap: .82rem;
            padding: .9rem;
            background:
                linear-gradient(145deg, rgb(255 255 255 / .96), rgb(var(--analysis-class-color) / .07));
            box-shadow: inset 3px 0 0 rgb(var(--analysis-class-color) / .78), 0 8px 22px rgb(15 23 42 / .045);
        }

        .analysis-class-panel:nth-child(4n + 1) {
            --analysis-class-color: var(--analysis-sky);
        }

        .analysis-class-panel:nth-child(4n + 2) {
            --analysis-class-color: var(--analysis-emerald);
        }

        .analysis-class-panel:nth-child(4n + 3) {
            --analysis-class-color: var(--analysis-amber);
        }

        .analysis-class-panel:nth-child(4n + 4) {
            --analysis-class-color: var(--analysis-rose);
        }

        .analysis-class-panel.is-selected {
            border-color: rgb(var(--analysis-sky) / .56);
            box-shadow:
                inset 3px 0 0 rgb(var(--analysis-sky)),
                0 14px 30px rgb(var(--analysis-sky) / .12);
        }

        .dark .analysis-class-panel {
            background: linear-gradient(145deg, rgb(15 23 42 / .92), rgb(var(--analysis-class-color) / .12));
        }

        .analysis-class-title {
            display: flex;
            min-width: 0;
            align-items: center;
            justify-content: space-between;
            gap: .5rem;
            color: rgb(15 23 42);
            font-size: .9rem;
            font-weight: 850;
        }

        .analysis-class-title span:first-child {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .dark .analysis-class-title {
            color: rgb(248 250 252);
        }

        .analysis-class-meta {
            margin-top: .22rem;
            color: rgb(100 116 139);
            font-size: .75rem;
            line-height: 1.45;
        }

        .dark .analysis-class-meta {
            color: rgb(148 163 184);
        }

        .analysis-severity-row {
            display: grid;
            grid-template-columns: 7rem minmax(0, 1fr);
            gap: .75rem;
            align-items: center;
        }

        .analysis-severity-label {
            color: rgb(71 85 105);
            font-size: .78rem;
            font-weight: 850;
        }

        .dark .analysis-severity-label {
            color: rgb(203 213 225);
        }

        .analysis-severity-bars {
            display: grid;
            gap: .38rem;
        }

        .analysis-severity-track {
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .analysis-severity-track span:first-child {
            width: 5.1rem;
            color: rgb(100 116 139);
            font-size: .7rem;
            font-weight: 750;
        }

        .dark .analysis-severity-track span:first-child {
            color: rgb(148 163 184);
        }

        .analysis-severity-line {
            height: .5rem;
            flex: 1;
        }

        .analysis-severity {
            display: inline-flex;
            width: fit-content;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: .22rem .58rem;
            background: rgb(241 245 249);
            color: rgb(71 85 105);
            font-size: .72rem;
            font-weight: 850;
            line-height: 1.2;
            white-space: nowrap;
        }

        .analysis-severity--normal {
            background: rgb(220 252 231);
            color: rgb(22 101 52);
        }

        .analysis-severity--mild {
            background: rgb(224 242 254);
            color: rgb(3 105 161);
        }

        .analysis-severity--moderate {
            background: rgb(254 243 199);
            color: rgb(146 64 14);
        }

        .analysis-severity--severe,
        .analysis-severity--extremely_severe {
            background: rgb(254 226 226);
            color: rgb(153 27 27);
        }

        .dark .analysis-severity {
            background: rgb(30 41 59);
            color: rgb(203 213 225);
        }

        .dark .analysis-severity--normal {
            background: rgb(20 83 45 / .52);
            color: rgb(187 247 208);
        }

        .dark .analysis-severity--mild {
            background: rgb(12 74 110 / .56);
            color: rgb(186 230 253);
        }

        .dark .analysis-severity--moderate {
            background: rgb(120 53 15 / .58);
            color: rgb(253 230 138);
        }

        .dark .analysis-severity--severe,
        .dark .analysis-severity--extremely_severe {
            background: rgb(127 29 29 / .58);
            color: rgb(254 202 202);
        }

        .analysis-student-board {
            display: grid;
            gap: .65rem;
        }

        .analysis-student-board-head,
        .analysis-student-row {
            display: grid;
            grid-template-columns: minmax(12rem, 1.1fr) minmax(17rem, 2fr) minmax(9.5rem, .75fr);
            gap: .8rem;
            align-items: center;
        }

        .analysis-student-board-head {
            padding: 0 .4rem;
            color: rgb(100 116 139);
            font-size: .68rem;
            font-weight: 850;
            text-transform: uppercase;
        }

        .dark .analysis-student-board-head {
            color: rgb(148 163 184);
        }

        .analysis-student-list {
            display: grid;
            max-height: 42rem;
            gap: .55rem;
            overflow: auto;
            padding-right: .15rem;
        }

        .analysis-student-row {
            min-height: 7.9rem;
            padding: .82rem;
            background: rgb(255 255 255 / .88);
            box-shadow: 0 8px 22px rgb(15 23 42 / .045);
        }

        .analysis-student-identity {
            display: flex;
            min-width: 0;
            align-items: center;
            gap: .7rem;
        }

        .analysis-avatar {
            display: grid;
            width: 2.65rem;
            height: 2.65rem;
            flex: 0 0 auto;
            place-items: center;
            border-radius: .6rem;
            background: linear-gradient(135deg, rgb(var(--analysis-rose)), rgb(var(--analysis-sky)));
            color: white;
            font-size: .82rem;
            font-weight: 900;
            box-shadow: 0 10px 18px rgb(var(--analysis-rose) / .18);
        }

        .analysis-student-name {
            display: block;
            overflow: hidden;
            color: rgb(15 23 42);
            font-weight: 850;
            line-height: 1.3;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .dark .analysis-student-name {
            color: rgb(248 250 252);
        }

        .analysis-student-meta,
        .analysis-student-foot {
            color: rgb(100 116 139);
            font-size: .74rem;
            line-height: 1.4;
        }

        .analysis-student-meta {
            display: block;
            overflow: hidden;
            margin-top: .18rem;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .dark .analysis-student-meta,
        .dark .analysis-student-foot {
            color: rgb(148 163 184);
        }

        .analysis-student-metrics {
            display: flex;
            flex-wrap: wrap;
            gap: .35rem;
            margin-top: .5rem;
        }

        .analysis-student-metric strong {
            margin-right: .25rem;
            color: rgb(30 41 59);
            font-weight: 850;
        }

        .dark .analysis-student-metric strong {
            color: rgb(248 250 252);
        }

        .analysis-student-status {
            display: grid;
            justify-items: start;
            gap: .45rem;
        }

        .analysis-table-wrap {
            max-height: 36rem;
            overflow: auto;
            border: 1px solid rgb(226 232 240 / .88);
            border-radius: .5rem;
            background: rgb(255 255 255 / .82);
        }

        .dark .analysis-table-wrap {
            border-color: rgb(148 163 184 / .16);
            background: rgb(15 23 42 / .76);
        }

        .analysis-table {
            width: 100%;
            min-width: 58rem;
            border-collapse: collapse;
            font-size: .82rem;
        }

        .analysis-table thead th {
            position: sticky;
            z-index: 1;
            top: 0;
            background: rgb(248 250 252 / .98);
            color: rgb(71 85 105);
            font-size: .7rem;
            font-weight: 850;
            text-align: left;
            text-transform: uppercase;
        }

        .dark .analysis-table thead th {
            background: rgb(15 23 42 / .98);
            color: rgb(203 213 225);
        }

        .analysis-table th,
        .analysis-table td {
            border-bottom: 1px solid rgb(226 232 240 / .86);
            padding: .72rem;
            vertical-align: top;
        }

        .dark .analysis-table th,
        .dark .analysis-table td {
            border-color: rgb(148 163 184 / .14);
        }

        .analysis-table tbody tr:hover {
            background: rgb(240 249 255 / .58);
        }

        .dark .analysis-table tbody tr:hover {
            background: rgb(12 74 110 / .18);
        }

        .analysis-empty {
            border: 1px dashed rgb(203 213 225);
            border-radius: .5rem;
            padding: 2rem 1rem;
            color: rgb(100 116 139);
            text-align: center;
            background: rgb(255 255 255 / .58);
        }

        .dark .analysis-empty {
            border-color: rgb(71 85 105);
            color: rgb(148 163 184);
            background: rgb(15 23 42 / .55);
        }

        @media (max-width: 1180px) {
            .analysis-stat-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .analysis-student-board-head,
            .analysis-student-row {
                grid-template-columns: minmax(13rem, 1fr) minmax(16rem, 1.4fr);
            }

            .analysis-student-board-head span:last-child,
            .analysis-student-status {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 960px) {
            .analysis-steps,
            .analysis-dashboard-grid {
                grid-template-columns: 1fr;
            }

            .analysis-command,
            .analysis-toolbar,
            .analysis-stage-head,
            .analysis-section-heading {
                align-items: stretch;
                flex-direction: column;
            }
        }

        @media (max-width: 720px) {
            .analysis-stat-grid {
                grid-template-columns: 1fr;
            }

            .analysis-student-board-head {
                display: none;
            }

            .analysis-student-row {
                grid-template-columns: 1fr;
                min-height: 0;
            }

            .analysis-severity-row {
                grid-template-columns: 1fr;
            }

            .analysis-severity-track {
                align-items: flex-start;
                flex-direction: column;
                gap: .25rem;
            }

            .analysis-severity-track span:first-child {
                width: auto;
            }
        }

        @media (max-width: 520px) {
            .analysis-choice {
                min-height: 0;
                flex-direction: column-reverse;
            }

            .analysis-choice-icon {
                width: 2.3rem;
                height: 2.3rem;
            }

            .analysis-step-value,
            .analysis-student-meta {
                white-space: normal;
            }
        }
    </style>

    <div class="analysis-flow">
        <nav class="analysis-steps" aria-label="Tahapan melihat hasil analisis data">
            @foreach ([
                1 => ['title' => 'Pilih Sekolah', 'value' => $currentStep > 1 ? $this->getSelectedSchoolLabel() : 'Belum dipilih'],
                2 => ['title' => 'Pilih Kelas', 'value' => $currentStep === 3 ? $this->getSelectedClassroomLabel() : 'Belum dipilih'],
                3 => ['title' => 'Lihat Grafik', 'value' => $currentStep === 3 ? 'Hasil ditampilkan' : 'Menunggu pilihan'],
            ] as $step => $content)
                <div @class([
                    'analysis-step',
                    'is-current' => $currentStep === $step,
                    'is-complete' => $currentStep > $step,
                ])>
                    <span class="analysis-step-number">
                        @if ($currentStep > $step)
                            <x-filament::icon icon="heroicon-o-check" />
                        @else
                            {{ $step }}
                        @endif
                    </span>
                    <span class="analysis-step-copy">
                        <span class="analysis-step-title">{{ $content['title'] }}</span>
                        <span class="analysis-step-value">{{ $content['value'] }}</span>
                    </span>
                </div>
            @endforeach
        </nav>

        @if ($currentStep === 1)
            <section class="analysis-stage">
                <div class="analysis-stage-head">
                    <div>
                        <span class="analysis-eyebrow">Langkah 1</span>
                        <h2>1. Pilih Sekolah</h2>
                        <p>Pilih sekolah untuk melihat kelas, grafik persekolah, grafik perkelas, dan grafik persiswa.</p>
                    </div>
                    <span class="analysis-heading-count">{{ $schools->count() }} sekolah</span>
                </div>

                @if ($schools->isEmpty())
                    <div class="analysis-empty">Belum ada sekolah yang dapat dipilih.</div>
                @else
                    <div class="analysis-choice-grid">
                        @foreach ($schools as $school)
                            @php
                                $schoolTone = ['sky', 'emerald', 'indigo', 'amber', 'fuchsia'][$loop->index % 5];
                            @endphp

                            <button
                                type="button"
                                class="analysis-choice analysis-choice--{{ $schoolTone }}"
                                wire:click="selectSchool({{ $school['id'] }})"
                            >
                                <span class="analysis-choice-copy">
                                    <span class="analysis-choice-title">{{ $school['name'] }}</span>
                                    <span class="analysis-choice-description">{{ $school['screening_results_count'] }} hasil tes screening tersedia.</span>
                                    <span class="analysis-choice-metrics">
                                        <span>{{ $school['code'] }}</span>
                                        <span>{{ $school['students_count'] }} siswa</span>
                                        <span>{{ $school['classrooms_count'] }} kelas</span>
                                    </span>
                                </span>
                                <span class="analysis-choice-icon">
                                    <x-filament::icon icon="heroicon-o-building-office-2" />
                                </span>
                            </button>
                        @endforeach
                    </div>
                @endif
            </section>
        @elseif ($currentStep === 2)
            <section class="analysis-stage">
                <div class="analysis-toolbar analysis-stage-head">
                    <div>
                        <span class="analysis-eyebrow">Langkah 2</span>
                        <h2>2. Pilih Kelas</h2>
                        <p>{{ $this->getSelectedSchoolLabel() }} - pilih semua kelas untuk grafik persekolah, atau kelas tertentu untuk fokus perkelas dan persiswa.</p>
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
                    <div class="analysis-empty">Belum ada kelas pada sekolah ini.</div>
                @else
                    <div class="analysis-choice-grid">
                        @foreach ($classrooms as $classroom)
                            <button
                                type="button"
                                class="analysis-choice analysis-choice--{{ $classroom['tone'] }}"
                                wire:click="selectClassroom(@js($classroom['value']))"
                            >
                                <span class="analysis-choice-copy">
                                    <span class="analysis-choice-title">{{ $classroom['label'] }}</span>
                                    <span class="analysis-choice-description">{{ $classroom['description'] }}</span>
                                    <span class="analysis-choice-metrics">
                                        <span>{{ $classroom['students_count'] }} siswa</span>
                                        <span>{{ $classroom['screening_results_count'] }} screening</span>
                                        <span>{{ $classroom['mood_entries_count'] }} mood</span>
                                    </span>
                                </span>
                                <span class="analysis-choice-icon">
                                    <x-filament::icon :icon="$classroom['icon']" />
                                </span>
                            </button>
                        @endforeach
                    </div>
                @endif
            </section>
        @else
            <section class="analysis-command">
                <div class="analysis-command-main">
                    <span class="analysis-command-icon">
                        <x-filament::icon icon="heroicon-o-chart-bar-square" />
                    </span>
                    <div class="analysis-student-copy">
                        <span class="analysis-eyebrow">Scope aktif</span>
                        <div class="analysis-command-title">{{ $this->getSelectedSchoolLabel() }} - {{ $this->getSelectedClassroomLabel() }}</div>
                        <p class="analysis-command-copy">Ringkasan mood, screening, streak login, kelas, dan siswa pada data yang dipilih.</p>
                        <div class="analysis-selection">
                            <span class="analysis-selection-chip">{{ $summary['student_count'] }} siswa</span>
                            <span class="analysis-selection-chip">{{ $summary['screening_count'] }} screening</span>
                            <span class="analysis-selection-chip">{{ $summary['mood_entries'] }} mood</span>
                            <span class="analysis-selection-chip">{{ $summary['active_alerts'] }} alert aktif</span>
                        </div>
                    </div>
                </div>

                <div class="analysis-actions">
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
            </section>

            <div class="analysis-stat-grid">
                <div class="analysis-stat analysis-stat--students" style="--analysis-width: {{ $summary['student_count'] > 0 ? 100 : 0 }}%;">
                    <div class="analysis-stat-top">
                        <span class="analysis-stat-icon">
                            <x-filament::icon icon="heroicon-o-users" />
                        </span>
                        <span class="analysis-stat-label">Siswa</span>
                    </div>
                    <div>
                        <span class="analysis-stat-value">{{ $summary['student_count'] }}</span>
                        <p class="analysis-stat-note">{{ $summary['active_logins'] }} aktif login dalam 7 hari terakhir.</p>
                    </div>
                    <span class="analysis-stat-meter"><i></i></span>
                </div>

                <div class="analysis-stat analysis-stat--streak" style="--analysis-width: {{ $summary['average_streak_percent'] }}%;">
                    <div class="analysis-stat-top">
                        <span class="analysis-stat-icon">
                            <x-filament::icon icon="heroicon-o-fire" />
                        </span>
                        <span class="analysis-stat-label">Full Streak Login</span>
                    </div>
                    <div>
                        <span class="analysis-stat-value">{{ $summary['max_streak'] }} hari</span>
                        <p class="analysis-stat-note">Rata-rata {{ number_format($summary['average_streak'], 1) }} hari pada scope ini.</p>
                    </div>
                    <span class="analysis-stat-meter"><i></i></span>
                </div>

                <div class="analysis-stat analysis-stat--mood" style="--analysis-width: {{ $summary['mood_average_percent'] }}%;">
                    <div class="analysis-stat-top">
                        <span class="analysis-stat-icon">
                            <x-filament::icon icon="heroicon-o-face-smile" />
                        </span>
                        <span class="analysis-stat-label">Mood</span>
                    </div>
                    <div>
                        <span class="analysis-stat-value">{{ $summary['mood_average'] === null ? '-' : number_format($summary['mood_average'], 1) }}</span>
                        <p class="analysis-stat-note">{{ $summary['mood_entries'] }} check-in mood dari {{ $summary['mood_students'] }} siswa.</p>
                    </div>
                    <span class="analysis-stat-meter"><i></i></span>
                </div>

                <div class="analysis-stat analysis-stat--screening" style="--analysis-width: {{ $summary['screening_coverage'] }}%;">
                    <div class="analysis-stat-top">
                        <span class="analysis-stat-icon">
                            <x-filament::icon icon="heroicon-o-clipboard-document-check" />
                        </span>
                        <span class="analysis-stat-label">Tes Screening</span>
                    </div>
                    <div>
                        <span class="analysis-stat-value">{{ $summary['screening_coverage'] }}%</span>
                        <p class="analysis-stat-note">{{ $summary['screened_students'] }} siswa sudah punya hasil screening.</p>
                    </div>
                    <span class="analysis-stat-meter"><i></i></span>
                </div>
            </div>

            <div class="analysis-dashboard-grid">
                <section class="analysis-panel analysis-panel--amber">
                    <div class="analysis-section-heading">
                        <div>
                            <h2>Grafik Full Streak Login</h2>
                            <p>Seluruh siswa pada scope terpilih, diurutkan dari streak login tertinggi.</p>
                        </div>
                        <span class="analysis-heading-count">{{ $streakChart->count() }} siswa</span>
                    </div>

                    @if ($streakChart->isEmpty())
                        <div class="analysis-empty">Belum ada siswa pada scope ini.</div>
                    @else
                        <div class="analysis-scroll-stack">
                            @foreach ($streakChart as $student)
                                <div class="analysis-bar-row">
                                    <div class="analysis-bar-top">
                                        <span class="analysis-bar-label">
                                            <span class="analysis-bar-rank">{{ $loop->iteration }}</span>
                                            <span>{{ $student['name'] }}</span>
                                        </span>
                                        <span class="analysis-bar-value">{{ $student['streak_days'] }} hari</span>
                                    </div>
                                    <div class="analysis-bar-track">
                                        <span
                                            class="analysis-bar-fill"
                                            style="--analysis-width: {{ $student['streak_percent'] }}%; --analysis-color: #f59e0b;"
                                        ></span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>

                <section class="analysis-panel analysis-panel--teal">
                    <div class="analysis-section-heading">
                        <div>
                            <h2>Gabungan Analisis Mood dan Tes Screening</h2>
                            <p>Indikator gabungan mood harian, tes screening DASS-21, streak login, cakupan screening, dan alert aktif.</p>
                        </div>
                    </div>

                    <div class="analysis-bar-list">
                        @foreach ($combinedAnalysis as $item)
                            <div class="analysis-bar-row">
                                <div class="analysis-bar-top">
                                    <span class="analysis-bar-label">
                                        <span>{{ $item['label'] }}</span>
                                    </span>
                                    <span class="analysis-bar-value">{{ $item['value'] }}</span>
                                </div>
                                <div class="analysis-bar-track">
                                    <span
                                        class="analysis-bar-fill"
                                        style="--analysis-width: {{ $item['percent'] }}%; --analysis-color: {{ $item['color'] }};"
                                    ></span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>

            <div class="analysis-dashboard-grid">
                <section class="analysis-panel analysis-panel--sky">
                    <div class="analysis-section-heading">
                        <div>
                            <h2>Grafik Per Sekolah</h2>
                            <p>{{ $schoolOverview['name'] }} - ringkasan seluruh kelas di sekolah ini.</p>
                        </div>
                    </div>

                    <div class="analysis-school-bars">
                        @foreach ($schoolOverview['bars'] as $bar)
                            <div class="analysis-bar-row">
                                <div class="analysis-bar-top">
                                    <span class="analysis-bar-label">
                                        <span>{{ $bar['label'] }}</span>
                                    </span>
                                    <span class="analysis-bar-value">{{ $bar['value'] }}</span>
                                </div>
                                <div class="analysis-bar-track">
                                    <span
                                        class="analysis-bar-fill"
                                        style="--analysis-width: {{ $bar['percent'] }}%; --analysis-color: {{ $bar['color'] }};"
                                    ></span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="analysis-panel analysis-panel--rose">
                    <div class="analysis-section-heading">
                        <div>
                            <h2>Distribusi Tes Screening DASS-21</h2>
                            <p>Jumlah hasil depresi, kecemasan, dan stres pada setiap kategori keparahan.</p>
                        </div>
                    </div>

                    @if ($severityDistribution->isEmpty())
                        <div class="analysis-empty">Belum ada hasil tes screening pada scope ini.</div>
                    @else
                        <div class="analysis-severity-chart">
                            @foreach ($severityDistribution as $row)
                                <div class="analysis-severity-row">
                                    <div class="analysis-severity-label">{{ $row['label'] }}</div>
                                    <div class="analysis-severity-bars">
                                        @foreach ([
                                            ['label' => 'Depresi', 'value' => $row['depression'], 'color' => '#f43f5e'],
                                            ['label' => 'Cemas', 'value' => $row['anxiety'], 'color' => '#f59e0b'],
                                            ['label' => 'Stres', 'value' => $row['stress'], 'color' => '#0ea5e9'],
                                        ] as $bar)
                                            <div class="analysis-severity-track">
                                                <span>{{ $bar['label'] }}: {{ $bar['value'] }}</span>
                                                <span class="analysis-severity-line">
                                                    <i style="--analysis-width: {{ round(($bar['value'] / $severityMax) * 100) }}%; --analysis-color: {{ $bar['color'] }};"></i>
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>
            </div>

            <section class="analysis-stage">
                <div class="analysis-section-heading">
                    <div>
                        <h2>Grafik Per Kelas</h2>
                        <p>Perbandingan kelas pada sekolah terpilih. Kelas yang sedang dibuka diberi penanda.</p>
                    </div>
                    <span class="analysis-heading-count">{{ $classroomCharts->count() }} kelas</span>
                </div>

                @if ($classroomCharts->isEmpty())
                    <div class="analysis-empty">Belum ada kelas aktif pada sekolah ini.</div>
                @else
                    <div class="analysis-class-grid">
                        @foreach ($classroomCharts as $classroom)
                            <article @class(['analysis-class-panel', 'is-selected' => $classroom['is_selected']])>
                                <div>
                                    <div class="analysis-class-title">
                                        <span>{{ $classroom['name'] }}</span>
                                        @if ($classroom['is_selected'])
                                            <x-filament::badge color="info">Dibuka</x-filament::badge>
                                        @endif
                                    </div>
                                    <div class="analysis-class-meta">
                                        {{ $classroom['summary']['student_count'] }} siswa - {{ $classroom['summary']['screening_count'] }} screening - {{ $classroom['summary']['mood_entries'] }} mood
                                    </div>
                                </div>

                                <div class="analysis-class-bars">
                                    @foreach ($classroom['bars'] as $bar)
                                        <div class="analysis-bar-row">
                                            <div class="analysis-bar-top">
                                                <span class="analysis-bar-label">
                                                    <span>{{ $bar['label'] }}</span>
                                                </span>
                                                <span class="analysis-bar-value">{{ $bar['value'] }}</span>
                                            </div>
                                            <div class="analysis-bar-track">
                                                <span
                                                    class="analysis-bar-fill"
                                                    style="--analysis-width: {{ $bar['percent'] }}%; --analysis-color: {{ $bar['color'] }};"
                                                ></span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>

            <section class="analysis-panel analysis-panel--violet">
                <div class="analysis-section-heading">
                    <div>
                        <h2>Grafik Per Siswa</h2>
                        <p>Ringkasan setiap siswa: streak login, mood rata-rata, skor screening terbaru, dan alert aktif.</p>
                    </div>
                    <span class="analysis-heading-count">{{ $studentCharts->count() }} siswa</span>
                </div>

                @if ($studentCharts->isEmpty())
                    <div class="analysis-empty">Belum ada siswa pada scope ini.</div>
                @else
                    <div class="analysis-student-board">
                        <div class="analysis-student-board-head" aria-hidden="true">
                            <span>Siswa</span>
                            <span>Grafik ringkas</span>
                            <span>Status</span>
                        </div>

                        <div class="analysis-student-list">
                            @foreach ($studentCharts as $student)
                                <article class="analysis-student-row">
                                    <div class="analysis-student-identity">
                                        <span class="analysis-avatar">{{ $student['initials'] }}</span>
                                        <div class="analysis-student-copy">
                                            <span class="analysis-student-name">{{ $student['name'] }}</span>
                                            <span class="analysis-student-meta">{{ $student['classroom'] }} - {{ $student['email'] }}</span>
                                            <div class="analysis-student-metrics">
                                                <span class="analysis-student-metric"><strong>{{ $student['streak_days'] }}</strong>hari streak</span>
                                                <span class="analysis-student-metric"><strong>{{ $student['mood_count'] }}</strong>mood</span>
                                                <span class="analysis-student-metric"><strong>{{ $student['screening_count'] }}</strong>screening</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="analysis-mini-bars">
                                        @foreach ([
                                            ['label' => 'Streak login', 'value' => $student['streak_days'].' hari', 'percent' => $student['streak_percent'], 'color' => '#f59e0b'],
                                            ['label' => 'Mood rata-rata', 'value' => $student['mood_average'] === null ? 'Belum ada' : number_format($student['mood_average'], 1).'/5', 'percent' => $student['mood_percent'], 'color' => '#10b981'],
                                            ['label' => 'Skor screening', 'value' => $student['screening_total'] === null ? 'Belum ada' : $student['screening_total'].' poin', 'percent' => $student['screening_percent'], 'color' => '#0ea5e9'],
                                        ] as $bar)
                                            <div class="analysis-bar-row">
                                                <div class="analysis-bar-top">
                                                    <span class="analysis-bar-label">
                                                        <span>{{ $bar['label'] }}</span>
                                                    </span>
                                                    <span class="analysis-bar-value">{{ $bar['value'] }}</span>
                                                </div>
                                                <div class="analysis-bar-track">
                                                    <span
                                                        class="analysis-bar-fill"
                                                        style="--analysis-width: {{ $bar['percent'] }}%; --analysis-color: {{ $bar['color'] }};"
                                                    ></span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="analysis-student-status">
                                        <span @class(['analysis-severity', 'analysis-severity--'.$student['severity_key'] => filled($student['severity_key'])])>
                                            {{ $student['severity_label'] }}
                                        </span>
                                        <span class="analysis-student-foot">Mood: {{ $student['latest_mood'] }}</span>
                                        <span class="analysis-student-foot">Screening: {{ $student['latest_screening_at'] }}</span>
                                        <span class="analysis-student-foot">Alert aktif: {{ $student['active_alerts'] }}</span>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endif
            </section>

            <section class="analysis-panel analysis-panel--emerald">
                <div class="analysis-section-heading">
                    <div>
                        <h2>Data Detail Siswa</h2>
                        <p>Tabel ringkas untuk mengecek data numerik di balik grafik persiswa.</p>
                    </div>
                    <span class="analysis-heading-count">{{ $studentCharts->count() }} baris</span>
                </div>

                @if ($studentCharts->isEmpty())
                    <div class="analysis-empty">Belum ada data siswa untuk ditampilkan.</div>
                @else
                    <div class="analysis-table-wrap">
                        <table class="analysis-table">
                            <thead>
                                <tr>
                                    <th>Siswa</th>
                                    <th>Kelas</th>
                                    <th>Streak</th>
                                    <th>Mood</th>
                                    <th>Energi/Stres</th>
                                    <th>Screening</th>
                                    <th>Risiko</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($studentCharts as $student)
                                    <tr>
                                        <td>
                                            <strong>{{ $student['name'] }}</strong><br>
                                            <span class="analysis-student-meta">{{ $student['email'] }}</span>
                                        </td>
                                        <td>{{ $student['classroom'] }}</td>
                                        <td>
                                            <span class="analysis-table-kicker">{{ $student['streak_days'] }} hari</span><br>
                                            <span class="analysis-student-meta">{{ $student['last_activity'] }}</span>
                                        </td>
                                        <td>
                                            {{ $student['mood_average'] === null ? '-' : number_format($student['mood_average'], 1).'/5' }}<br>
                                            <span class="analysis-student-meta">{{ $student['mood_count'] }} check-in</span>
                                        </td>
                                        <td>
                                            Energi {{ $student['energy_average'] === null ? '-' : number_format($student['energy_average'], 1) }}<br>
                                            Stres {{ $student['stress_average'] === null ? '-' : number_format($student['stress_average'], 1) }}
                                        </td>
                                        <td>
                                            {{ $student['screening_total'] === null ? '-' : $student['screening_total'].' poin' }}<br>
                                            <span class="analysis-student-meta">{{ $student['screening_count'] }} hasil</span>
                                        </td>
                                        <td>
                                            <span @class(['analysis-severity', 'analysis-severity--'.$student['severity_key'] => filled($student['severity_key'])])>
                                                {{ $student['severity_label'] }}
                                            </span>
                                            <br>
                                            <span class="analysis-student-meta">{{ $student['active_alerts'] }} alert aktif</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        @endif
    </div>
</x-filament-panels::page>
