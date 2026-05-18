@extends('layouts.admin')
@section('title', 'Meja')

@section('content')
<div class="max-w-4xl mx-auto space-y-4"
     x-data="tablesPage()" x-init="init()">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold" style="color: var(--text-dark);">Manajemen Meja</h1>
            <p class="text-sm" style="color: var(--text-mid);">Kelola meja kafe</p>
        </div>
        <button @click="openCreate()"
                class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm font-medium"
                style="background: var(--bg-medium); color: var(--text-light);">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Meja
        </button>
    </div>

    <div x-show="loading" class="grid grid-cols-2 sm:grid-cols-3 gap-3">
        <template x-for="i in 6" :key="i">
            <div class="h-24 rounded-2xl animate-pulse" style="background: hsl(35,25%,88%);"></div>
        </template>
    </div>

    <div x-show="!loading && tables.length === 0" x-cloak
         class="rounded-2xl border p-10 text-center" style="background: white; border-color: hsl(35,25%,88%);">
        <p class="text-sm" style="color: var(--text-mid);">Belum ada meja</p>
    </div>

    <div x-show="!loading && tables.length > 0" x-cloak class="grid grid-cols-2 sm:grid-cols-3 gap-3">
        <template x-for="table in tables" :key="table.id">
            <div class="rounded-2xl border p-4" style="background: white; border-color: hsl(35,25%,88%);">
                <div class="flex items-start justify-between mb-2">
                    <p class="font-bold text-lg" style="color: var(--text-dark);" x-text="'Meja ' + table.table_number"></p>
                    <div class="flex gap-1">
                        <button @click="openEdit(table)"
                                class="w-7 h-7 rounded-lg flex items-center justify-center border"
                                style="border-color: hsl(35,25%,85%);">
                            <svg class="w-3 h-3" fill="none" stroke="hsl(24,10%,40%)" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <button @click="deleteTable(table.id)"
                                class="w-7 h-7 rounded-lg flex items-center justify-center border"
                                style="border-color: hsl(35,25%,85%);">
                            <svg class="w-3 h-3" fill="none" stroke="var(--danger)" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <select @change="updateStatus(table.id, $event.target.value)"
                        :value="table.status"
                        class="w-full text-xs border rounded-lg h-7 px-2 outline-none mt-1"
                        style="border-color: var(--border);">
                    <option value="available">Tersedia</option>
                    <option value="occupied">Terisi</option>
                </select>
                <div class="mt-2">
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium"
                          :style="table.status === 'available' ? 'background: hsl(145,65%,85%); color: hsl(145,55%,30%);' : 'background: hsl(0,84%,93%); color: hsl(0,84%,40%);'"
                          x-text="table.status === 'available' ? 'Tersedia' : 'Terisi'"></span>
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
                x-text="editing ? 'Edit Meja' : 'Tambah Meja'"></h2>
            <button @click="dialogOpen = false">
                <svg class="w-5 h-5" fill="none" stroke="hsl(24,10%,50%)" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form @submit.prevent="save()" class="space-y-3">
            <div>
                <label class="block text-sm mb-1" style="color: hsl(24,10%,30%);">Nomor Meja</label>
                <input x-model="form.tableNumber" type="text" placeholder="1, 2, A1..."
                       class="w-full border rounded-xl h-10 px-3 text-sm outline-none"
                       style="border-color: var(--border);" required />
            </div>
            <div>
                <label class="block text-sm mb-1" style="color: hsl(24,10%,30%);">Status</label>
                <select x-model="form.status" class="w-full border rounded-xl h-10 px-3 text-sm outline-none"
                        style="border-color: var(--border);">
                    <option value="available">Tersedia</option>
                    <option value="occupied">Terisi</option>
                </select>
            </div>
            <button type="submit" :disabled="saving"
                    class="w-full h-10 rounded-xl font-semibold text-sm disabled:opacity-60"
                    style="background: var(--bg-medium); color: var(--text-light);">
                <span x-text="saving ? 'Menyimpan...' : (editing ? 'Simpan' : 'Tambah Meja')"></span>
            </button>
        </form>
    </div>
</div>
</div>

<script>
function tablesPage() {
    return {
        tables: [],
        loading: true,
        dialogOpen: false,
        editing: null,
        saving: false,
        form: { tableNumber: '', status: 'available' },

        async init() { await this.fetchTables(); },

        async fetchTables() {
            this.loading = true;
            const res = await fetch('/admin/api/tables');
            if (res.ok) this.tables = await res.json();
            this.loading = false;
        },

        openCreate() {
            this.editing = null;
            this.form = { tableNumber: '', status: 'available' };
            this.dialogOpen = true;
        },

        openEdit(t) {
            this.editing = t;
            this.form = { tableNumber: t.table_number, status: t.status };
            this.dialogOpen = true;
        },

        async save() {
            this.saving = true;
            const url = this.editing ? '/admin/api/tables/' + this.editing.id : '/admin/api/tables';
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
            await this.fetchTables();
        },

        async updateStatus(id, status) {
            const table = this.tables.find(t => t.id === id);
            if (!table) return;
            await fetch('/admin/api/tables/' + id, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ tableNumber: table.table_number, status }),
            });
            await this.fetchTables();
        },

        async deleteTable(id) {
            if (!confirm('Hapus meja ini?')) return;
            await fetch('/admin/api/tables/' + id, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            });
            await this.fetchTables();
        },
    };
}
</script>
@endsection
