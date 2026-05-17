# MANUAL BOOK: SISTEM MULTI-GUDANG (GUDANG UTAMA & GUDANG DAPUR)
## Nita Jaya Catering - SOP Manajemen Stok & Resep

---

### 1. PENDAHULUAN & KONSEP DASAR
Sistem inventaris Katering Nita Jaya kini menerapkan standar industri profesional dengan memisahkan **Gudang Utama** dan **Gudang Dapur**. Pemisahan ini bertujuan untuk mencegah kerancuan satuan (Karung vs Gram) dan menjaga kedisiplinan alur barang.

**Konsep Ruang Penyimpanan:**
*   **GUDANG UTAMA (Central Store)**: Terdiri dari zona *Bahan Basah (BB)*, *Bahan Kering (BK)*, dan *Bahan Penolong (BP)*. Area ini dikelola oleh Admin Gudang. Input barang menggunakan **Satuan Besar (Beli)** seperti Sak, Jerigen, Bal.
*   **GUDANG DAPUR (Kitchen Store)**: Area penyimpanan khusus di dalam dapur (kulkas dapur, toples, rak dapur). Area ini dikelola oleh Koki. Pemotongan barang menggunakan **Satuan Kecil (Pemakaian/Resep)** seperti Gram, Ml.

---

### 2. ALUR KERJA (S.O.P) HARIAN

Sistem dirancang agar masing-masing divisi bekerja di wilayahnya sendiri tanpa saling merusak laporan.

#### Langkah 1: Barang Datang dari Supplier (Tugas Admin Gudang)
1. Admin Gudang masuk ke menu **Stok Masuk**.
2. Pilih barang (Contoh: Beras Uduk).
3. Pilih lokasi penyimpanan fisiknya (Contoh: **Bahan Kering**).
4. Masukkan jumlah dalam satuan besar (Contoh: 10 Sak).

#### Langkah 2: Permintaan Dapur / Transfer Stok (Tugas Admin Gudang)
Koki *tidak bisa* mengambil barang sendiri dari sistem. Koki harus meminta kepada Admin Gudang.
1. Koki meminta 1 Sak beras untuk persiapan masak hari ini.
2. Admin Gudang masuk ke menu **Manajemen Stok > Mutasi Stok**.
3. Admin Gudang memindahkan 1 Sak Beras dari lokasi **Bahan Kering** ke lokasi **GUDANG DAPUR**.

#### Langkah 3: Eksekusi Masak (Tugas Koki / Admin Dapur)
1. Koki masuk ke menu **Dashboard Resep**.
2. Koki mencari resep "Nasi Putih" dan mengklik **Gunakan Resep**.
3. Sistem secara otomatis **HANYA akan memotong stok di GUDANG DAPUR**.
   *   *Peringatan*: Jika di Gudang Dapur berasnya habis, sistem akan menolak proses resep meskipun di Bahan Kering (Gudang Utama) masih ada 100 Sak. Koki harus kembali ke Langkah 2.

---

### 3. KEUNTUNGAN SISTEM INI UNTUK PEGAWAI

*   **Bagi Admin Gudang**: Pekerjaan Anda **TIDAK BERUBAH**. Anda tetap input barang dalam satuan besar (Karung/Dus) ke lokasi BB/BK/BP kesayangan Anda. Anda tidak perlu pusing memikirkan resep koki yang menggunakan satuan Gram. Tugas baru Anda hanyalah mencatat "Barang Pindah ke Dapur" setiap pagi.
*   **Bagi Koki/Dapur**: Anda memiliki "gudang" virtual sendiri. Resep yang Anda jalankan akan langsung memotong persediaan dapur secara presisi tanpa merusak perhitungan karungan milik Gudang Utama.
*   **Bagi Manajemen**: Laporan menjadi sangat rapi. Kebocoran barang bisa dilacak dengan mudah. Jika ada selisih, manajemen tahu persis apakah barang hilang saat masih di Gudang Utama, atau hilang setelah ditransfer ke Dapur.

---

### 4. TROUBLESHOOTING & STOCK OPNAME

**Bagaimana cara Stock Opname di akhir bulan?**
Proses opname dilakukan secara terpisah:
1.  **Admin Gudang** menghitung barang utuh (Karung/Dus) yang ada di zona BB, BK, dan BP.
2.  **Koki/Admin Dapur** menghitung sisa barang yang sudah terbuka/eceran (Gram/Toples) yang ada khusus di area Dapur.
3.  Keduanya menginput hasil perhitungannya ke sistem sesuai lokasi masing-masing.

**Bagaimana jika Koki salah ketik jumlah porsi resep?**
Gunakan fitur **Fixing Mutasi** (Superadmin) untuk mengembalikan stok di `GUDANG DAPUR` ke angka yang benar.

---
**Diperbarui oleh: Tim IT / hasanarofid.site**
**Untuk: Internal Nita Jaya Catering**
