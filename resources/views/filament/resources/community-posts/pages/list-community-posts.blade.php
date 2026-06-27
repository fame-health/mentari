<x-filament-panels::page>
    <style>
        .community-chat-table .fi-ta-table {
            border-spacing: 0 .55rem;
            border-collapse: separate;
        }

        .community-chat-table .fi-ta-row {
            background: transparent;
            box-shadow: none;
        }

        .community-chat-table .fi-ta-cell {
            border: 0;
            padding-block: .15rem;
        }

        .community-chat-scroll {
            max-height: calc(100vh - 15rem);
            min-height: 24rem;
            overflow-y: auto;
            padding-right: .35rem;
        }

        .community-chat-scroll::-webkit-scrollbar {
            width: .55rem;
        }

        .community-chat-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .community-chat-scroll::-webkit-scrollbar-thumb {
            border-radius: 9999px;
            background: rgb(203 213 225);
        }

        .dark .community-chat-scroll::-webkit-scrollbar-thumb {
            background: rgb(71 85 105);
        }

        .community-chat-card {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr);
            gap: .7rem;
            width: 100%;
        }

        .community-chat-avatar {
            display: grid;
            width: 2.35rem;
            height: 2.35rem;
            place-items: center;
            border-radius: 9999px;
            background: linear-gradient(135deg, rgb(14 165 233), rgb(217 70 239));
            color: rgb(255 255 255);
            font-size: .78rem;
            font-weight: 800;
            box-shadow: 0 8px 18px rgb(14 165 233 / .2);
        }

        .community-chat-bubble {
            position: relative;
            min-width: 0;
            padding: .75rem .85rem;
            border: 1px solid rgb(186 230 253);
            border-radius: .5rem;
            background: linear-gradient(135deg, rgb(240 249 255), rgb(255 255 255) 62%);
            box-shadow: 0 8px 24px rgb(15 23 42 / .08);
        }

        .community-chat-bubble::before {
            position: absolute;
            top: .9rem;
            left: -.45rem;
            width: .9rem;
            height: .9rem;
            border-bottom: 1px solid rgb(186 230 253);
            border-left: 1px solid rgb(186 230 253);
            background: rgb(240 249 255);
            content: "";
            transform: rotate(45deg);
        }

        .community-chat-bubble.is-pinned {
            border-color: rgb(253 230 138);
            background: linear-gradient(135deg, rgb(255 251 235), rgb(255 255 255) 62%);
        }

        .community-chat-bubble.is-pinned::before {
            border-color: rgb(253 230 138);
            background: rgb(255 251 235);
        }

        .community-chat-bubble.is-deleted {
            border-color: rgb(203 213 225);
            background: linear-gradient(135deg, rgb(248 250 252), rgb(255 255 255) 62%);
            opacity: .72;
        }

        .community-chat-header,
        .community-chat-footer {
            display: flex;
            min-width: 0;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: .5rem;
        }

        .community-chat-author {
            min-width: 0;
        }

        .community-chat-name {
            display: block;
            overflow: hidden;
            color: rgb(15 23 42);
            font-size: .84rem;
            font-weight: 800;
            line-height: 1.35;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .community-chat-meta {
            display: block;
            margin-top: .125rem;
            color: rgb(100 116 139);
            font-size: .7rem;
        }

        .community-chat-content {
            margin-block: .65rem;
            color: rgb(30 41 59);
            font-size: .86rem;
            line-height: 1.55;
            white-space: pre-line;
        }

        .community-chat-badges {
            display: flex;
            flex-wrap: wrap;
            gap: .375rem;
        }

        .community-chat-pill {
            display: inline-flex;
            align-items: center;
            gap: .25rem;
            padding: .25rem .5rem;
            border-radius: 9999px;
            background: rgb(224 242 254);
            color: rgb(3 105 161);
            font-size: .68rem;
            font-weight: 700;
        }

        .community-chat-pill--pin {
            background: rgb(254 243 199);
            color: rgb(146 64 14);
        }

        .community-chat-pill--deleted {
            background: rgb(241 245 249);
            color: rgb(71 85 105);
        }

        .community-chat-like {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            color: rgb(217 70 239);
            font-size: .74rem;
            font-weight: 800;
        }

        .community-chat-like svg,
        .community-chat-pill svg {
            width: .95rem;
            height: .95rem;
        }

        .dark .community-chat-bubble {
            border-color: rgb(12 74 110);
            background: linear-gradient(135deg, rgb(12 74 110 / .45), rgb(17 24 39) 62%);
            box-shadow: 0 8px 24px rgb(0 0 0 / .25);
        }

        .dark .community-chat-bubble::before {
            border-color: rgb(12 74 110);
            background: rgb(12 74 110);
        }

        .dark .community-chat-bubble.is-pinned {
            border-color: rgb(146 64 14);
            background: linear-gradient(135deg, rgb(146 64 14 / .35), rgb(17 24 39) 62%);
        }

        .dark .community-chat-bubble.is-pinned::before {
            border-color: rgb(146 64 14);
            background: rgb(146 64 14);
        }

        .dark .community-chat-name {
            color: rgb(248 250 252);
        }

        .dark .community-chat-meta,
        .dark .community-chat-content {
            color: rgb(203 213 225);
        }

        .dark .community-chat-pill {
            background: rgb(12 74 110 / .6);
            color: rgb(125 211 252);
        }

        .dark .community-chat-pill--pin {
            background: rgb(146 64 14 / .5);
            color: rgb(253 230 138);
        }

        .mentari-community-form-grid {
            align-items: start;
        }

        .mentari-community-modal-section,
        .mentari-community-view-section {
            overflow: hidden;
            border-color: rgb(226 232 240 / .95);
            background: rgb(255 255 255 / .96);
            box-shadow: 0 10px 26px rgb(15 23 42 / .06);
        }

        .mentari-community-modal-section--message,
        .mentari-community-view-section--message {
            border-top: 3px solid rgb(14 165 233);
            background:
                linear-gradient(180deg, rgb(240 249 255 / .78), rgb(255 255 255 / .97) 38%);
        }

        .mentari-community-modal-section--meta,
        .mentari-community-view-section--meta {
            border-top: 3px solid rgb(217 70 239);
            background:
                linear-gradient(180deg, rgb(253 244 255 / .74), rgb(255 255 255 / .97) 38%);
        }

        .mentari-community-modal-section--status {
            border-top: 3px solid rgb(16 185 129);
            background:
                linear-gradient(180deg, rgb(236 253 245 / .74), rgb(255 255 255 / .97) 38%);
        }

        .mentari-community-message-field textarea {
            min-height: 14rem;
            line-height: 1.6;
        }

        .mentari-community-modal-section .fi-section-header,
        .mentari-community-view-section .fi-section-header {
            padding-block: .8rem;
        }

        .mentari-community-modal-section .fi-section-header-heading,
        .mentari-community-view-section .fi-section-header-heading {
            font-size: .95rem;
            font-weight: 800;
        }

        .mentari-community-modal-section .fi-section-header-description,
        .mentari-community-view-section .fi-section-header-description {
            font-size: .76rem;
            line-height: 1.35;
        }

        .dark .mentari-community-modal-section,
        .dark .mentari-community-view-section,
        .dark .mentari-community-modal-section--message,
        .dark .mentari-community-view-section--message,
        .dark .mentari-community-modal-section--meta,
        .dark .mentari-community-view-section--meta,
        .dark .mentari-community-modal-section--status {
            border-color: rgb(148 163 184 / .18);
            background: rgb(15 23 42 / .92);
        }

        @media (max-width: 640px) {
            .community-chat-card {
                grid-template-columns: 1fr;
            }

            .community-chat-avatar {
                display: none;
            }

            .community-chat-bubble::before {
                display: none;
            }
        }
    </style>

    <div class="community-chat-table community-chat-scroll">
        {{ $this->table }}
    </div>
</x-filament-panels::page>
