# K-TiangAlam POS — Project Context

## Ringkasan
Aplikasi Kasir (Point of Sale) modern untuk kedai kopi "Kopi Tiang Alam". Dibangun sebagai **PWA (Progressive Web App)** agar bisa diinstal di Android/iOS langsung dari browser, ringan, dan offline-first.

## Tech Stack
- **Framework**: React 19 + TypeScript
- **Build**: Vite 6
- **Styling**: Tailwind CSS v4 (dengan @theme custom tokens)
- **Routing**: Wouter
- **State Management**: Zustand (cart + auth persisted)
- **Database**: Dexie.js v4 (IndexedDB wrapper) — semua data lokal, offline-first
- **Icons**: Lucide React
- **Animation**: Framer Motion (tersedia, belum dipakai semua)
- **Charts**: Recharts (tersedia untuk laporan)

## Warna & Tema
- Primary: Deep Blue (#0f4c75 series)
- Accent: Teal (#00b4b4 series)
- Custom tokens didefinisikan di `src/index.css` menggunakan `@theme`

## Struktur Folder
```
src/
├── components/    → Komponen reusable (BottomNav, dll)
├── db/            → Database schema (schema.ts) + Dexie instance (index.ts)
├── lib/           → Utility functions (formatRupiah, cn, generateTxNumber)
├── pages/         → Halaman utama (CashierPage, PaymentPage, HistoryPage, LoginPage)
│   └── admin/     → Halaman admin-only (ProductsPage, ReportsPage, SettingsPage)
└── store/         → Zustand stores (auth.ts, cart.ts)
```

## Database (IndexedDB via Dexie.js)
Tabel: `users`, `categories`, `products`, `transactions`, `transactionItems`, `settings`

### Seed Data Default
- Admin: PIN `1234`
- Kasir 1: PIN `0000`
- 4 kategori: Kopi, Non-Kopi, Makanan, Snack
- 12 produk contoh
- Settings toko default

## Fitur yang Sudah Selesai (MVP)
1. ✅ Login PIN-based (Admin & Kasir)
2. ✅ Role-based access (Admin bisa semua, Kasir hanya transaksi + riwayat)
3. ✅ Halaman Kasir (grid produk, search, filter kategori)
4. ✅ Sistem Keranjang (tambah, +/-, hapus, hitung subtotal/pajak 11%/diskon/total)
5. ✅ Pembayaran (Cash dengan hitung kembalian real-time, QRIS, E-Wallet)
6. ✅ Riwayat transaksi (hari ini / 7 hari / bulan ini + ringkasan omset & laba)
7. ✅ CRUD Produk (admin only)
8. ✅ Laporan keuangan (omset, laba kotor, pajak, grafik 7 hari, produk terlaris)
9. ✅ Pengaturan toko (nama, alamat, telepon, tarif pajak, footer struk)
10. ✅ PWA manifest + Service Worker (offline support & installable)
11. ✅ Responsive (mobile-first, grid adapts ke tablet/desktop)

## Fitur yang Belum Diimplementasi
- [ ] Cetak struk ke Printer Thermal Bluetooth
- [ ] Bagikan nota via WhatsApp / PDF
- [ ] Manajemen user (tambah/edit kasir dari admin)
- [ ] Manajemen kategori (CRUD kategori)
- [ ] Diskon per-item (saat ini diskon global saja)
- [ ] Void / refund transaksi
- [ ] Export laporan ke Excel/CSV
- [ ] Grafik menggunakan Recharts (saat ini pakai bar chart CSS manual)
- [ ] Foto produk (upload gambar)
- [ ] Multi-outlet support
- [ ] Sync ke cloud / backup data

## Cara Menjalankan
```bash
npm run dev     # Dev server di localhost:5173
npm run build   # Build production
npm run preview # Preview build
```

## Catatan Penting
- Data 100% lokal di browser (IndexedDB). Tidak ada backend/API.
- Jika user clear browser data, semua data transaksi hilang.
- Seed database jalan otomatis saat pertama kali (cek `src/db/index.ts` → `seedDatabase()`)
- Path alias `@/` mengarah ke `./src/`
