# Karsa - KKN Matching Platform

Platform untuk menghubungkan mahasiswa dengan perusahaan untuk program Kuliah Kerja Nyata (KKN).

## 📋 Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Installation](#installation)
- [Database Setup](#database-setup)
- [Development](#development)
- [Project Structure](#project-structure)

## ✨ Features

### For Students
- Browse and search KKN job opportunities
- Apply to job postings with cover letter
- Track application status
- Complete student profile with portfolio

### For Companies
- Post KKN job opportunities (4-6 per company)
- Review and manage applications (pending, reviewed, shortlisted, rejected, accepted)
- Save and categorize talented students (Baru, Dihubungi, Interview, Ditawari, Ditolak)
- View analytics and diagrams of recruitment process

### For Institutions
- Manage student data
- Monitor KKN placements
- Track student progress

## 🛠️ Tech Stack

- **Framework:** Laravel 11
- **Database:** PostgreSQL (Supabase)
- **Frontend:** Blade Templates
- **Authentication:** Laravel Breeze
- **Styling:** Tailwind CSS

## 📦 Installation

### Prerequisites

- PHP >= 8.2
- Composer
- Node.js & NPM
- PostgreSQL (Supabase account)

### Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd Karsa
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database**

   Edit `.env` file:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=your-supabase-host.supabase.co
   DB_PORT=5432
   DB_DATABASE=postgres
   DB_USERNAME=postgres
   DB_PASSWORD=your-password
   ```

5. **Run migrations and seeders**

   ⚠️ **IMPORTANT:** Untuk setup database yang benar, **WAJIB baca panduan lengkap di:**

   📖 **[DATABASE_SETUP.md](DATABASE_SETUP.md)**

   Quick start (untuk fresh database):
   ```bash
   php artisan migrate
   php artisan db:seed --class=ProvincesRegenciesSeeder
   php artisan db:seed --class=DummyDataSeeder
   php artisan db:seed --class=JobCategorySeeder
   php artisan db:seed --class=UserSeeder
   php artisan db:seed --class=CompanySeeder
   php artisan db:seed --class=JobPostingKKNSeeder
   php artisan db:seed --class=JobApplicationsCompanySeeder
   php artisan db:seed --class=SavedTalentsSeeder
   ```

6. **Build assets**
   ```bash
   npm run dev
   ```

7. **Start the server**
   ```bash
   php artisan serve
   ```

   Visit: `http://localhost:8000`

## 💾 Database Setup

**WAJIB BACA:** [DATABASE_SETUP.md](DATABASE_SETUP.md)

Panduan lengkap untuk:
- ✅ Migration dan seeding yang benar
- ⚠️ Troubleshooting common errors
- 🔄 Reset database
- 📊 Verifikasi data
- 🐛 Debug mode

### Seeded Data Summary

Setelah seeding berhasil, database akan berisi:

| Data Type | Count | Notes |
|-----------|-------|-------|
| **Companies** | 128 | Company users dengan role 'company' |
| **Job Postings** | 632 | 4-6 postings per company, >3 categories |
| **Job Categories** | 33 | Software, Marketing, Finance, dll |
| **Students** | ~495 | Dummy student data untuk testing |
| **Job Applications** | ~2,332 | 12-24 per company, ALL 5 statuses |
| **Saved Talents** | ~1,038 | 6-10 per company, ALL 5 categories |

### Default Credentials

**Company Users:**
- Email: `hr@{companyname}.co.id`
- Password: `password123`

**Example:**
- Email: `hr@telkomindonesia.co.id`
- Password: `password123`

⚠️ **WAJIB GANTI PASSWORD DI PRODUCTION!**

## 🚀 Development

### Running Development Server

```bash
# Terminal 1 - Laravel server
php artisan serve

# Terminal 2 - Vite dev server
npm run dev
```

### Code Style

```bash
# Format code
composer format

# Run linter
composer lint
```

### Database

```bash
# Create new migration
php artisan make:migration create_table_name

# Create new seeder
php artisan make:seeder TableNameSeeder

# Reset database
php artisan migrate:fresh --seed
```

## 📁 Project Structure

```
Karsa/
├── app/
│   ├── Http/Controllers/      # Controllers
│   ├── Models/                # Eloquent models
│   └── Services/              # Business logic (SupabaseService, etc)
├── database/
│   ├── migrations/            # Database migrations
│   └── seeders/               # Database seeders
│       ├── UserSeeder.php              # 128 company users
│       ├── CompanySeeder.php           # 128 companies
│       ├── JobCategorySeeder.php       # 33 categories
│       ├── JobPostingKKNSeeder.php     # 632 job postings
│       ├── JobApplicationsCompanySeeder.php  # 2,332 applications
│       └── SavedTalentsSeeder.php      # 1,038 saved talents
├── resources/
│   ├── views/                 # Blade templates
│   └── js/                    # JavaScript files
├── routes/
│   └── web.php               # Web routes
├── DATABASE_SETUP.md         # 📖 Database setup guide (WAJIB BACA!)
└── README.md                 # This file
```

## 🔑 Key Features Implementation

### Job Applications System
- 5 status types: **pending** → **reviewed** → **shortlisted** → **rejected/accepted**
- Timeline tracking dengan `viewed_at`, `responded_at`
- Cover letter dan resume attachment
- Admin notes untuk internal tracking

### Saved Talents System
- 5 categories: **Baru** → **Dihubungi** → **Interview** → **Ditawari** → **Ditolak**
- Company-specific talent pools
- Notes dan follow-up tracking
- Time-distributed data untuk analytics

### Analytics & Diagrams
- Application trends over time (90 days distribution)
- Talent pipeline visualization (60 days distribution)
- Status distribution charts
- Category breakdown statistics

## ⚠️ Common Issues

Untuk troubleshooting lengkap, lihat [DATABASE_SETUP.md](DATABASE_SETUP.md#-troubleshooting-common-errors)

**Quick fixes:**

1. **Migration error:** `php artisan migrate:fresh`
2. **Seeder error:** Baca error message, biasanya missing dependency seeder
3. **Connection error:** Cek `.env` database credentials
4. **Cache issues:** `php artisan config:clear && php artisan cache:clear`

## 📝 Development Notes

1. Semua seeders menggunakan **DB facade** (bukan SupabaseService) untuk reliability
2. Auto-reconnect setiap 10-20 iterations untuk avoid PostgreSQL prepared statement limit
3. Seeders bersifat **idempotent** - dapat dijalankan berkali-kali tanpa duplicate data
4. Time distribution menggunakan Carbon - relatif terhadap waktu seeding

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📄 License

This project is licensed under the MIT License.

## 📞 Support

Untuk pertanyaan atau issues:
1. Baca [DATABASE_SETUP.md](DATABASE_SETUP.md) terlebih dahulu
2. Cek existing issues
3. Create new issue dengan detail error message dan steps to reproduce

---

**Built with ❤️ for KKN matching**
