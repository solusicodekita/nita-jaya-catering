-- Query untuk menambahkan kolom verifikasi_by di tabel stock_transactions
-- Jalankan query ini di phpMyAdmin

ALTER TABLE `stock_transactions` 
ADD COLUMN `verifikasi_by` bigint(20) UNSIGNED NULL DEFAULT NULL 
AFTER `tanggal_verifikasi_adjusment`;

-- Tambahkan foreign key constraint (optional, untuk referensi ke tabel users)
ALTER TABLE `stock_transactions` 
ADD CONSTRAINT `stock_transactions_verifikasi_by_foreign` 
FOREIGN KEY (`verifikasi_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

