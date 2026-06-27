@php
    $authorName = $record->user?->name ?? 'Pengguna tidak diketahui';
    $initials = collect(explode(' ', $authorName))
        ->filter()
        ->take(2)
        ->map(fn (string $part): string => strtoupper(substr($part, 0, 1)))
        ->join('');
@endphp

<div class="community-chat-card">
    <div class="community-chat-avatar" aria-hidden="true">
        {{ $initials ?: '?' }}
    </div>

    <div @class([
        'community-chat-bubble',
        'is-pinned' => $record->is_pinned,
        'is-deleted' => method_exists($record, 'trashed') && $record->trashed(),
    ])>
        <div class="community-chat-header">
            <div class="community-chat-author">
                <span class="community-chat-name">{{ $authorName }}</span>
                <span class="community-chat-meta">
                    {{ $record->school?->name ?? 'Sekolah tidak diisi' }} · {{ $record->created_at?->diffForHumans() ?? '-' }}
                </span>
            </div>

            <div class="community-chat-badges">
                @if ($record->tag)
                    <span class="community-chat-pill">#{{ $record->tag }}</span>
                @endif

                @if ($record->is_pinned)
                    <span class="community-chat-pill community-chat-pill--pin">
                        <x-filament::icon icon="heroicon-o-bookmark" />
                        Disematkan
                    </span>
                @endif

                @if (method_exists($record, 'trashed') && $record->trashed())
                    <span class="community-chat-pill community-chat-pill--deleted">Dihapus</span>
                @endif
            </div>
        </div>

        <div class="community-chat-content">{{ $record->content }}</div>

        <div class="community-chat-footer">
            <span class="community-chat-meta">
                {{ $record->updated_at?->ne($record->created_at) ? 'Diperbarui '.$record->updated_at->diffForHumans() : 'Belum diedit' }}
            </span>

            <span class="community-chat-like">
                <x-filament::icon icon="heroicon-o-hand-thumb-up" />
                {{ number_format((int) $record->likes_count) }} suka
            </span>
        </div>
    </div>
</div>
