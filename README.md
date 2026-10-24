# Dinamic (Stockbarang)

Aplikasi manajemen inventaris barang berbasis **PHP Native (PDO)** dengan antarmuka Bootstrap 5 bertema gelap.

## Fitur

- Autentikasi admin menggunakan `password_verify()` (BCRYPT) + `session_regenerate_id()`.
- Dashboard: total jenis barang, total stok, stok menipis, stok habis, grafik mutasi stok (Chart.js), dan log transaksi terbaru.
- CRUD barang lengkap (tambah, edit, hapus) dengan kode barang yang digenerate otomatis (`BRG-0001`, `BRG-0002`, ...).
- Pencarian barang berdasarkan nama, kode, atau kategori.
- Pencatatan mutasi stok masuk/keluar dengan validasi agar stok tidak bisa minus.
- Stok awal barang baru otomatis tercatat sebagai transaksi masuk.

## Keamanan

| Aspek | Implementasi |
| --- | --- |
| SQL Injection | Seluruh query memakai **PDO prepared statement** dengan named parameter. |
| XSS | Output di-escape dengan `htmlspecialchars()` / helper `e()`. |
| CSRF | Token per sesi pada setiap form POST, diverifikasi dengan `hash_equals()`. |
| Password | Disimpan sebagai hash BCRYPT. |
| Integritas data | Mutasi stok dibungkus `beginTransaction()` / `commit()` / `rollBack()`. |
| Kebocoran informasi | Detail error hanya masuk `error_log()`, pengguna menerima pesan umum. |
| Kontrol akses | Setiap halaman panel memeriksa `$_SESSION['user_id']` lewat `includes/header.php`. |

## Kebutuhan

- PHP 7.4+ (disarankan 8.x) dengan ekstensi `pdo_mysql`.
- MySQL / MariaDB.

## Instalasi

1. Impor skema dan data contoh:

   ```bash
   mysql -uroot < database.sql
   ```

   Perintah tersebut membuat database `db_stockbarang_local` berisi tabel `users`, `barang`, dan `transaksi`.

2. Sesuaikan kredensial bila perlu di bagian atas `config/db.php`:

   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'db_stockbarang_local');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

3. Tempatkan folder ini di document root web server, lalu buka `index.php` (akan diarahkan ke `login.php`).

## Akun Demo

| Username | Password |
| --- | --- |
| `admin` | `admin` |

## Struktur Berkas

| Berkas | Keterangan |
| --- | --- |
| `config/db.php` | Konfigurasi koneksi PDO + helper keamanan (`e()`, CSRF). |
| `includes/header.php` | Guard sesi, `<head>`, judul halaman dinamis. |
| `includes/sidebar.php` | Navigasi samping + profil pengguna. |
| `includes/footer.php` | Penutup layout + Bootstrap JS bundle. |
| `index.php` | Redirect ke dashboard atau login. |
| `login.php` / `logout.php` | Masuk dan keluar aplikasi. |
| `dashboard.php` | Ringkasan statistik + grafik mutasi stok. |
| `barang.php` | CRUD data barang + pencarian. |
| `transaksi.php` | Pencatatan dan riwayat mutasi stok. |
| `database.sql` | Skema dan data contoh. |
