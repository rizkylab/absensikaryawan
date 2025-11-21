# Sistem Absensi Karyawan Modern

Sistem absensi karyawan modern dengan fitur lengkap menggunakan Laravel 12, dilengkapi dengan validasi GPS, Face Recognition, dan QR Code harian.

## 🎯 Fitur Utama

### Multi-Role Authentication
- **Admin**: Full access, manage users, payroll, system settings, reports, audit logs
- **Atasan**: Approve/reject overtime & leave requests, view team attendance & reports
- **Karyawan**: Check-in/out attendance, request overtime & leave, view personal reports

### Absensi Real-time dengan Validasi Canggih
- ✅ **GPS Validation**: Karyawan hanya bisa absen dalam radius kantor
- ✅ **Face Recognition**: Validasi wajah saat check-in/out (dummy API ready for integration)
- ✅ **QR Code Daily**: Generate QR code unik setiap hari untuk validasi tambahan
- ✅ **Metadata Lengkap**: Simpan koordinat GPS, foto selfie, face score, alamat

### Manajemen Lembur & Cuti
- 📝 Pengajuan lembur dengan approval workflow
- 📝 Pengajuan cuti dengan upload file pendukung (PDF/JPG max 5MB)
- 📝 Approval flow: Karyawan → Atasan → Admin (optional)
- 📝 Feedback notes dari approver

### Payroll Otomatis
- 💰 Perhitungan otomatis: hari masuk, telat, lembur, cuti
- 💰 Generate slip gaji PDF profesional dengan logo perusahaan
- 💰 Export laporan Excel

### Laporan & Audit Log
- 📊 Laporan absensi bulanan (PDF & Excel)
- 📊 Laporan lembur & cuti
- 📊 Audit log semua aktivitas penting
- 📊 Dashboard dengan charts (Chart.js/ApexCharts)

### UI Modern
- 🎨 Design Notion/Vercel style
- 🎨 Dark mode support
- 🎨 Responsive (mobile, tablet, desktop)
- 🎨 Lucide icons
- 🎨 TailwindCSS + Alpine.js

## 🛠️ Teknologi Stack

| Komponen | Teknologi |
|----------|-----------|
| Backend | PHP 8.2, Laravel 12 |
| Frontend | Blade, TailwindCSS, Alpine.js |
| Database | MySQL/MariaDB |
| PDF | barryvdh/laravel-dompdf |
| Excel | maatwebsite/excel |
| QR Code | simplesoftwareio/simple-qrcode |
| Icons | Lucide Icons |
| Charts | Chart.js / ApexCharts |

## 📋 Requirements

- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL/MariaDB
- XAMPP / MAMP / Laragon (untuk local development)

## 🚀 Installation

Lihat [INSTALLATION.md](INSTALLATION.md) untuk panduan instalasi lengkap.

### Quick Start

```bash
# Clone atau copy project
cd /Applications/MAMP/htdocs/absensikaryawan

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database di .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=absensikaryawan
DB_USERNAME=root
DB_PASSWORD=your_password

# Run migrations & seeders
php artisan migrate
php artisan db:seed

# Build assets
npm run build

# Start server
php artisan serve
```

## 👤 Default Credentials

Setelah run seeder, gunakan credentials berikut:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@example.com | password |
| Atasan | atasan@example.com | password |
| Karyawan | karyawan@example.com | password |
| Karyawan 2 | karyawan2@example.com | password |

## 📁 Struktur Project

```
absensikaryawan/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # Admin controllers
│   │   │   ├── Atasan/         # Supervisor controllers
│   │   │   ├── Karyawan/       # Employee controllers
│   │   │   └── Api/            # API controllers
│   │   └── Middleware/
│   │       └── RoleMiddleware.php
│   ├── Models/                 # Eloquent models
│   ├── Services/               # Business logic
│   └── Repositories/           # Data access layer
├── database/
│   ├── migrations/             # Database migrations
│   └── seeders/                # Database seeders
├── resources/
│   ├── views/
│   │   ├── admin/              # Admin views
│   │   ├── atasan/             # Supervisor views
│   │   ├── karyawan/           # Employee views
│   │   ├── components/         # Blade components
│   │   └── layouts/            # Layouts
│   ├── css/
│   └── js/
└── routes/
    ├── web.php                 # Web routes
    └── api.php                 # API routes
```

## 🎯 Fitur yang Sudah Diimplementasi

### ✅ Phase 1: Project Setup & Foundation
- Laravel 12 project initialized
- Dependencies installed (Breeze, DomPDF, Excel, QR Code)
- TailwindCSS + Alpine.js configured
- Dark mode support

### ✅ Phase 2: Database Schema
- 9 comprehensive migrations created
- All tables with proper indexes and foreign keys
- Support for GPS, Face Recognition, QR validation

### ✅ Phase 3: Authentication & Authorization
- Laravel Breeze installed
- Role-based middleware implemented
- Multi-role authentication ready

### ✅ Phase 4: Core Models
- All models created with relationships
- Business logic helpers
- Type casting and accessors

### ✅ Phase 5: Seeders
- RoleSeeder (Admin, Atasan, Karyawan)
- UserSeeder (default users for each role)
- SettingSeeder (system settings)
- OfficeLocationSeeder (default office location)

## 🔧 Configuration

### System Settings

Edit di Admin Dashboard → Settings atau langsung di database `settings` table:

- **Work Hours**: `work_start_time`, `work_end_time`
- **Late Tolerance**: `late_tolerance` (minutes)
- **Face Recognition**: `face_recognition_enabled`, `face_recognition_threshold`
- **Payroll**: `late_penalty_per_minute`, `overtime_rate_per_hour`, `leave_deduction_per_day`
- **Company Info**: `company_name`, `company_address`

### Office Location

Edit di Admin Dashboard → Settings → Office Location:
- Latitude & Longitude (koordinat kantor)
- Radius (dalam meter)

## 📱 Usage

### Karyawan
1. Login dengan credentials karyawan
2. Dashboard → Attendance
3. Click "Check In"
4. Allow GPS access
5. Scan QR code atau input token
6. Upload selfie photo
7. Submit

### Atasan
1. Login dengan credentials atasan
2. Dashboard → Approvals
3. View pending overtime/leave requests
4. Approve atau Reject dengan notes

### Admin
1. Login dengan credentials admin
2. Full access ke semua fitur
3. Manage users, settings, payroll
4. Generate reports
5. View audit logs

## 🎨 Customization

### Dark Mode
- Toggle dark mode di navbar
- Preferences tersimpan di localStorage

### Company Logo
- Upload logo di `public/images/logo.png`
- Logo akan muncul di slip gaji PDF

## 📊 Reports

### Available Reports
- Monthly Attendance Report (PDF & Excel)
- Overtime Report (PDF & Excel)
- Leave Report (PDF & Excel)
- Payroll Report (PDF)

## 🔐 Security

- Password hashing dengan bcrypt
- CSRF protection
- Role-based access control
- SQL injection protection (Eloquent ORM)
- XSS protection (Blade templating)

## 🐛 Troubleshooting

### Database Connection Error
```bash
# Check .env configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=absensikaryawan
DB_USERNAME=root
DB_PASSWORD=your_password

# Create database
mysql -u root -p
CREATE DATABASE absensikaryawan;
exit;

# Run migrations again
php artisan migrate:fresh --seed
```

### Permission Error
```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
```

### Assets Not Loading
```bash
# Rebuild assets
npm run build

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## 📝 License

This project is open-sourced software licensed under the MIT license.

## 👨‍💻 Developer

Built with ❤️ using Laravel 12

## 🙏 Credits

- [Laravel](https://laravel.com)
- [TailwindCSS](https://tailwindcss.com)
- [Alpine.js](https://alpinejs.dev)
- [Lucide Icons](https://lucide.dev)
- [DomPDF](https://github.com/barryvdh/laravel-dompdf)
- [Laravel Excel](https://laravel-excel.com)
- [Simple QR Code](https://www.simplesoftware.io/simple-qrcode)
