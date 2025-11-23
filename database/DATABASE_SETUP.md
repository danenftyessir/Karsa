
# Database Setup Guide

Panduan lengkap untuk melakukan migration dan seeding database untuk developer yang baru join project.

## 📌 Important: Ada 2 Sistem Berbeda!

Project ini memiliki **2 sistem utama** dengan seeders terpisah:

1. **Company/Job Matching System** - Companies post job opportunities, students apply
   - Seeders: JobCategorySeeder, UserSeeder, CompanySeeder, JobPostingKKNSeeder, JobApplicationsCompanySeeder, SavedTalentsSeeder

2. **Student/Problem Matching System** - Institutions post problems, students solve
   - Seeders: ProblemsSeeder, ApplicationsSeeder, ProjectsSeeder, DocumentsSeeder, NotificationsSeeder

Anda bisa seed salah satu atau kedua sistem sesuai kebutuhan development.

## 📋 Prerequisites

1. **Environment Variables** sudah dikonfigurasi di `.env`:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=your-supabase-host
   DB_PORT=5432
   DB_DATABASE=postgres
   DB_USERNAME=postgres
   DB_PASSWORD=your-password
   ```

2. **Composer dependencies** sudah terinstall:
   ```bash
   composer install
   ```

3. **Database kosong** di Supabase (PostgreSQL)

## 🚀 Quick Start (Fresh Database)

Jika database benar-benar kosong dan fresh:

### Option 1: Seed SEMUA (Company + Student Features) - RECOMMENDED

```bash
# 1. Jalankan migrations
php artisan migrate

# 2. Jalankan semua seeders dalam urutan yang benar
php artisan db:seed --class=ProvincesRegenciesSeeder
php artisan db:seed --class=DummyDataSeeder
php artisan db:seed --class=JobCategorySeeder
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=CompanySeeder
php artisan db:seed --class=JobPostingKKNSeeder
php artisan db:seed --class=JobApplicationsCompanySeeder
php artisan db:seed --class=SavedTalentsSeeder
php artisan db:seed --class=ProblemsSeeder
php artisan db:seed --class=ProblemImagesSeeder
php artisan db:seed --class=ApplicationsSeeder
php artisan db:seed --class=ProjectsSeeder
php artisan db:seed --class=DocumentsSeeder
php artisan db:seed --class=NotificationsSeeder
php artisan db:seed --class=FriendSeeder
```

**⚠️ PENTING:**
- Jalankan seeders SATU PER SATU secara sequential (bukan parallel!)
- Tunggu setiap seeder selesai sebelum menjalankan yang berikutnya
- Urutan di atas WAJIB diikuti karena ada dependencies antar seeder

### Option 2: Seed Company Side SAJA (KKN Job Matching)

```bash
# 1. Jalankan migrations
php artisan migrate

# 2. Jalankan seeders khusus company/job features
php artisan db:seed --class=ProvincesRegenciesSeeder
php artisan db:seed --class=DummyDataSeeder
php artisan db:seed --class=JobCategorySeeder
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=CompanySeeder
php artisan db:seed --class=JobPostingKKNSeeder
php artisan db:seed --class=JobApplicationsCompanySeeder
php artisan db:seed --class=SavedTalentsSeeder
```

### Option 3: Seed Student Side SAJA (Problem/Project Matching)

```bash
# 1. Jalankan migrations (jika belum)
php artisan migrate

# 2. Jalankan prerequisite seeders
php artisan db:seed --class=ProvincesRegenciesSeeder
php artisan db:seed --class=DummyDataSeeder

# 3. Jalankan student side seeders
php artisan db:seed --class=ProblemsSeeder
php artisan db:seed --class=ProblemImagesSeeder
php artisan db:seed --class=ApplicationsSeeder
php artisan db:seed --class=ProjectsSeeder
php artisan db:seed --class=DocumentsSeeder
php artisan db:seed --class=NotificationsSeeder
php artisan db:seed --class=FriendSeeder
```

### Option 4: Gunakan DatabaseSeeder (Partial - Student Side Only)

**⚠️ CATATAN:** DatabaseSeeder TIDAK memasukkan company side seeders!

```bash
php artisan migrate
php artisan db:seed  # Hanya akan seed student side features
```

Untuk seed company side, gunakan Option 2 di atas.

## 📋 Status Semua Seeders (Current Database)

Berikut adalah status seeding untuk database Anda saat ini:

### ✅ SUDAH DI-SEED (15 Seeders):

| No | Seeder | Status | Data Created |
|----|--------|--------|--------------|
| 1 | ProvincesRegenciesSeeder | ✅ DONE | 34 provinces, 514 regencies |
| 2 | DummyDataSeeder | ✅ DONE | 117 universities, 700 students, 79 institutions |
| 3 | JobCategorySeeder | ✅ DONE | 33 categories |
| 4 | UserSeeder | ✅ DONE | 128 company users |
| 5 | CompanySeeder | ✅ DONE | 128 companies |
| 6 | JobPostingKKNSeeder | ✅ DONE | 632 job postings |
| 7 | JobApplicationsCompanySeeder | ✅ DONE | 1,114 applications |
| 8 | SavedTalentsSeeder | ✅ DONE | 1,021 saved talents |
| 9 | ProblemsSeeder | ✅ DONE | 78 problems |
| 10 | ProblemImagesSeeder | ✅ DONE | 311 images |
| 11 | ApplicationsSeeder | ✅ DONE | 366 student applications |
| 12 | ProjectsSeeder | ✅ DONE | 266 projects |
| 13 | DocumentsSeeder | ✅ DONE | 227 documents |
| 14 | NotificationsSeeder | ✅ DONE | 58 notifications |
| 15 | FriendSeeder | ✅ DONE | 322 friendships |

**Total: 15/15 seeders telah berhasil di-seed ke Supabase! ✅**

## 📊 Dependency Graph & Manual Seeding Order

Jika Anda ingin melakukan seeding manual per class (1 per 1), **WAJIB** ikuti urutan berikut karena ada dependencies:

### 🔷 LAYER 1: Foundation (No Dependencies)
```bash
php artisan db:seed --class=ProvincesRegenciesSeeder
```
**Output:** 34 provinces, 514 regencies

---

### 🔷 LAYER 2: Users & Institutions (Depends on: Layer 1)
```bash
php artisan db:seed --class=DummyDataSeeder
```
**Output:** 117 universities, 700 students, 79 institutions
**Dependencies:** ProvincesRegenciesSeeder (butuh provinces/regencies untuk location)

---

### 🔷 LAYER 3A: Company Side - Setup (Depends on: Layer 2)
```bash
php artisan db:seed --class=JobCategorySeeder
php artisan db:seed --class=UserSeeder
```
**Output:** 33 job categories, 128 company users
**Dependencies:** None (independent)

```bash
php artisan db:seed --class=CompanySeeder
```
**Output:** 128 companies
**Dependencies:** UserSeeder (butuh company users), ProvincesRegenciesSeeder (location)

---

### 🔷 LAYER 3B: Company Side - Jobs (Depends on: Layer 3A)
```bash
php artisan db:seed --class=JobPostingKKNSeeder
```
**Output:** 632 job postings
**Dependencies:** CompanySeeder (butuh companies), JobCategorySeeder (butuh categories)

---

### 🔷 LAYER 3C: Company Side - Applications (Depends on: Layer 3B + Layer 2)
```bash
php artisan db:seed --class=JobApplicationsCompanySeeder
php artisan db:seed --class=SavedTalentsSeeder
```
**Output:** 1,114 job applications, 1,021 saved talents
**Dependencies:** JobPostingKKNSeeder (job postings), DummyDataSeeder (students)

---

### 🔷 LAYER 4A: Student Side - Problems (Depends on: Layer 2)
```bash
php artisan db:seed --class=ProblemsSeeder
```
**Output:** 78 problems
**Dependencies:** DummyDataSeeder (institutions), ProvincesRegenciesSeeder (location)

```bash
php artisan db:seed --class=ProblemImagesSeeder
```
**Output:** 311 problem images
**Dependencies:** ProblemsSeeder (butuh problems)

---

### 🔷 LAYER 4B: Student Side - Applications (Depends on: Layer 4A + Layer 2)
```bash
php artisan db:seed --class=ApplicationsSeeder
```
**Output:** 366 student applications
**Dependencies:** ProblemsSeeder (problems), DummyDataSeeder (students)

---

### 🔷 LAYER 5: Student Side - Projects (Depends on: Layer 4B)
```bash
php artisan db:seed --class=ProjectsSeeder
```
**Output:** 266 projects (with milestones, reports, reviews)
**Dependencies:** ApplicationsSeeder (accepted applications)

```bash
php artisan db:seed --class=DocumentsSeeder
```
**Output:** 227 project documents
**Dependencies:** ProjectsSeeder (projects)

---

### 🔷 LAYER 6: Notifications & Social (Depends on: All Previous)
```bash
php artisan db:seed --class=NotificationsSeeder
```
**Output:** 58 notifications
**Dependencies:** DummyDataSeeder (students, institutions)

```bash
php artisan db:seed --class=FriendSeeder
```
**Output:** 322 friendships
**Dependencies:** DummyDataSeeder (students)

---

## 🎯 Quick Copy-Paste Commands

### Seed SEMUA dari Awal (Fresh Database):
```bash
php artisan migrate:fresh && \
php artisan db:seed --class=ProvincesRegenciesSeeder && \
php artisan db:seed --class=DummyDataSeeder && \
php artisan db:seed --class=JobCategorySeeder && \
php artisan db:seed --class=UserSeeder && \
php artisan db:seed --class=CompanySeeder && \
php artisan db:seed --class=JobPostingKKNSeeder && \
php artisan db:seed --class=JobApplicationsCompanySeeder && \
php artisan db:seed --class=SavedTalentsSeeder && \
php artisan db:seed --class=ProblemsSeeder && \
php artisan db:seed --class=ProblemImagesSeeder && \
php artisan db:seed --class=ApplicationsSeeder && \
php artisan db:seed --class=ProjectsSeeder && \
php artisan db:seed --class=DocumentsSeeder && \
php artisan db:seed --class=NotificationsSeeder && \
php artisan db:seed --class=FriendSeeder
```

### Hanya Company Side:
```bash
php artisan db:seed --class=ProvincesRegenciesSeeder && \
php artisan db:seed --class=DummyDataSeeder && \
php artisan db:seed --class=JobCategorySeeder && \
php artisan db:seed --class=UserSeeder && \
php artisan db:seed --class=CompanySeeder && \
php artisan db:seed --class=JobPostingKKNSeeder && \
php artisan db:seed --class=JobApplicationsCompanySeeder && \
php artisan db:seed --class=SavedTalentsSeeder
```

### Hanya Student Side (setelah ProvincesRegenciesSeeder + DummyDataSeeder):
```bash
php artisan db:seed --class=ProblemsSeeder && \
php artisan db:seed --class=ProblemImagesSeeder && \
php artisan db:seed --class=ApplicationsSeeder && \
php artisan db:seed --class=ProjectsSeeder && \
php artisan db:seed --class=DocumentsSeeder && \
php artisan db:seed --class=NotificationsSeeder && \
php artisan db:seed --class=FriendSeeder
```

## ⚠️ Troubleshooting Common Errors

### Error 1: "SQLSTATE[25P02]: In failed sql transaction"

**Penyebab:** Migration constraint gagal karena table sudah ada atau constraint conflict.

**Solusi:**
```bash
# Reset migrations
php artisan migrate:fresh

# Atau manual drop specific constraint di Supabase SQL Editor:
ALTER TABLE users DROP CONSTRAINT IF EXISTS users_user_type_check;
```

### Error 2: "relation 'sessions' already exists"

**Penyebab:** Table sessions sudah ada di database tapi tidak tercatat di migrations table.

**Solusi di Supabase SQL Editor:**
```sql
-- Cek migrations yang sudah berjalan
SELECT * FROM migrations ORDER BY id DESC;

-- Jika table sudah ada tapi migration belum tercatat, insert manual:
INSERT INTO migrations (migration, batch)
VALUES ('2024_01_01_000001_create_sessions_table', 1);
```

### Error 3: "prepared statement does not exist"

**Penyebab:** PostgreSQL limit pada prepared statements saat bulk operations.

**Solusi:** Seeders sudah diperbaiki dengan auto-reconnect setiap 10-20 iterasi. Jika masih error:
```bash
# Jalankan seeder satu per satu dengan delay
php artisan db:seed --class=UserSeeder
sleep 2
php artisan db:seed --class=CompanySeeder
```

### Error 4: "column does not exist" saat seeding

**Penyebab:** Migration dan manual table creation tidak sinkron.

**Solusi:**
```bash
# 1. Drop table yang bermasalah di Supabase SQL Editor
DROP TABLE IF EXISTS table_name CASCADE;

# 2. Hapus record migration terkait
DELETE FROM migrations WHERE migration LIKE '%table_name%';

# 3. Jalankan ulang migration untuk table tersebut
php artisan migrate
```

### Error 5: Seeder mengatakan data sudah ada padahal database kosong

**Penyebab:** SupabaseService cache atau false positive.

**Solusi:** Sudah diperbaiki - semua seeder sekarang menggunakan DB facade. Jika masih terjadi:
```bash
# Clear config cache
php artisan config:clear
php artisan cache:clear

# Restart database connection
php artisan db:seed --class=YourSeeder
```

## 🔄 Reset Database Completely

Jika ingin reset database dari awal:

### Option 1: Via Artisan (Recommended)
```bash
# WARNING: Ini akan menghapus SEMUA data!
php artisan migrate:fresh --seed
```

### Option 2: Manual di Supabase

1. Buka **Supabase SQL Editor**
2. Jalankan script berikut:

```sql
-- Drop all tables (HATI-HATI: Akan menghapus semua data!)
DROP SCHEMA public CASCADE;
CREATE SCHEMA public;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO public;
```

3. Jalankan migrations dan seeders seperti di Quick Start

## 📊 Verification

Setelah seeding selesai, verifikasi data dengan script berikut:

```bash
php artisan tinker
```

Di tinker console:
```php
// Cek Company Side
echo "Companies: " . DB::table('companies')->count() . "\n";
echo "Job Postings: " . DB::table('job_postings')->count() . "\n";
echo "Job Applications: " . DB::table('job_applications')->count() . "\n";
echo "Saved Talents: " . DB::table('saved_talents')->count() . "\n";

// Cek Student Side
echo "Students: " . DB::table('users')->where('user_type', 'student')->count() . "\n";
echo "Institutions: " . DB::table('institutions')->count() . "\n";
echo "Problems: " . DB::table('problems')->count() . "\n";
echo "Problem Applications: " . DB::table('applications')->count() . "\n";
echo "Projects: " . DB::table('projects')->count() . "\n";
echo "Documents: " . DB::table('documents')->count() . "\n";
echo "Notifications: " . DB::table('notifications')->count() . "\n";
```

**Expected Results (Company Side):**
- Companies: 128
- Job Postings: 632 (4-6 per company)
- Job Applications: ~2,332 (12-24 per company)
- Saved Talents: ~1,038 (6-10 per company)

**Expected Results (Student Side):**
- Students: ~700
- Institutions: ~80
- Universities: 117
- Problems: ~150-200
- Problem Applications: ~500-800
- Projects: ~200-300 (75% completed)
- Documents: ~400-600
- Notifications: ~1,000+

## 📁 Seeder Details

### A. Master Data Seeders

#### ProvincesRegenciesSeeder
- Creates 34 provinces dari BPS
- Creates 514 regencies/cities
- Data real dari Badan Pusat Statistik Indonesia
- Required oleh semua seeder lain yang butuh location data

#### DummyDataSeeder
- **Universities:** 117 universities across Indonesia
- **Students:** 700 students (distributed across universities)
- **Institutions:** 80 institutions (Dinas, Puskesmas, NGO, Pemda, dll)
- **Student data:** NIM, major, semester, phone
- **Institution data:** PIC name, position, type, contact
- Required untuk semua seeder lain

### B. Company/Job Matching Seeders

#### JobCategorySeeder
- Creates 33 job categories (SDG-aligned)
- Categories: Software, Marketing, Finance, Healthcare, dll
- Idempotent (dapat dijalankan berkali-kali)

#### UserSeeder
- Creates 128 company users (email: `hr@{company}.co.id`)
- Password: `password123` (WAJIB ganti di production!)
- Uses DB facade untuk reliability
- Auto-reconnect setiap 10 users

#### CompanySeeder
- Creates 128 companies matching UserSeeder
- Data: name, industry, description, website, address
- Requires UserSeeder to run first
- Skips companies that already exist

#### JobPostingKKNSeeder
- Creates 4-6 job postings per company
- Ensures >3 different categories per company
- Total: ~632 job postings
- Data: title, description, requirements, benefits, salary range

#### JobApplicationsCompanySeeder
- Creates 12-24 applications per company
- **ALL 5 statuses represented:** pending, reviewed, shortlisted, rejected, accepted
- Distributed over 90 days for diagram visualization
- Updates job_postings.applications_count
- Requires DummyDataSeeder (students) first

#### SavedTalentsSeeder
- Creates 6-10 saved talents per company
- **ALL 5 categories represented:** Baru, Dihubungi, Interview, Ditawari, Ditolak
- Distributed over 60 days for visualization
- Notes dengan context sesuai category
- Requires DummyDataSeeder (students) first

### C. Student/Problem Matching Seeders

#### ProblemsSeeder
- Creates problems posted by institutions
- Problem types: KKN projects, community service, field work
- Data: title, description, requirements, location, duration
- Requires DummyDataSeeder (institutions) first

#### ProblemImagesSeeder
- Creates images for problems
- Multiple images per problem
- Requires ProblemsSeeder first

#### ApplicationsSeeder
- Creates student applications to problems
- Different from JobApplicationsCompanySeeder!
- Status tracking: applied → accepted/rejected
- Requires ProblemsSeeder and DummyDataSeeder (students)

#### ProjectsSeeder
- Creates ongoing KKN projects
- 75% projects sudah completed
- Data: milestones, progress, team members
- Requires ApplicationsSeeder (accepted applications)

#### DocumentsSeeder
- Creates project documents (reports, presentations, etc)
- File types: PDF, DOCX, PPTX, images
- Requires ProjectsSeeder first

#### NotificationsSeeder
- Creates system notifications for users
- Types: application updates, project reminders, announcements
- Distributed over time for realism
- Requires all other seeders

#### FriendSeeder
- Creates friend connections between students
- Social network feature
- Mutual friendships
- Requires DummyDataSeeder (students)

## 🎯 Data Requirements Met

✅ **>100 companies** (128 created)
✅ **>3 job postings per company** (4-6 per company)
✅ **>3 job categories per company** (4-6 categories)
✅ **>5 applications per company** (12-24 per company)
✅ **ALL 5 application statuses** represented
✅ **>5 saved talents per company** (6-10 per company)
✅ **ALL 5 talent categories** represented
✅ **Time-distributed data** for diagrams (90 days for apps, 60 days for talents)

## 🛠️ Maintenance

### Menambah lebih banyak companies

Edit file berikut dan tambahkan company names di array `$companies`:
- `database/seeders/UserSeeder.php` (line ~40)
- `database/seeders/CompanySeeder.php` (line ~40)

Pastikan company names **SAMA PERSIS** di kedua file!

### Menambah job categories

Edit `database/seeders/JobCategorySeeder.php` dan tambahkan di array `$categories`.

### Re-seed specific data only

```bash
# Re-seed job postings saja
php artisan db:seed --class=JobPostingKKNSeeder

# Re-seed applications saja
php artisan db:seed --class=JobApplicationsCompanySeeder
```

Seeders sudah idempotent - mereka akan skip data yang sudah ada.

## ⚡ Performance Tips

1. **Gunakan DB facade**, bukan SupabaseService untuk bulk operations
2. **Reconnect database** setiap 10-20 iterations untuk avoid prepared statement errors
3. **Batch insert** untuk multiple records sekaligus
4. **Skip existing records** dengan check terlebih dahulu

## 🐛 Debug Mode

Jika ada error yang tidak jelas, enable debug mode:

```bash
# Di .env
APP_DEBUG=true
DB_LOG_QUERIES=true

# Jalankan seeder dengan verbose
php artisan db:seed --class=YourSeeder -vvv
```

## 📞 Support

Jika mengalami error yang tidak tercantum di panduan ini:

1. Cek error message di terminal
2. Cek Supabase logs di Dashboard > Database > Logs
3. Pastikan .env sudah benar
4. Coba migrate:fresh dan seed ulang dari awal

## ⚠️ IMPORTANT NOTES

1. **JANGAN** jalankan seeders di production database tanpa backup!
2. **SELALU** backup database sebelum migrate:fresh
3. Seeders menggunakan `rand()` - data akan sedikit berbeda setiap run
4. Time distribution menggunakan Carbon - timestamps relatif terhadap waktu seeding
5. Email company users: `hr@{company_slug}.co.id` dengan password: `password123`

## 🔐 Security Notes

- Default password di seeders: `password123`
- **WAJIB GANTI** di production!
- Disable seeding di production environment
- Gunakan `php artisan db:seed` dengan `--class` untuk selective seeding

---

**Last Updated:** 2025-11-23
**Database Version:** PostgreSQL 15 (Supabase)
**Total Seed Time:** ~2-5 minutes (depending on connection speed)
