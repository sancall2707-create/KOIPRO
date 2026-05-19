@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<div class="max-w-4xl mx-auto space-y-5"
     x-data="dashboard()" x-init="init()">

    <div>
        <h1 class="text-xl font-bold" style="color: var(--text-dark);">Dashboard</h1>
        <p class="text-sm" style="color: var(--text-mid);">Ringkasan aktivitas hari ini</p>
    </div>

    <!-- Stats -->
    <div x-show="loading" class="grid grid-cols-2 lg:grid-cols-3 gap-3">
        <template x-for="i in 3" :key="i">
            <div class="h-28 rounded-2xl animate-pulse" style="background: hsl(35,25%,88%);"></div>
        </template>
    </div>

    <div x-show="!loading" x-cloak class="grid grid-cols-2 lg:grid-cols-3 gap-3">
        <div class="rounded-2xl p-4 border" style="background: white; border-color: hsl(35,25%,88%);">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background: hsl(210,80%,90%);">
                <svg class="w-5 h-5" fill="none" stroke="hsl(210,80%,55%)" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <p class="text-2xl font-bold mb-1" style="color: var(--text-dark);" x-text="stats.totalOrders ?? 0"></p>
            <p class="text-xs" style="color: var(--text-mid);">Total Pesanan</p>
        </div>
        <div class="rounded-2xl p-4 border" style="background: white; border-color: hsl(35,25%,88%);">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background: hsl(35,90%,90%);">
                <svg class="w-5 h-5" fill="none" stroke="var(--accent)" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <p class="text-2xl font-bold mb-1" style="color: var(--text-dark);" x-text="stats.activeOrders ?? 0"></p>
            <p class="text-xs" style="color: var(--text-mid);">Pesanan Aktif</p>
        </div>
        <div class="rounded-2xl p-4 border col-span-2 lg:col-span-1" style="background: white; border-color: hsl(35,25%,88%);">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3" style="background: hsl(145,65%,88%);">
                <svg class="w-5 h-5" fill="none" stroke="var(--success)" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <p class="text-2xl font-bold mb-1" style="color: var(--text-dark);"
               x-text="stats.dailySales ? 'Rp ' + stats.dailySales.toLocaleString('id-ID') : 'Rp 0'"></p>
            <p class="text-xs" style="color: var(--text-mid);">Penjualan Hari Ini</p>
        </div>
    </div>

    <!-- Chart -->
    <div class="rounded-2xl border p-4" style="background: white; border-color: hsl(35,25%,88%);">
        <h2 class="font-semibold text-sm mb-4" style="color: hsl(24,10%,15%);">Pesanan per Status</h2>
        <div x-show="!loading && (!stats.ordersByStatus || stats.ordersByStatus.length === 0)" x-cloak
             class="flex items-center justify-center h-32" style="color: var(--text-mid);">
            <p class="text-sm">Belum ada data pesanan</p>
        </div>
        <div style="position:relative; height:240px; width:100%;">
            <canvas id="statusChart"></canvas>
        </div>
    </div>

    <!-- Pending orders -->
    <div class="rounded-2xl border p-4" style="background: white; border-color: hsl(35,25%,88%);">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-sm" style="color: hsl(24,10%,15%);">Pesanan Menunggu</h2>
            <span x-show="pendingOrders.length > 0" x-cloak
                  class="text-xs font-semibold px-2 py-0.5 rounded-full"
                  style="background: var(--accent); color: hsl(24,10%,10%);"
                  x-text="pendingOrders.length"></span>
        </div>

        <div x-show="pendingOrders.length === 0" class="flex items-center justify-center py-8 text-center">
            <div>
                <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="hsl(35,25%,70%)" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm" style="color: var(--text-mid);">Tidak ada pesanan menunggu</p>
            </div>
        </div>

        <div x-show="pendingOrders.length > 0" x-cloak class="space-y-3">
            <template x-for="order in pendingOrders" :key="order.id">
                <div class="flex items-start justify-between gap-3 p-3 rounded-xl border"
                     style="border-color: hsl(35,25%,90%); background: hsl(35,20%,98%);">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm" style="color: var(--text-dark);" x-text="order.orderNumber"></p>
                        <p class="text-xs mt-0.5" style="color: var(--text-mid);"
                           x-text="order.customerName + (order.tableNumber ? ' — Meja ' + order.tableNumber : ' — Takeaway')"></p>
                        <p class="text-xs font-medium mt-1" style="color: hsl(35,90%,40%);"
                           x-text="'Rp ' + order.totalPrice.toLocaleString('id-ID')"></p>
                    </div>
                    <select @change="updateStatus(order.id, $event.target.value)"
                            :value="order.status"
                            class="text-xs border rounded-lg h-8 px-2 outline-none"
                            style="border-color: var(--border);">
                        <option value="pending">Menunggu</option>
                        <option value="processing">Diproses</option>
                        <option value="preparing">Dibuat</option>
                        <option value="ready">Siap</option>
                        <option value="completed">Selesai</option>
                        <option value="cancelled">Batal</option>
                    </select>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
function dashboard() {
    return {
        stats: {},
        pendingOrders: [],
        loading: true,
        chart: null,

        async init() {
            await Promise.all([this.fetchStats(), this.fetchPending()]);
            this.loading = false;
            this.waitAndRenderChart();
        },

        waitAndRenderChart(attempts = 0) {
            if (attempts > 20) return;
            const canvas = document.getElementById('statusChart');
            if (canvas) {
                this.renderChart();
            } else {
                setTimeout(() => this.waitAndRenderChart(attempts + 1), 100);
            }
        },

        async fetchStats() {
            const res = await fetch('/admin/api/dashboard');
            if (res.ok) this.stats = await res.json();
        },

        async fetchPending() {
            const res = await fetch('/admin/api/orders?status=pending');
            if (res.ok) this.pendingOrders = await res.json();
        },

        async updateStatus(id, status) {
            await fetch('/admin/api/orders/' + id, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ status }),
            });
            await Promise.all([this.fetchStats(), this.fetchPending()]);
            setTimeout(() => this.renderChart(), 50);
        },

        renderChart() {
            // Ensure Chart.js is loaded
            if (typeof Chart === 'undefined') {
                setTimeout(() => this.renderChart(), 200);
                return;
            }
            if (!this.stats.ordersByStatus || this.stats.ordersByStatus.length === 0) return;
            const labels = {
                pending: 'Menunggu', processing: 'Diproses', preparing: 'Dibuat',
                ready: 'Siap', completed: 'Selesai', cancelled: 'Batal',
            };
            const canvas = document.getElementById('statusChart');
            if (!canvas) return;

            // Check if Chart.js has any existing chart on this canvas and destroy it
            try {
                const existing = Chart.getChart(canvas);
                if (existing) existing.destroy();
            } catch (e) {}

            if (this.chart) {
                try { this.chart.destroy(); } catch(e) {}
                this.chart = null;
            }

            try {
                const ctx = canvas.getContext('2d');
                this.chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: this.stats.ordersByStatus.map(s => labels[s.status] || s.status),
                        datasets: [{
                            data: this.stats.ordersByStatus.map(s => s.count),
                            backgroundColor: '#c9a84c',
                            borderRadius: 6,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                            y: { grid: { display: false }, ticks: { font: { size: 11 }, stepSize: 1 } },
                        },
                    },
                });
            } catch (e) {
                console.error('Status chart error:', e);
            }
        },
    };
}
</script>
@endsection
