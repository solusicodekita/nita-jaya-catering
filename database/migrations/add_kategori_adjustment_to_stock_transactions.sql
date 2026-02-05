-- Query untuk menambahkan kolom kategori_adjustment di tabel stock_transactions
-- Jalankan query ini di phpMyAdmin

ALTER TABLE `stock_transactions` 
ADD COLUMN `kategori_adjustment` ENUM('stok', 'qty', 'pengembalian') NULL DEFAULT NULL 
AFTER `alasan_adjustment`;

