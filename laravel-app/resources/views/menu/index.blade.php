@extends('layouts.app')
@section('title', 'Menu')

@section('content')
<div class="min-h-screen" style="background: var(--bg-light);"
     x-data="menuPage('{{ $tableNumber ?? '' }}')" x-init="init()">

    <!-- Header -->
    <div class="sticky top-0 z-40 shadow-sm" style="background: var(--bg-medium);">
        <div class="flex items-center gap-3 px-4 py-3">
            <a href="{{ route('home') }}">
                <svg class="w-5 h-5" fill="none" stroke="hsl(40,33%,98%)" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="flex-1">
                <h1 class="font-bold text-base leading-none" style="color: hsl(40,33%,98%);">Menu</h1>
                @if($tableNumber)
                    <p class="text-xs mt-0.5" style="color: hsl(35,45%,70%);">Meja {{ $tableNumber }}</p>
                @endif
            </div>
            <a :href="'{{ route('cart') }}' + (tableNumber ? '?table=' + tableNumber : '')"
               class="relative p-2 rounded-xl" style="background: var(--accent);">
                <svg class="w-5 h-5" fill="none" stroke="hsl(24,10%,10%)" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span x-show="cartCount > 0" x-cloak x-text="cartCount"
                      class="absolute -top-1 -right-1 w-5 h-5 rounded-full text-xs font-bold flex items-center justify-center"
                      style="background: var(--danger); color: white;"></span>
            </a>
        </div>

        <!-- Search -->
        <div class="px-4 pb-3">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" fill="none" stroke="hsl(35,30%,55%)" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input x-model="searchQuery" @input.debounce.300ms="fetchProducts()"
                       type="text" placeholder="Cari menu..."
                       class="w-full pl-9 pr-4 border-0 text-sm h-10 rounded-xl outline-none"
                       style="background: hsl(24,35%,18%); color: hsl(40,33%,92%);" />
            </div>
        </div>

        <!-- Category tabs -->
        <div class="flex gap-2 px-4 pb-3 overflow-x-auto" style="-ms-overflow-style: none; scrollbar-width: none;">
            <button @click="selectedCategory = null; fetchProducts()"
                    class="shrink-0 px-4 py-1.5 rounded-full text-sm font-medium transition-colors"
                    :style="selectedCategory === null ? 'background: var(--accent); color: hsl(24,10%,10%);' : 'background: hsl(24,35%,20%); color: hsl(35,45%,70%);'">
                Semua
            </button>
            <template x-for="cat in categories" :key="cat.id">
                <button @click="selectedCategory = cat.id; fetchProducts()"
                        class="shrink-0 px-4 py-1.5 rounded-full text-sm font-medium transition-colors"
                        :style="selectedCategory === cat.id ? 'background: var(--accent); color: hsl(24,10%,10%);' : 'background: hsl(24,35%,20%); color: hsl(35,45%,70%);'"
                        x-text="cat.name"></button>
            </template>
        </div>
    </div>

    <!-- Product Grid -->
    <div class="px-4 py-4 pb-24">

        <!-- Loading skeleton -->
        <div x-show="loading" class="grid grid-cols-2 gap-3">
            <template x-for="i in 6" :key="i">
                <div class="rounded-2xl overflow-hidden border animate-pulse" style="border-color: var(--border);">
                    <div class="aspect-square w-full" style="background: hsl(35,25%,88%);"></div>
                    <div class="p-3">
                        <div class="h-4 w-3/4 rounded mb-2" style="background: hsl(35,25%,88%);"></div>
                        <div class="h-3 w-1/2 rounded" style="background: hsl(35,25%,88%);"></div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty state -->
        <div x-show="!loading && products.length === 0" x-cloak
             class="flex flex-col items-center justify-center py-20 text-center">
            <svg class="w-12 h-12 mb-4" fill="none" stroke="hsl(35,25%,70%)" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8zM6 1v3M10 1v3M14 1v3"/>
            </svg>
            <p class="font-medium" style="color: hsl(24,10%,40%);">Menu tidak ditemukan</p>
            <p class="text-sm mt-1" style="color: hsl(24,10%,60%);">Coba kata kunci lain</p>
        </div>

        <!-- Product cards -->
        <div x-show="!loading && products.length > 0" x-cloak class="grid grid-cols-2 gap-3">
            <template x-for="product in products" :key="product.id">
                <a :href="'/product/' + product.id + (tableNumber ? '?table=' + tableNumber : '')"
                   class="rounded-2xl overflow-hidden border text-left transition-transform active:scale-95 block"
                   style="border-color: var(--border); background: var(--bg-light);">
                    <div class="aspect-square overflow-hidden" style="background: hsl(35,35%,90%);">
                        <template x-if="product.image">
                            <img :src="product.image" :alt="product.name" class="w-full h-full object-cover" />
                        </template>
                        <template x-if="!product.image">
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-10 h-10" fill="none" stroke="hsl(24,35%,50%)" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8zM6 1v3M10 1v3M14 1v3"/>
                                </svg>
                            </div>
                        </template>
                    </div>
                    <div class="p-3">
                        <p class="font-semibold text-sm leading-snug mb-1" style="color: var(--text-dark); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;" x-text="product.name"></p>
                        <template x-if="product.categoryName">
                            <span class="inline-block text-xs px-1.5 py-0 rounded mb-1" style="background: hsl(35,25%,88%); color: hsl(24,10%,40%);" x-text="product.categoryName"></span>
                        </template>
                        <p class="font-bold text-sm" style="color: hsl(35,90%,40%);"
                           x-text="'Rp ' + product.price.toLocaleString('id-ID')"></p>
                        <p x-show="product.stock === 0" class="text-xs mt-1" style="color: var(--danger);">Habis</p>
                    </div>
                </a>
            </template>
        </div>
    </div>

    <!-- Cart FAB -->
    <div x-show="cartCount > 0" x-cloak class="fixed bottom-6 left-4 right-4">
        <a :href="'{{ route('cart') }}' + (tableNumber ? '?table=' + tableNumber : '')"
           class="flex items-center justify-center gap-2 w-full h-14 rounded-2xl font-semibold text-base shadow-xl"
           style="background: var(--bg-medium); color: var(--text-light);">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span x-text="'Lihat Keranjang (' + cartCount + ' item)'"></span>
        </a>
    </div>
</div>

<script>
function menuPage(tableNumber) {
    return {
        tableNumber: tableNumber || null,
        categories: [],
        products: [],
        selectedCategory: null,
        searchQuery: '',
        loading: true,

        get cartCount() {
            return Alpine.store('cart').count;
        },

        async init() {
            await Promise.all([this.fetchCategories(), this.fetchProducts()]);
        },

        async fetchCategories() {
            const res = await fetch('/api/categories');
            this.categories = await res.json();
        },

        async fetchProducts() {
            this.loading = true;
            const params = new URLSearchParams();
            if (this.selectedCategory) params.set('category_id', this.selectedCategory);
            if (this.searchQuery) params.set('search', this.searchQuery);

            const res = await fetch('/api/products?' + params.toString());
            this.products = await res.json();
            this.loading = false;
        },
    };
}
</script>
@endsection
