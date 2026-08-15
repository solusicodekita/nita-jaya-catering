-- SQL Patch for Fixing Transaksi 7865 (23-07-2026) Inflated Amount Bug
-- Jalankan query ini di database MySQL Server Nita Jaya Catering

-- 1. Correct Total Transaksi Keseluruhan on Stock Transaction 7865
UPDATE `stock_transactions` 
SET `total_harga_keseluruhan` = 10109159.80 
WHERE `id` = 7865;

-- 2. Correct Total Harga for Item Margarine in Detail 42609
UPDATE `stock_transaction_details` 
SET `total_harga` = 298909.80 
WHERE `id` = 42609 AND `stock_transaction_id` = 7865;

-- 3. Check and verify the updated data
SELECT id, type, total_harga_keseluruhan, date FROM stock_transactions WHERE id = 7865;
SELECT id, stock_transaction_id, item_id, quantity, harga_satuan, total_harga FROM stock_transaction_details WHERE stock_transaction_id = 7865;
