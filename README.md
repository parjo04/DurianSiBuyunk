# 🥑 Durian Si Buyunk - Sistem Manajemen Stok Durian

Aplikasi manajemen stok durian segar dengan sistem untuk dua cabang toko Durian Si Buyunk di Tasikmalaya dan Garut. Sistem ini dirancang khusus untuk manajemen durian segar dengan tracking berat per buah yang akurat.

## 🛠️ Teknologi Utama
- **PHP 7.4+** (dengan PDO untuk koneksi database)
- **MySQL 5.7+**
- **Bootstrap 5** (untuk tampilan responsif)
- **Font Awesome** (untuk ikon)
- **Individual Weight Tracking System** (sistem tracking berat per buah)

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
│       ├── images/             # Gambar produk & logo
│       │   └── products/       # Folder gambar produk
│       └── receipts/           # Folder bukti transaksi (foto nota)
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
│   │   │   ├── hapus.php       # Hapus produk
│   │   │   ├── get_stock_info.php # API info stok
│   │   │   └── update_stok.php # API update stok
│   │   ├── manajemen-stok/     # Manajemen stok dengan bukti transaksi
│   │   │   └── index.php       # Update stok + upload bukti
│   │   ├── laporan/            # Laporan komprehensif
│   │   │   └── index.php       # 6 metrik + print-friendly
│   │   └── kategori/           # Manajemen kategori durian
│   │       └── index.php       # Kelola kategori
│   │
│   └── garut/                  # Admin Garut (struktur sama dengan Tasik)
│       ├── dashboard.php
│       ├── auth/
│       ├── produk/
│       ├── manajemen-stok/
│       ├── laporan/
│       └── kategori/
│
├── includes/                   # Fungsi-fungsi pendukung
│   ├── auth.php                # Sistem otentikasi
│   ├── functions.php           # Fungsi utilitas utama
│   ├── weight_functions.php    # Fungsi individual weight tracking
│   ├── report_functions_simple.php # Fungsi laporan
│   ├── header.php              # Template header
│   └── footer.php              # Template footer
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

2. **Setup Database**
   - Buat database MySQL dengan nama `durian_sibuyunk`
   - Import file `durian_sibuyunk.sql` ke dalam database tersebut
   - Database sudah berisi data sample 12 varietas durian autentik dan produk sample

3. **Konfigurasi Aplikasi**
   - Buka file `config/database.php`
   - Sesuaikan dengan setting database lokal Anda:
     ```php
     define('DB_HOST', 'localhost');    // Host database
     define('DB_USER', 'root');         // Username database
     define('DB_PASS', '');            // Password database (kosong jika tidak ada)
     define('DB_NAME', 'durian_sibuyunk'); // Nama database
     ```

4. **Setup Permission Folder**
   - Pastikan folder `public/assets/images/products/` dapat ditulis (chmod 755 atau 777)
   - Pastikan folder `public/assets/receipts/` dapat ditulis (chmod 755 atau 777)

4. **Setup Permission Folder**
   - Pastikan folder `public/assets/images/products/` dapat ditulis (chmod 755 atau 777)
   - Pastikan folder `public/assets/receipts/` dapat ditulis (chmod 755 atau 777)

5. **Akses Aplikasi**
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

## 🥑 Varietas Durian Autentik

Aplikasi ini dilengkapi dengan **12 varietas durian autentik** yang sudah dikategorikan:

### 🏆 Premium Category
- **Musang King** - Raja durian dengan daging golden-yellow
- **Black Thorn** - Rival Musang King, untuk pecinta durian berpengalaman  
- **Red Prawn** - Daging kemerahan-orange yang unik
- **XO** - Rasa fermentasi alkohol alami, eksklusif

### 🎯 Regular Category  
- **D24 Sultan** - Tekstur mentega, rasa manis ringan
- **Monthong** - Thailand import, daging tebal
- **D13** - Cocok untuk pemula, aroma ringan

### 🏠 Local Category
- **Bawor** - Jawa Tengah, daging tebal rasa legit
- **Kampung Durian** - Tradisional, harga ekonomis
- **IOI** - Hybrid modern, rasa seimbang
- **Golden Phoenix** - Daging kuning cerah
- **Udang Merah** - Pemula, aroma ringan

## ⚖️ Individual Weight Tracking System

### Fitur Utama:
- **Per-Buah Tracking**: Setiap transaksi mencatat bobot (kg) dan jumlah (pcs)
- **Rata-rata Bobot**: Otomatis menghitung kg/buah untuk setiap produk
- **History Lengkap**: Semua pergerakan stok tersimpan dengan detail
- **Real-time Update**: Stok dan bobot terupdate otomatis

### Cara Kerja:
1. **Input Stok**: Masukkan bobot total (kg) + jumlah pieces
2. **Auto Calculate**: Sistem menghitung rata-rata bobot per buah
3. **Track Transaction**: Setiap transaksi (masuk/keluar) dicatat lengkap
4. **Update Stock**: Stok dan bobot total terupdate real-time

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

## 📊 Dashboard & Laporan

### Dashboard Features:
- **4 Statistik Utama**: Total Produk, Total Stok (Pcs), Total Berat (Kg), Stok Menipis
- **Color-coded Cards**: Visual yang jelas dengan warna per metrik  
- **Branch-specific**: Hanya menampilkan data cabang masing-masing
- **Real-time Data**: Update otomatis setiap transaksi

### Laporan Komprehensif:
- **6 Metrik Utama**: Total Produk, Total Buah, Total Kg, Stok Menipis, Stok Habis, Nilai Stok
- **Print-friendly**: CSS khusus untuk print tanpa navbar/sidebar
- **Detail Per Produk**: Nama, kategori, jenis, stok, bobot, harga, nilai
- **Filter & Search**: Cari berdasarkan nama atau kategori
- **Responsive Design**: Optimal di semua perangkat

## 📊 Manajemen Stok per Cabang

### Fitur Stok Terpisah:
- **Admin Tasikmalaya**: Hanya dapat mengelola stok Tasikmalaya
- **Admin Garut**: Hanya dapat mengelola stok Garut
- **Halaman Publik**: Menampilkan stok dari kedua cabang dengan filter
- **Kontrol Akses**: Setiap admin hanya melihat dan mengelola data cabangnya
- **Weight Tracking**: Setiap perubahan stok mencatat bobot dan pieces

### Tampilan Stok:
- **Publik**: Menampilkan stok kedua cabang atau filter per cabang
- **Admin**: Menampilkan stok cabang sendiri dengan informasi cabang lain (read-only)
- **Badge Warna**: Hijau untuk Tasikmalaya, Biru untuk Garut
- **Detail Bobot**: Menampilkan total kg, pieces, dan rata-rata kg/buah

### Upload Bukti Transaksi:
- **Format Support**: JPG, PNG, PDF
- **Max Size**: 5MB per file
- **Auto Rename**: File otomatis di-rename untuk keamanan
- **Storage**: Tersimpan di `public/assets/receipts/`

## 🔄 Reset Password
Jika lupa password:
1. Klik "Lupa Password" di halaman login
2. Masukkan kode reset: `BUYUNK2025`
3. Masukkan username dan password baru
4. Password akan diupdate secara otomatis

## ⚠️ Keamanan & Performance
- **Password Encryption**: password_hash() dengan salt otomatis
- **SQL Injection Protection**: PDO prepared statements untuk semua query
- **Input Validation**: Validasi ketat untuk semua form dan file upload
- **Session Management**: Secure session dengan proper timeout
- **Access Control**: Role-based access dengan pembatasan per cabang
- **File Upload Security**: Validasi tipe file, ukuran, dan auto-rename
- **Database Optimization**: Proper indexes dan foreign keys untuk performa
- **Error Handling**: Comprehensive error logging dan user-friendly messages

## 🔧 Technical Specifications

### Database Schema:
- **Engine**: InnoDB untuk ACID compliance
- **Charset**: utf8mb4 untuk support emoji dan karakter khusus
- **Indexes**: Optimized indexes untuk query performa
- **Foreign Keys**: Relational integrity dengan cascade options

### File Structure:
- **MVC Pattern**: Separation of concerns yang jelas
- **Modular Functions**: Reusable functions dalam includes/
- **Clean Code**: Consistent naming dan dokumentasi
- **Security Best Practices**: Input sanitization dan output escaping

### Performance Features:
- **Optimized Queries**: Efficient SQL dengan minimal N+1 problems
- **Image Optimization**: Proper image handling dan storage
- **Caching Ready**: Structure siap untuk implementasi caching
- **Mobile Optimized**: Lightweight dan fast loading

## 📊 Struktur Database (Updated)
Aplikasi menggunakan **6 tabel utama** dengan relasi yang optimal:

### 1. **users** - Data login admin
- Menyimpan credentials dengan enkripsi password_hash()
- Role-based access per cabang

### 2. **cabang** - Master data cabang
- Tasikmalaya (kode: TSK)
- Garut (kode: GRT)

### 3. **kategori** - Kategori durian modern
- **Durian Premium**: Musang King, Black Thorn, Red Prawn, XO
- **Durian Reguler**: D24, Monthong, D13  
- **Durian Lokal**: Bawor, Kampung, IOI
- **Durian Frozen**: Ready-to-eat kemasan

### 4. **jenis_durian** - 12 varietas autentik
- Data lengkap dengan deskripsi karakteristik masing-masing
- Berdasarkan riset varietas durian asli

### 5. **produk** - Data produk dengan weight tracking
- `harga_per_kg`: Harga per kilogram
- `total_kg_tasik/garut`: Total berat stok per cabang
- `total_pcs_tasik/garut`: Total pieces per cabang  
- `stok_tasik/garut`: Legacy stock counter
- Auto-calculate rata-rata bobot per buah

### 6. **stok_history** - History tracking lengkap
- `cabang`: Identifier cabang (tasik/garut)
- `bobot_kg`: Bobot yang terlibat dalam transaksi
- `jumlah_pcs`: Pieces yang terlibat dalam transaksi
- `total_kg_sebelum/sesudah`: Total bobot sebelum/sesudah
- `harga_per_kg_saat_itu`: Harga per kg saat transaksi
- Tracking lengkap setiap pergerakan stok

**Foreign Keys & Indexes**: Optimized untuk performa dengan relasi yang tepat

## 🎯 Fitur Unggulan

### 1. Individual Weight Tracking System 🆕
- **Per-Transaction Recording**: Setiap transaksi mencatat bobot dan pieces secara terpisah
- **Auto-Calculate Average**: Sistem otomatis menghitung rata-rata kg per buah
- **Real-time Updates**: Stok dan bobot terupdate langsung setiap transaksi
- **Complete History**: History lengkap dengan before/after weight tracking

### 2. Comprehensive Reporting 🆕  
- **6-Metric Dashboard**: Total Produk, Buah, Kg, Stok Menipis, Habis, Nilai Stok
- **Print-Optimized**: CSS khusus untuk print laporan tanpa UI elements
- **Real-time Calculations**: Nilai stok dihitung real-time berdasarkan kg × harga/kg
- **Visual Status Indicators**: Color-coded badges untuk status stok

### 3. Transaction Receipt Upload 🆕
- **Multi-format Support**: JPG, PNG, PDF untuk bukti transaksi
- **Secure Storage**: Auto-rename files dengan timestamp untuk keamanan
- **Integration**: Terintegrasi dengan setiap update stok
- **Audit Trail**: Bukti transaksi tersimpan untuk audit

### 4. Authentic Durian Varieties 🆕
- **12 Real Varieties**: Berdasarkan riset durian asli (bukan generik)
- **Detailed Descriptions**: Karakteristik lengkap setiap varietas
- **Price Categorization**: Premium, Regular, Local dengan harga realistis
- **Market-based Pricing**: Harga sesuai dengan market rate durian

### 5. Design Thinking Implementation
- **Empathy**: Interface yang mudah dipahami untuk admin non-teknis
- **Define**: Pemisahan yang jelas antara fungsi publik dan admin
- **Ideate**: Solusi visual untuk membedakan cabang dengan color coding
- **Prototype**: Layout yang konsisten dan intuitif
- **Test**: Feedback visual yang jelas untuk setiap aksi

### 6. User Experience (UX)
- **Consistent Navigation**: Navigasi yang sama di semua halaman admin
- **Visual Feedback**: Alert, badge, dan notifikasi yang informatif
- **Error Prevention**: Validasi form dan konfirmasi untuk aksi penting
- **Accessibility**: Kontras warna yang baik dan ukuran font yang readable
- **Responsive Design**: Mobile-first approach untuk semua perangkat

## 🆘 Bantuan & Dukungan
Jika menemui masalah:

### Troubleshooting Umum:
1. **Database Connection Error**: 
   - Pastikan MySQL service running
   - Cek konfigurasi di `config/database.php`
   - Pastikan database `durian_sibuyunk` sudah dibuat

2. **File Upload Error**:
   - Cek permission folder `public/assets/images/products/` (755/777)
   - Cek permission folder `public/assets/receipts/` (755/777)
   - Pastikan `upload_max_filesize` dan `post_max_size` di php.ini cukup

3. **Weight Tracking Issues**:
   - Pastikan kolom `harga_per_kg`, `total_kg_*`, `total_pcs_*` ada di tabel produk
   - Jalankan script `database/update_individual_weight.sql` jika perlu

4. **Print Function Not Working**:
   - Pastikan browser mendukung CSS @media print
   - Coba Print Preview di browser sebelum print

### System Requirements:
- **PHP**: 7.4+ dengan ekstensi PDO, GD, mbstring
- **MySQL**: 5.7+ atau MariaDB 10.2+
- **Web Server**: Apache/Nginx dengan mod_rewrite
- **Storage**: Minimal 100MB untuk aplikasi + data
- **Memory**: PHP memory_limit minimal 128MB

### Untuk Bantuan Lebih Lanjut:
- **Email**: support@duriansibuyunk.com
- **WhatsApp**: +62 812-3456-7890
- **Documentation**: Lihat komentar dalam kode untuk detail teknis

## 📜 Lisensi & Credits
Aplikasi ini dikembangkan khusus untuk Durian Si Buyunk dengan fitur-fitur canggih:

### Technologies Used:
- **Backend**: PHP 7.4+ dengan PDO
- **Database**: MySQL 5.7+ dengan InnoDB engine
- **Frontend**: Bootstrap 5, Font Awesome, vanilla JavaScript
- **Security**: password_hash(), PDO prepared statements
- **File Handling**: PHP GD extension untuk image processing

### Features Developed:
- ✅ **Individual Weight Tracking System**
- ✅ **12 Authentic Durian Varieties Database**  
- ✅ **Comprehensive Reporting with 6 Metrics**
- ✅ **Transaction Receipt Upload System**
- ✅ **Print-Optimized Reports**
- ✅ **Real-time Stock & Weight Updates**
- ✅ **Branch-Specific Access Control**
- ✅ **Mobile-Responsive Design**

### Development Notes:
- Clean, maintainable code dengan proper dokumentasi
- Security-first approach dengan input validation
- User-centered design dengan accessibility considerations
- Performance optimized dengan proper database indexing
- Modular architecture untuk easy maintenance

**© 2025 Durian Si Buyunk. All rights reserved.**

---

*Aplikasi ini telah dioptimalkan untuk manajemen durian segar dengan sistem tracking individual yang akurat dan komprehensif. Dikembangkan dengan teknologi modern dan best practices untuk reliability dan security.*