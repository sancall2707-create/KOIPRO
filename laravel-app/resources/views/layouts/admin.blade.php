<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'Admin') — Kopi Tiang Alam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --bg-dark:   hsl(24,35%,18%);
            --bg-medium: hsl(24,35%,25%);
            --bg-light:  hsl(40,33%,98%);
            --accent:    hsl(35,90%,50%);
            --text-dark: hsl(24,10%,10%);
            --text-mid:  hsl(24,10%,40%);
            --text-light:hsl(40,33%,98%);
            --border:    hsl(35,25%,85%);
            --success:   hsl(145,65%,42%);
            --danger:    hsl(0,84%,60%);
        }
        [x-cloak] { display: none !important; }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body style="background: hsl(35,20%,96%);">

<div class="min-h-screen flex" x-data="{ sidebarOpen: false }">

    <!-- Mobile overlay -->
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
         class="fixed inset-0 z-30 lg:hidden"
         style="background: rgba(0,0,0,0.5)"></div>

    <!-- Sidebar -->
    <aside class="fixed top-0 left-0 h-full z-40 w-64 flex flex-col transition-transform duration-200 lg:translate-x-0 lg:relative lg:z-auto"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           style="background: var(--bg-dark);">

        <div class="flex items-center gap-2 px-5 py-5 border-b" style="border-color: hsl(24,30%,25%);">
            <div class="p-1.5 rounded-lg" style="background: var(--accent);">
                <svg class="w-4 h-4" fill="none" stroke="hsl(24,10%,10%)" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8zM6 1v3M10 1v3M14 1v3"/>
                </svg>
            </div>
            <div>
                <p class="font-bold text-sm leading-none" style="color: var(--text-light);">Kopi Tiang Alam</p>
                <p class="text-xs mt-0.5" style="color: hsl(35,30%,55%);">Admin Panel</p>
            </div>
            <button class="ml-auto lg:hidden" @click="sidebarOpen = false">
                <svg class="w-5 h-5" fill="none" stroke="hsl(35,30%,60%)" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            @php
                $navItems = [
                    ['route' => 'admin.dashboard',   'label' => 'Dashboard',  'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                    ['route' => 'admin.orders',      'label' => 'Pesanan',    'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
                    ['route' => 'admin.products',    'label' => 'Produk',     'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                    ['route' => 'admin.categories',  'label' => 'Kategori',   'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'],
                    ['route' => 'admin.tables',      'label' => 'Meja',       'icon' => 'M3 10h18M3 14h18M10 10v8m4-8v8M5 6l1-2h12l1 2'],
                    ['route' => 'admin.analytics',   'label' => 'Analisis',   'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                ];
                $current = request()->route()->getName();
            @endphp
            @foreach($navItems as $item)
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors"
                   style="{{ $current === $item['route'] ? 'background: var(--accent); color: var(--text-dark);' : 'color: hsl(35,30%,65%);' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                    </svg>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="px-3 py-4 border-t" style="border-color: hsl(24,30%,25%);">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium w-full hover:opacity-80" style="color: hsl(0,70%,65%);">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- Main content -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Mobile topbar -->
        <div class="flex items-center gap-3 px-4 py-3 border-b lg:hidden"
             style="background: var(--bg-dark); border-color: hsl(24,30%,25%);">
            <button @click="sidebarOpen = true">
                <svg class="w-5 h-5" fill="none" stroke="hsl(40,33%,90%)" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <span class="font-semibold text-sm" style="color: var(--text-light);">@yield('title', 'Admin')</span>
        </div>

        <main class="flex-1 overflow-auto p-4 lg:p-6">
            @yield('content')
        </main>

        <!-- Order Notification System -->
        <div x-data="orderNotification()" x-init="init()" x-cloak>
            <!-- Notification popup -->
            <div x-show="showNotif" x-transition
                 class="fixed top-4 right-4 z-50 max-w-sm w-full rounded-2xl border p-4 shadow-lg"
                 style="background: white; border-color: hsl(35,90%,50%);">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 animate-pulse"
                         style="background: hsl(35,90%,50%);">
                        <svg class="w-5 h-5" fill="none" stroke="white" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-sm" style="color: var(--text-dark);">Pesanan Baru Masuk!</p>
                        <p class="text-xs mt-1" style="color: var(--text-mid);" x-text="notifMessage"></p>
                    </div>
                    <button @click="showNotif = false" class="shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="hsl(24,10%,50%)" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <a href="{{ route('admin.orders') }}" class="block mt-3 text-center text-xs font-semibold py-2 rounded-xl"
                   style="background: hsl(35,90%,50%); color: hsl(24,10%,10%);">
                    Lihat Pesanan
                </a>
            </div>
        </div>

        <script>
        function orderNotification() {
            return {
                lastOrderCount: null,
                showNotif: false,
                notifMessage: '',
                audioCtx: null,

                init() {
                    this.checkNewOrders();
                    setInterval(() => this.checkNewOrders(), 10000);
                },

                async checkNewOrders() {
                    try {
                        const res = await fetch('/admin/api/orders?status=pending');
                        if (!res.ok) return;
                        const orders = await res.json();
                        const currentCount = orders.length;

                        if (this.lastOrderCount !== null && currentCount > this.lastOrderCount) {
                            const newCount = currentCount - this.lastOrderCount;
                            this.notifMessage = newCount + ' pesanan baru menunggu konfirmasi';
                            this.showNotif = true;
                            this.playSound();
                            setTimeout(() => this.showNotif = false, 8000);
                        }
                        this.lastOrderCount = currentCount;
                    } catch (e) {}
                },

                playSound() {
                    try {
                        // Play loud bell sound first
                        const ctx = new (window.AudioContext || window.webkitAudioContext)();
                        const notes = [880, 1100, 1320, 1100, 880];
                        notes.forEach((freq, i) => {
                            const osc = ctx.createOscillator();
                            const gain = ctx.createGain();
                            osc.connect(gain);
                            gain.connect(ctx.destination);
                            osc.frequency.value = freq;
                            osc.type = 'square';
                            gain.gain.setValueAtTime(0.5, ctx.currentTime + i * 0.12);
                            gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + i * 0.12 + 0.3);
                            osc.start(ctx.currentTime + i * 0.12);
                            osc.stop(ctx.currentTime + i * 0.12 + 0.3);
                        });

                        // Then speak "ADA ORDERAN MASUK"
                        setTimeout(() => {
                            const utterance = new SpeechSynthesisUtterance('ADA ORDERAN MASUK');
                            utterance.lang = 'id-ID';
                            utterance.volume = 1;
                            utterance.rate = 0.9;
                            utterance.pitch = 1.2;
                            speechSynthesis.speak(utterance);
                        }, 700);
                    } catch (e) {}
                }
            };
        }
        </script>
    </div>


</body>
</html>
