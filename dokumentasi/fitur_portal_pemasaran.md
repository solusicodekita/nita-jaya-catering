# Dokumentasi Fitur Portal Pemasaran & RBAC (Branch: main_improve)

Dokumentasi ini menjelaskan perubahan struktur database dan penambahan modul "Portal Admin" pada sistem katering.

## 1. Konsep Utama
Sistem kini memiliki dua area dashboard yang terpisah berdasarkan peran (Role):
- **Dashboard POS (Lama)**: Untuk operasional harian (Akses: Staff & Superadmin).
- **Portal Admin (Baru)**: Untuk pengelolaan Company Profile, Foto Menu, dan Promosi (Akses: Khusus Superadmin).

## 2. Struktur Database
Perubahan dilakukan melalui migrasi Laravel (`add_role_and_portal_tables_to_v19`):
- **Tabel `users`**: Penambahan kolom `role` (enum: `superadmin`, `staff`).
- **Tabel `company_settings`**: Menyimpan profil perusahaan (Nama, Logo, Alamat, Deskripsi).
- **Tabel `portal_menus`**: Menyimpan data menu yang dipromosikan (Nama, Gambar, Status Promoted).

## 3. Sistem Keamanan (RBAC)
- **Middleware**: `EnsureRole` (alias: `role_check`).
- **Implementasi**: Melindungi rute `/portal/*` agar hanya bisa diakses oleh user dengan `role = superadmin`.

## 4. Langkah-Langkah Go Live (Deployment)

Jika Anda ingin menerapkan perubahan ini ke server produksi nantinya, lakukan langkah ini:

1. **Pull Branch**: Pastikan branch `main_improve` sudah ditarik.
2. **Migrasi Database**:
   ```bash
   php artisan migrate
   ```
3. **Sinkronisasi Role**: Jalankan seeder untuk mengubah data *Supervisor* server menjadi *Superadmin* sistem baru:
   ```bash
   php artisan db:seed --class=PortalSyncSeeder
   ```
4. **Penyambungan Storage**: (Wajib agar gambar bisa muncul di publik)
   ```bash
   php artisan storage:link
   ```
5. **Bersihkan Cache**:
   ```bash
   php artisan clear-cache
   ```

## 5. Daftar Rute Baru
- `GET /` : Halaman depan publik (Company Profile).
- `GET /portal/dashboard` : Panel admin pemasaran.
- `GET /portal/settings` : Pengaturan data perusahaan.
- `GET /portal/menus` : Pengelolaan foto menu dan promosi.

---
*Dibuat oleh: Antigravity Assistant (Google Deepmind)*
*Tanggal: 23 April 2026*
