# Panduan Deploy DPIS ke cPanel Hosting
## DPIS — DTHREE Production Integration System v1.0
### PT DTHREE SUKSES MULIA

---

## Daftar Isi

1. [Spesifikasi Sistem](#1-spesifikasi-sistem)
2. [Persiapan: Download Project dari GitHub](#2-persiapan-download-project-dari-github)
3. [Setup Database MySQL di cPanel](#3-setup-database-mysql-di-cpanel)
4. [Upload & Ekstrak File ke cPanel](#4-upload--ekstrak-file-ke-cpanel)
5. [Konfigurasi File .env](#5-konfigurasi-file-env)
6. [Setup PHP & Ekstensi di cPanel](#6-setup-php--ekstensi-di-cpanel)
7. [Konfigurasi Document Root](#7-konfigurasi-document-root)
8. [Jalankan Perintah Artisan via Terminal](#8-jalankan-perintah-artisan-via-terminal)
9. [Verifikasi & Test Login](#9-verifikasi--test-login)
10. [Akun Pengguna Default](#10-akun-pengguna-default)
11. [Troubleshooting](#11-troubleshooting)
12. [Langkah Setelah Deploy](#12-langkah-setelah-deploy)

---

## 1. Spesifikasi Sistem

### Kebutuhan Hosting Minimum

| Kebutuhan       | Spesifikasi                          |
|-----------------|--------------------------------------|
| PHP             | **8.3 atau 8.4** (wajib)             |
| Database        | MySQL 5.7+ / MariaDB 10.3+           |
| Disk Space      | Minimal 300 MB                       |
| RAM             | Minimal 256 MB                       |
| Terminal / SSH  | Sangat disarankan                    |
| cPanel Version  | 90+ (disarankan)                     |

### Ekstensi PHP yang Wajib Aktif

```
pdo_mysql    mbstring    openssl     tokenizer
xml          ctype       json        bcmath
fileinfo     intl        zip         curl
```

### Stack Teknologi

- **Framework**: Laravel 13 (PHP ^8.3)
- **Database**: MySQL (production) / SQLite (development)
- **UI**: Bootstrap 5.3.3 + Bootstrap Icons 1.11.3 + Chart.js 4.4.0
- **Asset**: Semua CSS/JS sudah tersimpan lokal di `public/` — tidak butuh CDN

---

## 2. Persiapan: Download Project dari GitHub

### Langkah di Komputer Lokal Anda

**A. Clone repository:**
```bash
git clone https://github.com/akmaldthree-ux/wiptracker.git dpis
cd dpis
git checkout claude/clever-bohr-6i3l8a
```

**B. Install dependencies Composer:**
```bash
composer install --optimize-autoloader --no-dev
```
> Pastikan Composer sudah terinstall di komputer Anda. Download di [getcomposer.org](https://getcomposer.org)

**C. Buat file `.env` untuk production:**

Buat file bernama `.env` (bukan `.env.example`) dengan isi berikut.
Sesuaikan bagian yang bertanda `← GANTI`:

```env
APP_NAME="DPIS - DTHREE Production"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://yourdomain.com    ← GANTI dengan domain Anda

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=namauser_dpis         ← GANTI (lihat Langkah 3)
DB_USERNAME=namauser_dpis         ← GANTI (lihat Langkah 3)
DB_PASSWORD=PasswordKuat123!      ← GANTI dengan password Anda

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

> `APP_KEY` dikosongkan dulu — akan di-generate otomatis di server.

**D. Buat file ZIP project:**

**Windows:** Klik kanan folder `dpis` → Send to → Compressed (zipped) folder

**Mac / Linux (Terminal):**
```bash
cd ..
zip -r dpis.zip dpis \
  --exclude "dpis/.git/*" \
  --exclude "dpis/node_modules/*" \
  --exclude "dpis/database/*.sqlite" \
  --exclude "dpis/storage/logs/*" \
  --exclude "dpis/.env.example"
```

> Ukuran ZIP normalnya sekitar 20–40 MB (termasuk folder `vendor/`)

---

## 3. Setup Database MySQL di cPanel

1. Login ke **cPanel** → cari menu **"MySQL® Databases"**

2. **Buat Database Baru:**
   - Di kolom "Create New Database", ketik: `dpis`
   - Klik **Create Database**
   - Nama lengkap otomatis: `namauser_dpis`

3. **Buat MySQL User Baru:**
   - Scroll ke "MySQL Users" → "Add New User"
   - Username: `dpis`
   - Password: buat password yang kuat (gunakan tombol **Password Generator**)
   - Klik **Create User**
   - Nama lengkap user: `namauser_dpis`

4. **Hubungkan User ke Database:**
   - Scroll ke "Add User To Database"
   - Pilih User: `namauser_dpis`
   - Pilih Database: `namauser_dpis`
   - Klik **Add**
   - Di halaman berikutnya centang **ALL PRIVILEGES**
   - Klik **Make Changes**

5. **Catat informasi ini** untuk diisi ke file `.env`:
   ```
   DB_DATABASE = namauser_dpis
   DB_USERNAME = namauser_dpis
   DB_PASSWORD = [password yang Anda buat]
   DB_HOST     = localhost
   ```

---

## 4. Upload & Ekstrak File ke cPanel

### A. Upload via File Manager

1. Buka **cPanel → File Manager**
2. Navigasi ke `/home/namauser/` (satu level di atas `public_html`)
3. Klik tombol **Upload** di toolbar atas
4. Pilih file `dpis.zip` → tunggu hingga upload 100%
5. Setelah selesai, klik kanan `dpis.zip` → **Extract**
6. Pastikan extract ke `/home/namauser/` → klik **Extract File(s)**

Setelah proses selesai, struktur folder menjadi:

```
/home/namauser/
│
├── public_html/          ← yang diakses browser (domain Anda)
│   └── (masih kosong / isi lama)
│
└── dpis/                 ← folder Laravel hasil extract
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── public/           ← ISI FOLDER INI yang akan dipindah ke public_html
    │   ├── index.php
    │   ├── .htaccess
    │   ├── css/
    │   └── js/
    ├── resources/
    ├── routes/
    ├── storage/
    ├── vendor/
    └── .env
```

### B. Pindahkan Isi Folder `public/` ke `public_html/`

1. Di File Manager, masuk ke `/home/namauser/dpis/public/`
2. Tekan **Ctrl+A** (Select All) untuk memilih semua file
3. Klik **Move** di toolbar
4. Tujuan: `/home/namauser/public_html/`
5. Klik **Move File(s)**

Setelah dipindah, `public_html` berisi:

```
public_html/
├── index.php         ← entry point Laravel
├── .htaccess         ← konfigurasi Apache URL rewriting
├── favicon.ico
├── robots.txt
├── css/
│   ├── bootstrap.min.css
│   └── bootstrap-icons/
└── js/
    ├── bootstrap.bundle.min.js
    └── chart.umd.min.js
```

### C. Edit `index.php` di `public_html/`

1. Klik kanan `index.php` di `public_html/` → **Edit**
2. Cari dan ubah **dua baris** berikut:

**Baris 1 — ubah path autoload:**
```php
// SEBELUM:
require __DIR__.'/../vendor/autoload.php';

// SESUDAH:
require __DIR__.'/../dpis/vendor/autoload.php';
```

**Baris 2 — ubah path bootstrap:**
```php
// SEBELUM:
$app = require_once __DIR__.'/../bootstrap/app.php';

// SESUDAH:
$app = require_once __DIR__.'/../dpis/bootstrap/app.php';
```

3. Klik **Save Changes**

---

## 5. Konfigurasi File .env

File `.env` sudah ikut ter-upload bersama project di langkah sebelumnya.

Jika perlu edit ulang:
1. File Manager → `/home/namauser/dpis/.env`
2. Klik kanan → **Edit**
3. Pastikan semua nilai sudah benar:

```env
APP_URL=https://yourdomain.com
DB_DATABASE=namauser_dpis
DB_USERNAME=namauser_dpis
DB_PASSWORD=PasswordKuat123!
```

4. **Save Changes**

---

## 6. Setup PHP & Ekstensi di cPanel

### A. Set Versi PHP

1. cPanel → cari **"MultiPHP Manager"**
2. Centang domain Anda dari daftar
3. Pilih versi PHP: **PHP 8.3** atau **PHP 8.4**
4. Klik **Apply**

> Jika PHP 8.3/8.4 tidak tersedia, hubungi support hosting Anda.

### B. Aktifkan Ekstensi PHP

1. cPanel → **"MultiPHP INI Editor"** (atau "PHP Extensions")
2. Pilih domain Anda
3. Pastikan ekstensi berikut **dicentang/aktif**:

```
☑ pdo_mysql       ☑ mbstring        ☑ openssl
☑ tokenizer       ☑ xml             ☑ ctype
☑ json            ☑ bcmath          ☑ fileinfo
☑ intl            ☑ zip             ☑ curl
```

4. Klik **Save**

---

## 7. Konfigurasi Document Root

Jika domain Anda mengarah langsung ke `public_html`, proses di Langkah 4B & 4C sudah cukup.

**Jika menggunakan Subdomain:**
1. cPanel → **Subdomains**
2. Pilih subdomain yang digunakan
3. Ubah **Document Root** ke `/home/namauser/public_html/`

**Alternatif tanpa edit index.php (lebih bersih):**

Jika hosting mendukung konfigurasi document root langsung ke subfolder, arahkan domain ke `/home/namauser/dpis/public/` — maka `index.php` tidak perlu diedit.

---

## 8. Jalankan Perintah Artisan via Terminal

Buka **cPanel → Terminal** (atau SSH dengan PuTTY):

```bash
ssh namauser@yourdomain.com
```

Masuk ke folder Laravel:
```bash
cd ~/dpis
```

Jalankan perintah berikut **secara berurutan**:

### 8.1 — Generate Application Key
```bash
php artisan key:generate
```
Output yang benar:
```
INFO  Application key set successfully.
```

### 8.2 — Set Permission Folder
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### 8.3 — Jalankan Migrasi Database
```bash
php artisan migrate --force
```
Output yang benar (setiap tabel akan tercetak):
```
INFO  Running migrations.
  2024_01_01_000001_create_core_tables ......... DONE
  2026_06_16_014719_create_permission_tables ... DONE
  2026_06_18_000001_create_sewing_locations .... DONE
  2026_06_18_000002_create_cutting_plans ....... DONE
```

### 8.4 — Isi Data Awal (Seeder)
```bash
php artisan db:seed --force
```
Ini akan membuat:
- Roles & permissions
- 5 stasiun produksi (Cutting, Sewing, Finishing, QC, Warehouse)
- Akun admin dan semua user default

### 8.5 — Buat Symlink Storage
```bash
php artisan storage:link
```

### 8.6 — Optimasi Cache Production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 9. Verifikasi & Test Login

1. Buka browser → `https://yourdomain.com`
2. Halaman login DPIS harus tampil dengan tampilan Bootstrap yang benar
3. Jika CSS tidak muncul → pastikan folder `public_html/css/` sudah ada
4. Jika redirect terus-menerus → cek `.htaccess` sudah ada di `public_html/`

---

## 10. Akun Pengguna Default

Login pertama kali menggunakan akun berikut:

| Role            | Email                    | Password    |
|-----------------|--------------------------|-------------|
| Admin           | admin@dpis.com           | password123 |
| Supervisor      | supervisor@dpis.com      | password123 |
| Manager         | manager@dpis.com         | password123 |
| PIC Cutting     | cutting@dpis.com         | password123 |
| PIC Sewing      | sewing@dpis.com          | password123 |
| PIC Finishing   | finishing@dpis.com       | password123 |
| PIC QC          | qc@dpis.com              | password123 |
| Staff Gudang    | warehouse@dpis.com       | password123 |

> **PENTING:** Segera ganti password semua akun setelah login pertama!
>
> Caranya: Login → klik nama user di pojok kanan atas → **Profil Saya** → ubah password

---

## 11. Troubleshooting

### Error 500 — Internal Server Error
**Penyebab:** Error PHP di aplikasi

**Solusi:**
```bash
# Lihat log error terbaru
tail -50 ~/dpis/storage/logs/laravel.log

# Aktifkan debug sementara (matikan setelah selesai!)
# Edit .env: APP_DEBUG=true
# Refresh browser → lihat pesan error
# Setelah selesai: APP_DEBUG=false → php artisan config:cache
```

### Error 419 — Page Expired (CSRF)
**Penyebab:** Session bermasalah

**Solusi:**
```bash
chmod -R 775 ~/dpis/storage/framework/sessions
php artisan config:clear
php artisan cache:clear
```

### Error 404 — Semua URL Tidak Ditemukan
**Penyebab:** `.htaccess` tidak aktif / mod_rewrite belum diaktifkan

**Solusi:**
- Pastikan file `.htaccess` ada di `public_html/`
- Hubungi support hosting, minta aktifkan `mod_rewrite`
- Atau tambahkan di `.htaccess`:
```apache
Options +FollowSymLinks
RewriteEngine On
```

### Halaman Tampil Tanpa CSS / Tampilan Rusak
**Penyebab:** Folder `public_html/css/` tidak ada atau path salah

**Solusi:**
- Pastikan `public_html/css/bootstrap.min.css` ada
- Pastikan `public_html/css/bootstrap-icons/` ada (folder ikon)
- Pastikan `APP_URL` di `.env` sesuai domain (bukan localhost)

### SQLSTATE: Access Denied — Database Error
**Penyebab:** Kredensial database salah atau user belum diberi akses

**Solusi:**
```bash
# Cek .env
cat ~/dpis/.env | grep DB_

# Setelah edit .env, bersihkan config cache:
php artisan config:clear
php artisan config:cache
```
- Pastikan user MySQL sudah di-assign ke database di cPanel MySQL Databases

### Class Not Found / Composer Error
**Penyebab:** Folder `vendor/` tidak lengkap atau tidak ada

**Solusi:**
```bash
cd ~/dpis
composer install --optimize-autoloader --no-dev
```

### Storage Permission Denied
**Solusi:**
```bash
chmod -R 775 ~/dpis/storage
chmod -R 775 ~/dpis/bootstrap/cache
chown -R namauser:namauser ~/dpis/storage
```

### Jika Tidak Ada Akses Terminal / SSH

Buat file sementara `public_html/setup.php`:

```php
<?php
// HAPUS FILE INI SEGERA SETELAH SELESAI!
chdir(__DIR__ . '/../dpis');
require __DIR__ . '/../dpis/vendor/autoload.php';
$app = require_once __DIR__ . '/../dpis/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "<pre>";
$kernel->call('key:generate');     echo "\n";
$kernel->call('migrate', ['--force' => true]);  echo "\n";
$kernel->call('db:seed', ['--force' => true]);  echo "\n";
$kernel->call('storage:link');     echo "\n";
$kernel->call('config:cache');     echo "\n";
$kernel->call('route:cache');      echo "\n";
$kernel->call('view:cache');       echo "\n";
echo "</pre><strong style='color:red'>HAPUS FILE INI SEKARANG!</strong>";
```

Akses: `https://yourdomain.com/setup.php`
**Langsung hapus file ini setelah selesai!**

---

## 12. Langkah Setelah Deploy

### Konfigurasi Awal Sistem

Setelah berhasil login sebagai Admin, lakukan konfigurasi berikut sesuai urutan:

**1. Ganti semua password default**
> Profil Saya → Ubah Password (lakukan untuk setiap akun)

**2. Setup Master Data** (menu Master Data di sidebar)
- **Produk** → tambahkan produk yang diproduksi
- **Series** → tambahkan seri produk
- **Warna** → tambahkan daftar warna
- **Ukuran** → tambahkan ukuran (S, M, L, XL, dst.)
- **Stasiun** → periksa 5 stasiun default, sesuaikan nama & threshold
- **Tempat Sewing** → tambahkan lokasi/tempat sewing yang ada

**3. Tambah User Sesuai Kebutuhan**
> Menu Users → Tambah User → assign role yang sesuai

**4. Buat Production Order Pertama**
> Menu Order Produksi → Buat Order Baru

**5. Buat Cutting Plan**
> Menu Cutting Plan → Buat Plan → pilih order, tanggal, target qty

---

## Checklist Deploy (Ringkasan)

```
PERSIAPAN LOKAL
□ Clone/download project dari GitHub
□ composer install --optimize-autoloader --no-dev
□ Buat file .env (isi APP_URL, DB_*, SESSION_DRIVER=file, CACHE_STORE=file)
□ Buat ZIP project (exclude .git, node_modules, *.sqlite)

DI cPANEL
□ Buat database MySQL + user + assign ALL PRIVILEGES
□ Upload dpis.zip ke /home/namauser/
□ Extract ZIP → folder dpis/ terbentuk
□ Pindah isi dpis/public/ → public_html/
□ Edit public_html/index.php → ubah 2 baris path

KONFIGURASI
□ Set PHP 8.3 atau 8.4 di MultiPHP Manager
□ Aktifkan ekstensi: pdo_mysql, mbstring, openssl, xml, bcmath, dll.
□ Pastikan .env sudah berisi credential database yang benar

TERMINAL / SSH
□ cd ~/dpis
□ php artisan key:generate
□ chmod -R 775 storage bootstrap/cache
□ php artisan migrate --force
□ php artisan db:seed --force
□ php artisan storage:link
□ php artisan config:cache route:cache view:cache

VERIFIKASI
□ Buka https://yourdomain.com → halaman login muncul
□ Login admin@dpis.com / password123 → berhasil masuk
□ Navigasi semua menu → tidak ada error
□ Ganti semua password default
□ Tambah master data sesuai kebutuhan perusahaan
```

---

*Panduan ini dibuat untuk DPIS v1.0 — PT DTHREE SUKSES MULIA*
*Laravel 13 · PHP 8.3+ · MySQL · Bootstrap 5.3*
*Terakhir diperbarui: Juni 2026*
