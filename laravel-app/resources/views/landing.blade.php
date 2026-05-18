@extends('layouts.app')
@section('title', 'Kopi Tiang Alam')

@section('content')
<div class="min-h-screen" style="background: var(--bg-light);">

    <!-- Hero -->
    <div x-data="dineInModal()">
        <div class="relative min-h-[70vh] flex flex-col items-center justify-center text-center px-6 overflow-hidden"
             style="background: linear-gradient(160deg, hsl(24,35%,15%) 0%, hsl(24,35%,25%) 60%, hsl(35,45%,30%) 100%);">
            <div class="absolute inset-0 opacity-10"
                 style="background-image: radial-gradient(circle at 20% 80%, hsl(35,90%,50%) 0%, transparent 50%), radial-gradient(circle at 80% 20%, hsl(45,70%,60%) 0%, transparent 50%);"></div>

            <div class="relative z-10 max-w-lg mx-auto">
                <div class="flex items-center justify-center gap-2 mb-6">
                    <div class="p-3 rounded-full" style="background: var(--accent);">
                        <svg class="w-8 h-8" fill="none" stroke="hsl(24,10%,10%)" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8zM6 1v3M10 1v3M14 1v3"/>
                        </svg>
                    </div>
                </div>
                <h1 class="text-4xl font-bold tracking-tight mb-2" style="color: hsl(40,33%,98%);">Kopi Tiang Alam</h1>
                <p class="text-lg mb-2" style="color: hsl(35,45%,75%);">Warung kopi modern dengan cita rasa autentik</p>
                <p class="text-sm mb-10" style="color: hsl(35,30%,60%);">Pesan langsung dari meja Anda, tanpa menunggu</p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <button @click="open()"
                            class="inline-flex items-center justify-center gap-2 text-base font-semibold h-14 px-8 rounded-xl shadow-lg transition-opacity hover:opacity-90"
                            style="background: var(--accent); color: hsl(24,10%,10%);">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        Dine In
                    </button>
                    <a href="{{ route('menu') }}"
                       class="inline-flex items-center justify-center gap-2 text-base font-semibold h-14 px-8 rounded-xl border transition-opacity hover:opacity-80"
                       style="border-color: hsl(35,45%,65%); color: hsl(40,33%,98%); background: transparent;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8zM6 1v3M10 1v3M14 1v3"/>
                        </svg>
                        Takeaway
                    </a>
                </div>
            </div>

            <div class="absolute bottom-0 left-0 right-0 h-16"
                 style="background: linear-gradient(to top, hsl(40,33%,98%), transparent);"></div>
        </div>

        <!-- Table Selection Modal -->
        <div x-show="show" x-cloak
             class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 pb-6 sm:pb-0"
             style="background: rgba(0,0,0,0.6);"
             @click.self="show = false">
            <div class="w-full max-w-sm rounded-2xl overflow-hidden" style="background: white;">

                <!-- Modal Header -->
                <div class="px-5 py-4 flex items-center justify-between"
                     style="background: hsl(24,35%,18%);">
                    <div>
                        <p class="font-bold text-base" style="color: hsl(40,33%,98%);">Pilih Meja</p>
                        <p class="text-xs mt-0.5" style="color: hsl(35,30%,60%);">Tap meja tempat Anda duduk</p>
                    </div>
                    <button @click="show = false">
                        <svg class="w-5 h-5" fill="none" stroke="hsl(35,30%,60%)" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Loading -->
                <div x-show="loading" class="grid grid-cols-3 gap-3 p-5">
                    <template x-for="i in 6" :key="i">
                        <div class="h-16 rounded-xl animate-pulse" style="background: hsl(35,25%,88%);"></div>
                    </template>
                </div>

                <!-- No tables -->
                <div x-show="!loading && tables.length === 0" x-cloak class="p-8 text-center">
                    <p class="text-sm" style="color: hsl(24,10%,50%);">Tidak ada meja tersedia</p>
                </div>

                <!-- Table Grid -->
                <div x-show="!loading && tables.length > 0" x-cloak class="p-5">
                    <div class="grid grid-cols-3 gap-3 max-h-64 overflow-y-auto">
                        <template x-for="table in tables" :key="table.id">
                            <button @click="table.status === 'available' && selectTable(table.table_number)"
                                    :disabled="table.status !== 'available'"
                                    class="rounded-xl p-3 text-center border transition-all"
                                    :style="table.status === 'available'
                                        ? 'border-color: hsl(35,25%,80%); background: white; cursor: pointer;'
                                        : 'border-color: hsl(35,15%,88%); background: hsl(35,15%,94%); cursor: not-allowed; opacity: 0.55;'">
                                <p class="font-bold text-sm" style="color: var(--text-dark);"
                                   x-text="table.table_number"></p>
                                <p class="text-xs mt-0.5"
                                   :style="table.status === 'available' ? 'color: hsl(145,55%,38%);' : 'color: hsl(24,10%,55%);'"
                                   x-text="table.status === 'available' ? 'Tersedia' : 'Terisi'"></p>
                            </button>
                        </template>
                    </div>
                    <p class="text-xs text-center mt-4" style="color: hsl(24,10%,60%);">
                        Meja terisi tidak dapat dipilih
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="px-4 pt-6 max-w-lg mx-auto" x-data="recentOrders()" x-init="init()">

        <!-- Orders from localStorage -->
        <template x-if="orders.length > 0">
            <div class="mb-6">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="font-bold text-base" style="color: var(--text-dark);">Pesanan Anda</h2>
                    <button @click="clearOrders()"
                            class="text-xs px-2 py-1 rounded-lg border"
                            style="border-color: var(--border); color: hsl(24,10%,55%);">Hapus Riwayat</button>
                </div>
                <div class="space-y-2">
                    <template x-for="o in orders" :key="o.orderNumber">
                        <a :href="'/order/' + o.orderNumber"
                           class="flex items-center gap-3 rounded-2xl border p-4 hover:opacity-80 transition-opacity"
                           style="background: white; border-color: hsl(35,25%,88%); text-decoration: none;">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
                                 style="background: hsl(35,45%,88%);">
                                <svg class="w-4 h-4" fill="none" stroke="hsl(24,35%,35%)" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-sm truncate" style="color: var(--text-dark);"
                                   x-text="o.orderNumber"></p>
                                <p class="text-xs mt-0.5" style="color: hsl(24,10%,55%);"
                                   x-text="o.customerName + ' · ' + formatDate(o.createdAt)"></p>
                            </div>
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="hsl(24,10%,60%)" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </template>
                </div>
            </div>
        </template>

        <!-- Manual lookup -->
        <div class="rounded-2xl border p-4 mb-6" style="border-color: hsl(35,25%,88%); background: white;">
            <p class="text-sm font-semibold mb-2" style="color: var(--text-dark);">Cek Status Pesanan</p>
            <p class="text-xs mb-3" style="color: hsl(24,10%,55%);">Masukkan nomor pesanan untuk melihat status</p>
            <div class="flex gap-2">
                <input x-model="searchNumber" type="text" placeholder="KTA-20250516-XXXX"
                       @keydown.enter="searchOrder()"
                       class="flex-1 border rounded-xl h-10 px-3 text-sm outline-none"
                       style="border-color: var(--border);" />
                <button @click="searchOrder()"
                        class="h-10 px-4 rounded-xl text-sm font-semibold shrink-0"
                        style="background: var(--bg-medium); color: var(--text-light);">Cek</button>
            </div>
            <p x-show="searchError" x-cloak x-text="searchError"
               class="text-xs mt-2" style="color: var(--danger);"></p>
        </div>
    </div>

    <!-- Features -->
    <div class="py-14 px-6 max-w-4xl mx-auto">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            @foreach([
                ['icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'Cepat & Mudah', 'desc' => 'Pesan dalam hitungan menit langsung dari ponsel Anda'],
                ['icon' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z', 'title' => 'Kopi Pilihan', 'desc' => 'Biji kopi premium dari petani lokal Nusantara'],
                ['icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z', 'title' => 'Lacak Pesanan', 'desc' => 'Pantau status pesanan Anda secara real-time'],
            ] as $f)
            <div class="rounded-2xl p-6 border" style="border-color: var(--border); background: var(--bg-light);">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-4"
                     style="background: hsl(35,45%,88%); color: hsl(24,35%,25%);">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $f['icon'] }}"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-base mb-1" style="color: var(--text-dark);">{{ $f['title'] }}</h3>
                <p class="text-sm leading-relaxed" style="color: var(--text-mid);">{{ $f['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Promo Banner -->
    <div class="mx-4 mb-10 rounded-2xl p-6 overflow-hidden relative"
         style="background: linear-gradient(135deg, hsl(24,35%,20%) 0%, hsl(35,60%,35%) 100%);">
        <div class="relative z-10">
            <span class="inline-block mb-3 text-xs font-semibold px-3 py-1 rounded-full"
                  style="background: var(--accent); color: hsl(24,10%,10%);">Promo Hari Ini</span>
            <h2 class="text-2xl font-bold mb-2" style="color: hsl(40,33%,98%);">Happy Hours</h2>
            <p class="text-sm mb-4" style="color: hsl(35,35%,75%);">Diskon 20% untuk semua minuman kopi pukul 14.00 – 16.00</p>
            <a href="{{ route('menu') }}"
               class="inline-block text-sm font-semibold px-4 py-2 rounded-lg transition-opacity hover:opacity-90"
               style="background: var(--accent); color: hsl(24,10%,10%);">Pesan Sekarang</a>
        </div>
        <div class="absolute -right-6 -top-6 w-32 h-32 rounded-full opacity-10" style="background: hsl(35,90%,70%);"></div>
        <div class="absolute -right-2 bottom-0 w-20 h-20 rounded-full opacity-10" style="background: hsl(45,80%,70%);"></div>
    </div>

    <!-- Categories -->
    <div class="px-4 pb-12">
        <h2 class="text-xl font-bold mb-5 px-2" style="color: var(--text-dark);">Kategori Menu</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            @php
                $categoryColors = [
                    'hsl(24,35%,25%)', 'hsl(185,50%,35%)', 'hsl(15,55%,40%)',
                    'hsl(35,70%,40%)', 'hsl(45,65%,42%)', 'hsl(160,45%,35%)',
                    'hsl(270,40%,40%)', 'hsl(200,50%,38%)',
                ];
                $categoryEmojis = [
                    'Coffee' => '☕', 'Non Coffee' => '🥤', 'Food' => '🍜',
                    'Snacks' => '🍪', 'Dessert' => '🍮', 'Tea' => '🍵',
                ];
            @endphp
            @forelse($categories as $index => $category)
            <a href="{{ route('menu') }}"
               class="rounded-xl p-4 text-left transition-transform active:scale-95 hover:opacity-90"
               style="background: {{ $categoryColors[$index % count($categoryColors)] }}; color: hsl(40,33%,97%);">
                <div class="text-2xl mb-2">{{ $categoryEmojis[$category->name] ?? '🍽️' }}</div>
                <div class="text-sm font-semibold">{{ $category->name }}</div>
            </a>
            @empty
            <p class="col-span-full text-center text-sm" style="color: hsl(24,10%,50%);">Belum ada kategori</p>
            @endforelse
        </div>
    </div>

    <!-- Footer -->
    <div class="border-t py-8 px-6 text-center" style="border-color: var(--border);">
        <div class="flex items-center justify-center gap-2 mb-2">
            <svg class="w-4 h-4" fill="none" stroke="var(--accent)" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8zM6 1v3M10 1v3M14 1v3"/>
            </svg>
            <span class="font-semibold text-sm" style="color: var(--text-dark);">Kopi Tiang Alam</span>
        </div>
        <p class="text-xs" style="color: hsl(24,10%,50%);">Menikmati kopi terbaik Nusantara</p>
    </div>
</div>

<script>
function dineInModal() {
    return {
        show: false,
        loading: false,
        tables: [],

        async open() {
            this.show = true;
            if (this.tables.length === 0) {
                this.loading = true;
                try {
                    const res = await fetch('/api/tables');
                    if (res.ok) this.tables = await res.json();
                } catch (e) {}
                this.loading = false;
            }
        },

        selectTable(tableNumber) {
            window.location.href = '/menu?table=' + tableNumber;
        },
    };
}

function recentOrders() {
    return {
        orders: [],
        searchNumber: '',
        searchError: '',

        init() {
            this.orders = JSON.parse(localStorage.getItem('kta_orders') || '[]');
        },

        clearOrders() {
            if (!confirm('Hapus semua riwayat pesanan?')) return;
            localStorage.removeItem('kta_orders');
            this.orders = [];
        },

        formatDate(iso) {
            const d = new Date(iso);
            return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
        },

        async searchOrder() {
            this.searchError = '';
            const num = this.searchNumber.trim().toUpperCase();
            if (!num) { this.searchError = 'Masukkan nomor pesanan terlebih dahulu'; return; }
            try {
                const res = await fetch('/api/orders/' + num);
                if (res.ok) {
                    window.location.href = '/order/' + num;
                } else {
                    this.searchError = 'Nomor pesanan tidak ditemukan';
                }
            } catch (e) {
                this.searchError = 'Gagal memeriksa pesanan. Coba lagi.';
            }
        },
    };
}
</script>
@endsection
