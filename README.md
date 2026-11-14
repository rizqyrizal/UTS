# UTS - Sistem Login Nusa Studio

Proyek UTS individu berupa **sistem login** dengan dashboard untuk studi kasus **Nusa Studio**, sebuah studio kreatif yang perlu mengelola project klien secara internal.

Dibangun dengan **PHP native + Bootstrap 5**. Login, session, role, dan CRUD project bekerja tanpa perlu membuat database.

## Fitur

- Login & logout dengan session PHP
- Password disimpan aman menggunakan `password_hash()`
- Proteksi CSRF token pada semua form
- Halaman dashboard yang hanya bisa diakses setelah login
- CRUD project (tambah, ubah, hapus, cari)
- Registrasi anggota baru dengan role `Member`
- Daftar anggota tim
- Penyimpanan data JSON di folder `storage/`
- Halaman profil Nusa Studio sebagai halaman publik

## Cara menjalankan

### Opsi 1: XAMPP / Laragon

1. Copy folder `nusa-studio` ke `htdocs` (XAMPP) atau `www` (Laragon).
2. Jalankan Apache.
3. Buka `http://localhost/nusa-studio/`.
4. Kamu akan diarahkan ke halaman login.

### Opsi 2: PHP built-in server

```bash
php -S localhost:8000
```

Lalu buka `http://localhost:8000`.

## Akun demo

| Email | Password | Role |
| --- | --- | --- |
| `admin@nusastudio.id` | `nusa123` | Administrator |

Akun baru juga bisa dibuat lewat halaman **Daftar**.

## Struktur folder

```
nusa-studio/
├── index.php              # entry point, arahkan ke login/dashboard
├── profil.html            # halaman profil publik Nusa Studio
├── login.php              # form login
├── register.php           # form registrasi anggota
├── logout.php             # hapus session
├── dashboard.php          # dashboard + CRUD project (protected)
├── app/
│   ├── bootstrap.php      # session, helper auth, storage JSON
│   └── project-form.php   # partial form project
├── storage/
│   ├── users.json         # data akun (auto dibuat)
│   └── projects.json      # data project (auto dibuat)
└── assets/
    ├── css/
    ├── js/
    └── fonts/
```

## Alur kerja sistem

1. Pengguna membuka halaman → diarahkan ke `login.php` bila belum memiliki session.
2. Kredensial diverifikasi dengan `password_verify()`.
3. Jika valid, session dibuat ulang (`session_regenerate_id`) dan user diarahkan ke `dashboard.php`.
4. Setiap request ke dashboard diperiksa oleh `require_auth()`; user tanpa session akan dikembalikan ke halaman login.
5. Tombol keluar menjalankan `logout.php` dan menghancurkan session.
