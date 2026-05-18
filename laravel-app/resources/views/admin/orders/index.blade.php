@extends('layouts.admin')
@section('title', 'Pesanan')

@section('content')
<div class="max-w-4xl mx-auto space-y-4"
     x-data="ordersPage()" x-init="init()">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold" style="color: var(--text-dark);">Manajemen Pesanan</h1>
            <p class="text-sm" style="color: var(--text-mid);">Kelola semua pesanan masuk</p>
        </div>
        <select x-model="filterStatus" @change="fetchOrders()"
                class="text-sm border rounded-xl h-9 px-3 outline-none"
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

    <!-- Loading -->
    <div x-show="loading" class="space-y-2">
        <template x-for="i in 5" :key="i">
            <div class="h-20 rounded-2xl animate-pulse" style="background: hsl(35,25%,88%);"></div>
        </template>
    </div>

    <!-- Empty -->
    <div x-show="!loading && orders.length === 0" x-cloak
         class="rounded-2xl border p-10 text-center" style="background: white; border-color: hsl(35,25%,88%);">
        <p class="text-sm" style="color: var(--text-mid);">Tidak ada pesanan</p>
    </div>

    <!-- Orders list -->
    <div x-show="!loading && orders.length > 0" x-cloak class="space-y-3">
        <template x-for="order in orders" :key="order.id">
            <div class="rounded-2xl border p-4" style="background: white; border-color: hsl(35,25%,88%);">
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div>
                        <p class="font-semibold text-sm" style="color: var(--text-dark);" x-text="order.orderNumber"></p>
                        <p class="text-xs mt-0.5" style="color: var(--text-mid);"
                           x-text="order.customerName + (order.tableNumber ? ' — Meja ' + order.tableNumber : ' — Takeaway')"></p>
                        <p class="text-xs mt-0.5" style="color: hsl(24,10%,55%);"
                           x-text="new Date(order.createdAt).toLocaleString('id-ID')"></p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="text-xs px-2 py-1 rounded-full font-medium"
                              :style="statusStyle(order.status)"
                              x-text="statusLabel(order.status)"></span>
                        <select @change="updateStatus(order.id, $event.target.value)"
                                :value="order.status"
                                class="text-xs border rounded-lg h-7 px-1.5 outline-none"
                                style="border-color: var(--border);">
                            <option value="pending">Menunggu</option>
                            <option value="processing">Diproses</option>
                            <option value="preparing">Dibuat</option>
                            <option value="ready">Siap</option>
                            <option value="completed">Selesai</option>
                            <option value="cancelled">Batal</option>
                        </select>
                    </div>
                </div>
                <div class="text-xs space-y-1 pt-2 border-t" style="border-color: hsl(35,25%,90%);">
                    <template x-for="item in order.items" :key="item.id">
                        <div class="flex justify-between">
                            <span style="color: hsl(24,10%,40%);" x-text="item.productName + ' x' + item.quantity"></span>
                            <span style="color: var(--text-dark);" x-text="'Rp ' + item.subtotal.toLocaleString('id-ID')"></span>
                        </div>
                    </template>
                    <div class="flex justify-between font-semibold pt-1 border-t" style="border-color: hsl(35,25%,90%);">
                        <span style="color: var(--text-dark);">Total</span>
                        <span style="color: hsl(35,90%,40%);" x-text="'Rp ' + order.totalPrice.toLocaleString('id-ID')"></span>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>

<script>
function ordersPage() {
    return {
        orders: [],
        filterStatus: '',
        loading: true,

        async init() {
            await this.fetchOrders();
        },

        async fetchOrders() {
            this.loading = true;
            const params = this.filterStatus ? '?status=' + this.filterStatus : '';
            const res = await fetch('/admin/api/orders' + params);
            if (res.ok) this.orders = await res.json();
            this.loading = false;
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
            await this.fetchOrders();
        },

        statusLabel(s) {
            const map = { pending:'Menunggu', processing:'Diproses', preparing:'Dibuat', ready:'Siap', completed:'Selesai', cancelled:'Batal' };
            return map[s] || s;
        },

        statusStyle(s) {
            const map = {
                pending: 'background: hsl(35,90%,90%); color: hsl(35,90%,30%);',
                processing: 'background: hsl(210,80%,90%); color: hsl(210,80%,30%);',
                preparing: 'background: hsl(270,70%,92%); color: hsl(270,70%,35%);',
                ready: 'background: hsl(145,65%,88%); color: hsl(145,55%,30%);',
                completed: 'background: hsl(145,65%,88%); color: hsl(145,55%,30%);',
                cancelled: 'background: hsl(0,84%,93%); color: hsl(0,84%,40%);',
            };
            return map[s] || '';
        },
    };
}
</script>
@endsection
