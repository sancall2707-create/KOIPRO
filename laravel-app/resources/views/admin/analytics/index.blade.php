@extends('layouts.admin')
@section('title', 'Analisis')

@section('content')
<div class="max-w-5xl mx-auto space-y-5" x-data="analyticsPage()" x-init="init()">

    {{-- ── HEADER & FILTER ── --}}
    <div>
        <h1 class="text-xl font-bold" style="color: var(--text-dark);">Analisis Penjualan</h1>
        <p class="text-sm" style="color: var(--text-mid);">Data pesanan dan rekap penjualan berdasarkan periode</p>
    </div>

    {{-- Filter bar --}}
    <div class="rounded-2xl border p-4" style="background: white; border-color: hsl(35,25%,88%);">
        {{-- Quick period buttons --}}
        <div class="flex flex-wrap gap-2 mb-4">
            @foreach([
                ['label' => 'Hari Ini',   'key' => 'today'],
                ['label' => '7 Hari',     'key' => 'week'],
                ['label' => '30 Hari',    'key' => 'month'],
                ['label' => 'Bulan Ini',  'key' => 'this_month'],
                ['label' => 'Tahun Ini',  'key' => 'this_year'],
            ] as $p)
            <button @click="setPreset('{{ $p['key'] }}')"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium border transition-colors"
                    :style="preset === '{{ $p['key'] }}'
                        ? 'background: var(--bg-medium); color: var(--text-light); border-color: var(--bg-medium);'
                        : 'background: white; color: var(--text-dark); border-color: var(--border);'">
                {{ $p['label'] }}
            </button>
            @endforeach
        </div>

        {{-- Date range --}}
        <div class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs mb-1" style="color: var(--text-mid);">Dari Tanggal</label>
                <input x-model="from" type="date"
                       class="border rounded-xl h-9 px-3 text-sm outline-none"
                       style="border-color: var(--border);" />
            </div>
            <div>
                <label class="block text-xs mb-1" style="color: var(--text-mid);">Sampai Tanggal</label>
                <input x-model="to" type="date"
                       class="border rounded-xl h-9 px-3 text-sm outline-none"
                       style="border-color: var(--border);" />
            </div>
            <button @click="preset = 'custom'; fetch()"
                    class="h-9 px-4 rounded-xl text-sm font-semibold"
                    style="background: var(--accent); color: hsl(24,10%,10%);">
                Tampilkan
            </button>
        </div>

        <p x-show="data" x-cloak class="text-xs mt-3" style="color: var(--text-mid);">
            Menampilkan data <span class="font-semibold" x-text="data && data.period.from"></span>
            s/d <span class="font-semibold" x-text="data && data.period.to"></span>
        </p>
    </div>

    {{-- Loading skeleton --}}
    <div x-show="loading" x-cloak class="space-y-4">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <template x-for="i in 4" :key="i">
                <div class="h-24 rounded-2xl animate-pulse" style="background: hsl(35,25%,88%);"></div>
            </template>
        </div>
        <div class="h-60 rounded-2xl animate-pulse" style="background: hsl(35,25%,88%);"></div>
        <div class="h-60 rounded-2xl animate-pulse" style="background: hsl(35,25%,88%);"></div>
    </div>

    <div x-show="!loading && data" x-cloak>
    <template x-if="data">

        <div class="space-y-5">

            {{-- ── SUMMARY CARDS ── --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">

                <div class="rounded-2xl border p-4" style="background: white; border-color: hsl(35,25%,88%);">
                    <p class="text-xs mb-1" style="color: var(--text-mid);">Total Pesanan</p>
                    <p class="text-2xl font-bold" style="color: var(--text-dark);" x-text="data.summary.totalOrders"></p>
                    <div class="flex gap-2 mt-1 text-xs flex-wrap">
                        <span style="color: var(--success);" x-text="data.summary.completedOrders + ' selesai'"></span>
                        <span style="color: var(--danger);" x-text="data.summary.cancelledOrders + ' batal'"></span>
                    </div>
                </div>

                <div class="rounded-2xl border p-4" style="background: white; border-color: hsl(35,25%,88%);">
                    <p class="text-xs mb-1" style="color: var(--text-mid);">Total Pendapatan</p>
                    <p class="text-xl font-bold leading-tight" style="color: var(--text-dark);"
                       x-text="'Rp ' + data.summary.totalRevenue.toLocaleString('id-ID')"></p>
                    <p class="text-xs mt-1" style="color: var(--text-mid);">Tidak termasuk yang batal</p>
                </div>

                <div class="rounded-2xl border p-4" style="background: white; border-color: hsl(35,25%,88%);">
                    <p class="text-xs mb-1" style="color: var(--text-mid);">Rata-rata Pesanan</p>
                    <p class="text-xl font-bold leading-tight" style="color: var(--text-dark);"
                       x-text="'Rp ' + data.summary.avgOrderValue.toLocaleString('id-ID')"></p>
                    <p class="text-xs mt-1" style="color: var(--text-mid);">Per transaksi</p>
                </div>

                <div class="rounded-2xl border p-4" style="background: white; border-color: hsl(35,25%,88%);">
                    <p class="text-xs mb-1" style="color: var(--text-mid);">Pesanan Aktif</p>
                    <p class="text-2xl font-bold" style="color: hsl(35,90%,40%);" x-text="data.summary.activeOrders"></p>
                    <p class="text-xs mt-1" style="color: var(--text-mid);">Belum selesai</p>
                </div>
            </div>

            {{-- ── GRAFIK PENJUALAN HARIAN ── --}}
            <div class="rounded-2xl border p-4" style="background: white; border-color: hsl(35,25%,88%);">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="font-semibold text-sm" style="color: var(--text-dark);">Grafik Penjualan Harian</h2>
                        <p class="text-xs mt-0.5" style="color: var(--text-mid);">Jumlah pesanan dan pendapatan per hari</p>
                    </div>
                    <div class="flex gap-2">
                        <button @click="dailyChart = 'revenue'; updateDailyChart()"
                                class="px-2.5 py-1 rounded-lg text-xs font-medium border"
                                :style="dailyChart === 'revenue'
                                    ? 'background: var(--bg-medium); color: white; border-color: var(--bg-medium);'
                                    : 'background: white; color: var(--text-dark); border-color: var(--border);'">
                            Pendapatan
                        </button>
                        <button @click="dailyChart = 'orders'; updateDailyChart()"
                                class="px-2.5 py-1 rounded-lg text-xs font-medium border"
                                :style="dailyChart === 'orders'
                                    ? 'background: var(--bg-medium); color: white; border-color: var(--bg-medium);'
                                    : 'background: white; color: var(--text-dark); border-color: var(--border);'">
                            Pesanan
                        </button>
                    </div>
                </div>

                <div x-show="data && data.dailySales && data.dailySales.length === 0" x-cloak class="flex items-center justify-center h-32"
                     style="color: var(--text-mid);">
                    <p class="text-sm">Tidak ada data untuk periode ini</p>
                </div>
                <div id="dailyChartWrapper" style="position:relative; height:200px; width:100%;">
                    <canvas id="dailyChart"></canvas>
                </div>
            </div>

            {{-- ── REKAP PENJUALAN HARIAN (TABEL) ── --}}
            <div class="rounded-2xl border" style="background: white; border-color: hsl(35,25%,88%);">
                <div class="p-4 border-b flex items-center justify-between" style="border-color: hsl(35,25%,90%);">
                    <div>
                        <h2 class="font-semibold text-sm" style="color: var(--text-dark);">Rekap Penjualan per Hari</h2>
                        <p class="text-xs mt-0.5" style="color: var(--text-mid);">Total transaksi dan pendapatan setiap hari</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full font-medium"
                          style="background: hsl(35,45%,90%); color: hsl(24,35%,25%);"
                          x-text="data.dailySales.length + ' hari'"></span>
                </div>

                <div x-show="data.dailySales.length === 0" class="p-8 text-center">
                    <p class="text-sm" style="color: var(--text-mid);">Tidak ada data untuk periode ini</p>
                </div>

                <div x-show="data.dailySales.length > 0" class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr style="background: hsl(35,20%,97%); border-bottom: 1px solid hsl(35,25%,90%);">
                                <th class="text-left px-4 py-3 font-semibold text-xs" style="color: var(--text-mid);">Tanggal</th>
                                <th class="text-center px-4 py-3 font-semibold text-xs" style="color: var(--text-mid);">Jml Pesanan</th>
                                <th class="text-right px-4 py-3 font-semibold text-xs" style="color: var(--text-mid);">Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(row, i) in [...data.dailySales].reverse()" :key="row.date">
                                <tr class="border-b" :style="i % 2 === 0 ? 'background: white;' : 'background: hsl(35,20%,99%);'"
                                    style="border-color: hsl(35,25%,93%);">
                                    <td class="px-4 py-3 font-medium" style="color: var(--text-dark);"
                                        x-text="new Date(row.date).toLocaleDateString('id-ID', {weekday:'short', year:'numeric', month:'short', day:'numeric'})"></td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold"
                                              style="background: hsl(210,80%,90%); color: hsl(210,80%,35%);"
                                              x-text="row.orderCount"></span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold" style="color: hsl(35,90%,40%);"
                                        x-text="'Rp ' + row.revenue.toLocaleString('id-ID')"></td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot>
                            <tr style="background: hsl(35,20%,95%); border-top: 2px solid hsl(35,25%,85%);">
                                <td class="px-4 py-3 font-bold text-sm" style="color: var(--text-dark);">Total</td>
                                <td class="px-4 py-3 text-center font-bold" style="color: var(--text-dark);"
                                    x-text="data.dailySales.reduce((s,r) => s + r.orderCount, 0)"></td>
                                <td class="px-4 py-3 text-right font-bold" style="color: hsl(35,90%,40%);"
                                    x-text="'Rp ' + data.dailySales.reduce((s,r) => s + r.revenue, 0).toLocaleString('id-ID')"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- ── TOP PRODUK ── --}}
            <div class="rounded-2xl border" style="background: white; border-color: hsl(35,25%,88%);">
                <div class="p-4 border-b" style="border-color: hsl(35,25%,90%);">
                    <h2 class="font-semibold text-sm" style="color: var(--text-dark);">Produk Terlaris</h2>
                    <p class="text-xs mt-0.5" style="color: var(--text-mid);">Berdasarkan jumlah item yang dipesan (tidak termasuk pesanan batal)</p>
                </div>

                <div x-show="data && data.topProducts && data.topProducts.length === 0" x-cloak class="p-8 text-center">
                    <p class="text-sm" style="color: var(--text-mid);">Tidak ada data produk untuk periode ini</p>
                </div>

                <div x-show="data && data.topProducts && data.topProducts.length > 0" x-cloak>
                    {{-- Bar chart produk --}}
                    <div class="px-4 pt-4 pb-2" style="position:relative; height:200px; width:100%;">
                        <canvas id="productChart"></canvas>
                    </div>

                    {{-- Tabel produk --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr style="background: hsl(35,20%,97%); border-bottom: 1px solid hsl(35,25%,90%);">
                                    <th class="text-left px-4 py-3 font-semibold text-xs" style="color: var(--text-mid);">#</th>
                                    <th class="text-left px-4 py-3 font-semibold text-xs" style="color: var(--text-mid);">Nama Produk</th>
                                    <th class="text-center px-4 py-3 font-semibold text-xs" style="color: var(--text-mid);">Jml Terjual</th>
                                    <th class="text-center px-4 py-3 font-semibold text-xs" style="color: var(--text-mid);">Jml Pesanan</th>
                                    <th class="text-right px-4 py-3 font-semibold text-xs" style="color: var(--text-mid);">Total Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(p, i) in data.topProducts" :key="p.productId">
                                    <tr class="border-b" :style="i % 2 === 0 ? 'background: white;' : 'background: hsl(35,20%,99%);'"
                                        style="border-color: hsl(35,25%,93%);">
                                        <td class="px-4 py-3">
                                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold"
                                                  :style="i === 0 ? 'background: hsl(45,90%,50%); color: hsl(24,10%,10%);'
                                                        : i === 1 ? 'background: hsl(0,0%,80%); color: hsl(24,10%,20%);'
                                                        : i === 2 ? 'background: hsl(25,70%,65%); color: white;'
                                                        : 'background: hsl(35,25%,88%); color: var(--text-mid);'"
                                                  x-text="i + 1"></span>
                                        </td>
                                        <td class="px-4 py-3 font-medium" style="color: var(--text-dark);" x-text="p.productName"></td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="font-bold" style="color: var(--text-dark);" x-text="p.totalQty"></span>
                                            <span class="text-xs" style="color: var(--text-mid);"> item</span>
                                        </td>
                                        <td class="px-4 py-3 text-center" style="color: var(--text-mid);" x-text="p.orderCount + 'x'"></td>
                                        <td class="px-4 py-3 text-right font-semibold" style="color: hsl(35,90%,40%);"
                                            x-text="'Rp ' + p.totalRevenue.toLocaleString('id-ID')"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ── RINCIAN PESANAN ── --}}
            <div class="rounded-2xl border" style="background: white; border-color: hsl(35,25%,88%);">
                <div class="p-4 border-b flex items-center justify-between flex-wrap gap-2" style="border-color: hsl(35,25%,90%);">
                    <div>
                        <h2 class="font-semibold text-sm" style="color: var(--text-dark);">Rincian Pesanan</h2>
                        <p class="text-xs mt-0.5" style="color: var(--text-mid);">Daftar semua pesanan dalam periode ini (maks 50 terbaru)</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <input x-model="orderSearch" type="text" placeholder="Cari nomor / nama..."
                               class="border rounded-lg h-8 px-3 text-xs outline-none"
                               style="border-color: var(--border); width: 180px;" />
                        <select x-model="orderStatusFilter"
                                class="border rounded-lg h-8 px-2 text-xs outline-none"
                                style="border-color: var(--border);">
                            <option value="">Semua Status</option>
                            <option value="pending">Menunggu</option>
                            <option value="processing">Diproses</option>
                            <option value="preparing">Dibuat</option>
                            <option value="ready">Siap</option>
                            <option value="completed">Selesai</option>
                            <option value="cancelled">Batal</option>
                        </select>
                    </div>
                </div>

                <div x-show="filteredOrders.length === 0" class="p-8 text-center">
                    <p class="text-sm" style="color: var(--text-mid);">Tidak ada pesanan ditemukan</p>
                </div>

                <div x-show="filteredOrders.length > 0" class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr style="background: hsl(35,20%,97%); border-bottom: 1px solid hsl(35,25%,90%);">
                                <th class="text-left px-4 py-3 font-semibold text-xs" style="color: var(--text-mid);">No. Pesanan</th>
                                <th class="text-left px-4 py-3 font-semibold text-xs" style="color: var(--text-mid);">Pelanggan</th>
                                <th class="text-left px-4 py-3 font-semibold text-xs" style="color: var(--text-mid);">Item Dipesan</th>
                                <th class="text-center px-4 py-3 font-semibold text-xs" style="color: var(--text-mid);">Status</th>
                                <th class="text-left px-4 py-3 font-semibold text-xs" style="color: var(--text-mid);">Waktu</th>
                                <th class="text-right px-4 py-3 font-semibold text-xs" style="color: var(--text-mid);">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(o, i) in filteredOrders" :key="o.id">
                                <tr class="border-b" :style="i % 2 === 0 ? 'background: white;' : 'background: hsl(35,20%,99%);'"
                                    style="border-color: hsl(35,25%,93%);">
                                    <td class="px-4 py-3 font-mono font-semibold text-xs" style="color: var(--text-dark);"
                                        x-text="o.orderNumber"></td>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-xs" style="color: var(--text-dark);" x-text="o.customerName"></p>
                                        <p class="text-xs" style="color: var(--text-mid);"
                                           x-text="o.orderType === 'dine_in' ? (o.tableNumber ? 'Meja ' + o.tableNumber : 'Dine In') : 'Takeaway'"></p>
                                    </td>
                                    <td class="px-4 py-3 max-w-xs">
                                        <template x-for="item in o.items" :key="item.productName">
                                            <div class="text-xs" style="color: var(--text-mid);">
                                                <span x-text="item.productName + ' ×' + item.quantity"></span>
                                            </div>
                                        </template>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-block text-xs px-2 py-0.5 rounded-full font-medium"
                                              :style="statusStyle(o.status)"
                                              x-text="statusLabel(o.status)"></span>
                                    </td>
                                    <td class="px-4 py-3 text-xs" style="color: var(--text-mid);" x-text="o.createdAt"></td>
                                    <td class="px-4 py-3 text-right font-semibold text-xs" style="color: hsl(35,90%,40%);"
                                        x-text="'Rp ' + o.totalPrice.toLocaleString('id-ID')"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Footer rekap --}}
                <div x-show="filteredOrders.length > 0" x-cloak
                     class="px-4 py-3 border-t flex items-center justify-between flex-wrap gap-2"
                     style="border-color: hsl(35,25%,90%); background: hsl(35,20%,97%);">
                    <p class="text-xs" style="color: var(--text-mid);"
                       x-text="filteredOrders.length + ' pesanan ditampilkan'"></p>
                    <p class="text-sm font-bold" style="color: hsl(35,90%,40%);"
                       x-text="'Total: Rp ' + filteredOrdersTotal().toLocaleString('id-ID')"></p>
                </div>
            </div>

        </div>
    </template>
    </div>

</div>

<script>
function analyticsPage() {
    return {
        from: '',
        to: '',
        preset: 'month',
        data: null,
        loading: false,
        dailyChart: 'revenue',
        orderSearch: '',
        orderStatusFilter: '',
        chartDaily: null,
        chartProduct: null,

        get filteredOrders() {
            if (!this.data) return [];
            return this.data.recentOrders.filter(o => {
                const matchStatus = !this.orderStatusFilter || o.status === this.orderStatusFilter;
                const q = this.orderSearch.toLowerCase();
                const matchSearch = !q ||
                    o.orderNumber.toLowerCase().includes(q) ||
                    o.customerName.toLowerCase().includes(q);
                return matchStatus && matchSearch;
            });
        },

        filteredOrdersTotal() {
            return this.filteredOrders
                .filter(o => o.status !== 'cancelled')
                .reduce((s, o) => s + o.totalPrice, 0);
        },

        setPreset(key) {
            this.preset = key;
            const today = new Date();
            const fmt = d => d.toISOString().split('T')[0];

            if (key === 'today') {
                this.from = this.to = fmt(today);
            } else if (key === 'week') {
                const d = new Date(today); d.setDate(d.getDate() - 6);
                this.from = fmt(d); this.to = fmt(today);
            } else if (key === 'month') {
                const d = new Date(today); d.setDate(d.getDate() - 29);
                this.from = fmt(d); this.to = fmt(today);
            } else if (key === 'this_month') {
                this.from = fmt(new Date(today.getFullYear(), today.getMonth(), 1));
                this.to = fmt(today);
            } else if (key === 'this_year') {
                this.from = fmt(new Date(today.getFullYear(), 0, 1));
                this.to = fmt(today);
            }
            this.fetch();
        },

        async fetch() {
            this.loading = true;
            const params = new URLSearchParams();
            if (this.from) params.set('from', this.from);
            if (this.to)   params.set('to', this.to);

            const res = await fetch('/admin/api/analytics?' + params.toString());
            if (res.ok) {
                this.data = await res.json();
            }
            this.loading = false;
            // Wait for DOM to settle, then render charts
            setTimeout(() => {
                this.renderDailyChart();
                this.renderProductChart();
            }, 300);
        },

        updateDailyChart() {
            setTimeout(() => this.renderDailyChart(), 50);
        },

        renderDailyChart() {
            if (!this.data || !this.data.dailySales || this.data.dailySales.length === 0) return;
            
            // Destroy previous instance
            if (this.chartDaily) {
                this.chartDaily.destroy();
                this.chartDaily = null;
            }

            const canvas = document.getElementById('dailyChart');
            if (!canvas) return;
            
            // Reset canvas
            const parent = canvas.parentNode;
            const newCanvas = document.createElement('canvas');
            newCanvas.id = 'dailyChart';
            parent.removeChild(canvas);
            parent.appendChild(newCanvas);

            const labels = this.data.dailySales.map(r =>
                new Date(r.date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })
            );
            const isRevenue = this.dailyChart === 'revenue';
            const values = this.data.dailySales.map(r => isRevenue ? r.revenue : r.orderCount);

            this.chartDaily = new Chart(newCanvas, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: isRevenue ? 'Pendapatan (Rp)' : 'Jumlah Pesanan',
                        data: values,
                        backgroundColor: '#c9a84c',
                        borderRadius: 5,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return isRevenue
                                        ? 'Rp ' + context.parsed.y.toLocaleString('id-ID')
                                        : context.parsed.y + ' pesanan';
                                },
                            },
                        },
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 10 }, maxRotation: 45 } },
                        y: {
                            grid: { color: '#eee' },
                            ticks: {
                                font: { size: 10 },
                                callback: function(v) { return isRevenue ? 'Rp ' + (v / 1000).toFixed(0) + 'rb' : v; },
                            },
                        },
                    },
                },
            });
        },

        renderProductChart() {
            if (!this.data || !this.data.topProducts || this.data.topProducts.length === 0) return;
            
            if (this.chartProduct) {
                this.chartProduct.destroy();
                this.chartProduct = null;
            }

            const canvas = document.getElementById('productChart');
            if (!canvas) return;
            
            // Reset canvas
            const parent = canvas.parentNode;
            const newCanvas = document.createElement('canvas');
            newCanvas.id = 'productChart';
            parent.removeChild(canvas);
            parent.appendChild(newCanvas);

            const top = this.data.topProducts.slice(0, 8);
            this.chartProduct = new Chart(newCanvas, {
                type: 'bar',
                data: {
                    labels: top.map(p => p.productName),
                    datasets: [{
                        label: 'Jumlah Terjual',
                        data: top.map(p => p.totalQty),
                        backgroundColor: [
                            '#c9a84c', '#d4b96a', '#8b7a3c',
                            '#e8d48b', '#a89240', '#b8982e',
                            '#d4a017', '#cca43b',
                        ],
                        borderRadius: 5,
                    }],
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: { label: function(context) { return context.parsed.x + ' item'; } },
                        },
                    },
                    scales: {
                        x: { grid: { color: '#eee' }, ticks: { font: { size: 10 } } },
                        y: { grid: { display: false }, ticks: { font: { size: 11 } } },
                    },
                },
            });
        },

        statusLabel(s) {
            const map = { pending:'Menunggu', processing:'Diproses', preparing:'Dibuat',
                          ready:'Siap', completed:'Selesai', cancelled:'Batal' };
            return map[s] || s;
        },

        statusStyle(s) {
            const map = {
                pending:    'background: hsl(35,90%,90%); color: hsl(35,90%,30%);',
                processing: 'background: hsl(210,80%,90%); color: hsl(210,80%,30%);',
                preparing:  'background: hsl(270,70%,92%); color: hsl(270,70%,35%);',
                ready:      'background: hsl(145,65%,88%); color: hsl(145,55%,30%);',
                completed:  'background: hsl(145,65%,88%); color: hsl(145,55%,30%);',
                cancelled:  'background: hsl(0,84%,93%); color: hsl(0,84%,40%);',
            };
            return map[s] || '';
        },

        init() {
            this.setPreset('month');
        },
    };
}
</script>
@endsection
