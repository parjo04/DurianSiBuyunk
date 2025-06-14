# 🥑 Durian Si Buyunk - Sistem Manajemen Stok

Aplikasi untuk membantu manajemen stok produk durian dan olahan durian untuk dua cabang toko Durian Si Buyunk di Tasikmalaya dan Garut. Dengan sistem terpusat namun terpisah untuk masing-masing cabang.

## 🛠️ Teknologi Utama
- **PHP 7.4+** (dengan PDO untuk koneksi database)
- **MySQL 5.7+**
- **Bootstrap 5** (untuk tampilan responsif)
- **Font Awesome** (untuk ikon)

## 🎯 Fitur Utama
1. **Sistem Login Terpisah** untuk admin cabang Tasik dan Garut
2. **Manajemen Produk** (CRUD - Create, Read, Update, Delete)
3. **Manajemen Kategori** produk
4. **Tampilan Publik** untuk pelanggan melihat produk dengan filter cabang
5. **Reset Password** dengan kode khusus
6. **Keamanan** dengan session dan validasi ketat
7. **Stok Terpisah** per cabang dengan kontrol akses yang tepat

## 📂 Struktur Folder

```
DurianSiBuyunk/
│
├── index.php                  # Halaman utama login admin (pilih cabang)
│
├── init/
│   └── database.php            # Script inisialisasi database (dijalankan sekali)
│
├── config/
│   ├── database.php            # Konfigurasi koneksi database
│   └── config.php              # Pengaturan global aplikasi
│
├── public/                     # Halaman yang bisa diakses umum
│   ├── index.php               # Tampilan produk untuk pelanggan (dengan filter cabang)
│   └── assets/
│       ├── css/
│       ├── js/
│       └── images/             # Gambar produk & logo
│           └── products/       # Folder gambar produk
│
├── cabang/                     # Folder khusus admin cabang
│   ├── tasik/                  # Admin Tasikmalaya
│   │   ├── dashboard.php       # Dashboard utama
│   │   ├── auth/               # Sistem autentikasi
│   │   │   ├── login.php
│   │   │   ├── logout.php
│   │   │   └── reset.php
│   │   ├── produk/             # Manajemen produk
│   │   │   ├── index.php       # Daftar produk
│   │   │   ├── tambah.php      # Tambah produk
│   │   │   ├── edit.php        # Edit produk
│   │   │   └── hapus.php       # Hapus produk
│   │   └── kategori/           # Manajemen kategori
│   │       └── index.php       # Kelola kategori
│   │
│   └── garut/                  # Admin Garut (struktur sama dengan Tasik)
│       ├── dashboard.php
│       ├── auth/
│       │   ├── login.php
│       │   ├── logout.php
│       │   └── reset.php
│       ├── produk/
│       │   ├── index.php
│       │   ├── tambah.php
│       │   ├── edit.php
│       │   └── hapus.php
│       └── kategori/
│           └── index.php
│
├── includes/                   # Fungsi-fungsi pendukung
│   ├── auth.php                # Sistem otentikasi
│   ├── functions.php           # Fungsi utilitas
│   └── header.php              # Template header
│
└── README.md                   # Dokumentasi ini
```

## 🛠️ Cara Instalasi (Untuk Pemula)

### 1. Persyaratan Sistem
- Web server (XAMPP/WAMP/MAMP/LAMP)
- PHP 7.4 atau lebih baru
- MySQL 5.7 atau lebih baru
- Browser modern (Chrome, Firefox, Edge)

### 2. Langkah-langkah Instalasi
1. **Download Aplikasi**
   - Clone atau download ZIP dari repository ini
   - Letakkan folder `DurianSiBuyunk` di dalam `htdocs` (XAMPP) atau `www` (WAMP)

2. **Inisialisasi Database**
   - Buka browser, akses: `http://localhost/DurianSiBuyunk/init/database.php`
   - Script akan otomatis membuat database dan tabel-tabel yang diperlukan
   - **Catatan:** Hanya dijalankan sekali saja, setelah berhasil hapus folder `init` untuk keamanan

3. **Konfigurasi Aplikasi**
   - Buka file `config/database.php`
   - Sesuaikan dengan setting database lokal Anda:
     ```php
     define('DB_HOST', 'localhost');    // Host database
     define('DB_USER', 'root');         // Username database
     define('DB_PASS', '');            // Password database (kosong jika tidak ada)
     define('DB_NAME', 'durian_db');    // Nama database
     ```

4. **Akses Aplikasi**
   - **Halaman Utama (Pilih Login):** `http://localhost/DurianSiBuyunk/`
   - **Halaman Publik:** `http://localhost/DurianSiBuyunk/public/`
   - **Admin Tasik:** `http://localhost/DurianSiBuyunk/cabang/tasik/auth/login.php`
   - **Admin Garut:** `http://localhost/DurianSiBuyunk/cabang/garut/auth/login.php`

## 🔐 Informasi Login Default
| Role          | Username      | Password    |
|---------------|---------------|-------------|
| Admin Tasik   | admin_tasik   | tasikmalaya |
| Admin Garut   | admin_garut   | garut       |

**Catatan:** Setelah login pertama, sangat disarankan untuk mengganti password default!

## 🎨 Fitur Design Thinking

### 1. Halaman Utama (index.php)
- **User-Centered Design**: Pilihan cabang yang jelas dengan visual yang berbeda
- **Visual Hierarchy**: Warna hijau untuk Tasikmalaya, biru untuk Garut
- **Call-to-Action**: Tombol login yang prominent dan mudah diakses
- **Progressive Disclosure**: Informasi kontak dan fitur tersedia tanpa overwhelm

### 2. Halaman Publik
- **Filter Intuitif**: Filter berdasarkan cabang dan kategori
- **Visual Feedback**: Badge stok per cabang dengan warna yang konsisten
- **Responsive Design**: Optimal di semua perangkat
- **Floating Action Button**: Tombol login admin yang mudah diakses

### 3. Admin Dashboard
- **Branch-Specific Theming**: Setiap cabang memiliki tema warna yang konsisten
- **Quick Actions**: Akses cepat ke fungsi yang sering digunakan
- **Data Visualization**: Statistik yang mudah dipahami dengan ikon dan warna

## 📊 Manajemen Stok per Cabang

### Fitur Stok Terpisah:
- **Admin Tasikmalaya**: Hanya dapat mengelola stok Tasikmalaya
- **Admin Garut**: Hanya dapat mengelola stok Garut
- **Halaman Publik**: Menampilkan stok dari kedua cabang dengan filter
- **Kontrol Akses**: Setiap admin hanya melihat dan mengelola data cabangnya

### Tampilan Stok:
- **Publik**: Menampilkan stok kedua cabang atau filter per cabang
- **Admin**: Menampilkan stok cabang sendiri dengan informasi cabang lain (read-only)
- **Badge Warna**: Hijau untuk Tasikmalaya, Biru untuk Garut

## 🔄 Reset Password
Jika lupa password:
1. Klik "Lupa Password" di halaman login
2. Masukkan kode reset: `BUYUNK2025`
3. Masukkan username dan password baru
4. Password akan diupdate secara otomatis

## ⚠️ Keamanan
- Password disimpan dengan enkripsi `password_hash()`
- Proteksi terhadap SQL Injection dengan PDO prepared statement
- Validasi input ketat untuk semua form
- Pembatasan akses dengan session
- Direct URL access diblokir jika tidak login
- Kontrol akses per cabang yang ketat

## 📊 Struktur Database
Aplikasi akan membuat 5 tabel utama:

1. **users** - Menyimpan data login admin
2. **cabang** - Daftar cabang toko
3. **kategori** - Kategori produk (Durian, Olahan, dll)
4. **produk** - Data produk beserta stok per cabang (stok_tasik, stok_garut)
5. **jenis_durian** - Jenis-jenis durian yang dijual

Relasi antar tabel menggunakan foreign key untuk integritas data.

## 🎯 Fitur Unggulan

### 1. Design Thinking Implementation
- **Empathy**: Interface yang mudah dipahami untuk admin non-teknis
- **Define**: Pemisahan yang jelas antara fungsi publik dan admin
- **Ideate**: Solusi visual untuk membedakan cabang
- **Prototype**: Layout yang konsisten dan intuitif
- **Test**: Feedback visual yang jelas untuk setiap aksi

### 2. User Experience (UX)
- **Consistent Navigation**: Navigasi yang sama di semua halaman admin
- **Visual Feedback**: Alert, badge, dan notifikasi yang informatif
- **Error Prevention**: Validasi form dan konfirmasi untuk aksi penting
- **Accessibility**: Kontras warna yang baik dan ukuran font yang readable

### 3. Responsive Design
- **Mobile-First**: Optimized untuk perangkat mobile
- **Flexible Grid**: Layout yang adaptif untuk semua ukuran layar
- **Touch-Friendly**: Button dan link yang mudah di-tap di mobile

## 🆘 Bantuan & Dukungan
Jika menemui masalah:
1. Pastikan semua persyaratan sistem terpenuhi
2. Cek error log di PHP atau MySQL
3. Pastikan folder `public/assets/images/products/` memiliki permission write
4. Untuk bantuan lebih lanjut, hubungi:
   - Email: support@duriansibuyunk.com
   - WhatsApp: +62 812-3456-7890

## 📜 Lisensi
Aplikasi ini dikembangkan untuk keperluan internal Durian Si Buyunk. Dilarang memperbanyak atau mendistribusikan tanpa izin tertulis.

---

**© 2025 Durian Si Buyunk. All rights reserved.**