@extends('layouts.admin')
@section('title', 'Kategori')

@section('content')
<div class="max-w-4xl mx-auto space-y-4"
     x-data="categoriesPage()" x-init="init()">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold" style="color: var(--text-dark);">Manajemen Kategori</h1>
            <p class="text-sm" style="color: var(--text-mid);">Kelola kategori menu</p>
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

    <div x-show="loading" class="space-y-2">
        <template x-for="i in 4" :key="i">
            <div class="h-14 rounded-2xl animate-pulse" style="background: hsl(35,25%,88%);"></div>
        </template>
    </div>

    <div x-show="!loading && categories.length === 0" x-cloak
         class="rounded-2xl border p-10 text-center" style="background: white; border-color: hsl(35,25%,88%);">
        <p class="text-sm" style="color: var(--text-mid);">Belum ada kategori</p>
    </div>

    <div x-show="!loading && categories.length > 0" x-cloak class="space-y-2">
        <template x-for="cat in categories" :key="cat.id">
            <div class="rounded-2xl border p-4 flex items-center justify-between"
                 style="background: white; border-color: hsl(35,25%,88%);">
                <div>
                    <p class="font-semibold text-sm" style="color: var(--text-dark);" x-text="cat.name"></p>
                    <p class="text-xs mt-0.5" style="color: var(--text-mid);"
                       x-text="'Dibuat: ' + new Date(cat.created_at).toLocaleDateString('id-ID')"></p>
                </div>
                <div class="flex gap-1">
                    <button @click="openEdit(cat)"
                            class="w-8 h-8 rounded-lg flex items-center justify-center border"
                            style="border-color: hsl(35,25%,85%);">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="hsl(24,10%,40%)" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>
                    <button @click="deleteCategory(cat.id)"
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
    <div class="w-full max-w-sm rounded-2xl p-5" style="background: white;" @click.stop>
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-bold text-base" style="color: var(--text-dark);"
                x-text="editing ? 'Edit Kategori' : 'Tambah Kategori'"></h2>
            <button @click="dialogOpen = false">
                <svg class="w-5 h-5" fill="none" stroke="hsl(24,10%,50%)" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form @submit.prevent="save()" class="space-y-3">
            <div>
                <label class="block text-sm mb-1" style="color: hsl(24,10%,30%);">Nama Kategori</label>
                <input x-model="form.name" type="text" placeholder="Nama kategori"
                       class="w-full border rounded-xl h-10 px-3 text-sm outline-none"
                       style="border-color: var(--border);" required />
            </div>
            <button type="submit" :disabled="saving"
                    class="w-full h-10 rounded-xl font-semibold text-sm disabled:opacity-60"
                    style="background: var(--bg-medium); color: var(--text-light);">
                <span x-text="saving ? 'Menyimpan...' : (editing ? 'Simpan' : 'Tambah')"></span>
            </button>
        </form>
    </div>
</div>
</div>

<script>
function categoriesPage() {
    return {
        categories: [],
        loading: true,
        dialogOpen: false,
        editing: null,
        saving: false,
        form: { name: '' },

        async init() { await this.fetchCategories(); },

        async fetchCategories() {
            this.loading = true;
            const res = await fetch('/admin/api/categories');
            if (res.ok) this.categories = await res.json();
            this.loading = false;
        },

        openCreate() {
            this.editing = null;
            this.form = { name: '' };
            this.dialogOpen = true;
        },

        openEdit(cat) {
            this.editing = cat;
            this.form = { name: cat.name };
            this.dialogOpen = true;
        },

        async save() {
            this.saving = true;
            const url = this.editing ? '/admin/api/categories/' + this.editing.id : '/admin/api/categories';
            await fetch(url, {
                method: this.editing ? 'PUT' : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify(this.form),
            });
            this.dialogOpen = false;
            this.saving = false;
            await this.fetchCategories();
        },

        async deleteCategory(id) {
            if (!confirm('Hapus kategori ini?')) return;
            await fetch('/admin/api/categories/' + id, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            });
            await this.fetchCategories();
        },
    };
}
</script>
@endsection
