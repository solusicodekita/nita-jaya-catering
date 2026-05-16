# MANUAL BOOK: MANAJEMEN RESEP & AUDIT STOK PRESISI
## Nita Jaya Catering - Versi 2.0 (Optimized)

---

### 1. PENDAHULUAN
Sistem Manajemen Resep Nita Jaya Catering kini dilengkapi dengan fitur **Audit Otomatis**. Selain membantu konversi satuan (seperti Gram ke Ball), sistem kini mampu melakukan pemotongan stok otomatis, pencatatan nilai finansial (Estimasi Jual), dan pelacakan riwayat penggunaan yang sangat transparan.

> [!IMPORTANT]
> **CATATAN PENTING UNTUK AKURASI HARGA:**
> Untuk menjaga akurasi **Estimasi Jual** dan laporan keuangan, pastikan harga bahan baku di menu **Master Bahan Baku** selalu diperbarui jika ada kenaikan atau perubahan harga dari supplier. Sistem menggunakan harga master tersebut sebagai dasar perhitungan otomatis setiap kali resep digunakan.

---

### 2. PENGATURAN RESEP (POTONG STOK OTOMATIS)
Setiap resep kini memiliki kendali atas inventaris gudang.

*   **Toggle Potong Stok**: Saat membuat atau mengedit resep, Anda dapat memilih apakah resep ini akan memotong stok secara otomatis atau tidak.
*   **Keuntungan**: Meminimalisir kesalahan manual. Setiap kali resep digunakan, sistem akan menghitung mundur stok bahan baku di gudang secara real-time.

---

### 3. PENGGUNAAN RESEP & ESTIMASI JUAL
Sistem kini tidak hanya mencatat "apa" yang dimasak, tapi juga "berapa nilainya".

**Cara Menggunakan Resep:**
1. Klik tombol biru **Gunakan Resep** pada kartu resep.
2. Masukkan **Jumlah Porsi** yang akan dimasak.
3. Klik **Proses**.

**Apa yang terjadi di balik layar?**
*   **Pemotongan Stok**: Stok bahan baku di gudang akan berkurang sesuai takaran dikali jumlah porsi.
*   **Pencatatan Finansial**: Sistem otomatis menghitung total nilai (Modal + Profit) dan menyimpannya sebagai transaksi **Estimasi Jual** di Riwayat Stok Keluar.
*   **Identitas Jelas**: Setiap transaksi stok otomatis akan diberi keterangan lengkap: *"Proses penggunaan resep [Nama Resep] oleh [Nama Admin] pada tanggal [Waktu]"*.

---

### 4. RIWAYAT PENGGUNAAN & INTEGRITAS DATA 
Kami menjamin data audit Anda tidak akan rusak (Corrupt) meskipun ada perubahan di masa depan.

*   **Sistem Snapshot**: Saat resep digunakan, sistem menyimpan salinan Nama, Nomor, dan Harga resep *saat itu juga*. 
*   **Keamanan Audit**: Jika suatu hari resep dihapus atau diganti namanya, riwayat lama Anda di menu **Riwayat Penggunaan** tidak akan berubah atau menjadi "Error". Anda tetap bisa melihat data asli saat transaksi itu terjadi.
*   **Detail Bahan**: Pada halaman riwayat, Anda dapat mengklik tombol **Detail Pemakaian Bahan** untuk melihat daftar bahan baku apa saja yang terpotong beserta jumlah desimalnya yang presisi.

---

<!-- ### 5. PERBAIKAN STOK (FITUR SUPERADMIN)
Jika terjadi selisih stok karena kesalahan input manusia atau bug sistem, Superadmin memiliki "Katup Pengaman".

*   **Menu Fixing Mutasi**: Hanya dapat diakses oleh Role Admin/Superadmin.
*   **Force Adjust Stock**: Anda cukup memilih Item dan Lokasi, lalu masukkan **Target Stok** yang benar. Sistem akan otomatis menghitung selisihnya dan membuat transaksi penyesuaian agar stok kembali balance.

---

### 6. LOG AKTIVITAS (USER FRIENDLY)
Menu **Log Aktivitas** kini lebih mudah dibaca oleh manusia.

*   **Detail Tabel**: Data teknis (JSON) kini ditampilkan dalam bentuk tabel yang rapi. Anda bisa melihat perbandingan *"Data Lama"* vs *"Data Baru"* saat terjadi perubahan resep secara mendetail.
*   **Pagination**: Halaman log kini memiliki navigasi (halaman 1, 2, 3...) sehingga performa sistem tetap cepat meskipun data sudah mencapai puluhan ribu. -->

---

**Dibuat oleh: hasanarofid.site**
**Untuk: Nita Jaya Catering**
