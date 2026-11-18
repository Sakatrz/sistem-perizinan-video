# Sistem Perizinan Menonton Video (Video Access Management)

Aplikasi web berbasis **Laravel 12** untuk mengelola perizinan menonton video oleh **Admin** kepada **Customer** dengan batas waktu akses yang dapat ditentukan.

---

## Fitur Utama

### 1. Autentikasi & Hak Akses (2 Role)
- **Admin**: Mengelola data customer, mengupload/mengelola data video, serta menyetujui, mengubah durasi, mencabut, atau menolak permintaan akses video.
- **Customer**: Melihat daftar video yang tersedia, mengajukan permintaan akses menonton, menonton video sesuai tenggang waktu yang diberikan, dan mengajukan permintaan ulang setelah waktu akses habis.

### 2. Manajemen Video & Keamanan
- **Upload Video Lokal**: Video diupload langsung ke server (bukan link URL eksternal) dan disimpan di direktori privat (`storage/app/private/videos`).
- **Protected Streaming**: Video hanya dapat diputar melalui route streaming terproteksi (`GET /customer/videos/{video}/stream`) yang memverifikasi kepemilikan akses aktif di server.

### 3. Manajemen Akses Berbatas Waktu
- Admin dapat menyetujui permintaan akses dengan satuan **Menit**, **Jam**, atau **Hari** (maksimal 1 tahun).
- Admin dapat **mengubah durasi akses** yang sudah aktif atau **mencabut akses** sewaktu-waktu.
- Halaman pemutar video dilengkapi **hitung mundur waktu nyata (countdown)** dan menghentikan pemutaran secara otomatis ketika waktu akses berakhir.
- Zona waktu aplikasi menggunakan **WIB (`Asia/Jakarta`)**.

### 4. Tampilan Responsif
- Antarmuka dibangun dengan **Bootstrap 5**, menampilkan tabel pada layar desktop/tablet lebar (`≥ 768px` untuk Customer/Video, `≥ 1024px` untuk Permintaan Akses) dan tampilan kartu (*card*) pada layar kecil.

---

## Struktur Database (7 Tabel)

| No | Nama Tabel | Keterangan |
|---|---|---|
| 1 | `users` | Data pengguna (Admin & Customer) beserta kolom `role` dan `phone` |
| 2 | `videos` | Metadata video hasil upload (`title`, `description`, `file_path`, `mime_type`) |
| 3 | `access_requests` | Riwayat permintaan akses menonton dari customer (`pending`, `approved`, `rejected`) |
| 4 | `video_accesses` | Izin akses aktif/riwayat beserta rentang waktu (`starts_at`, `expires_at`) |
| 5 | `sessions` | Manajemen sesi login Laravel |
| 6 | `cache` | Penyimpanan cache aplikasi |
| 7 | `jobs` | Antrian proses (*queue jobs*) bawaan Laravel |

---

## Spesifikasi Teknis & Kebutuhan Sistem

- **PHP**: `^8.2`
- **Framework**: Laravel `12.x`
- **Database**: MySQL / MariaDB
- **Composer**: `2.x`
- **Web Server**: XAMPP / Apache / Nginx / `php artisan serve`

---

## Cara Instalasi & Menjalankan Project

### 1. Clone Repository
```bash
git clone <URL_REPOSITORY_ANDA>
cd video-access
```

### 2. Install Dependency
```bash
composer install
```

### 3. Konfigurasi Environment (`.env`)
Salin file `.env.example` menjadi `.env`, lalu sesuaikan konfigurasi database dan zona waktu:
```bash
cp .env.example .env
php artisan key:generate
```
Pastikan isi `.env` pada bagian berikut sudah sesuai:
```env
APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=video_access
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Setup Database
Anda dapat memilih salah satu dari dua cara berikut:
- **Opsi A (Import File SQL):**
  1. Buat database baru bernama `video_access` di MySQL/phpMyAdmin.
  2. Import file `database/video_access.sql` ke dalam database `video_access`.
- **Opsi B (Migration & Seeder):**
  1. Buat database baru bernama `video_access`.
  2. Jalankan perintah:
     ```bash
     php artisan migrate --seed
     ```

### 5. Konfigurasi Batas Upload PHP (Opsional, untuk video berukuran besar)
Pada `php.ini`, pastikan batas ukuran upload memadai:
```ini
upload_max_filesize=512M
post_max_size=512M
```

### 6. Jalankan Aplikasi
```bash
php artisan serve
```
Buka browser dan akses `http://127.0.0.1:8000`.

---

## Akun Demo (Hasil Seeder)

| Role | Email | Password |
|---|---|---|
| **Admin** | `admin@example.com` | `password` |
| **Customer** | `customer@example.com` | `password` |

---

## Alur Kerja Pengujian
1. **Admin** login, menambahkan data customer (jika perlu), dan mengupload file video.
2. **Customer** login, melihat daftar video, lalu menekan tombol **Request Akses**.
3. **Admin** membuka menu **Permintaan Akses**, menentukan durasi akses (menit/jam/hari), lalu menekan **Setujui**.
4. **Customer** menekan **Tonton** dan dapat memutar video hingga batas waktu berakhir.
5. Setelah waktu akses habis, **Customer** dapat menekan **Request Akses Ulang** untuk meminta izin kembali kepada Admin.
