# Panduan Deploy DPIS ke cPanel Hosting

**DPIS — DTHREE Production Integration System v1.0**  
PT DTHREE SUKSES MULIA

---

## Daftar Isi

1. [Prasyarat](#1-prasyarat)
2. [Tahap 1 — Persiapan di Komputer Lokal](#tahap-1--persiapan-di-komputer-lokal)
3. [Tahap 2 — Setup Database MySQL di cPanel](#tahap-2--setup-database-mysql-di-cpanel)
4. [Tahap 3 — Upload File ke cPanel](#tahap-3--upload-file-ke-cpanel)
5. [Tahap 4 — Konfigurasi via Terminal / SSH](#tahap-4--konfigurasi-via-terminal--ssh)
6. [Tahap 5 — Setup PHP Version](#tahap-5--setup-php-version-di-cpanel)
7. [Tahap 6 — Verifikasi](#tahap-6--verifikasi)
8. [Troubleshooting](#troubleshooting)
9. [Jika Tidak Ada Akses Terminal](#jika-tidak-ada-akses-terminal)
10. [Update / Re-deploy](#update--re-deploy)

---

## 1. Prasyarat

Pastikan hosting Anda memenuhi syarat berikut sebelum memulai:

| Kebutuhan | Minimum |
|-----------|---------|
| PHP | **8.2 atau lebih baru** |
| MySQL | 5.7+ / MariaDB 10.3+ |
| Disk Space | Minimal 200 MB |
| Terminal/SSH | Sangat disarankan |

**Ekstensi PHP yang harus aktif:**
`pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`

---

## Tahap 1 — Persiapan di Komputer Lokal

### 1.1 Download / Clone Project

```bash
git clone https://github.com/akmaldthree-ux/wiptracker.git dpis
cd dpis
git checkout claude/clever-bohr-6i3l8a
```

Atau download ZIP dari GitHub → Extract ke folder `dpis/`.

### 1.2 Install Composer Dependencies

```bash
composer install --optimize-autoloader --no-dev
```

> Flag `--no-dev` menghilangkan package development yang tidak diperlukan di production.

### 1.3 Buat File `.env` Production

Copy dari template lalu edit:

```bash
cp .env.example .env
```

Isi file `.env` dengan konfigurasi berikut (sesuaikan dengan data hosting Anda):

```env
APP_NAME="DPIS - DTHREE Production"
APP_ENV=production
APP_KEY=             # kosongkan, akan diisi di server
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=namauser_dpis
DB_USERNAME=namauser_dpis
DB_PASSWORD=passwordkuat123

SESSION_DRIVER=file
SESSION_LIFETIME=30
CACHE_STORE=file
QUEUE_CONNECTION=sync

LOG_CHANNEL=single
LOG_LEVEL=error

MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="DPIS System"
```

> **Ganti:** `yourdomain.com`, `namauser_dpis`, dan `passwordkuat123` sesuai data hosting Anda.  
> `APP_KEY` dikosongkan dulu — akan di-generate di server.

### 1.4 Buat ZIP Archive Project

**Windows:** Klik kanan folder `dpis` → Send to → Compressed (zipped) folder

**Mac / Linux:**
```bash
zip -r dpis.zip . --exclude=".git/*" --exclude="node_modules/*" --exclude="*.sqlite"
```

> Jangan include file `.sqlite` karena di server akan pakai MySQL.

---

## Tahap 2 — Setup Database MySQL di cPanel

1. Login ke **cPanel** → cari menu **"MySQL Databases"**

2. **Buat Database Baru:**
   - Isi nama kolom: `dpis`
   - Klik **Create Database**
   - Nama lengkap otomatis menjadi: `namauser_dpis`

3. **Buat User MySQL Baru:**
   - Scroll ke bagian "MySQL Users"
   - Isi username: `dpis`
   - Isi password yang kuat (gunakan generator)
   - Klik **Create User**
   - User lengkap: `namauser_dpis`

4. **Hubungkan User ke Database:**
   - Scroll ke bagian "Add User To Database"
   - Pilih user `namauser_dpis`
   - Pilih database `namauser_dpis`
   - Klik **Add**
   - Centang **ALL PRIVILEGES** → klik **Make Changes**

5. **Catat ketiga info ini untuk diisi ke `.env`:**
   ```
   DB_DATABASE = namauser_dpis
   DB_USERNAME = namauser_dpis
   DB_PASSWORD = password yang dibuat tadi
   DB_HOST     = localhost
   ```

---

## Tahap 3 — Upload File ke cPanel

### 3.1 Upload via File Manager

1. cPanel → **File Manager**
2. Navigasi ke `/home/namauser/` (satu level di atas `public_html`)
3. Klik tombol **Upload**
4. Pilih file `dpis.zip` → tunggu hingga selesai
5. Setelah upload, klik kanan `dpis.zip` → **Extract** → pilih lokasi `/home/namauser/`

Setelah extract, struktur folder menjadi:
```
/home/namauser/
├── public_html/          ← yang diakses pengunjung website
└── dpis/                 ← folder Laravel hasil extract
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── public/           ← isi folder ini yang akan dipindah ke public_html
    ├── resources/
    ├── routes/
    ├── storage/
    ├── vendor/
    └── .env
```

### 3.2 Pindahkan Isi Folder `public/` ke `public_html/`

Di File Manager:

1. Navigasi ke `/home/namauser/dpis/public/`
2. Pilih semua file: `index.php`, `.htaccess`, `css/`, `js/`, `favicon.ico`, `robots.txt`
3. Klik **Move** → tujuan: `/home/namauser/public_html/`

Struktur `public_html` setelah dipindah:
```
public_html/
├── index.php
├── .htaccess
├── favicon.ico
├── robots.txt
├── css/
│   ├── bootstrap.min.css
│   └── bootstrap-icons/
└── js/
    ├── bootstrap.bundle.min.js
    └── chart.umd.min.js
```

### 3.3 Edit `index.php` di `public_html/`

Buka `/home/namauser/public_html/index.php` → klik **Edit**.

**Cari baris ini:**
```php
require __DIR__.'/../vendor/autoload.php';
```
**Ubah menjadi:**
```php
require __DIR__.'/../dpis/vendor/autoload.php';
```

**Cari baris ini:**
```php
$app = require_once __DIR__.'/../bootstrap/app.php';
```
**Ubah menjadi:**
```php
$app = require_once __DIR__.'/../dpis/bootstrap/app.php';
```

Klik **Save Changes**.

---

## Tahap 4 — Konfigurasi via Terminal / SSH

Buka **cPanel → Terminal**, atau gunakan SSH client (PuTTY / Terminal):

```bash
ssh namauser@yourdomain.com
```

### 4.1 Masuk ke Folder Laravel

```bash
cd ~/dpis
```

### 4.2 Update `.env` dengan Kredensial Database

```bash
nano .env
```

Pastikan nilai `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` sudah sesuai dengan yang dibuat di Tahap 2. Simpan: `Ctrl+X` → `Y` → `Enter`.

### 4.3 Generate Application Key

```bash
php artisan key:generate
```

Output yang benar:
```
INFO  Application key set successfully.
```

### 4.4 Set Permission Storage & Cache

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### 4.5 Jalankan Migrasi & Seeder

```bash
php artisan migrate --force
php artisan db:seed --force
```

Proses ini akan:
- Membuat semua tabel database
- Mengisi data awal (roles, permissions, user admin)

### 4.6 Buat Symlink Storage

```bash
php artisan storage:link
```

### 4.7 Optimasi Cache untuk Production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Tahap 5 — Setup PHP Version di cPanel

1. cPanel → **"MultiPHP Manager"** (atau "Select PHP Version")
2. Pilih domain Anda dari daftar
3. Ubah PHP version ke **PHP 8.2** atau **PHP 8.3**
4. Klik **Apply**

**Pastikan ekstensi berikut aktif** (klik "PHP Extensions"):

- [x] pdo_mysql
- [x] mbstring
- [x] openssl
- [x] tokenizer
- [x] xml
- [x] ctype
- [x] json
- [x] bcmath
- [x] fileinfo

---

## Tahap 6 — Verifikasi

1. Buka browser → `https://yourdomain.com`
2. Halaman login DPIS harus tampil dengan UI Bootstrap yang benar

3. Login menggunakan akun default:

   | Role | Email | Password |
   |------|-------|----------|
   | Admin | admin@dpis.com | password123 |
   | Supervisor | supervisor@dpis.com | password123 |
   | Manager | manager@dpis.com | password123 |
   | PIC Cutting | cutting@dpis.com | password123 |
   | PIC Sewing | sewing@dpis.com | password123 |
   | PIC Finishing | finishing@dpis.com | password123 |
   | PIC QC | qc@dpis.com | password123 |
   | Staff Gudang | warehouse@dpis.com | password123 |

4. **Setelah login pertama kali, segera ganti password semua akun!**

---

## Troubleshooting

### 500 Internal Server Error

**Penyebab:** Error PHP di aplikasi

**Solusi:**
1. Cek log error: `cat ~/dpis/storage/logs/laravel.log | tail -50`
2. Sementara aktifkan debug: set `APP_DEBUG=true` di `.env`, refresh, cek error, matikan lagi setelahnya

### 419 Page Expired

**Penyebab:** CSRF token / session bermasalah

**Solusi:**
```bash
chmod -R 775 ~/dpis/storage/framework/sessions
php artisan config:clear
```

### 404 Not Found (semua halaman)

**Penyebab:** `.htaccess` tidak aktif / mod_rewrite belum diaktifkan

**Solusi:** Hubungi support hosting, minta aktifkan `mod_rewrite`. Atau tambahkan di `.htaccess`:
```apache
Options +FollowSymLinks
RewriteEngine On
```

### Halaman Tampil Tanpa Style (CSS tidak muncul)

**Penyebab:** File CSS tidak terpindah ke `public_html`

**Solusi:** Pastikan folder `public_html/css/` berisi `bootstrap.min.css` dan folder `bootstrap-icons/`

### SQLSTATE: Access Denied

**Penyebab:** Kredensial database salah

**Solusi:**
1. Cek kembali `.env` — pastikan `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` benar
2. Pastikan user sudah di-assign ke database di MySQL Databases cPanel
3. Jalankan: `php artisan config:clear`

### Class Not Found / Composer Error

**Penyebab:** Folder `vendor/` tidak lengkap

**Solusi:**
```bash
cd ~/dpis
composer install --optimize-autoloader --no-dev
```

### Storage Permission Denied

**Penyebab:** Permission folder storage tidak benar

**Solusi:**
```bash
chmod -R 775 ~/dpis/storage
chmod -R 775 ~/dpis/bootstrap/cache
chown -R namauser:namauser ~/dpis/storage
```

---

## Jika Tidak Ada Akses Terminal

Jika hosting tidak menyediakan Terminal atau SSH:

### Alternatif 1 — Gunakan PHP Script Artisan Web

Buat file sementara `public_html/run.php`:
```php
<?php
// HAPUS FILE INI SETELAH SELESAI!
require __DIR__.'/../dpis/vendor/autoload.php';
$app = require_once __DIR__.'/../dpis/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->call('migrate', ['--force' => true]);
$kernel->call('db:seed', ['--force' => true]);
$kernel->call('key:generate');
echo "Done!";
```

Akses via browser: `https://yourdomain.com/run.php`

> **PENTING: Hapus file `run.php` segera setelah selesai!**

### Alternatif 2 — Siapkan Semua di Lokal

1. Generate key di lokal: `php artisan key:generate --show` → copy nilai `base64:...` → paste ke `APP_KEY` di `.env` server
2. Set DB di lokal ke hostname MySQL hosting → jalankan migrate dari lokal
3. Upload `vendor/` yang sudah ada (hasil `composer install` lokal)

---

## Update / Re-deploy

Ketika ada update code:

```bash
# 1. Upload file yang berubah ke ~/dpis/

# 2. Jalankan migrasi jika ada perubahan database
php artisan migrate --force

# 3. Bersihkan dan rebuild cache
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Ringkasan Urutan Deploy

```
✅ 1.  composer install --no-dev                     (di lokal)
✅ 2.  Edit .env → switch ke MySQL hosting           (di lokal)
✅ 3.  Buat database + user MySQL di cPanel
✅ 4.  Zip project → upload ke /home/namauser/       (via File Manager)
✅ 5.  Extract zip → folder dpis/ terbentuk
✅ 6.  Pindah isi dpis/public/ → public_html/
✅ 7.  Edit public_html/index.php → arahkan path ke dpis/
✅ 8.  SSH/Terminal: cd ~/dpis
✅ 9.  php artisan key:generate
✅ 10. chmod -R 775 storage bootstrap/cache
✅ 11. php artisan migrate --force
✅ 12. php artisan db:seed --force
✅ 13. php artisan storage:link
✅ 14. php artisan config:cache route:cache view:cache
✅ 15. Set PHP 8.2+ di MultiPHP Manager cPanel
✅ 16. Buka browser → test login di yourdomain.com
```

---

*Dokumen ini dibuat untuk DPIS v1.0 — PT DTHREE SUKSES MULIA*  
*Laravel 13 · PHP 8.2+ · MySQL · Bootstrap 5*
