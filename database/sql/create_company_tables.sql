-- ============================================================================
-- SUPABASE SQL MIGRATION FOR COMPANY MODULE
-- ============================================================================
-- PENTING: SEMUA tabel ini WAJIB dibuat di Supabase PostgreSQL
-- TIDAK ADA data yang disimpan di local database
-- ============================================================================

-- Table: job_categories
-- Kategori lowongan pekerjaan
CREATE TABLE IF NOT EXISTS job_categories (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index untuk job_categories
CREATE INDEX idx_job_categories_slug ON job_categories(slug);

-- Table: job_postings
-- Lowongan pekerjaan yang dibuat oleh company
CREATE TABLE IF NOT EXISTS job_postings (
    id BIGSERIAL PRIMARY KEY,
    company_id BIGINT NOT NULL REFERENCES companies(id) ON DELETE CASCADE,
    job_category_id BIGINT REFERENCES job_categories(id) ON DELETE SET NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    department VARCHAR(100),
    location VARCHAR(255) NOT NULL,
    job_type VARCHAR(50) NOT NULL, -- Full-time, Part-time, Contract, Internship, Freelance
    salary_min DECIMAL(15, 2),
    salary_max DECIMAL(15, 2),
    salary_currency VARCHAR(10) DEFAULT 'USD',
    salary_period VARCHAR(20) DEFAULT 'monthly', -- hourly, daily, monthly, yearly
    description TEXT NOT NULL,
    responsibilities TEXT NOT NULL,
    qualifications TEXT NOT NULL,
    benefits TEXT,
    skills JSONB, -- Array of required skills stored as JSON
    sdg_alignment JSONB, -- Array of SDG goals stored as JSON
    impact_metrics TEXT,
    success_criteria TEXT,
    status VARCHAR(20) DEFAULT 'draft', -- draft, posted, closed, archived
    allow_guest_applications BOOLEAN DEFAULT FALSE,
    views_count INT DEFAULT 0,
    applications_count INT DEFAULT 0,
    published_at TIMESTAMP,
    expires_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP
) WITHOUT OIDS;

-- Indexes untuk job_postings
CREATE INDEX idx_job_postings_company_id ON job_postings(company_id);
CREATE INDEX idx_job_postings_category_id ON job_postings(job_category_id);
CREATE INDEX idx_job_postings_slug ON job_postings(slug);
CREATE INDEX idx_job_postings_status ON job_postings(status);
CREATE INDEX idx_job_postings_job_type ON job_postings(job_type);
CREATE INDEX idx_job_postings_published_at ON job_postings(published_at);
CREATE INDEX idx_job_postings_skills ON job_postings USING GIN(skills);
CREATE INDEX idx_job_postings_sdg_alignment ON job_postings USING GIN(sdg_alignment);

-- Table: job_applications
-- Lamaran pekerjaan dari kandidat
CREATE TABLE IF NOT EXISTS job_applications (
    id BIGSERIAL PRIMARY KEY,
    job_posting_id BIGINT NOT NULL REFERENCES job_postings(id) ON DELETE CASCADE,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    status VARCHAR(20) DEFAULT 'new', -- new, reviewing, shortlisted, interview, offer, rejected, hired
    cover_letter TEXT,
    resume_url TEXT,
    portfolio_url TEXT,
    expected_salary DECIMAL(15, 2),
    available_from DATE,
    notes TEXT, -- Internal notes from company
    rating INT CHECK (rating >= 1 AND rating <= 5),
    reviewed_at TIMESTAMP,
    reviewed_by BIGINT REFERENCES users(id),
    interview_scheduled_at TIMESTAMP,
    offer_extended_at TIMESTAMP,
    hired_at TIMESTAMP,
    rejected_at TIMESTAMP,
    rejection_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) WITHOUT OIDS;

-- Indexes untuk job_applications
CREATE INDEX idx_job_applications_job_posting_id ON job_applications(job_posting_id);
CREATE INDEX idx_job_applications_user_id ON job_applications(user_id);
CREATE INDEX idx_job_applications_status ON job_applications(status);
CREATE INDEX idx_job_applications_created_at ON job_applications(created_at);

-- Unique constraint: user tidak bisa apply 2x ke job yang sama
CREATE UNIQUE INDEX idx_job_applications_unique ON job_applications(job_posting_id, user_id);

-- Table: saved_talents
-- Pivot table untuk company menyimpan talent favorit
CREATE TABLE IF NOT EXISTS saved_talents (
    id BIGSERIAL PRIMARY KEY,
    company_id BIGINT NOT NULL REFERENCES companies(id) ON DELETE CASCADE,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    category VARCHAR(100), -- Optional: untuk grouping (AI/ML, Marketing, etc)
    notes TEXT,
    saved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) WITHOUT OIDS;

-- Indexes untuk saved_talents
CREATE INDEX idx_saved_talents_company_id ON saved_talents(company_id);
CREATE INDEX idx_saved_talents_user_id ON saved_talents(user_id);
CREATE INDEX idx_saved_talents_category ON saved_talents(category);

-- Unique constraint: company tidak bisa save talent yang sama 2x
CREATE UNIQUE INDEX idx_saved_talents_unique ON saved_talents(company_id, user_id);

-- Table: job_posting_skills (Many-to-Many)
-- Untuk relasi many-to-many antara job_postings dan skills
-- Note: Ini opsional karena kita sudah pakai JSONB di job_postings.skills
-- Tapi bisa digunakan jika ingin relasi yang lebih terstruktur
CREATE TABLE IF NOT EXISTS job_posting_skills (
    id BIGSERIAL PRIMARY KEY,
    job_posting_id BIGINT NOT NULL REFERENCES job_postings(id) ON DELETE CASCADE,
    skill_name VARCHAR(100) NOT NULL,
    proficiency_level VARCHAR(20) DEFAULT 'intermediate', -- beginner, intermediate, advanced, expert
    required BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Indexes untuk job_posting_skills
CREATE INDEX idx_job_posting_skills_job_posting_id ON job_posting_skills(job_posting_id);
CREATE INDEX idx_job_posting_skills_skill_name ON job_posting_skills(skill_name);

-- ============================================================================
-- FUNCTIONS & TRIGGERS
-- ============================================================================

-- Function untuk auto-update updated_at timestamp
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Triggers untuk auto-update updated_at
CREATE TRIGGER update_job_categories_updated_at BEFORE UPDATE ON job_categories
FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_job_postings_updated_at BEFORE UPDATE ON job_postings
FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_job_applications_updated_at BEFORE UPDATE ON job_applications
FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_saved_talents_updated_at BEFORE UPDATE ON saved_talents
FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- Function untuk auto-increment applications_count pada job_postings
CREATE OR REPLACE FUNCTION increment_applications_count()
RETURNS TRIGGER AS $$
BEGIN
    UPDATE job_postings
    SET applications_count = applications_count + 1
    WHERE id = NEW.job_posting_id;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER increment_job_applications_count
AFTER INSERT ON job_applications
FOR EACH ROW EXECUTE FUNCTION increment_applications_count();

-- Function untuk auto-decrement applications_count pada job_postings
CREATE OR REPLACE FUNCTION decrement_applications_count()
RETURNS TRIGGER AS $$
BEGIN
    UPDATE job_postings
    SET applications_count = applications_count - 1
    WHERE id = OLD.job_posting_id;
    RETURN OLD;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER decrement_job_applications_count
AFTER DELETE ON job_applications
FOR EACH ROW EXECUTE FUNCTION decrement_applications_count();

-- ============================================================================
-- SEED DATA - Job Categories
-- ============================================================================

INSERT INTO job_categories (name, slug, description, icon) VALUES
('Engineering', 'engineering', 'Software engineering and development roles', 'code'),
('Marketing', 'marketing', 'Marketing and growth positions', 'megaphone'),
('Design', 'design', 'UI/UX and product design roles', 'palette'),
('HR', 'hr', 'Human resources and recruitment', 'users'),
('Sales', 'sales', 'Sales and business development', 'trending-up'),
('Finance', 'finance', 'Finance and accounting positions', 'dollar-sign'),
('Operations', 'operations', 'Operations and logistics', 'settings'),
('Product', 'product', 'Product management roles', 'package'),
('Customer Support', 'customer-support', 'Customer service positions', 'headphones'),
('Legal', 'legal', 'Legal and compliance roles', 'file-text'),
('Data Science', 'data-science', 'Data analysis and science', 'bar-chart'),
('DevOps', 'devops', 'DevOps and infrastructure', 'server')
ON CONFLICT (slug) DO NOTHING;

-- ============================================================================
-- ROW LEVEL SECURITY (RLS) POLICIES
-- ============================================================================
-- CATATAN: RLS policies di-disable karena aplikasi menggunakan Laravel backend
-- dengan authorization layer sendiri (Policies, Gates, Middleware).
-- RLS tidak diperlukan karena semua akses database melalui Laravel, bukan direct client.
--
-- Jika ingin mengaktifkan RLS untuk direct client access di masa depan,
-- uncomment code di bawah dan sesuaikan dengan auth system yang digunakan.
--
-- -- Enable RLS on all tables
-- ALTER TABLE job_categories ENABLE ROW LEVEL SECURITY;
-- ALTER TABLE job_postings ENABLE ROW LEVEL SECURITY;
-- ALTER TABLE job_applications ENABLE ROW LEVEL SECURITY;
-- ALTER TABLE saved_talents ENABLE ROW LEVEL SECURITY;
-- ALTER TABLE job_posting_skills ENABLE ROW LEVEL SECURITY;

-- ============================================================================
-- NOTES
-- ============================================================================
--
-- Untuk menjalankan SQL ini di Supabase:
-- 1. Buka Supabase Dashboard
-- 2. Pilih project Anda
-- 3. Pergi ke SQL Editor
-- 4. Copy-paste SQL ini dan execute
--
-- Atau via Laravel:
-- DB::unprepared(file_get_contents(database_path('sql/create_company_tables.sql')));
--
-- ============================================================================
