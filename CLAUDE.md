# Panduan Pengembangan KosConnect

## Ringkasan Produk
KosConnect adalah platform manajemen operasional rumah kos yang terdiri dari 3 role utama:
- **Pemilik**: Kelola data penghuni, master data kamar, lihat seluruh rekap & monitor sistem.
- **Pengurus**: Catat pembayaran, verifikasi jadwal kebersihan, respon laporan kerusakan & pindah kamar, override manual reminder.
- **Penghuni**: Lihat tagihan & riwayat pribadi, ajukan jadwal kebersihan, lapor kerusakan, ajukan pindah kamar.

## Konvensi Penting
- **Bahasa**: Nama tabel, kolom, model, dan route dalam bahasa Inggris (snake_case / PascalCase). Label UI/teks dalam bahasa Indonesia.
- **Istilah**: Gunakan selalu istilah **"pengurus"** (bukan petugas).
- **Timezone & Locale**: `APP_TIMEZONE=Asia/Jakarta`, `APP_LOCALE=id`.
- **Aturan Migration**: Jangan pernah mengubah file migration lama yang sudah di-commit. Buat migration baru jika ada perubahan skema.
- **Privasi Data**: Semua akses data pribadi penghuni wajib melalui `TenantPolicy`.

## Perintah Umum
- Install & Setup: `composer install && cp .env.example .env && php artisan key:generate && php artisan migrate --seed`
- Development Server: `php artisan serve` & `npm run dev`
- Menjalankan Test: `php artisan test`
- Database Fresh & Seed: `php artisan migrate:fresh --seed`
- Queue Worker (Schedule): `php artisan schedule:work`
