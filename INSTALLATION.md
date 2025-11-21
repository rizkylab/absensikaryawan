# Installation Guide - Sistem Absensi Karyawan

Panduan instalasi lengkap untuk Sistem Absensi Karyawan Modern.

## 📋 Prerequisites

Pastikan sistem Anda sudah memiliki:

- **PHP >= 8.2** dengan extensions:
  - BCMath
  - Ctype
  - Fileinfo
  - JSON
  - Mbstring
  - OpenSSL
  - PDO
  - Tokenizer
  - XML
  - GD (untuk QR Code & image processing)
  
- **Composer** (latest version)
- **Node.js >= 18** & NPM
- **MySQL >= 8.0** atau **MariaDB >= 10.3**
- **XAMPP / MAMP / Laragon** (untuk local development)

## 🚀 Step-by-Step Installation

### 1. Prepare Environment

#### Menggunakan MAMP (macOS)
```bash
# Start MAMP
# Pastikan Apache dan MySQL sudah running
# Default MySQL port: 8889 (MAMP) atau 3306 (MAMP PRO)
```

#### Menggunakan XAMPP (Windows/Linux)
```bash
# Start XAMPP Control Panel
# Start Apache dan MySQL
# Default MySQL port: 3306
```

#### Menggunakan Laragon (Windows)
```bash
# Start Laragon
# Start All
# Default MySQL port: 3306
```

### 2. Create Database

```bash
# Login ke MySQL
mysql -u root -p

# Create database
CREATE DATABASE absensikaryawan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Create user (optional, untuk production)
CREATE USER 'absensi_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON absensikaryawan.* TO 'absensi_user'@'localhost';
FLUSH PRIVILEGES;

# Exit
exit;
```

### 3. Project Setup

```bash
# Navigate to project directory
cd /Applications/MAMP/htdocs/absensikaryawan

# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 4. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Configure .env File

Edit `.env` file dengan editor favorit Anda:

```env
APP_NAME="Sistem Absensi Karyawan"
APP_ENV=local
APP_KEY=base64:... # Already generated
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_TIMEZONE=Asia/Jakarta

APP_LOCALE=id
APP_FALLBACK_LOCALE=en

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306                    # 8889 for MAMP, 3306 for XAMPP/Laragon
DB_DATABASE=absensikaryawan
DB_USERNAME=root
DB_PASSWORD=                    # Your MySQL password (empty for MAMP/XAMPP default)

# Mail Configuration (optional, untuk notifications)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

# Face Recognition API (dummy for now)
FACE_RECOGNITION_API_URL=http://localhost:5000/api/verify
FACE_RECOGNITION_API_KEY=your_api_key_here
```

### 6. Run Migrations & Seeders

```bash
# Run migrations (create all tables)
php artisan migrate

# Run seeders (populate initial data)
php artisan db:seed

# Or run both in one command
php artisan migrate:fresh --seed
```

Expected output:
```
INFO  Preparing database.
Creating migration table ........................... DONE
INFO  Running migrations.
2025_11_21_083753_create_roles_table ............... DONE
2025_11_21_083823_add_role_id_to_users_table ....... DONE
2025_11_21_083824_create_office_locations_table .... DONE
2025_11_21_083825_create_qr_codes_table ............ DONE
2025_11_21_083826_create_attendances_table ......... DONE
2025_11_21_083826_create_overtimes_table ........... DONE
2025_11_21_083832_create_leaves_table .............. DONE
2025_11_21_083833_create_payrolls_table ............ DONE
2025_11_21_083834_create_settings_table ............ DONE
2025_11_21_083835_create_audit_logs_table .......... DONE

INFO  Seeding database.
Database\Seeders\RoleSeeder ........................ DONE
Database\Seeders\UserSeeder ........................ DONE
Database\Seeders\OfficeLocationSeeder .............. DONE
Database\Seeders\SettingSeeder ..................... DONE
```

### 7. Build Frontend Assets

```bash
# Development build
npm run dev

# Production build
npm run build
```

### 8. Set Permissions (Linux/macOS)

```bash
# Set storage and cache permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Set ownership (optional)
chown -R www-data:www-data storage bootstrap/cache
```

### 9. Create Storage Link

```bash
# Create symbolic link for file storage
php artisan storage:link
```

### 10. Start Development Server

```bash
# Start Laravel development server
php artisan serve

# Server will start at http://localhost:8000
```

Atau akses via MAMP/XAMPP:
- MAMP: `http://localhost:8888/absensikaryawan/public`
- XAMPP: `http://localhost/absensikaryawan/public`

## 👤 Default Login Credentials

| Role | Email | Password | Employee ID |
|------|-------|----------|-------------|
| Admin | admin@example.com | password | EMP001 |
| Atasan | atasan@example.com | password | EMP002 |
| Karyawan | karyawan@example.com | password | EMP003 |
| Karyawan 2 | karyawan2@example.com | password | EMP004 |

## 🔧 Post-Installation Configuration

### 1. Update Office Location

Login sebagai Admin → Settings → Office Location

Update koordinat kantor Anda:
- **Latitude**: Koordinat latitude kantor
- **Longitude**: Koordinat longitude kantor
- **Radius**: Radius dalam meter (default: 100m)

Cara mendapatkan koordinat:
1. Buka Google Maps
2. Klik kanan pada lokasi kantor
3. Copy koordinat yang muncul (format: -6.200000, 106.816666)

### 2. Update System Settings

Login sebagai Admin → Settings → System Settings

Update pengaturan sesuai kebutuhan:
- Work start time (default: 08:00)
- Work end time (default: 17:00)
- Late tolerance (default: 15 minutes)
- Face recognition threshold (default: 70%)
- Payroll settings (penalties & bonuses)

### 3. Update Company Information

Edit di Settings:
- Company name
- Company address
- Upload company logo (`public/images/logo.png`)

### 4. Generate Daily QR Code

```bash
# Generate QR code for today
php artisan qr:generate-daily

# Or setup cron job untuk auto-generate setiap hari
# Add to crontab:
0 0 * * * cd /path/to/project && php artisan qr:generate-daily
```

## 🎨 Customization

### Change Theme Colors

Edit `tailwind.config.js`:

```javascript
module.exports = {
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#f0f9ff',
          // ... customize colors
        }
      }
    }
  }
}
```

Then rebuild:
```bash
npm run build
```

### Add Company Logo

```bash
# Add logo to public/images/
cp /path/to/your/logo.png public/images/logo.png

# Logo will appear in:
# - Login page
# - Dashboard header
# - Salary slip PDF
```

## 🐛 Troubleshooting

### Error: "Access denied for user 'root'@'localhost'"

**Solution:**
```bash
# Check MySQL is running
# Update .env with correct credentials
DB_USERNAME=root
DB_PASSWORD=your_password

# Clear config cache
php artisan config:clear
```

### Error: "SQLSTATE[HY000] [2002] Connection refused"

**Solution:**
```bash
# Check MySQL port in .env
# MAMP uses port 8889
DB_PORT=8889

# XAMPP/Laragon uses port 3306
DB_PORT=3306

# Clear config cache
php artisan config:clear
```

### Error: "Class 'DomPDF' not found"

**Solution:**
```bash
# Reinstall dependencies
composer install

# Clear cache
php artisan cache:clear
php artisan config:clear
```

### Error: "npm run build" fails

**Solution:**
```bash
# Remove node_modules and package-lock.json
rm -rf node_modules package-lock.json

# Reinstall
npm install

# Build again
npm run build
```

### Error: "Storage link already exists"

**Solution:**
```bash
# Remove existing link
rm public/storage

# Create new link
php artisan storage:link
```

### Permission Denied Errors

**Solution:**
```bash
# Fix permissions
chmod -R 775 storage bootstrap/cache

# If using Apache/Nginx
chown -R www-data:www-data storage bootstrap/cache

# If using MAMP/XAMPP (macOS)
chown -R _www:_www storage bootstrap/cache
```

## 📱 Testing Installation

### 1. Test Login
- Navigate to `http://localhost:8000`
- Login dengan credentials admin
- Verify redirect ke admin dashboard

### 2. Test Attendance (Karyawan)
- Login sebagai karyawan
- Navigate ke Attendance page
- Test check-in (akan error GPS jika tidak allow location)

### 3. Test Approval (Atasan)
- Login sebagai atasan
- Navigate ke Approvals
- Verify dapat melihat pending requests

### 4. Test Reports (Admin)
- Login sebagai admin
- Navigate ke Reports
- Test generate PDF/Excel

## 🔄 Update & Maintenance

### Update Dependencies

```bash
# Update PHP dependencies
composer update

# Update Node dependencies
npm update

# Rebuild assets
npm run build
```

### Clear All Caches

```bash
# Clear application cache
php artisan cache:clear

# Clear config cache
php artisan config:clear

# Clear view cache
php artisan view:clear

# Clear route cache
php artisan route:clear

# Optimize for production
php artisan optimize
```

### Backup Database

```bash
# Backup database
mysqldump -u root -p absensikaryawan > backup_$(date +%Y%m%d).sql

# Restore database
mysql -u root -p absensikaryawan < backup_20251121.sql
```

## 🚀 Deployment to Production

### 1. Update .env for Production

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Use strong database password
DB_PASSWORD=strong_secure_password

# Configure mail
MAIL_MAILER=smtp
# ... mail configuration
```

### 2. Optimize for Production

```bash
# Cache config
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev

# Build production assets
npm run build
```

### 3. Set Proper Permissions

```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 4. Setup Cron Jobs

Add to crontab:
```bash
# Laravel scheduler
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1

# Daily QR code generation
0 0 * * * cd /path/to/project && php artisan qr:generate-daily
```

## 📞 Support

Jika mengalami masalah saat instalasi:
1. Check error logs: `storage/logs/laravel.log`
2. Check web server error logs
3. Verify all requirements terpenuhi
4. Clear all caches
5. Restart web server & database

## ✅ Installation Checklist

- [ ] PHP 8.2+ installed
- [ ] Composer installed
- [ ] Node.js & NPM installed
- [ ] MySQL/MariaDB installed
- [ ] Database created
- [ ] `.env` configured
- [ ] `composer install` completed
- [ ] `npm install` completed
- [ ] `php artisan key:generate` executed
- [ ] `php artisan migrate` completed
- [ ] `php artisan db:seed` completed
- [ ] `npm run build` completed
- [ ] `php artisan storage:link` executed
- [ ] Permissions set correctly
- [ ] Can login with default credentials
- [ ] Office location updated
- [ ] System settings configured

Selamat! Sistem Absensi Karyawan sudah siap digunakan! 🎉
