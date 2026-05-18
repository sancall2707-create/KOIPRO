@extends('layouts.app')
@section('title', 'Tracking Pesanan')

@section('content')
<div class="min-h-screen" style="background: var(--bg-light);"
     x-data="orderTracking('{{ $orderNumber }}')" x-init="init()">

    <!-- Loading -->
    <div x-show="loading" class="p-6 animate-pulse">
        <div class="h-8 w-48 rounded mb-4" style="background: hsl(35,25%,88%);"></div>
        <div class="h-32 w-full rounded-2xl mb-4" style="background: hsl(35,25%,88%);"></div>
        <div class="h-48 w-full rounded-2xl" style="background: hsl(35,25%,88%);"></div>
    </div>

    <template x-if="!loading && order">
        <div>
            <!-- Header -->
            <div class="px-4 pt-12 pb-8 text-center"
                 style="background: linear-gradient(160deg, hsl(24,35%,18%) 0%, hsl(24,35%,28%) 100%);">
                <div class="flex items-center justify-center gap-2 mb-4">
                    <svg class="w-5 h-5" fill="none" stroke="var(--accent)" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8zM6 1v3M10 1v3M14 1v3"/>
                    </svg>
                    <span class="text-sm font-semibold" style="color: hsl(35,45%,70%);">Kopi Tiang Alam</span>
                </div>
                <p class="text-xs mb-1" style="color: hsl(35,30%,60%);">Nomor Pesanan</p>
                <h1 class="text-2xl font-bold tracking-widest mb-3" style="color: var(--text-light);"
                    x-text="order.orderNumber"></h1>
                <span class="inline-block text-sm px-4 py-1.5 font-semibold rounded-full"
                      :style="'background: ' + statusColor(order.status) + '; color: white;'"
                      x-text="statusLabel(order.status)"></span>
            </div>

            <div class="px-4 py-5 space-y-4">

                <!-- Customer info -->
                <div class="rounded-2xl border p-4" style="border-color: var(--border); background: var(--bg-light);">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-xs mb-1" style="color: hsl(24,10%,50%);">Nama</p>
                            <p class="font-semibold" style="color: var(--text-dark);" x-text="order.customerName"></p>
                        </div>
                        <div>
                            <p class="text-xs mb-1" style="color: hsl(24,10%,50%);">Tipe</p>
                            <p class="font-semibold" style="color: var(--text-dark);"
                               x-text="order.orderType === 'dine_in' ? 'Dine In' + (order.tableNumber ? ' — Meja ' + order.tableNumber : '') : 'Takeaway'"></p>
                        </div>
                    </div>
                </div>

                <!-- Status stepper -->
                <template x-if="order.status !== 'cancelled'">
                    <div class="rounded-2xl border p-4" style="border-color: var(--border); background: var(--bg-light);">
                        <h2 class="font-semibold text-sm mb-4" style="color: hsl(24,10%,20%);">Status Pesanan</h2>
                        <div class="space-y-3">
                            <template x-for="(step, i) in statusSteps" :key="step.key">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0"
                                         :style="stepBg(i)">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                             :style="stepIconColor(i)">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  :d="step.icon"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium"
                                           :style="isAfterCurrent(i) ? 'color: hsl(24,10%,60%);' : 'color: var(--text-dark);'"
                                           x-text="step.label"></p>
                                    </div>
                                    <template x-if="isCurrentStep(i)">
                                        <span class="text-xs font-medium px-2 py-0.5 rounded-full"
                                              style="background: var(--accent); color: hsl(24,10%,10%);">Sekarang</span>
                                    </template>
                                    <template x-if="isBeforeCurrent(i)">
                                        <svg class="w-4 h-4" fill="none" stroke="var(--success)" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <template x-if="order.status === 'cancelled'">
                    <div class="rounded-2xl border p-4 text-center"
                         style="border-color: hsl(0,84%,85%); background: hsl(0,84%,97%);">
                        <p class="font-semibold" style="color: hsl(0,84%,45%);">Pesanan Dibatalkan</p>
                        <p class="text-sm mt-1" style="color: hsl(0,50%,55%);">Pesanan ini telah dibatalkan</p>
                    </div>
                </template>

                <!-- Order items -->
                <div class="rounded-2xl border p-4" style="border-color: var(--border); background: var(--bg-light);">
                    <h2 class="font-semibold text-sm mb-3" style="color: hsl(24,10%,20%);">Detail Pesanan</h2>
                    <div class="space-y-2">
                        <template x-for="item in order.items" :key="item.id">
                            <div class="flex justify-between text-sm">
                                <span style="color: hsl(24,10%,30%);"
                                      x-text="item.productName + ' x' + item.quantity"></span>
                                <span class="font-medium" style="color: var(--text-dark);"
                                      x-text="'Rp ' + item.subtotal.toLocaleString('id-ID')"></span>
                            </div>
                        </template>
                    </div>
                    <template x-if="order.notes">
                        <div>
                            <div class="border-t my-2" style="border-color: var(--border);"></div>
                            <p class="text-xs" style="color: hsl(24,10%,50%);" x-text="'Catatan: ' + order.notes"></p>
                        </div>
                    </template>
                    <div class="border-t my-3" style="border-color: var(--border);"></div>
                    <div class="flex justify-between font-bold">
                        <span style="color: var(--text-dark);">Total</span>
                        <span style="color: hsl(35,90%,40%);"
                              x-text="'Rp ' + order.totalPrice.toLocaleString('id-ID')"></span>
                    </div>
                </div>

                <p class="text-center text-xs" style="color: hsl(24,10%,60%);">Halaman ini diperbarui otomatis setiap 10 detik</p>

                <a href="{{ route('home') }}"
                   class="flex items-center justify-center gap-2 w-full py-3 rounded-xl border font-medium text-sm"
                   style="border-color: var(--border); color: var(--text-dark);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </template>
</div>

<script>
function orderTracking(orderNumber) {
    return {
        orderNumber,
        order: null,
        loading: true,
        statusSteps: [
            { key: 'pending',    label: 'Menunggu',  icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' },
            { key: 'processing', label: 'Diproses',  icon: 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9' },
            { key: 'preparing',  label: 'Dibuat',    icon: 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4' },
            { key: 'ready',      label: 'Siap',      icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' },
            { key: 'completed',  label: 'Selesai',   icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
        ],

        get currentStepIndex() {
            if (!this.order || this.order.status === 'cancelled') return -1;
            return this.statusSteps.findIndex(s => s.key === this.order.status);
        },

        isCurrentStep(i)  { return i === this.currentStepIndex; },
        isBeforeCurrent(i){ return i < this.currentStepIndex; },
        isAfterCurrent(i) { return i > this.currentStepIndex; },

        stepBg(i) {
            if (i < this.currentStepIndex)  return 'background: var(--success);';
            if (i === this.currentStepIndex) return 'background: var(--accent);';
            return 'background: hsl(35,25%,88%);';
        },

        stepIconColor(i) {
            if (i <= this.currentStepIndex) return 'color: white;';
            return 'color: hsl(24,10%,50%);';
        },

        statusColor(s) {
            const map = {
                pending: 'hsl(35,90%,50%)', processing: 'hsl(210,80%,55%)',
                preparing: 'hsl(270,70%,55%)', ready: 'hsl(145,65%,42%)',
                completed: 'hsl(145,65%,42%)', cancelled: 'hsl(0,84%,60%)',
            };
            return map[s] || 'hsl(35,25%,60%)';
        },

        statusLabel(s) {
            const map = {
                pending: 'Menunggu Konfirmasi', processing: 'Sedang Diproses',
                preparing: 'Sedang Dibuat', ready: 'Siap Diambil',
                completed: 'Selesai', cancelled: 'Dibatalkan',
            };
            return map[s] || s;
        },

        async fetchOrder() {
            try {
                const res = await fetch('/api/orders/' + this.orderNumber);
                if (res.ok) this.order = await res.json();
            } catch (e) {}
            this.loading = false;
        },

        init() {
            this.fetchOrder();
            setInterval(() => this.fetchOrder(), 10000);
        },
    };
}
</script>
@endsection
