@extends('layouts.app')
@section('title', 'Keranjang')

@section('content')
<div class="min-h-screen" style="background: var(--bg-light);"
     x-data="cartPage('{{ $tableNumber ?? '' }}')"
     x-init="$store.cart.load()">

    <!-- Header -->
    <div class="sticky top-0 z-40 flex items-center gap-3 px-4 py-4 border-b shadow-sm"
         style="background: var(--bg-light); border-color: var(--border);">
        <a :href="'/menu' + (tableNumber ? '?table=' + tableNumber : '')">
            <svg class="w-5 h-5" fill="none" stroke="hsl(24,10%,20%)" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="font-bold text-lg" style="color: var(--text-dark);">Keranjang</h1>
        <span x-show="$store.cart.count > 0" x-cloak
              class="ml-auto text-sm" style="color: hsl(24,10%,50%);"
              x-text="$store.cart.count + ' item'"></span>
    </div>

    <!-- Empty cart -->
    <div x-show="$store.cart.items.length === 0"
         class="flex flex-col items-center justify-center py-24 text-center px-6">
        <div class="w-20 h-20 rounded-full flex items-center justify-center mb-6"
             style="background: hsl(35,35%,90%);">
            <svg class="w-10 h-10" fill="none" stroke="hsl(24,35%,50%)" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8zM6 1v3M10 1v3M14 1v3"/>
            </svg>
        </div>
        <h2 class="text-xl font-bold mb-2" style="color: hsl(24,10%,15%);">Keranjang kosong</h2>
        <p class="text-sm mb-8" style="color: hsl(24,10%,50%);">Belum ada item yang ditambahkan</p>
        <a :href="'/menu' + (tableNumber ? '?table=' + tableNumber : '')"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold"
           style="background: var(--accent); color: hsl(24,10%,10%);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            Lihat Menu
        </a>
    </div>

    <!-- Cart items -->
    <div x-show="$store.cart.items.length > 0" x-cloak>
        <div class="px-4 py-4 space-y-3 pb-48">
            <template x-for="item in $store.cart.items" :key="item.id">
                <div class="rounded-2xl p-4 flex gap-4 border"
                     style="background: var(--bg-light); border-color: var(--border);">
                    <div class="w-16 h-16 rounded-xl overflow-hidden shrink-0" style="background: hsl(35,35%,88%);">
                        <template x-if="item.image">
                            <img :src="item.image" :alt="item.name" class="w-full h-full object-cover" />
                        </template>
                        <template x-if="!item.image">
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-7 h-7" fill="none" stroke="hsl(24,35%,50%)" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8zM6 1v3M10 1v3M14 1v3"/>
                                </svg>
                            </div>
                        </template>
                    </div>

                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm leading-snug mb-1 truncate" style="color: var(--text-dark);" x-text="item.name"></p>
                        <p class="text-sm font-bold mb-3" style="color: hsl(35,90%,40%);"
                           x-text="'Rp ' + item.price.toLocaleString('id-ID')"></p>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <button @click="$store.cart.updateQuantity(item.id, item.quantity - 1)"
                                        class="w-7 h-7 rounded-full flex items-center justify-center border"
                                        style="border-color: hsl(35,25%,80%);">
                                    <svg class="w-3 h-3" fill="none" stroke="hsl(24,10%,30%)" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                    </svg>
                                </button>
                                <span class="w-7 text-center font-bold text-sm" style="color: var(--text-dark);" x-text="item.quantity"></span>
                                <button @click="$store.cart.updateQuantity(item.id, item.quantity + 1)"
                                        class="w-7 h-7 rounded-full flex items-center justify-center"
                                        style="background: var(--bg-medium); color: white;">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </button>
                            </div>
                            <p class="text-sm font-bold" style="color: var(--text-dark);"
                               x-text="'Rp ' + (item.price * item.quantity).toLocaleString('id-ID')"></p>
                        </div>
                    </div>

                    <button @click="$store.cart.removeFromCart(item.id)" class="self-start p-1">
                        <svg class="w-4 h-4" fill="none" stroke="var(--danger)" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </template>
        </div>

        <!-- Sticky checkout bar -->
        <div class="fixed bottom-0 left-0 right-0 border-t shadow-2xl px-4 py-5"
             style="background: var(--bg-light); border-color: var(--border);">
            <div class="flex items-center justify-between mb-4">
                <span class="font-medium" style="color: hsl(24,10%,40%);">Total</span>
                <span class="text-xl font-bold" style="color: var(--text-dark);"
                      x-text="'Rp ' + $store.cart.total.toLocaleString('id-ID')"></span>
            </div>
            <a :href="'/checkout' + (tableNumber ? '?table=' + tableNumber : '')"
               class="flex items-center justify-center w-full h-13 py-3.5 rounded-xl font-semibold text-base"
               style="background: var(--bg-medium); color: var(--text-light);">
                Lanjut ke Checkout
            </a>
        </div>
    </div>
</div>

<script>
function cartPage(tableNumber) {
    return {
        tableNumber: tableNumber || null,
    };
}
</script>
@endsection
