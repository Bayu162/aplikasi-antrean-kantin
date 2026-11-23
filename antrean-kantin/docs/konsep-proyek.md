# Konsep dan Kerangka Kerja Proyek
## Kantin.Antri — Aplikasi Antrean Kantin Sederhana

### 1. Ringkasan ide

**Nama produk:** Kantin.Antri  
**Bentuk produk:** website sederhana untuk melihat menu dan memesan makanan kantin.  
**Pengguna utama:** murid sebagai pemesan dan petugas kantin sebagai penerima nomor pesanan.  
**Masalah:** antrean kantin dapat menumpuk pada waktu istirahat dan murid perlu cara mudah untuk mencatat pesanannya.

**Solusi:** murid memilih menu melalui website, mengisi nama dan kelas, lalu menerima nomor antrean otomatis. Nomor tersebut ditunjukkan kepada petugas kantin saat mengambil pesanan.

### 2. Hubungan dengan materi Entrepreneurship in Action

- **Melihat peluang:** antrean kantin merupakan masalah sehari-hari yang dapat dibantu dengan teknologi.
- **Kreativitas dan problem solving:** website menjadi cara sederhana untuk mengirim dan mencatat pesanan.
- **Kemandirian dan tanggung jawab:** murid mengisi pesanan dengan benar serta menyimpan nomor antreannya.
- **Risiko terukur:** mulai dari satu kantin dan fitur dasar sebelum menambah fitur lain.
- **MVP:** versi awal dibuat sederhana agar dapat diuji dan diperbaiki berdasarkan masukan pengguna.

### 3. Tujuan proyek

1. Memudahkan murid melihat menu dan harga.
2. Memudahkan murid mengirim pesanan tanpa harus langsung berdesakan di depan kantin.
3. Membuat nomor antrean otomatis agar urutan pesanan tercatat.
4. Menguji apakah website mudah digunakan oleh murid dan membantu pencatatan pesanan.
5. Memberi kesempatan kepada murid mengonfirmasi bahwa pesanan sudah diterima dan menilai layanan kantin.

### 4. Validasi kebutuhan

Sebelum menyatakan aplikasi berhasil:

- Wawancarai 5–10 calon pengguna, yaitu murid dan pedagang kantin.
- Tanyakan cara memesan saat ini dan masalah yang paling sering terjadi.
- Minta beberapa murid mencoba memilih menu dan mengirim pesanan.
- Amati apakah mereka memahami nomor antrean yang muncul.
- Catat saran pengguna, lalu perbaiki bagian yang membingungkan.

### 5. Ruang lingkup MVP versi sederhana

| Fitur | Hasil |
|---|---|
| Melihat menu | Murid mengetahui menu dan harga |
| Mengisi data pemesan | Nama dan kelas tersimpan |
| Memilih beberapa menu | Item dan jumlah pesanan tercatat |
| Nomor antrean harian | Setiap pesanan mendapat nomor unik per tanggal |
| Cek antrean | Murid dapat melihat ringkasan pesanan dengan nomor dan tanggal |
| Konfirmasi diterima | Murid menandai pesanan setelah benar-benar diterima |
| Rating | Murid memberi rating 1–5 bintang dan komentar singkat setelah menerima pesanan |

**Sengaja tidak disertakan:** halaman admin, login, dashboard, pembayaran online, notifikasi WhatsApp, dan akun murid. Daftar menu contoh dapat diedit langsung melalui tabel `menus` di phpMyAdmin.

**Batasan versi ini:** status awal setiap pesanan adalah Menunggu dibuat. Karena tidak ada halaman petugas/admin, murid sendiri menekan tombol Pesanan Sudah Diterima setelah menerima makanan. Rating hanya muncul setelah konfirmasi tersebut, dan satu pesanan hanya dapat diberi rating satu kali. Konfirmasi ini merupakan catatan dari pembeli, bukan pembaruan otomatis dari petugas kantin.

### 6. Alur penggunaan

1. Murid membuka halaman beranda.
2. Murid melihat daftar menu dan harga.
3. Murid membuka halaman pemesanan.
4. Murid mengisi nama dan kelas, memilih menu, lalu mengisi jumlah.
5. Sistem menghitung total, menyimpan pesanan, dan membuat nomor antrean harian.
6. Murid menyimpan nomor antrean dan dapat mengecek kembali rincian pesanan.
7. Murid menunjukkan nomor antrean tersebut kepada petugas kantin.
8. Setelah menerima pesanan, murid membuka halaman Cek Antrean dan menekan tombol Pesanan Sudah Diterima.
9. Murid memberi rating 1–5 bintang dan, bila perlu, menulis komentar singkat.

### 7. Rancangan database

Database bernama `antrean_kantin` terdiri dari empat tabel:

| Tabel | Fungsi |
|---|---|
| `menus` | Menyimpan nama, deskripsi, harga, dan ketersediaan menu |
| `queue_counters` | Menyimpan nomor antrean terakhir untuk setiap tanggal |
| `orders` | Menyimpan data pemesan, nomor antrean, total, status penerimaan, rating, dan komentar |
| `order_items` | Menyimpan menu, harga, jumlah, dan subtotal dalam setiap pesanan |

Satu pesanan pada tabel `orders` dapat memiliki beberapa baris detail pada tabel `order_items`.

### 8. Kerangka kerja 30 hari

| Waktu | Kegiatan | Hasil |
|---|---|---|
| Minggu 1 | Wawancara murid dan pedagang; catat masalah antrean | Daftar kebutuhan |
| Minggu 2 | Buat prototipe menu, pemesanan, dan nomor antrean | Prototipe berjalan |
| Minggu 3 | Uji coba pada satu kantin | Catatan kendala dan masukan |
| Minggu 4 | Perbaiki tampilan dan alur; presentasikan hasil | Versi 1.0 |

### 9. Indikator pengujian

- Apakah murid dapat menemukan menu dan harga?
- Apakah murid dapat mengirim pesanan tanpa bantuan?
- Apakah nomor antrean muncul dan tersimpan dengan benar?
- Apakah jumlah dan total harga sesuai dengan pilihan?
- Apakah murid memahami cara menunjukkan nomor antrean kepada petugas?

### 10. Risiko dan perbaikan

| Risiko | Penanganan |
|---|---|
| Murid bingung mengisi formulir | Sederhanakan label dan petunjuk |
| Koneksi internet terputus | Siapkan pencatatan manual sebagai cadangan |
| Menu atau harga berubah | Perbarui data pada tabel `menus` |
| Pengguna salah memasukkan jumlah | Validasi jumlah dan tampilkan pesan kesalahan |

**Prinsip proyek:** mulai dari masalah nyata, buat solusi paling sederhana yang bisa diuji, dengarkan pengguna, lalu perbaiki.
