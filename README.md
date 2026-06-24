# MENTARI Backend

Backend Laravel untuk aplikasi Android MENTARI, dilengkapi REST API, autentikasi Laravel Sanctum, dashboard admin Filament, database MySQL, data awal, dan test otomatis.

## Teknologi

- PHP 8.2+
- Laravel 12
- Filament 5
- Laravel Sanctum
- MySQL/MariaDB
- PHPUnit

## Fitur

- Login dan registrasi siswa menggunakan bearer token.
- Profil siswa dan data sekolah.
- Check-in mood harian.
- Screening DASS-21 dan perhitungan severity.
- Alert risiko berdasarkan hasil screening.
- Konten edukasi dan rekomendasi self-care.
- Postingan komunitas dan fitur like.
- Dashboard admin dengan statistik, tren mood, dan alert terbaru.
- CRUD admin untuk seluruh tabel utama.

> Hasil DASS-21 di aplikasi ini adalah screening awal, bukan diagnosis klinis.

## Menjalankan Project

Pastikan Apache/MySQL XAMPP atau MySQL lokal sudah aktif.

```bash
composer install
copy .env.example .env
php artisan key:generate
```

Buat database:

```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS mentari CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Sesuaikan koneksi di `.env` bila username atau password MySQL berbeda:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mentari
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan migrasi dan data awal:

```bash
php artisan migrate --seed
```

Jalankan server:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

## Dashboard Admin

URL:

```text
http://127.0.0.1:8000/admin
```

Akun demo:

```text
Email: admin@mentari.test
Password: Mentari123!
```

Ganti password demo sebelum aplikasi dipakai di lingkungan production.

Dashboard admin dapat mengelola:

- sekolah dan pengguna;
- pilihan serta catatan mood;
- pertanyaan, jawaban, dan hasil screening;
- kategori serta konten edukasi;
- rekomendasi;
- postingan dan like komunitas;
- alert risiko.

## REST API Android

Base URL:

```text
http://127.0.0.1:8000/api/v1
```

Untuk Android Emulator gunakan:

```text
http://10.0.2.2:8000/api/v1
```

Untuk perangkat Android fisik gunakan IP LAN komputer, misalnya:

```text
http://192.168.1.10:8000/api/v1
```

Komputer dan HP harus berada di jaringan yang sama. Izinkan port `8000` pada Windows Firewall bila koneksi ditolak.

### Autentikasi

Login:

```http
POST /api/v1/auth/login
Content-Type: application/json
Accept: application/json
```

```json
{
  "email": "siswa@mentari.test",
  "password": "Mentari123!",
  "device_name": "android"
}
```

Kirim token dari respons login pada request berikutnya:

```http
Authorization: Bearer TOKEN_DARI_LOGIN
Accept: application/json
```

### Endpoint

| Method | Endpoint | Fungsi |
|---|---|---|
| POST | `/auth/register` | Registrasi siswa |
| POST | `/auth/login` | Login dan mendapatkan token |
| GET | `/auth/me` | Data pengguna login |
| PATCH | `/auth/profile` | Memperbarui profil |
| PUT | `/auth/password` | Mengganti password |
| POST | `/auth/logout` | Menghapus token aktif |
| GET | `/dashboard` | Ringkasan halaman utama Android |
| GET | `/mood-options` | Daftar pilihan mood |
| GET | `/mood-entries` | Riwayat mood pengguna |
| POST | `/mood-entries` | Membuat/memperbarui mood harian |
| DELETE | `/mood-entries/{id}` | Menghapus catatan mood |
| GET | `/screening/questions` | Pertanyaan screening aktif |
| GET | `/screening/results` | Riwayat hasil screening |
| POST | `/screening/results` | Mengirim jawaban dan menghitung hasil |
| GET | `/education` | Kategori dan konten edukasi |
| GET | `/education/search?q=...` | Mencari konten |
| GET | `/education/{id}` | Detail konten |
| GET | `/recommendations` | Daftar rekomendasi |
| GET | `/community/posts` | Daftar postingan sekolah |
| POST | `/community/posts` | Membuat postingan |
| DELETE | `/community/posts/{id}` | Menghapus postingan sendiri |
| POST | `/community/posts/{id}/like` | Toggle like |
| GET | `/risk-alerts` | Daftar alert milik pengguna |
| PATCH | `/risk-alerts/{id}/dismiss` | Admin menandai alert sudah dibaca |

Semua endpoint selain register dan login memerlukan `Authorization: Bearer`.

## Format Submit Screening

Ambil pertanyaan dari `/screening/questions`, lalu kirim seluruh pertanyaan aktif:

```json
{
  "answers": [
    {
      "question_id": 1,
      "score": 0
    },
    {
      "question_id": 2,
      "score": 2
    }
  ]
}
```

Nilai jawaban harus `0` sampai `3`. Backend menghitung skor depresi, kecemasan, dan stres serta membuat alert bila hasil memerlukan perhatian.

Setiap siswa hanya dapat mengirim screening satu kali. Admin dapat membuka akses satu kali lagi melalui menu Pengguna tanpa menghapus riwayat screening sebelumnya.

## Test dan Pemeriksaan Kode

```bash
php artisan test
vendor/bin/pint --test
composer audit
```

## Data Demo

Seeder membuat:

- satu sekolah demo;
- akun admin dan siswa;
- lima pilihan mood;
- 21 pertanyaan screening;
- konten edukasi;
- rekomendasi aktivitas.

Akun siswa demo:

```text
Email: siswa@mentari.test
Password: Mentari123!
```

Untuk mengulang database dari awal:

```bash
php artisan migrate:fresh --seed
```
