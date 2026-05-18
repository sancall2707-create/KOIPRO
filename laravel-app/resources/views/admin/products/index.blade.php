@extends('layouts.admin')
@section('title', 'Produk')

@section('content')
<div class="max-w-4xl mx-auto space-y-4"
     x-data="productsPage()" x-init="init()">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold" style="color: var(--text-dark);">Manajemen Produk</h1>
            <p class="text-sm" style="color: var(--text-mid);">Kelola menu dan stok produk</p>
        </div>
        <button @click="openCreate()"
                class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-medium"
                style="background: var(--bg-medium); color: var(--text-light);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah
        </button>
    </div>

    <!-- Loading -->
    <div x-show="loading" class="space-y-2">
        <template x-for="i in 4" :key="i">
            <div class="h-16 rounded-2xl animate-pulse" style="background: hsl(35,25%,88%);"></div>
        </template>
    </div>

    <!-- Empty -->
    <div x-show="!loading && products.length === 0" x-cloak
         class="rounded-2xl border p-10 text-center" style="background: white; border-color: hsl(35,25%,88%);">
        <p class="text-sm" style="color: var(--text-mid);">Belum ada produk</p>
    </div>

    <!-- Products list -->
    <div x-show="!loading && products.length > 0" x-cloak class="space-y-2">
        <template x-for="p in products" :key="p.id">
            <div class="rounded-2xl border p-4 flex items-center gap-4"
                 style="background: white; border-color: hsl(35,25%,88%);">
                <div class="w-12 h-12 rounded-xl overflow-hidden shrink-0" style="background: hsl(35,35%,88%);">
                    <template x-if="p.image">
                        <img :src="p.image" :alt="p.name" class="w-full h-full object-cover" />
                    </template>
                    <template x-if="!p.image">
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="hsl(24,35%,50%)" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M18 8h1a4 4 0 010 8h-1M2 8h16v9a4 4 0 01-4 4H6a4 4 0 01-4-4V8zM6 1v3M10 1v3M14 1v3"/>
                            </svg>
                        </div>
                    </template>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-sm" style="color: var(--text-dark);" x-text="p.name"></p>
                    <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                        <span class="text-xs" style="color: hsl(35,90%,40%);"
                              x-text="'Rp ' + p.price.toLocaleString('id-ID')"></span>
                        <span class="text-xs" style="color: hsl(24,10%,55%);" x-text="'· Stok: ' + p.stock"></span>
                        <template x-if="p.categoryName">
                            <span class="text-xs px-1.5 py-0 rounded"
                                  style="background: hsl(35,25%,88%); color: hsl(24,10%,40%);" x-text="p.categoryName"></span>
                        </template>
                        <span class="text-xs px-1.5 py-0 rounded"
                              :style="p.status === 'active' ? 'background: hsl(145,65%,85%); color: hsl(145,55%,30%);' : 'background: hsl(0,0%,88%); color: hsl(24,10%,45%);'"
                              x-text="p.status === 'active' ? 'Aktif' : 'Nonaktif'"></span>
                    </div>
                </div>
                <div class="flex gap-1 shrink-0">
                    <button @click="openEdit(p)"
                            class="w-8 h-8 rounded-lg flex items-center justify-center border"
                            style="border-color: hsl(35,25%,85%);">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="hsl(24,10%,40%)" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    <button @click="deleteProduct(p.id)"
                            class="w-8 h-8 rounded-lg flex items-center justify-center border"
                            style="border-color: hsl(35,25%,85%);">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="var(--danger)" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </template>
    </div>

<!-- Modal -->
<div x-show="dialogOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4"
     style="background: rgba(0,0,0,0.5);">
    <div class="w-full max-w-sm rounded-2xl p-5 max-h-[85vh] overflow-y-auto" style="background: white;"
         @click.stop>
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-bold text-base" style="color: var(--text-dark);"
                x-text="editing ? 'Edit Produk' : 'Tambah Produk'"></h2>
            <button @click="dialogOpen = false">
                <svg class="w-5 h-5" fill="none" stroke="hsl(24,10%,50%)" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form @submit.prevent="saveProduct()" class="space-y-3">
            <div>
                <label class="block text-sm mb-1" style="color: hsl(24,10%,30%);">Nama</label>
                <input x-model="form.name" type="text" placeholder="Nama produk"
                       class="w-full border rounded-xl h-10 px-3 text-sm outline-none"
                       style="border-color: var(--border);" required />
            </div>
            <div>
                <label class="block text-sm mb-1" style="color: hsl(24,10%,30%);">Kategori</label>
                <select x-model="form.categoryId" class="w-full border rounded-xl h-10 px-3 text-sm outline-none"
                        style="border-color: var(--border);">
                    <option value="">Tanpa Kategori</option>
                    <template x-for="cat in categories" :key="cat.id">
                        <option :value="cat.id" x-text="cat.name"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="block text-sm mb-1" style="color: hsl(24,10%,30%);">Deskripsi</label>
                <textarea x-model="form.description" placeholder="Deskripsi produk" rows="2"
                          class="w-full border rounded-xl px-3 py-2 text-sm outline-none resize-none"
                          style="border-color: var(--border);"></textarea>
            </div>
            <div>
                <label class="block text-sm mb-1" style="color: hsl(24,10%,30%);">Harga (Rp)</label>
                <input x-model="form.price" type="number" min="0" placeholder="25000"
                       class="w-full border rounded-xl h-10 px-3 text-sm outline-none"
                       style="border-color: var(--border);" required />
            </div>
            <div>
                <label class="block text-sm mb-1" style="color: hsl(24,10%,30%);">URL Gambar</label>
                <input x-model="form.image" type="url" placeholder="https://..."
                       class="w-full border rounded-xl h-10 px-3 text-sm outline-none"
                       style="border-color: var(--border);" />
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm mb-1" style="color: hsl(24,10%,30%);">Stok</label>
                    <input x-model="form.stock" type="number" min="0"
                           class="w-full border rounded-xl h-10 px-3 text-sm outline-none"
                           style="border-color: var(--border);" />
                </div>
                <div>
                    <label class="block text-sm mb-1" style="color: hsl(24,10%,30%);">Status</label>
                    <select x-model="form.status" class="w-full border rounded-xl h-10 px-3 text-sm outline-none"
                            style="border-color: var(--border);">
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                </div>
            </div>
            <button type="submit" :disabled="saving"
                    class="w-full h-10 rounded-xl font-semibold text-sm disabled:opacity-60"
                    style="background: var(--bg-medium); color: var(--text-light);">
                <span x-text="saving ? 'Menyimpan...' : (editing ? 'Simpan Perubahan' : 'Tambah Produk')"></span>
            </button>
        </form>
    </div>
</div>
</div>

<script>
function productsPage() {
    return {
        products: [],
        categories: [],
        loading: true,
        dialogOpen: false,
        editing: null,
        saving: false,
        form: { name: '', categoryId: '', description: '', price: 0, image: '', stock: 0, status: 'active' },

        async init() {
            await Promise.all([this.fetchProducts(), this.fetchCategories()]);
        },

        async fetchProducts() {
            this.loading = true;
            const res = await fetch('/admin/api/products');
            if (res.ok) this.products = await res.json();
            this.loading = false;
        },

        async fetchCategories() {
            const res = await fetch('/api/categories');
            if (res.ok) this.categories = await res.json();
        },

        openCreate() {
            this.editing = null;
            this.form = { name: '', categoryId: '', description: '', price: 0, image: '', stock: 0, status: 'active' };
            this.dialogOpen = true;
        },

        openEdit(p) {
            this.editing = p;
            this.form = {
                name: p.name, categoryId: p.categoryId ?? '', description: p.description ?? '',
                price: p.price, image: p.image ?? '', stock: p.stock, status: p.status,
            };
            this.dialogOpen = true;
        },

        async saveProduct() {
            this.saving = true;
            const payload = {
                name: this.form.name,
                categoryId: this.form.categoryId ? Number(this.form.categoryId) : null,
                description: this.form.description || null,
                price: Number(this.form.price),
                image: this.form.image || null,
                stock: Number(this.form.stock),
                status: this.form.status,
            };
            const url = this.editing ? '/admin/api/products/' + this.editing.id : '/admin/api/products';
            const method = this.editing ? 'PUT' : 'POST';
            await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify(payload),
            });
            this.dialogOpen = false;
            this.saving = false;
            await this.fetchProducts();
        },

        async deleteProduct(id) {
            if (!confirm('Hapus produk ini?')) return;
            await fetch('/admin/api/products/' + id, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            });
            await this.fetchProducts();
        },
    };
}
</script>
@endsection
