# KosConnect

Platform manajemen operasional rumah kos berbasis Laravel 12 dan Tailwind CSS.

## Langkah Setup Lokal
1. Pastikan PHP >= 8.2 dan Composer terinstal, serta MySQL aktif.
2. Clone repository dan masuk ke folder proyek.
3. Salin file `.env.example` menjadi `.env`:
   ```bash
   cp .env.example .env
   ```
4. Sesuaikan konfigurasi database pada `.env`:
   ```env
   DB_DATABASE=kosconnect
   DB_USERNAME=root
   DB_PASSWORD=
   ```
5. Install dependensi PHP dan Node.js:
   ```bash
   composer install
   npm install
   ```
6. Generate application key:
   ```bash
   php artisan key:generate
   ```
7. Jalankan migration dan seeder:
   ```bash
   php artisan migrate:fresh --seed
   ```
8. Jalankan development server dan Vite:
   ```bash
   php artisan serve
   npm run dev
   ```

## Akun Dummy (Seeder)
Semua akun menggunakan password default: `password`
- **Pemilik**: `pemilik@kosconnect.test`
- **Pengurus 1**: `pengurus1@kosconnect.test`
- **Pengurus 2**: `pengurus2@kosconnect.test`
- **Penghuni 1 - 5**: `penghuni1@kosconnect.test` s.d. `penghuni5@kosconnect.test`

## Aturan Branch Git
- `main`: Branch produksi / rilis stabil
- `develop`: Branch integrasi pengembangan aktif
- `feature/<modul>-<deskripsi>`: Branch pengerjaan fitur mandiri (wajib Code Review / PR sebelum merge ke `develop`).
