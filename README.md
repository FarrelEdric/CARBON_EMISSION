#  ACE (AirNav Carbon Emission) - Panduan Instalasi & Penggunaan

Aplikasi web pemantauan emisi karbon penerbangan (*Aviation Carbon Emission Calculator & Monitoring System*) berbasis **Laravel 12** dan **PostgreSQL**.

---

##  1. Kebutuhan Sistem (Prerequisites)

Sebelum memulai instalasi, pastikan laptop / PC Anda sudah terpasang:

1. **PHP**: Versi `8.2` atau lebih baru
   - Pastikan ekstensi berikut sudah aktif di `php.ini`:
     - `extension=pdo_pgsql`
     - `extension=pgsql`
     - `extension=fileinfo`
     - `extension=gd` (untuk export/import file spreadsheet)
     - `extension=zip`
2. **Composer**: Versi `2.x` ([Unduh Composer](https://getcomposer.org/))
3. **Node.js & NPM**: Node.js `v18+` / `v20+` ([Unduh Node.js](https://nodejs.org/))
4. **PostgreSQL**: Versi `14+` / `15+` / `16+` (dan pgAdmin jika ingin antarmuka GUI)
5. **Git**: Untuk cloning repository

---

##  2. Langkah-Langkah Instalasi (Step-by-Step)

Buka terminal (**Command Prompt**, **PowerShell**, atau **Git Bash**), lalu ikuti urutan berikut:

### Langkah 1: Masuk ke Folder Project
```bash
cd "d:\MAGANGHUB\PROJECTAIRNAV\carbon emmision\ace"
```

---

### Langkah 2: Buat Database di PostgreSQL
Buka aplikasi **pgAdmin** atau melalui command line `psql`, lalu buat database baru bernama `carbonemmision`:
```sql
CREATE DATABASE carbonemmision;
```

---

### Langkah 3: Konfigurasi File Lingkungan (`.env`)
Salin file template `.env.example` menjadi `.env` (jika belum ada):
```bash
# Untuk Windows Command Prompt:
copy .env.example .env

# Atau jika di Git Bash / PowerShell:
cp .env.example .env
```

Buka file [`.env`](file:///d:/MAGANGHUB/PROJECTAIRNAV/carbon%20emmision/ace/.env) menggunakan teks editor (VS Code, Notepad, dll), lalu sesuaikan konfigurasi database Anda:
```env
APP_NAME="ACE"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

# Konfigurasi Database PostgreSQL
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=carbonemmision
DB_USERNAME=postgres
DB_PASSWORD=password_postgres_anda

# Konfigurasi Flight Tracking API (Opsional / Jika Diperlukan)
FLIGHT_TRACKING_PROVIDER=flightradar24
FLIGHT_API_KEY=your_api_key_here
```
>  **Catatan**: Ganti `password_postgres_anda` dengan password database PostgreSQL yang Anda buat saat instalasi PostgreSQL.

---

### Langkah 4: Install Dependensi PHP (Composer)
Jalankan perintah ini untuk mengunduh semua library PHP:
```bash
composer install
```

---

### Langkah 5: Generate Application Key
Buat kunci enkripsi unik aplikasi Laravel:
```bash
php artisan key:generate
```

---

### Langkah 6: Jalankan Migrasi & Data Awal (Seeder)
Buat tabel database dan isi data referensi (bandara, jenis pesawat, faktor emisi, rute, dan akun admin):
```bash
php artisan migrate:fresh --seed
```

> **Informasi Akun Default Admin:**
> - **Username**: `admin`
> - **Password**: `admin`

---

### Langkah 7: Install Dependensi JavaScript & Kompilasi Aset
Pasang dependensi antarmuka frontend (Tailwind CSS, Alpine.js, Chart.js, Leaflet):
```bash
npm install
npm run build
```

---

##  3. Menjalankan Aplikasi

Buka **2 jendela terminal**:

### Terminal 1: Menjalankan Web Server Laravel
```bash
php artisan serve
```
*Server akan berjalan di:* `http://127.0.0.1:8000` atau `http://localhost:8000`

### Terminal 2: Menjalankan Vite (Mode Development / Hot-Reload)
```bash
npm run dev
```

---

##  4. Login ke Sistem

1. Buka browser dan akses: **[http://localhost:8000](http://localhost:8000)**
2. Klik tombol **Login** atau langsung kunjungi **[http://localhost:8000/login](http://localhost:8000/login)**
3. Masukkan kredensial:
   - **Username**: `admin`
   - **Password**: `admin`
4. Anda sekarang dapat mengakses menu **Dashboard**, **Kalkulator Emisi**, **Data Bandara**, **Pesawat**, **Rute Operasional**, dan **Laporan**.

---

##  5. Fitur Peta & Radar Penerbangan (Catatan Khusus)

- **Peta Lokal / Offline**: Secara default aplikasi sudah dilengkapi file koordinat GeoJSON di folder `public/data/` (`indonesia-provinces.geojson` dan `world-countries.geojson`) agar peta dapat tampil normal tanpa koneksi internet.
- **Mode Satelit & Radar Real-Time**: Untuk melacak posisi pesawat secara real-time dari API Flightradar24 atau OpenSky, pastikan mengisi `FLIGHT_API_KEY` pada file `.env`.

---

##  6. Penyelesaian Masalah Umum (Troubleshooting)

| Kendala | Penyebab | Solusi |
|---|---|---|
| `FATAL: the database system is starting up` | Layanan PostgreSQL baru saja dinyalakan dan belum siap menerima query. | Tunggu 5-10 detik lalu refresh halaman web. |
| `Connection refused` atau `could not connect to server` | PostgreSQL belum berjalan atau port salah. | Buka Windows Services (`services.msc`), cari layanan `postgresql-x64-...`, lalu klik **Start**. |
| `Call to undefined function pg_connect()` | Ekstensi PostgreSQL di PHP belum aktif. | Buka file `php.ini`, hapus tanda titik koma (`;`) di depan baris `extension=pdo_pgsql` dan `extension=pgsql`, lalu restart terminal. |
| Peta tidak muncul / layer kosong | Cache browser atau koneksi tile map terblokir. | Klik tombol toggle peta di kanan atas untuk beralih antara mode **Lokal / Satelit / Standar**. |
| Tampilan berantakan (CSS tidak termuat) | Aset frontend belum dikompilasi. | Jalankan `npm run build` atau biarkan `npm run dev` berjalan di terminal terpisah. |

---

*Selamat menggunakan ACE - AirNav Carbon Emission System!*
