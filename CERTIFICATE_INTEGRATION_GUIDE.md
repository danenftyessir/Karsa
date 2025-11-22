# 🎓 PANDUAN INTEGRASI SERTIFIKAT OTOMATIS

## 📋 OVERVIEW
Sistem auto-generate sertifikat untuk proyek KKN yang telah completed.

**Alur:**
1. Instansi mengubah status proyek → `completed`
2. Sertifikat otomatis dibuat (background process)
3. Sertifikat muncul di profile mahasiswa (private & public)

---

## 🗄️ STEP 1: UPDATE DATABASE (SUPABASE)

Jalankan SQL berikut di **Supabase SQL Editor**:

```sql
-- 1. Tambahkan kolom sertifikat di table projects
ALTER TABLE projects
ADD COLUMN IF NOT EXISTS certificate_path VARCHAR(255) NULL,
ADD COLUMN IF NOT EXISTS certificate_number VARCHAR(20) NULL,
ADD COLUMN IF NOT EXISTS certificate_generated_at TIMESTAMP NULL;

-- 2. Tambahkan kolom pic_name di table institutions (Nama Kepala Desa)
ALTER TABLE institutions
ADD COLUMN IF NOT EXISTS pic_name VARCHAR(255) NULL;

-- 3. Verifikasi perubahan
SELECT column_name, data_type
FROM information_schema.columns
WHERE table_name = 'projects'
  AND column_name IN ('certificate_path', 'certificate_number', 'certificate_generated_at');

SELECT column_name, data_type
FROM information_schema.columns
WHERE table_name = 'institutions'
  AND column_name = 'pic_name';
```

**⚠️ PENTING**: Setelah menjalankan SQL, **update data institutions** yang sudah ada:
```sql
-- Contoh: Update nama kepala desa untuk institusi tertentu
UPDATE institutions
SET pic_name = 'Nama Kepala Desa'
WHERE id = <institution_id>;
```

---

## 📁 STEP 2: UPLOAD FILE TEMPLATE & FONTS

### Struktur Folder yang Dibutuhkan:

```
public/
  assets/
    certificate/
      template.png              ← UPLOAD FILE INI
      fonts/
        Poppins-Regular.ttf    ← UPLOAD FILE INI
        PinyonScript-Regular.ttf ← UPLOAD FILE INI
```

### Cara Upload:

1. **Buat folder structure:**
   ```bash
   mkdir -p public/assets/certificate/fonts
   ```

2. **Copy file dari project lama Anda:**
   - `template.png` → `public/assets/certificate/template.png`
   - `Poppins-Regular.ttf` → `public/assets/certificate/fonts/Poppins-Regular.ttf`
   - `PinyonScript-Regular.ttf` → `public/assets/certificate/fonts/PinyonScript-Regular.ttf`

3. **Verifikasi file ada:**
   ```bash
   ls -la public/assets/certificate/
   ls -la public/assets/certificate/fonts/
   ```

---

## 🧪 STEP 3: TESTING

### Testing di Local:

1. **Update PIC Name institution** (via Supabase):
   ```sql
   UPDATE institutions
   SET pic_name = 'Budi Santoso, S.Sos'
   WHERE id = 24;
   ```

2. **Login sebagai Institution**

3. **Buka project yang statusnya `active`:**
   - Navigasi: `/institution/projects/{id}/manage`

4. **Ubah status project ke `completed`:**
   - Klik dropdown status
   - Pilih "Completed"
   - Klik "Update Status"

5. **Check Log** (Laravel log):
   ```bash
   tail -f storage/logs/laravel.log
   ```

   Cari log seperti ini:
   ```
   [INFO] 🎓 Generating certificate for project {id}
   [INFO] 📤 Uploading certificate to Supabase
   [INFO] ✅ Certificate upload SUCCESS
   [INFO] ✅ Certificate generated successfully: 001/KKN/MHS/KARSA/XI/2024
   ```

6. **Verifikasi di Database:**
   ```sql
   SELECT id, status, certificate_path, certificate_number, certificate_generated_at
   FROM projects
   WHERE id = <project_id>;
   ```

7. **Check di Profile Student:**
   - Login sebagai student yang punya project tersebut
   - Buka `/student/profile`
   - Lihat section "Proyek yang Telah Diselesaikan"
   - **Harus ada box kuning "Sertifikat Tersedia"** dengan tombol Download

8. **Check di Public Profile:**
   - Klik button "Preview Public" di `/student/profile`
   - Atau buka `/u/{username}`
   - **Harus ada box kuning sertifikat** di setiap completed project

9. **Test Download:**
   - Klik tombol "Download" di sertifikat
   - File PNG sertifikat harus terdownload

---

## 🔍 TROUBLESHOOTING

### Error: "Template sertifikat tidak ditemukan"
**Solusi:** Pastikan file `template.png` ada di `public/assets/certificate/template.png`
```bash
ls -la public/assets/certificate/template.png
```

### Error: "Font Poppins tidak ditemukan"
**Solusi:** Pastikan file font ada di folder yang benar
```bash
ls -la public/assets/certificate/fonts/
```

### Sertifikat tidak muncul di profile
**Solusi:** Check database apakah `certificate_path` terisi
```sql
SELECT certificate_path FROM projects WHERE id = <project_id>;
```

### Nomor sertifikat tidak generate
**Solusi:** Check log error di `storage/logs/laravel.log`

---

## 📊 DATA YANG DIGUNAKAN UNTUK GENERATE SERTIFIKAT

| Field Template | Data Source | Contoh |
|----------------|-------------|--------|
| `no_sertifikat` | Auto-increment (001, 002, ...) | "001" |
| `thn_sertifikat` | Current year | "2024" |
| `nama_penerima` | `student.user.name` | "Ahmad Fauzi" |
| `nim_penerima` | `student.nim` | "123456789" |
| `thn_laksana` | `project.start_date` (year) | "2024" |
| `tgl_laksana` | `project.start_date - project.actual_end_date` | "1 Januari - 31 Maret 2024" |
| `kades` | `institution.pic_name` | "Budi Santoso, S.Sos" |

**Format Nomor Sertifikat Lengkap:**
```
001/KKN/MHS/KARSA/XI/2024
```
- `001` = Nomor urut (auto-increment)
- `/KKN/MHS/KARSA/XI/` = Sudah ada di template image
- `2024` = Tahun sertifikat

---

## 📝 FILE YANG TELAH DIBUAT/DIUBAH

### ✅ File Baru:
1. `app/Services/CertificateService.php` - Service generate sertifikat

### ✅ File yang Diubah:
1. `app/Services/SupabaseStorageService.php` - Tambah method `uploadCertificate()`
2. `app/Models/Project.php` - Tambah fillable certificate fields
3. `app/Http/Controllers/Institution/ProjectManagementController.php` - Hook auto-generate
4. `resources/views/student/profile/index.blade.php` - Tampilan sertifikat (private)
5. `resources/views/student/profile/public.blade.php` - Tampilan sertifikat (public)

---

## 🎯 FITUR YANG SUDAH DIIMPLEMENTASI

✅ Auto-generate sertifikat saat project completed
✅ Nomor sertifikat otomatis (format: 001, 002, 003, ...)
✅ Upload sertifikat ke Supabase Storage
✅ Fallback ke local storage jika Supabase tidak dikonfigurasi
✅ Tampilan sertifikat di private profile student
✅ Tampilan sertifikat di public profile student
✅ Download button untuk sertifikat
✅ Error handling & logging lengkap

---

## 🚀 DEPLOYMENT KE PRODUCTION

Ketika deploy ke production:

1. **Pastikan Supabase dikonfigurasi** di `.env`:
   ```env
   SUPABASE_PROJECT_ID=your-project-id
   SUPABASE_SERVICE_KEY=your-service-key
   SUPABASE_BUCKET=kkngo-storage
   ```

2. **Upload file template & fonts** ke server production

3. **Jalankan SQL di Supabase production database**

4. **Update pic_name** untuk semua institutions

5. **Test generate sertifikat** dengan 1 project dulu

---

## 💡 TIPS

1. **Batch Generate Sertifikat untuk Project Lama:**
   ```php
   // Buat artisan command untuk generate sertifikat project lama
   php artisan certificate:generate-old-projects
   ```

2. **Re-generate Sertifikat:**
   - Hapus `certificate_path`, `certificate_number`, `certificate_generated_at` dari database
   - Ubah status project kembali ke `completed`
   - Sertifikat akan auto-generate ulang

3. **Custom Template per Institution:**
   - Bisa dikembangkan dengan menambahkan field `certificate_template` di table institutions
   - Load template sesuai institution ID

---

## 📞 SUPPORT

Jika ada error atau pertanyaan, check:
1. **Laravel Log**: `storage/logs/laravel.log`
2. **Browser Console**: F12 → Console tab
3. **Supabase Logs**: Supabase Dashboard → Logs

---

**🎉 Selamat! Sistem sertifikat otomatis sudah terintegrasi!**
