@extends('layouts.app')
@section('title', 'Checkout')

@section('content')
<div class="min-h-screen" style="background: var(--bg-light);"
     x-data="checkoutPage('{{ $tableNumber ?? '' }}')" x-init="init()">

    <!-- Header -->
    <div class="sticky top-0 z-40 flex items-center gap-3 px-4 py-4 border-b shadow-sm"
         style="background: var(--bg-light); border-color: var(--border);">
        <a :href="'/cart' + (tableNumber ? '?table=' + tableNumber : '')">
            <svg class="w-5 h-5" fill="none" stroke="hsl(24,10%,20%)" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="font-bold text-lg" style="color: var(--text-dark);">Checkout</h1>
    </div>

    <!-- Empty cart redirect -->
    <div x-show="$store.cart.items.length === 0"
         class="flex flex-col items-center justify-center min-h-screen p-6 text-center">
        <p style="color: hsl(24,10%,40%);">Keranjang kosong</p>
        <a href="{{ route('menu') }}" class="mt-4 px-5 py-2 rounded-xl font-semibold"
           style="background: var(--bg-medium); color: var(--text-light);">Ke Menu</a>
    </div>

    <div x-show="$store.cart.items.length > 0" x-cloak class="px-4 py-5 pb-32">

        <!-- Order info -->
        <div class="rounded-2xl border p-4 mb-5" style="border-color: var(--border); background: var(--bg-light);">
            <h2 class="font-semibold text-base mb-3" style="color: var(--text-dark);">Info Pesanan</h2>
            <div class="flex gap-3 text-sm">
                <div class="flex-1 rounded-xl p-3 text-center"
                     :style="tableNumber ? 'background: var(--bg-medium);' : 'background: hsl(35,35%,90%);'">
                    <p class="font-bold"
                       :style="tableNumber ? 'color: var(--text-light);' : 'color: hsl(24,10%,50%);'"
                       x-text="tableNumber ? 'Meja ' + tableNumber : 'Dine In'"></p>
                    <p class="text-xs mt-0.5"
                       :style="tableNumber ? 'color: hsl(35,45%,70%);' : 'color: hsl(24,10%,60%);'"
                       x-text="tableNumber ? 'Dine In' : '-'"></p>
                </div>
                <div class="flex-1 rounded-xl p-3 text-center"
                     :style="!tableNumber ? 'background: var(--bg-medium);' : 'background: hsl(35,35%,90%);'">
                    <p class="font-bold"
                       :style="!tableNumber ? 'color: var(--text-light);' : 'color: hsl(24,10%,50%);'">Takeaway</p>
                    <p class="text-xs mt-0.5"
                       :style="!tableNumber ? 'color: hsl(35,45%,70%);' : 'color: hsl(24,10%,60%);'"
                       x-text="!tableNumber ? 'Aktif' : '-'"></p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form @submit.prevent="submitOrder()">
            <div class="rounded-2xl border p-4 mb-4" style="border-color: var(--border); background: var(--bg-light);">
                <h2 class="font-semibold text-base mb-4" style="color: var(--text-dark);">Detail Pemesan</h2>
                <div class="mb-4">
                    <label class="block text-sm mb-1.5" style="color: hsl(24,10%,30%);">Nama</label>
                    <input x-model="customerName" type="text" placeholder="Masukkan nama Anda"
                           class="w-full border rounded-xl h-11 px-3 text-sm outline-none"
                           style="border-color: var(--border);" required />
                    <p x-show="errors.customerName" x-cloak x-text="errors.customerName"
                       class="text-xs mt-1" style="color: var(--danger);"></p>
                </div>
                <div>
                    <label class="block text-sm mb-1.5" style="color: hsl(24,10%,30%);">Catatan (opsional)</label>
                    <textarea x-model="notes" placeholder="Contoh: tanpa gula, extra shot..."
                              rows="3" class="w-full border rounded-xl px-3 py-2 text-sm outline-none resize-none"
                              style="border-color: var(--border);"></textarea>
                </div>
            </div>

            <!-- Order summary -->
            <div class="rounded-2xl border p-4 mb-4" style="border-color: var(--border); background: var(--bg-light);">
                <h2 class="font-semibold text-base mb-3" style="color: var(--text-dark);">Ringkasan Pesanan</h2>
                <div class="space-y-2">
                    <template x-for="item in $store.cart.items" :key="item.id">
                        <div class="flex justify-between text-sm">
                            <span style="color: hsl(24,10%,30%);" x-text="item.name + ' x' + item.quantity"></span>
                            <span class="font-medium" style="color: var(--text-dark);"
                                  x-text="'Rp ' + (item.price * item.quantity).toLocaleString('id-ID')"></span>
                        </div>
                    </template>
                </div>
                <div class="border-t my-3" style="border-color: var(--border);"></div>
                <div class="flex justify-between font-bold">
                    <span style="color: var(--text-dark);">Total</span>
                    <span style="color: hsl(35,90%,40%);"
                          x-text="'Rp ' + $store.cart.total.toLocaleString('id-ID')"></span>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" :disabled="submitting"
                        class="w-full py-3.5 rounded-xl font-semibold text-base disabled:opacity-60"
                        style="background: var(--bg-medium); color: var(--text-light);">
                    <span x-text="submitting ? 'Memproses...' : 'Buat Pesanan'"></span>
                </button>
            </div>

            <p x-show="errors.general" x-cloak x-text="errors.general"
               class="text-center text-sm mt-3" style="color: var(--danger);"></p>
        </form>
    </div>
</div>

<script>
function checkoutPage(tableNumber) {
    return {
        tableNumber: tableNumber || null,
        customerName: '',
        notes: '',
        submitting: false,
        errors: {},

        init() {},

        async submitOrder() {
            this.errors = {};
            if (!this.customerName.trim()) {
                this.errors.customerName = 'Nama wajib diisi';
                return;
            }
            if (Alpine.store('cart').items.length === 0) return;

            this.submitting = true;
            try {
                const res = await fetch('/api/orders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        customerName: this.customerName,
                        tableNumber: this.tableNumber,
                        orderType: this.tableNumber ? 'dine_in' : 'takeaway',
                        notes: this.notes || null,
                        items: Alpine.store('cart').items.map(i => ({
                            productId: i.id,
                            quantity: i.quantity,
                        })),
                    }),
                });

                if (!res.ok) {
                    const err = await res.json();
                    this.errors.general = err.error || 'Gagal membuat pesanan. Silakan coba lagi.';
                    return;
                }

                const order = await res.json();
                Alpine.store('cart').clearCart();
                const saved = JSON.parse(localStorage.getItem('kta_orders') || '[]');
                saved.unshift({ orderNumber: order.orderNumber, customerName: this.customerName, createdAt: new Date().toISOString() });
                localStorage.setItem('kta_orders', JSON.stringify(saved.slice(0, 5)));
                window.location.href = '/order/' + order.orderNumber;
            } catch (e) {
                this.errors.general = 'Terjadi kesalahan. Silakan coba lagi.';
            } finally {
                this.submitting = false;
            }
        },
    };
}
</script>
@endsection
