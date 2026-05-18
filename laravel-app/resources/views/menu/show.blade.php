@extends('layouts.app')
@section('title', 'Detail Produk')

@section('content')
<div class="min-h-screen" style="background: var(--bg-light);"
     x-data="productPage({{ $productId }}, '{{ $tableNumber ?? '' }}')" x-init="init()">

    <!-- Header -->
    <div class="sticky top-0 z-40 flex items-center gap-3 px-4 py-4 border-b shadow-sm"
         style="background: var(--bg-light); border-color: var(--border);">
        <a :href="'/menu' + (tableNumber ? '?table=' + tableNumber : '')">
            <svg class="w-5 h-5" fill="none" stroke="hsl(24,10%,20%)" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="font-bold text-lg" style="color: var(--text-dark);">Detail Produk</h1>
    </div>

    <!-- Loading -->
    <div x-show="loading" class="p-4 animate-pulse">
        <div class="aspect-square rounded-2xl mb-4" style="background: hsl(35,25%,88%);"></div>
        <div class="h-6 w-3/4 rounded mb-2" style="background: hsl(35,25%,88%);"></div>
        <div class="h-4 w-1/2 rounded" style="background: hsl(35,25%,88%);"></div>
    </div>

    <!-- Product detail -->
    <div x-show="!loading && product" x-cloak>
        <div class="aspect-square overflow-hidden" style="background: hsl(35,35%,90%);">
            <template x-if="product && product.image">
                <img :src="product.image" :alt="product.name" class="w-full h-full object-cover" />
            </template>
            <template x-if="!product || !product.image">
                <div class="w-full h-full flex items-center justify-center">
                    <svg class="w-24 h-24" fill="none" stroke="hsl(24,35%,50%)" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8zM6 1v3M10 1v3M14 1v3"/>
                    </svg>
                </div>
            </template>
        </div>

        <div class="px-4 py-5 pb-32">
            <template x-if="product && product.categoryName">
                <span class="inline-block text-xs px-2 py-0.5 rounded-full mb-2"
                      style="background: hsl(35,45%,88%); color: hsl(24,35%,25%);" x-text="product && product.categoryName"></span>
            </template>
            <h1 class="text-2xl font-bold mb-2" style="color: var(--text-dark);" x-text="product && product.name"></h1>
            <p class="text-2xl font-bold mb-4" style="color: hsl(35,90%,40%);"
               x-text="product ? 'Rp ' + product.price.toLocaleString('id-ID') : ''"></p>
            <p class="text-sm leading-relaxed mb-6" style="color: var(--text-mid);"
               x-text="product && product.description ? product.description : 'Tidak ada deskripsi.'"></p>

            <template x-if="product && product.stock === 0">
                <div class="rounded-xl p-3 text-sm font-medium text-center mb-4"
                     style="background: hsl(0,84%,95%); color: hsl(0,84%,45%);">Stok Habis</div>
            </template>

            <template x-if="product && product.stock > 0">
                <div>
                    <!-- Qty selector -->
                    <div class="flex items-center justify-between mb-6">
                        <span class="font-medium" style="color: var(--text-dark);">Jumlah</span>
                        <div class="flex items-center gap-3">
                            <button @click="qty > 1 && qty--"
                                    class="w-9 h-9 rounded-full border flex items-center justify-center"
                                    style="border-color: var(--border);">
                                <svg class="w-4 h-4" fill="none" stroke="hsl(24,10%,30%)" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                </svg>
                            </button>
                            <span class="w-8 text-center font-bold text-lg" style="color: var(--text-dark);" x-text="qty"></span>
                            <button @click="qty++"
                                    class="w-9 h-9 rounded-full flex items-center justify-center"
                                    style="background: var(--bg-medium); color: white;">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Add to cart bar -->
    <div x-show="product && product.stock > 0" x-cloak class="fixed bottom-0 left-0 right-0 border-t shadow-2xl px-4 py-4"
         style="background: var(--bg-light); border-color: var(--border);">
        <div class="flex items-center justify-between mb-3">
            <span style="color: var(--text-mid);">Subtotal</span>
            <span class="text-xl font-bold" style="color: var(--text-dark);"
                  x-text="product ? 'Rp ' + (product.price * qty).toLocaleString('id-ID') : ''"></span>
        </div>
        <button @click="addToCart()"
                class="w-full h-13 py-3 rounded-xl font-semibold text-base"
                style="background: var(--bg-medium); color: var(--text-light);">
            Tambah ke Keranjang
        </button>
    </div>
</div>

<script>
function productPage(productId, tableNumber) {
    return {
        tableNumber: tableNumber || null,
        product: null,
        loading: true,
        qty: 1,
        added: false,

        async init() {
            const res = await fetch('/api/products/' + productId);
            if (res.ok) this.product = await res.json();
            this.loading = false;
        },

        addToCart() {
            if (!this.product) return;
            Alpine.store('cart').addToCart(this.product, this.qty);
            window.location.href = '/cart' + (this.tableNumber ? '?table=' + this.tableNumber : '');
        },
    };
}
</script>
@endsection
