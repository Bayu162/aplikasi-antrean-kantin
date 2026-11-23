# Kantin.Antri — Aplikasi Antrean Kantin Sederhana

Website sederhana untuk pemesanan makanan kantin sekolah menggunakan **PHP, HTML, CSS, dan MySQL**. Versi ini **tidak memakai admin, login, atau dashboard**.

## Fitur

- Melihat daftar menu dan harga.
- Memesan satu atau beberapa menu sekaligus.
- Mengisi nama dan kelas pemesan.
- Mendapat nomor antrean otomatis berdasarkan tanggal.
- Mengecek kembali nomor antrean dan ringkasan pesanan.
- Mengonfirmasi pesanan sudah diterima setelah pesanan diambil.
- Memberi rating 1–5 bintang dan komentar singkat setelah konfirmasi penerimaan.
- Menyimpan data menu, pesanan, dan detail pesanan di database MySQL.

## Isi folder

```text
antrean-kantin/
├── assets/css/style.css
├── config/database.php
├── database/antrean_kantin.sql
├── docs/konsep-proyek.md
├── includes/
│   ├── bootstrap.php
│   ├── footer.php
│   ├── functions.php
│   └── header.php
├── index.php
├── pesan.php
├── status.php
└── README.md
```

## Cara menjalankan di XAMPP

1. Ekstrak folder `antrean-kantin` ke `C:\xampp\htdocs\`.
2. Buka XAMPP Control Panel, lalu jalankan **Apache** dan **MySQL**.
3. Buka `http://localhost/phpmyadmin`.
4. Pilih tab **Import**, pilih file `antrean-kantin/database/antrean_kantin.sql`, lalu klik **Import/Go**. Database `antrean_kantin` dan enam menu contoh akan dibuat otomatis.
5. Jika username atau password MySQL berbeda, sesuaikan `config/database.php`. Pengaturan bawaan: host `127.0.0.1`, port `3306`, database `antrean_kantin`, user `root`, password kosong.
6. Pastikan ekstensi PHP `pdo_mysql` dan `mbstring` aktif.
7. Buka `http://localhost/antrean-kantin/`.

## Cara menggunakan

1. Buka beranda dan lihat menu kantin.
2. Klik **Pesan** pada menu yang diinginkan atau klik **Pesan Sekarang**.
3. Isi nama dan kelas, kemudian masukkan jumlah menu yang akan dipesan.
4. Klik **Kirim Pesanan**.
5. Simpan nomor antrean yang muncul. Nomor dapat dicek kembali melalui menu **Cek Antrean** dengan memasukkan nomor dan tanggal pesanan.
6. Tunjukkan nomor antrean kepada petugas kantin saat mengambil pesanan.
7. Setelah menerima pesanan, buka kembali halaman **Cek Antrean**, masukkan nomor dan tanggal pesanan, lalu klik **Pesanan Sudah Diterima**.
8. Pilih rating 1–5 bintang, isi komentar jika perlu, lalu klik **Kirim Rating**. Rating hanya dapat dikirim satu kali untuk setiap pesanan.

## Catatan versi tanpa admin

- Tidak ada halaman login atau pengelolaan admin.
- Daftar menu contoh disimpan di database. Untuk mengganti nama menu, deskripsi, atau harga, ubah data pada tabel `menus` melalui phpMyAdmin.
- Nomor antrean dibuat otomatis oleh sistem.
- Status awal pesanan adalah **Menunggu dibuat**. Karena versi ini tidak memiliki halaman petugas/admin, pembeli sendiri menekan tombol **Pesanan Sudah Diterima** setelah mengambil pesanan. Tombol ini menandai pesanan sebagai diterima.
- Rating hanya dapat diberikan setelah pesanan ditandai diterima dan hanya satu kali per pesanan.
- Rating dan komentar disimpan pada tabel `orders`.
- File SQL menghapus tabel proyek yang bernama sama sebelum membuat ulang. Jika sudah ada data pesanan penting, lakukan ekspor/backup database terlebih dahulu sebelum impor ulang. Struktur database terbaru menambahkan kolom status penerimaan dan rating.

## Kesesuaian dengan materi Bab I — Entrepreneurship in Action

Proyek ini mengambil contoh **Aplikasi Antrean Kantin** pada rencana 30 hari dalam materi:

- **Minggu 1:** wawancarai murid dan pedagang untuk mengetahui kebutuhan.
- **Minggu 2:** buat prototipe sederhana dengan fitur pesan dan nomor antrean.
- **Minggu 3:** uji coba di satu kantin dan catat masukan pengguna.
- **Minggu 4:** perbaiki tampilan dan alur berdasarkan hasil uji coba.

Fitur dibuat sederhana sebagai MVP (*Minimum Viable Product*) agar mudah diuji terlebih dahulu. Pembayaran online, notifikasi WhatsApp, akun pengguna, dan dashboard petugas belum dimasukkan.
