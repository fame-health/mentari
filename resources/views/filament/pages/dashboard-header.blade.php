<div
    class="mentari-dashboard-toolbar"
    x-data="{
        time: '',
        updateTime() {
            this.time = new Intl.DateTimeFormat('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
            }).format(new Date())
        },
    }"
    x-init="updateTime(); setInterval(() => updateTime(), 1000)"
>
    <div class="mentari-dashboard-toolbar-copy">
        <div class="mentari-dashboard-toolbar-icon" aria-hidden="true">
            <x-heroicon-o-squares-2x2 />
        </div>

        <div>
            <h1>Dashboard MENTARI</h1>
            <p>{{ now()->translatedFormat('l, d F Y') }}</p>
        </div>
    </div>

    <div class="mentari-dashboard-live" title="Data diperbarui otomatis setiap 10 detik">
        <span class="mentari-dashboard-live-dot" aria-hidden="true"></span>
        <span>Live</span>
        <time x-text="time">{{ now()->format('H:i:s') }}</time>
    </div>
</div>
