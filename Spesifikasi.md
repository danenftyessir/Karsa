# KKN-GO AI Integration Specification

## 📋 Daftar Isi
1. [Overview](#overview)
2. [User Flow Architecture](#user-flow-architecture)
3. [AI Features Specification](#ai-features-specification)
4. [Payment & Verification Flow](#payment--verification-flow)
5. [Technical Architecture](#technical-architecture)
6. [Database Schema](#database-schema)
7. [API Endpoints](#api-endpoints)
8. [Security & Privacy](#security--privacy)
9. [Future Roadmap](#future-roadmap)

---

## 🎯 Overview

### Vision
Platform KKN berbasis AI yang memfasilitasi matching mahasiswa dengan proyek institusi secara intelligent, dengan verifikasi otomatis dan asisten AI untuk optimalisasi kolaborasi.

### Core AI Features
| Feature | Target User | Priority | Status |
|---------|-------------|----------|--------|
| Document Verification AI | Institution | P0 (Critical) | Phase 1 |
| Project Matching AI | Student | P0 (Critical) | Phase 1 |
| AI Chatbot Assistant | Student | P1 (High) | Phase 2 |
| Project Suggestion AI | Student | P1 (High) | Phase 2 |
| Auto Moderation | Both | P2 (Medium) | Phase 3 |

---

## 🔄 User Flow Architecture

### 1. Institution Registration & Verification Flow

```mermaid
graph TD
    A[Institusi Akses /register/institution] --> B[Isi Form Registrasi]
    B --> C[Upload Dokumen Verifikasi]
    C --> D{AI Document Verification}
    D -->|Valid| E[Status: PENDING_PAYMENT]
    D -->|Invalid/Suspicious| F[Status: NEEDS_REVIEW]
    D -->|Fraud Detected| G[Status: REJECTED]

    E --> H[Email: Pilih Paket]
    F --> I[Human Review Queue]

    I -->|Approved| E
    I -->|Rejected| G

    H --> J[Pilih Paket Subscription]
    J --> K[Payment Gateway]
    K -->|Success| L[Status: ACTIVE]
    K -->|Failed| M[Retry Payment]

    L --> N[Akses Dashboard Penuh]
    N --> O[Bisa Post Problems]
```

#### 1.1 Registration States

```typescript
enum InstitutionStatus {
  PENDING_VERIFICATION = 'pending_verification',  // Baru daftar, AI sedang proses
  NEEDS_REVIEW = 'needs_review',                  // AI flag, butuh human review
  PENDING_PAYMENT = 'pending_payment',            // Verified, belum bayar
  ACTIVE = 'active',                              // Paid & Active
  SUSPENDED = 'suspended',                        // Suspended by admin
  REJECTED = 'rejected'                           // Verification failed
}
```

#### 1.2 Registration Data Required

**Basic Info:**
- Institution Name
- Institution Type (Desa, NGO, Puskesmas, dll)
- Official Email
- Phone Number
- Website (optional)

**Location:**
- Address
- Province
- Regency/City

**PIC (Person in Charge):**
- Name
- Position
- Contact

**Verification Documents (AI will analyze):**
- ✅ Surat Pengantar/SK Institusi (PDF)
- ✅ Logo Institusi (Image)
- ✅ KTP PIC (Image)
- ✅ NPWP (Optional, gives higher trust score)

---

### 2. Student Registration & AI Matching Flow

```mermaid
graph TD
    A[Mahasiswa Akses /register/student] --> B[Isi Form Registrasi]
    B --> C[Verifikasi Email Universitas]
    C --> D{Email Verified?}
    D -->|Yes| E[Status: ACTIVE]
    D -->|No| F[Resend Email]

    E --> G[Welcome: AI Onboarding]
    G --> H[AI Questionnaire]
    H --> I[Generate Interest Profile]
    I --> J[Browse Problems dengan AI Score]

    J --> K[Apply to Project]
    K --> L{Accepted?}
    L -->|Yes| M[Akses AI Project Assistant]
    L -->|No| N[Continue Browsing]
```

#### 2.1 Student Status

```typescript
enum StudentStatus {
  PENDING_VERIFICATION = 'pending_verification',  // Email belum verified
  ACTIVE = 'active',                              // Verified & Active
  PROFILE_INCOMPLETE = 'profile_incomplete',      // Perlu lengkapi AI questionnaire
  SUSPENDED = 'suspended'                         // Suspended by admin
}
```

---

## 🤖 AI Features Specification

### Feature 1: AI Document Verification (Institution)

#### Purpose
Otomatis verifikasi legitimasi dokumen institusi untuk mencegah fraud dan mempercepat approval.

#### AI Model
- **Primary:** Document Classification (CNN/Transformer)
- **Secondary:** OCR + NER untuk extract informasi
- **Tertiary:** Fraud Detection (Anomaly Detection)

#### Verification Checks

| Check | Method | Weight |
|-------|--------|--------|
| Document Type Classification | Image Classification | 20% |
| Text Authenticity | OCR + Tamper Detection | 25% |
| Logo Match | Image Similarity | 15% |
| Information Consistency | NER + Cross-validation | 25% |
| Historical Pattern | Anomaly Detection | 15% |

#### Verification Score

```typescript
interface VerificationResult {
  score: number;              // 0-100
  confidence: number;         // 0-1
  status: 'approved' | 'needs_review' | 'rejected';
  flags: VerificationFlag[];
  extractedData: {
    institutionName: string;
    picName: string;
    identityNumber: string;
    issueDate: Date;
  };
  reasoning: string;          // AI explanation
}

interface VerificationFlag {
  type: 'warning' | 'error' | 'info';
  field: string;
  message: string;
  severity: number;           // 0-10
}
```

#### Decision Rules

```typescript
if (score >= 85 && confidence >= 0.8) {
  return 'approved';          // Auto-approve
} else if (score >= 60) {
  return 'needs_review';      // Human review
} else {
  return 'rejected';          // Auto-reject
}
```

#### Advanced Fraud Detection System

**Problem:** Sophisticated fraudsters can create fake documents that look authentic in format and appearance.

**Solution:** Multi-layer verification system that goes beyond document appearance checking.

##### Layer 1: Document Authenticity Analysis

| Check Type | Method | Description |
|------------|--------|-------------|
| **Pixel-Level Analysis** | Computer Vision | Detect inconsistent pixel patterns, clone stamping, copy-paste artifacts |
| **Metadata Verification** | EXIF/Metadata parsing | Check creation date, software used, modification history |
| **Watermark Detection** | Pattern matching | Verify presence and authenticity of government watermarks |
| **Font Analysis** | OCR + Typography | Check if fonts match official document standards |
| **Paper Texture** | Image analysis | Detect digital artifacts that shouldn't exist in scanned docs |
| **Signature Verification** | Biometric analysis | Compare signature consistency across documents |
| **Template Matching** | Image similarity | Compare against known authentic document templates |
| **QR/Barcode Validation** | Decoder + API call | Validate QR codes against issuing authority database |

```typescript
interface DocumentAuthenticityCheck {
  tamperDetection: {
    cloneStamping: boolean;
    photoManipulation: boolean;
    textOverlay: boolean;
    pixelInconsistency: boolean;
    score: number; // 0-100
  };
  metadataAnalysis: {
    creationDate: Date;
    modificationHistory: string[];
    softwareUsed: string;
    suspiciousFlags: string[];
    score: number;
  };
  watermarkVerification: {
    present: boolean;
    authentic: boolean;
    position: string;
    score: number;
  };
  overallAuthenticity: number; // 0-100
}
```

##### Layer 2: Risk Scoring Algorithm

```typescript
interface RiskFactors {
  // Document-based factors
  documentAuthenticity: number;        // 0-100
  documentQuality: number;             // Low quality = suspicious

  // Information consistency
  crossDocumentConsistency: number;    // Info matches across all docs?
  extractedDataValidity: number;       // Is extracted data logical?

  // Institution verification
  institutionExists: boolean;          // External validation result
  webPresenceScore: number;            // 0-100
  socialMediaPresence: number;         // 0-100

  // Pattern analysis
  emailDomainLegitimacy: number;       // Custom domain vs free email
  phoneNumberValidity: number;         // Real number? Region match?
  addressVerification: number;         // Real address?

  // Historical patterns
  similarRejectedApplications: number; // Similar to past frauds?
  unusualRegistrationTime: boolean;    // Registered at odd hours?
  ipReputationScore: number;           // IP address reputation
  velocityCheck: number;               // Too many registrations from same source?
}

function calculateRiskScore(factors: RiskFactors): number {
  const weights = {
    documentAuthenticity: 0.25,
    crossDocumentConsistency: 0.20,
    institutionExists: 0.15,
    webPresenceScore: 0.10,
    documentQuality: 0.10,
    emailDomainLegitimacy: 0.08,
    addressVerification: 0.07,
    similarRejectedApplications: 0.05
  };

  // Calculate weighted score
  let score = 0;
  Object.entries(weights).forEach(([key, weight]) => {
    score += (factors[key] || 0) * weight;
  });

  // Penalty for red flags
  if (factors.similarRejectedApplications > 0) {
    score -= 20; // Significant penalty
  }
  if (!factors.institutionExists) {
    score -= 30; // Major red flag
  }

  return Math.max(0, Math.min(100, score));
}

interface FraudDecision {
  riskScore: number;              // 0-100 (100 = most trustworthy)
  confidence: number;             // 0-1
  decision: 'approved' | 'needs_review' | 'rejected';
  redFlags: RedFlag[];
  reasoning: string;
}

interface RedFlag {
  type: 'critical' | 'warning' | 'info';
  category: string;
  message: string;
  severity: number;              // 1-10
  impact: number;                // Impact on final score
}
```

##### Layer 3: External Validation

**Government Database Integration:**
```typescript
interface ExternalValidation {
  // ID Card Verification
  ktpValidation: {
    provider: 'Dukcapil API' | 'Third-party KYC';
    nik: string;
    valid: boolean;
    holderName: string;
    matchScore: number;          // Name match with application
  };

  // Tax ID Verification
  npwpValidation?: {
    provider: 'DJP API';
    npwp: string;
    valid: boolean;
    registeredName: string;
    status: 'active' | 'inactive';
  };

  // Business Entity Verification
  businessValidation?: {
    provider: 'BKPM / OSS System';
    nib: string;                 // Nomor Induk Berusaha
    companyName: string;
    valid: boolean;
    registrationDate: Date;
  };

  // Web Presence Check
  webPresence: {
    domainAge: number;           // Days since domain registered
    domainRegistrar: string;
    hasSSL: boolean;
    websiteContent: {
      hasContactInfo: boolean;
      hasAboutPage: boolean;
      hasNews: boolean;
      contentQualityScore: number;
    };
    socialMedia: {
      facebook?: { followers: number; verified: boolean; };
      instagram?: { followers: number; verified: boolean; };
      linkedin?: { followers: number; verified: boolean; };
    };
  };
}
```

**Web Presence Scoring:**
```typescript
function calculateWebPresenceScore(presence: WebPresence): number {
  let score = 0;

  // Domain age (max 30 points)
  if (presence.domainAge > 365) score += 30;
  else if (presence.domainAge > 180) score += 20;
  else if (presence.domainAge > 30) score += 10;
  else score += 0; // New domain = suspicious

  // SSL (10 points)
  if (presence.hasSSL) score += 10;

  // Website content (30 points)
  score += presence.websiteContent.contentQualityScore * 0.3;

  // Social media (30 points)
  const socialScore =
    (presence.socialMedia.facebook?.followers || 0) / 100 +
    (presence.socialMedia.instagram?.followers || 0) / 100 +
    (presence.socialMedia.linkedin?.followers || 0) / 100;
  score += Math.min(30, socialScore);

  return Math.min(100, score);
}
```

##### Layer 4: Fraud Pattern Recognition

```typescript
interface FraudPattern {
  patternType: 'template_scam' | 'identity_theft' | 'mass_registration' | 'email_fraud';
  description: string;
  detectionMethod: string;
  matchConfidence: number;
}

// Common fraud patterns
const fraudPatterns = {
  templateScam: {
    // Using same document template for multiple fake institutions
    check: async (documents: Document[]) => {
      const similarityThreshold = 0.95;
      const matchesInDb = await findSimilarDocuments(documents, similarityThreshold);
      return matchesInDb.length > 0;
    },
    severity: 10
  },

  identityTheft: {
    // Using stolen real identity documents
    check: async (ktp: Document, institutionInfo: any) => {
      // Cross-reference with reported stolen IDs
      const isStolen = await checkStolenIdDatabase(ktp.identityNumber);
      // Check if same identity used for multiple registrations
      const multipleUse = await countIdentityUsage(ktp.identityNumber);
      return isStolen || multipleUse > 1;
    },
    severity: 10
  },

  massRegistration: {
    // Multiple registrations from same source
    check: async (registration: Registration) => {
      const timeWindow = 24 * 60 * 60 * 1000; // 24 hours
      const registrationsFromIp = await countRecentRegistrations(
        registration.ipAddress,
        timeWindow
      );
      return registrationsFromIp > 3; // More than 3 in 24h = suspicious
    },
    severity: 8
  },

  emailFraud: {
    // Disposable email, free email for institution
    check: (email: string) => {
      const freeEmailProviders = ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com'];
      const disposableEmailPattern = /temp|disposable|trash|guerrilla/i;

      const domain = email.split('@')[1];
      return freeEmailProviders.includes(domain) ||
             disposableEmailPattern.test(domain);
    },
    severity: 6
  }
};
```

##### Decision Matrix

```typescript
function makeVerificationDecision(
  documentScore: number,
  riskScore: number,
  externalValidation: ExternalValidation,
  fraudPatterns: FraudPattern[],
  confidence: number
): FraudDecision {

  const criticalFlags = fraudPatterns.filter(p => p.matchConfidence > 0.8);
  const hasExternalValidation = externalValidation.ktpValidation.valid;

  // Auto-reject conditions
  if (criticalFlags.length > 0) {
    return {
      riskScore: 0,
      confidence: 0.95,
      decision: 'rejected',
      redFlags: criticalFlags.map(p => ({
        type: 'critical',
        category: p.patternType,
        message: p.description,
        severity: 10,
        impact: -100
      })),
      reasoning: `Critical fraud pattern detected: ${criticalFlags[0].patternType}`
    };
  }

  if (!hasExternalValidation && riskScore < 50) {
    return {
      riskScore,
      confidence: 0.90,
      decision: 'rejected',
      redFlags: [{
        type: 'critical',
        category: 'external_validation_failed',
        message: 'Could not validate identity against government database',
        severity: 9,
        impact: -50
      }],
      reasoning: 'External validation failed and low risk score'
    };
  }

  // Auto-approve conditions
  if (riskScore >= 85 && confidence >= 0.8 && hasExternalValidation) {
    return {
      riskScore,
      confidence,
      decision: 'approved',
      redFlags: [],
      reasoning: 'High risk score with external validation confirmed'
    };
  }

  // Manual review conditions
  return {
    riskScore,
    confidence,
    decision: 'needs_review',
    redFlags: fraudPatterns.map(p => ({
      type: p.matchConfidence > 0.5 ? 'warning' : 'info',
      category: p.patternType,
      message: p.description,
      severity: Math.round(p.matchConfidence * 10),
      impact: -Math.round(p.matchConfidence * 20)
    })),
    reasoning: 'Score within review range. Human verification recommended.'
  };
}
```

##### Manual Review Checklist

For human reviewers when AI flags `needs_review`:

```typescript
interface ManualReviewChecklist {
  // Document Review
  documentsAppearAuthentic: boolean;
  signatureConsistent: boolean;
  stampAndSealPresent: boolean;

  // Cross-verification
  informationConsistentAcrossDocs: boolean;
  nameSpellingConsistent: boolean;
  datesLogical: boolean;

  // External Checks
  institutionWebsiteVisited: boolean;
  institutionPhoneNumberCalled: boolean;
  addressVerifiedOnMaps: boolean;
  socialMediaChecked: boolean;

  // Risk Assessment
  anyRedFlagsFound: boolean;
  redFlagDetails?: string;

  // Final Decision
  reviewerDecision: 'approve' | 'reject';
  reviewerNotes: string;
  reviewerConfidence: number; // 1-5
  reviewTime: number; // minutes spent reviewing
}
```

##### Real-World Scenario Examples

**Scenario 1: Legitimate Institution (Desa Sukamaju)**
```json
{
  "documentScore": 92,
  "riskScore": 88,
  "externalValidation": {
    "ktpValidation": { "valid": true, "matchScore": 95 },
    "webPresence": {
      "domainAge": 730,
      "hasSSL": true,
      "socialMedia": { "facebook": { "followers": 2500 } }
    }
  },
  "fraudPatterns": [],
  "decision": "approved",
  "confidence": 0.91
}
```

**Scenario 2: New NGO (Needs Review)**
```json
{
  "documentScore": 75,
  "riskScore": 68,
  "externalValidation": {
    "ktpValidation": { "valid": true, "matchScore": 100 },
    "webPresence": {
      "domainAge": 45,
      "hasSSL": true,
      "socialMedia": { "instagram": { "followers": 120 } }
    }
  },
  "fraudPatterns": [
    {
      "patternType": "email_fraud",
      "matchConfidence": 0.3,
      "description": "Using free email provider"
    }
  ],
  "redFlags": [
    {
      "type": "warning",
      "category": "new_organization",
      "message": "Recently established organization with limited online presence",
      "severity": 5
    }
  ],
  "decision": "needs_review",
  "confidence": 0.72,
  "reasoning": "Valid documents but limited track record. Manual review recommended."
}
```

**Scenario 3: Fraudulent Institution (Auto-Reject)**
```json
{
  "documentScore": 45,
  "riskScore": 22,
  "externalValidation": {
    "ktpValidation": { "valid": false },
    "webPresence": {
      "domainAge": 7,
      "hasSSL": false,
      "socialMedia": {}
    }
  },
  "fraudPatterns": [
    {
      "patternType": "template_scam",
      "matchConfidence": 0.94,
      "description": "Document template matches 3 previously rejected applications"
    },
    {
      "patternType": "mass_registration",
      "matchConfidence": 0.85,
      "description": "5 registrations from same IP in last 12 hours"
    }
  ],
  "redFlags": [
    {
      "type": "critical",
      "category": "template_scam",
      "message": "Document appears to use same template as known fraud cases",
      "severity": 10
    },
    {
      "type": "critical",
      "category": "external_validation_failed",
      "message": "Identity verification failed - NIK not found in Dukcapil",
      "severity": 10
    }
  ],
  "decision": "rejected",
  "confidence": 0.96,
  "reasoning": "Multiple critical fraud indicators detected. External validation failed."
}
```

#### Sample API Request

```json
POST /api/ai/verify-documents
{
  "institution_id": "inst_123",
  "documents": {
    "official_letter": "https://storage/docs/letter.pdf",
    "logo": "https://storage/docs/logo.png",
    "pic_identity": "https://storage/docs/ktp.jpg"
  }
}

Response:
{
  "verification_id": "ver_456",
  "status": "approved",
  "score": 92,
  "confidence": 0.91,
  "flags": [],
  "extracted_data": {
    "institution_name": "Desa Sukamaju",
    "pic_name": "Budi Santoso",
    "identity_number": "3201234567890123"
  },
  "reasoning": "All documents appear authentic. Logo matches official records. Text extraction shows consistent information across documents.",
  "processed_at": "2025-01-15T10:30:00Z"
}
```

---

### Feature 2: AI Questionnaire & Project Matching (Student)

#### Purpose
Memahami preferensi, skill, dan minat mahasiswa untuk recommend proyek yang paling sesuai.

#### Questionnaire Categories

**1. Academic Background**
- Major/Program Studi
- Semester
- GPA Range (optional)
- Relevant Coursework

**2. Skills & Expertise**
```typescript
interface SkillAssessment {
  category: string;           // e.g., "Programming", "Design", "Social Work"
  skills: string[];           // e.g., ["Python", "React", "Community Engagement"]
  proficiency: 'beginner' | 'intermediate' | 'advanced';
  yearsOfExperience?: number;
  portfolioUrl?: string;
}
```

**3. Interests & Preferences**
- Problem domains (Education, Health, Infrastructure, dll)
- Preferred location (Urban/Rural, specific regions)
- Team size preference
- Duration preference
- Remote/On-site preference

**4. Personal Motivation**
```typescript
interface Motivation {
  primaryGoal: string;        // Career, Social Impact, Learning, etc.
  careerAspirations: string[];
  socialCauses: string[];
  learningGoals: string[];
}
```

#### AI Matching Algorithm

**Input:**
- Student profile + questionnaire data
- All available problems/projects

**Output:**
- Ranked list of problems with match scores

**Matching Factors:**

| Factor | Weight | Description |
|--------|--------|-------------|
| Skill Match | 30% | Student skills vs required skills |
| Interest Alignment | 25% | Domain interest vs problem category |
| Location Preference | 15% | Preferred location vs project location |
| Difficulty Level | 15% | Student level vs project complexity |
| Team Composition | 10% | Team dynamics & diversity |
| Availability | 5% | Timeline compatibility |

**Matching Score Formula:**

```typescript
interface MatchingScore {
  overallScore: number;       // 0-100
  breakdown: {
    skillMatch: number;
    interestAlignment: number;
    locationFit: number;
    difficultyFit: number;
    teamFit: number;
    timelineFit: number;
  };
  reasoning: string;
  recommendations: string[];   // Why this is a good match
  concerns: string[];          // Potential challenges
}

function calculateMatchScore(
  student: StudentProfile,
  problem: Problem
): MatchingScore {
  // AI ML model here
  // Uses embeddings + similarity search
  // Consider both explicit (questionnaire) and implicit (behavior) data
}
```

#### Sample Matching API

```json
GET /api/ai/match-problems?student_id=std_789

Response:
{
  "matches": [
    {
      "problem_id": "prob_101",
      "problem_title": "Sistem Informasi Desa Digital",
      "institution": "Desa Sukamaju",
      "match_score": 94,
      "breakdown": {
        "skill_match": 95,
        "interest_alignment": 92,
        "location_fit": 88,
        "difficulty_fit": 90,
        "team_fit": 100,
        "timeline_fit": 95
      },
      "reasoning": "Your web development skills align perfectly with this project. You've expressed strong interest in rural development, and this project is in your preferred region.",
      "recommendations": [
        "Your React and Laravel expertise is exactly what they need",
        "This project aligns with your goal of social impact in rural areas",
        "Team size (3-4) matches your preference"
      ],
      "concerns": [
        "Project duration (3 months) is slightly longer than your preference (2 months)"
      ],
      "estimated_impact": "high"
    }
  ],
  "total_matches": 47,
  "recommendations": {
    "top_picks": 5,
    "good_fits": 12,
    "stretch_opportunities": 30
  }
}
```

---

### Feature 3: AI Chatbot Assistant (Student)

#### Purpose
Menjawab pertanyaan mahasiswa tentang proyek, institusi, dan best practices KKN menggunakan RAG (Retrieval-Augmented Generation).

#### AI Models Used
- **Claude 3.5 Sonnet**: Main conversational AI for generating responses
- **Claude 3 Haiku**: For simple queries to reduce costs
- **Cohere Embed v3**: Generate embeddings for knowledge base
- **Cohere Rerank**: Improve search result relevance

#### Architecture

```
Student Question
    ↓
Query Understanding (NLP)
    ↓
Generate Query Embedding (Cohere Embed v3)
    ↓
Vector Search in Knowledge Base (ChromaDB/pgvector)
    ↓
Rerank Results (Cohere Rerank)
    ↓
Context Retrieval (Top 5-10 relevant documents)
    ↓
LLM Generation (Claude 3.5 Sonnet/Haiku)
    ↓
Response with Sources
```

#### Knowledge Base Sources

1. **Project Documentation**
   - Problem descriptions
   - Institution profiles
   - Project milestones
   - Previous project reports

2. **FAQ Database**
   - Common questions
   - Policy documents
   - Guidelines

3. **Historical Data**
   - Past student experiences
   - Project outcomes
   - Best practices

#### Chatbot Capabilities

| Capability | Example |
|------------|---------|
| Project Q&A | "Apa saja deliverables untuk project Desa Sukamaju?" |
| Institution Info | "Ceritakan tentang track record Desa Sukamaju" |
| Best Practices | "Bagaimana cara approach awal meeting dengan institusi?" |
| Technical Help | "Tools apa yang direkomendasikan untuk project web ini?" |
| Timeline Planning | "Buatkan timeline untuk project 3 bulan" |

#### Sample Chat API

```json
POST /api/ai/chat
{
  "student_id": "std_789",
  "project_id": "prob_101",
  "message": "Apa saja teknologi yang harus saya kuasai untuk project ini?",
  "conversation_id": "conv_456"
}

Response:
{
  "message_id": "msg_789",
  "response": "Berdasarkan requirement project Sistem Informasi Desa Digital, kamu perlu menguasai:\n\n1. **Frontend**: React.js untuk UI yang interaktif\n2. **Backend**: Laravel untuk REST API\n3. **Database**: MySQL untuk data management\n4. **Tools**: Git, Postman, dan Figma untuk kolaborasi\n\nUntuk level beginner-intermediate, saya sarankan fokus ke React dan Laravel dulu. Tim biasanya ada yang handle database.",
  "sources": [
    {
      "type": "problem_description",
      "id": "prob_101",
      "excerpt": "Tech stack: React, Laravel, MySQL"
    },
    {
      "type": "previous_project",
      "id": "proj_55",
      "excerpt": "Tim kami berhasil dengan React + Laravel"
    }
  ],
  "confidence": 0.92,
  "follow_up_suggestions": [
    "Apakah ada learning resources yang direkomendasikan?",
    "Berapa lama estimasi learning curve untuk tech stack ini?",
    "Apakah institusi provide mentorship untuk technical skills?"
  ]
}
```

---

### Feature 4: AI Project Suggestion (Student)

#### Purpose
Memberikan saran proyek proaktif berdasarkan trending topics, student profile, dan market demand.

#### Suggestion Types

**1. Personalized Recommendations**
- Based on questionnaire
- Based on browsing history
- Based on saved problems

**2. Trending Projects**
- Most applied
- High impact
- Urgent deadlines

**3. Career-Aligned**
- Match dengan career goals
- Industry connections
- Portfolio building opportunities

**4. Stretch Opportunities**
- Slightly above current skill level
- High learning potential
- Mentorship available

#### Sample Suggestion API

```json
GET /api/ai/suggest-projects?student_id=std_789&type=personalized

Response:
{
  "suggestions": [
    {
      "reason": "Perfect Match",
      "projects": [
        {
          "problem_id": "prob_101",
          "match_score": 94,
          "why": "Aligns with your web dev skills and rural development interest"
        }
      ]
    },
    {
      "reason": "Trending Now",
      "projects": [
        {
          "problem_id": "prob_205",
          "match_score": 78,
          "why": "High demand for health tech projects. 15 students already applied.",
          "urgency": "high"
        }
      ]
    },
    {
      "reason": "Stretch Opportunity",
      "projects": [
        {
          "problem_id": "prob_312",
          "match_score": 65,
          "why": "Learn mobile development (React Native). Mentorship provided.",
          "growth_potential": "high"
        }
      ]
    }
  ],
  "insights": {
    "market_trends": "Web development projects up 30% this month",
    "your_advantage": "Your Laravel skills are in high demand",
    "recommendation": "Consider learning React Native for mobile projects"
  }
}
```

---

## 💳 Payment & Verification Flow

### Subscription Packages

| Package | Duration | Price | Features | Target |
|---------|----------|-------|----------|--------|
| **Trial** | 1 month | Free | 1 problem post, limited applicants | Small institutions |
| **Basic** | 3 months | Rp 500K | 5 problems, unlimited applicants | Medium institutions |
| **Pro** | 6 months | Rp 800K | 15 problems, priority support, analytics | Large institutions |
| **Enterprise** | 12 months | Custom | Unlimited, dedicated support, API access | Government/NGO |

### Payment Flow

```mermaid
graph TD
    A[Institution Dashboard] --> B{Subscription Status}
    B -->|PENDING_PAYMENT| C[Pilih Paket]
    B -->|ACTIVE| D[Dashboard]

    C --> E[Review Paket Details]
    E --> F[Checkout Page]
    F --> G[Pilih Payment Method]

    G --> H[Midtrans Payment Gateway]
    H --> I{Payment Status}

    I -->|Success| J[Webhook: payment.success]
    I -->|Pending| K[Webhook: payment.pending]
    I -->|Failed| L[Webhook: payment.failed]

    J --> M[Update Status: ACTIVE]
    M --> N[Send Invoice Email]
    N --> O[Grant Full Access]

    K --> P[Show Pending Status]
    P --> Q[Wait for Confirmation]

    L --> R[Show Error Message]
    R --> S[Retry or Change Method]
```

### Payment Integration (Midtrans)

```typescript
interface PaymentRequest {
  institution_id: string;
  package_id: string;
  payment_method: 'credit_card' | 'bank_transfer' | 'e_wallet' | 'qris';
  billing_info: {
    name: string;
    email: string;
    phone: string;
    address?: string;
  };
}

interface PaymentResponse {
  order_id: string;
  payment_token: string;
  redirect_url: string;
  expires_at: Date;
}

// Midtrans Webhook Handler
interface MidtransWebhook {
  order_id: string;
  status_code: string;
  transaction_status: 'capture' | 'settlement' | 'pending' | 'deny' | 'cancel' | 'expire';
  fraud_status: 'accept' | 'challenge' | 'deny';
  payment_type: string;
  gross_amount: string;
}
```

### Subscription Management

```typescript
interface Subscription {
  id: string;
  institution_id: string;
  package_id: string;
  status: 'active' | 'expired' | 'cancelled' | 'suspended';

  // Dates
  started_at: Date;
  expires_at: Date;
  cancelled_at?: Date;

  // Limits
  problem_limit: number;
  problem_used: number;

  // Billing
  amount: number;
  currency: 'IDR';
  payment_method: string;
  last_payment_at: Date;

  // Auto-renewal
  auto_renew: boolean;
  next_billing_date?: Date;
}

// Usage Tracking
interface UsageMetrics {
  institution_id: string;
  period_start: Date;
  period_end: Date;

  problems_posted: number;
  applications_received: number;
  students_accepted: number;

  ai_document_verifications: number;
  ai_chat_interactions: number;
}
```

---

## 🏗️ Technical Architecture

### System Architecture

```
┌─────────────────────────────────────────────────────────┐
│                    Frontend (Vue.js)                     │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐    │
│  │  Student    │  │ Institution │  │    Admin    │    │
│  │  Dashboard  │  │  Dashboard  │  │   Panel     │    │
│  └─────────────┘  └─────────────┘  └─────────────┘    │
└─────────────────────────────────────────────────────────┘
                         │
                         ↓ REST API
┌─────────────────────────────────────────────────────────┐
│              Backend (Laravel 11)                        │
│  ┌──────────────────────────────────────────────────┐  │
│  │          Core Application Services                │  │
│  │  • Authentication (Sanctum)                       │  │
│  │  • Authorization (Policies)                       │  │
│  │  • Business Logic                                 │  │
│  │  • File Upload (S3)                              │  │
│  └──────────────────────────────────────────────────┘  │
│                         │                                │
│  ┌─────────────────────┴────────────────────────────┐  │
│  │          AI Service Layer                         │  │
│  │  ┌──────────────┐  ┌──────────────┐             │  │
│  │  │ Queue Worker │  │ Event Bus    │             │  │
│  │  │ (Jobs)       │  │ (Events)     │             │  │
│  │  └──────────────┘  └──────────────┘             │  │
│  └──────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────┘
                         │
        ┌────────────────┴────────────────┐
        ↓                                  ↓
┌──────────────────┐            ┌──────────────────┐
│  AI Microservice │            │   Payment API    │
│   (Python/FastAPI)│           │   (Midtrans)     │
├──────────────────┤            └──────────────────┘
│ • Document OCR   │
│ • Classification │            ┌──────────────────┐
│ • Matching ML    │            │   Storage        │
│ • Chatbot LLM    │            │   (AWS S3)       │
│ • Vector DB      │            └──────────────────┘
└──────────────────┘
        │
        ↓
┌──────────────────┐
│  Database        │
│  (PostgreSQL)    │
│                  │
│  • Users         │
│  • Institutions  │
│  • Problems      │
│  • Applications  │
│  • Projects      │
│  • Payments      │
│  • AI Logs       │
└──────────────────┘
```

### Tech Stack

**Frontend:**
- Vue.js 3 + Composition API
- Tailwind CSS
- Pinia (State Management)
- Axios

**Backend:**
- Laravel 11
- **Supabase (PostgreSQL)** - Primary database with pgvector extension
- Redis (Cache & Queue)
- Laravel Sanctum (Auth)
- Laravel Horizon (Queue Management)

**AI Service:**
- Python 3.11 + FastAPI
- **Anthropic Claude API** (Primary LLM - for chatbot, reasoning, text analysis)
  - Model: Claude 3.5 Sonnet / Claude 3 Haiku (for cost optimization)
- **Cohere API** (Trial - for embeddings & search)
  - Embed v3 model for vector embeddings
  - Rerank model for search optimization
- PyTorch / TensorFlow (for custom models)
- Transformers (Hugging Face) (for document OCR & classification)
- **Supabase pgvector** - Vector similarity search in PostgreSQL
- Celery (Task Queue)

**Infrastructure:**
- AWS EC2 / DigitalOcean
- AWS S3 (File Storage)
- CloudFlare (CDN)
- GitHub Actions (CI/CD)

---

## 🗄️ Database Schema

### Core Tables

```sql
-- Institutions
CREATE TABLE institutions (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT REFERENCES users(id),

    -- Basic Info
    name VARCHAR(255) NOT NULL,
    type VARCHAR(50) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(20),
    website VARCHAR(255),
    description TEXT,

    -- Location
    address TEXT,
    province_id BIGINT REFERENCES provinces(id),
    regency_id BIGINT REFERENCES regencies(id),

    -- PIC
    pic_name VARCHAR(255),
    pic_position VARCHAR(255),
    pic_phone VARCHAR(20),

    -- Verification
    verification_status VARCHAR(50) DEFAULT 'pending_verification',
    verification_score DECIMAL(5,2),
    verification_confidence DECIMAL(3,2),
    verified_at TIMESTAMP,
    verified_by BIGINT REFERENCES users(id),
    rejection_reason TEXT,

    -- Subscription
    subscription_status VARCHAR(50) DEFAULT 'pending_payment',
    subscription_package_id BIGINT REFERENCES subscription_packages(id),
    subscription_started_at TIMESTAMP,
    subscription_expires_at TIMESTAMP,

    -- Metadata
    logo_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Verification Documents
CREATE TABLE verification_documents (
    id BIGSERIAL PRIMARY KEY,
    institution_id BIGINT REFERENCES institutions(id),

    document_type VARCHAR(50) NOT NULL, -- 'official_letter', 'logo', 'pic_identity'
    file_url VARCHAR(255) NOT NULL,
    file_name VARCHAR(255),
    file_size BIGINT,
    mime_type VARCHAR(100),

    -- AI Analysis
    ai_verification_id VARCHAR(255),
    ai_status VARCHAR(50), -- 'approved', 'needs_review', 'rejected'
    ai_score DECIMAL(5,2),
    ai_confidence DECIMAL(3,2),
    ai_flags JSONB,
    ai_extracted_data JSONB,
    ai_reasoning TEXT,
    ai_processed_at TIMESTAMP,

    -- Human Review
    human_reviewed BOOLEAN DEFAULT FALSE,
    human_status VARCHAR(50),
    human_notes TEXT,
    reviewed_by BIGINT REFERENCES users(id),
    reviewed_at TIMESTAMP,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Students with AI Profile
CREATE TABLE students (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT REFERENCES users(id),

    -- Basic Info
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    nim VARCHAR(50) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    whatsapp_number VARCHAR(20),

    -- Academic
    university_id BIGINT REFERENCES universities(id),
    major VARCHAR(255),
    semester INTEGER,
    gpa DECIMAL(3,2),

    -- AI Profile
    questionnaire_completed BOOLEAN DEFAULT FALSE,
    questionnaire_data JSONB,
    interest_profile JSONB,
    skill_profile JSONB,
    ai_profile_vector VECTOR(1536), -- For similarity search

    -- Metadata
    profile_photo_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- AI Student Questionnaire Responses
CREATE TABLE student_questionnaire_responses (
    id BIGSERIAL PRIMARY KEY,
    student_id BIGINT REFERENCES students(id),

    -- Academic Background
    relevant_coursework JSONB,

    -- Skills & Expertise
    skills JSONB, -- [{ category, skills, proficiency }]
    portfolio_url VARCHAR(255),

    -- Interests
    problem_domains JSONB, -- ['education', 'health', 'infrastructure']
    preferred_locations JSONB,
    team_size_preference VARCHAR(50),
    duration_preference VARCHAR(50),
    work_mode_preference VARCHAR(50), -- 'remote', 'onsite', 'hybrid'

    -- Motivation
    primary_goal VARCHAR(255),
    career_aspirations JSONB,
    social_causes JSONB,
    learning_goals JSONB,

    -- AI Processing
    ai_processed BOOLEAN DEFAULT FALSE,
    ai_profile_generated_at TIMESTAMP,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Problems/Projects with AI Matching
CREATE TABLE problems (
    id BIGSERIAL PRIMARY KEY,
    institution_id BIGINT REFERENCES institutions(id),

    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(100), -- 'education', 'health', 'infrastructure', etc.
    location TEXT,

    -- Requirements
    required_skills JSONB,
    team_size_min INTEGER,
    team_size_max INTEGER,
    duration_months INTEGER,
    difficulty_level VARCHAR(50), -- 'beginner', 'intermediate', 'advanced'

    -- Timeline
    application_deadline DATE,
    project_start_date DATE,
    project_end_date DATE,

    -- AI Matching
    ai_embedding VECTOR(1536), -- For similarity search with student profiles
    ai_tags JSONB,

    -- Status
    status VARCHAR(50) DEFAULT 'open',
    views_count INTEGER DEFAULT 0,
    applications_count INTEGER DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- AI Match Scores (Precomputed for performance)
CREATE TABLE ai_match_scores (
    id BIGSERIAL PRIMARY KEY,
    student_id BIGINT REFERENCES students(id),
    problem_id BIGINT REFERENCES problems(id),

    overall_score DECIMAL(5,2) NOT NULL,

    -- Score Breakdown
    skill_match_score DECIMAL(5,2),
    interest_alignment_score DECIMAL(5,2),
    location_fit_score DECIMAL(5,2),
    difficulty_fit_score DECIMAL(5,2),
    team_fit_score DECIMAL(5,2),
    timeline_fit_score DECIMAL(5,2),

    -- Reasoning
    reasoning TEXT,
    recommendations JSONB,
    concerns JSONB,

    -- Metadata
    calculated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    UNIQUE(student_id, problem_id)
);

-- AI Chatbot Conversations
CREATE TABLE ai_chat_conversations (
    id BIGSERIAL PRIMARY KEY,
    student_id BIGINT REFERENCES students(id),
    problem_id BIGINT REFERENCES problems(id),

    conversation_context JSONB,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_message_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE ai_chat_messages (
    id BIGSERIAL PRIMARY KEY,
    conversation_id BIGINT REFERENCES ai_chat_conversations(id),

    role VARCHAR(20) NOT NULL, -- 'user' or 'assistant'
    content TEXT NOT NULL,

    -- Sources (for RAG)
    sources JSONB,
    confidence DECIMAL(3,2),

    -- Feedback
    helpful BOOLEAN,
    feedback_text TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Subscription Packages
CREATE TABLE subscription_packages (
    id BIGSERIAL PRIMARY KEY,

    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,

    duration_months INTEGER NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'IDR',

    -- Limits
    problem_limit INTEGER,
    applicant_limit INTEGER,
    ai_verification_limit INTEGER,
    ai_chat_limit INTEGER,

    -- Features
    features JSONB,

    -- Status
    is_active BOOLEAN DEFAULT TRUE,
    is_featured BOOLEAN DEFAULT FALSE,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Payments
CREATE TABLE payments (
    id BIGSERIAL PRIMARY KEY,
    institution_id BIGINT REFERENCES institutions(id),
    subscription_package_id BIGINT REFERENCES subscription_packages(id),

    -- Order Info
    order_id VARCHAR(255) NOT NULL UNIQUE,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'IDR',

    -- Payment Status
    status VARCHAR(50) NOT NULL, -- 'pending', 'success', 'failed', 'expired'
    payment_method VARCHAR(100),
    payment_token VARCHAR(255),

    -- Midtrans
    midtrans_transaction_id VARCHAR(255),
    midtrans_transaction_status VARCHAR(50),
    midtrans_fraud_status VARCHAR(50),

    -- Billing
    billing_name VARCHAR(255),
    billing_email VARCHAR(255),
    billing_phone VARCHAR(20),

    -- Timestamps
    paid_at TIMESTAMP,
    expires_at TIMESTAMP,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- AI Audit Logs
CREATE TABLE ai_audit_logs (
    id BIGSERIAL PRIMARY KEY,

    service VARCHAR(100) NOT NULL, -- 'document_verification', 'matching', 'chatbot'
    action VARCHAR(100) NOT NULL,

    user_id BIGINT REFERENCES users(id),
    institution_id BIGINT REFERENCES institutions(id),
    student_id BIGINT REFERENCES students(id),

    -- Input/Output
    input_data JSONB,
    output_data JSONB,

    -- Metrics
    processing_time_ms INTEGER,
    model_version VARCHAR(50),
    confidence_score DECIMAL(3,2),

    -- Status
    status VARCHAR(50),
    error_message TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Indexes for Performance

```sql
-- For AI matching queries (pgvector in Supabase)
CREATE INDEX idx_students_ai_profile ON students USING ivfflat (ai_profile_vector vector_cosine_ops) WITH (lists = 100);
CREATE INDEX idx_problems_ai_embedding ON problems USING ivfflat (ai_embedding vector_cosine_ops) WITH (lists = 100);

-- For filtering and sorting
CREATE INDEX idx_problems_category ON problems(category);
CREATE INDEX idx_problems_status ON problems(status);
CREATE INDEX idx_match_scores_student ON ai_match_scores(student_id, overall_score DESC);
CREATE INDEX idx_match_scores_problem ON ai_match_scores(problem_id, overall_score DESC);

-- For verification tracking
CREATE INDEX idx_institutions_verification ON institutions(verification_status);
CREATE INDEX idx_verification_docs_status ON verification_documents(institution_id, ai_status);

-- For payment tracking
CREATE INDEX idx_payments_status ON payments(status, created_at);
CREATE INDEX idx_payments_institution ON payments(institution_id, created_at);
```

---

## 🔌 API Endpoints

### Authentication

```
POST   /api/auth/register/student
POST   /api/auth/register/institution
POST   /api/auth/login
POST   /api/auth/logout
POST   /api/auth/refresh
POST   /api/auth/forgot-password
POST   /api/auth/reset-password
GET    /api/auth/me
```

### Institution Endpoints

```
# Registration & Verification
POST   /api/institutions                          # Create institution
GET    /api/institutions/{id}                     # Get institution details
PUT    /api/institutions/{id}                     # Update institution
POST   /api/institutions/{id}/documents           # Upload verification documents
GET    /api/institutions/{id}/verification-status # Check verification status

# Subscription & Payment
GET    /api/subscription-packages                 # List packages
POST   /api/institutions/{id}/subscribe           # Start subscription
POST   /api/institutions/{id}/payment             # Create payment
GET    /api/institutions/{id}/subscription        # Get subscription details
POST   /api/institutions/{id}/cancel-subscription # Cancel subscription

# Problem Management
GET    /api/institutions/{id}/problems            # List problems
POST   /api/institutions/{id}/problems            # Create problem
PUT    /api/problems/{id}                         # Update problem
DELETE /api/problems/{id}                         # Delete problem
GET    /api/problems/{id}/applications            # List applications

# Analytics
GET    /api/institutions/{id}/analytics           # Get institution analytics
```

### Student Endpoints

```
# Profile
GET    /api/students/me                           # Get student profile
PUT    /api/students/me                           # Update profile
POST   /api/students/me/photo                     # Upload photo

# AI Questionnaire
GET    /api/students/me/questionnaire             # Get questionnaire
POST   /api/students/me/questionnaire             # Submit questionnaire
GET    /api/students/me/profile-analysis          # Get AI profile analysis

# Problem Discovery
GET    /api/problems                              # List problems (with AI scoring)
GET    /api/problems/{id}                         # Get problem details
GET    /api/problems/recommended                  # AI recommended problems
POST   /api/problems/{id}/save                    # Save problem (bookmark)

# Applications
POST   /api/problems/{id}/apply                   # Apply to problem
GET    /api/students/me/applications              # List my applications
PUT    /api/applications/{id}                     # Update application
DELETE /api/applications/{id}                     # Withdraw application

# Projects
GET    /api/students/me/projects                  # List my projects
GET    /api/projects/{id}                         # Get project details
POST   /api/projects/{id}/milestones/{mid}/complete # Complete milestone
POST   /api/projects/{id}/reports                 # Submit report
```

### AI Endpoints

```
# Document Verification
POST   /api/ai/verify-documents                   # Verify institution documents
GET    /api/ai/verification/{id}                  # Get verification results

# Student Matching
POST   /api/ai/match-problems                     # Get matched problems for student
GET    /api/ai/match-score/{student_id}/{problem_id} # Get specific match score

# Chatbot
POST   /api/ai/chat                               # Send chat message
GET    /api/ai/conversations                      # List conversations
GET    /api/ai/conversations/{id}                 # Get conversation history
DELETE /api/ai/conversations/{id}                 # Delete conversation

# Suggestions
GET    /api/ai/suggest-projects                   # Get AI project suggestions
GET    /api/ai/career-insights                    # Get career insights

# Feedback
POST   /api/ai/feedback                           # Submit AI feedback
```

### Admin Endpoints

```
# Verification Management
GET    /api/admin/verifications/pending           # List pending verifications
PUT    /api/admin/verifications/{id}/approve      # Approve institution
PUT    /api/admin/verifications/{id}/reject       # Reject institution

# AI Management
GET    /api/admin/ai/metrics                      # AI performance metrics
POST   /api/admin/ai/retrain                      # Trigger model retraining
GET    /api/admin/ai/audit-logs                   # AI audit logs

# Analytics
GET    /api/admin/analytics/dashboard             # Admin dashboard metrics
GET    /api/admin/analytics/revenue               # Revenue analytics
```

---

## 🔒 Security & Privacy

### Data Protection

**1. Document Security**
- Encrypted storage (AWS S3 with encryption at rest)
- Signed URLs with expiration
- Access control via IAM policies
- Audit logging for all document access

**2. AI Model Security**
- No storage of raw documents in AI service
- Process documents in memory only
- Secure API communication (TLS 1.3)
- Rate limiting on AI endpoints

**3. Personal Data**
```typescript
// GDPR Compliant Data Handling
interface DataRetentionPolicy {
  student_questionnaire: '2 years after graduation',
  chat_history: '1 year',
  verification_documents: '5 years (legal requirement)',
  ai_logs: '90 days',
  payment_records: '7 years (tax requirement)'
}

// Data Anonymization
function anonymizeStudentData(student: Student): AnonymizedStudent {
  return {
    id: hashId(student.id),
    skills: student.skill_profile,
    interests: student.interest_profile,
    // Remove PII
    name: '[REDACTED]',
    email: '[REDACTED]',
    phone: '[REDACTED]'
  };
}
```

### AI Ethics & Bias Prevention

**1. Bias Mitigation**
- Regular audit of matching algorithm
- Diverse training data
- Fairness metrics monitoring
- Human-in-the-loop for edge cases

**2. Transparency**
```typescript
// Always explain AI decisions
interface AIDecisionExplanation {
  decision: string;
  reasoning: string;
  confidence: number;
  factors: {
    factor: string;
    weight: number;
    contribution: number;
  }[];
  human_review_recommended: boolean;
}
```

**3. User Control**
- Opt-out from AI matching
- Request human review
- Data export (GDPR right)
- Account deletion

### Rate Limiting

```typescript
// Rate limits per endpoint
const rateLimits = {
  'api/ai/chat': '60 requests/minute',
  'api/ai/match-problems': '10 requests/minute',
  'api/ai/verify-documents': '5 requests/hour',
  'api/problems': '100 requests/minute'
};
```

---

## 🛡️ Comprehensive Security Enhancements

### Security Architecture Overview

Platform KKN-GO menangani data sensitif mahasiswa dan institusi, termasuk NIK, NPWP, dokumen identitas, dan informasi finansial. Implementasi keamanan menggunakan **Defense in Depth** strategy dengan multiple layers of protection.

### Security Threat Model

| Threat | Risk Level | Mitigation |
|--------|-----------|------------|
| **Data Breach** | CRITICAL | Encryption at rest, access control, audit logging |
| **SQL Injection** | HIGH | Parameterized queries, ORM, input validation |
| **XSS (Cross-Site Scripting)** | HIGH | Input sanitization, output escaping, CSP headers |
| **CSRF** | HIGH | CSRF tokens, SameSite cookies |
| **File Upload Attack** | HIGH | Magic byte validation, antivirus scanning, size limits |
| **Brute Force** | MEDIUM | Rate limiting, account lockout, 2FA |
| **Session Hijacking** | MEDIUM | Secure cookies, session regeneration, HTTPS only |
| **API Abuse** | MEDIUM | Rate limiting, authentication, API keys |
| **Social Engineering** | MEDIUM | User education, verification process |
| **DDoS** | LOW | CloudFlare, rate limiting, load balancing |

---

### Enhancement 1: Data Encryption at Rest & In Transit

#### 🎯 Objective
Protect sensitive PII (Personally Identifiable Information) from database breaches and unauthorized access.

#### 📋 Implementation

**1.1. Field-Level Encryption (Laravel Native)**

```php
// app/Models/Student.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'nim',
        'phone',
        'address',
        'university_id',
        'major',
        'semester',
    ];

    /**
     * Laravel automatically encrypts/decrypts these fields
     * Uses AES-256-CBC encryption with APP_KEY
     */
    protected $casts = [
        'nim' => 'encrypted',              // Student ID Number
        'phone' => 'encrypted',            // Phone number
        'address' => 'encrypted',          // Home address
        'questionnaire_data' => 'encrypted:array',
    ];

    /**
     * Attributes that should be hashed for search
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($student) {
            // Create hash for searchable encrypted fields
            if ($student->isDirty('nim')) {
                $student->nim_hash = hash_hmac('sha256', $student->nim, config('app.key'));
            }
            if ($student->isDirty('phone')) {
                $student->phone_hash = hash_hmac('sha256', $student->phone, config('app.key'));
            }
        });
    }
}

// app/Models/Institution.php
class Institution extends Model
{
    protected $casts = [
        'phone' => 'encrypted',
        'address' => 'encrypted',
        'pic_name' => 'encrypted',
        'pic_position' => 'encrypted',
        'description' => 'encrypted',
    ];
}
```

**1.2. Database Migration for Encrypted Fields**

```php
// database/migrations/2025_01_15_add_encrypted_field_support.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Change to LONGTEXT to accommodate encrypted data
            $table->longText('nim')->change();
            $table->longText('phone')->change();
            $table->longText('address')->nullable()->change();

            // Add hash columns for searching encrypted data
            $table->string('nim_hash', 64)->nullable()->after('nim');
            $table->string('phone_hash', 64)->nullable()->after('phone');

            // Indexes for fast search
            $table->index('nim_hash');
            $table->index('phone_hash');
        });

        Schema::table('institutions', function (Blueprint $table) {
            $table->longText('phone')->change();
            $table->longText('address')->change();
            $table->longText('pic_name')->change();
            $table->longText('pic_position')->change();

            $table->string('phone_hash', 64)->nullable()->after('phone');
            $table->index('phone_hash');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['nim_hash', 'phone_hash']);
            $table->string('nim', 50)->change();
            $table->string('phone', 20)->change();
            $table->text('address')->nullable()->change();
        });

        Schema::table('institutions', function (Blueprint $table) {
            $table->dropColumn('phone_hash');
            $table->string('phone', 20)->change();
            $table->text('address')->change();
            $table->string('pic_name', 100)->change();
            $table->string('pic_position', 100)->change();
        });
    }
};
```

**1.3. Encryption Service for Advanced Use Cases**

```php
// app/Services/EncryptionService.php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Log;

class EncryptionService
{
    /**
     * Encrypt sensitive document metadata
     */
    public function encryptDocumentMetadata(array $metadata): string
    {
        return Crypt::encryptString(json_encode($metadata));
    }

    /**
     * Decrypt document metadata
     */
    public function decryptDocumentMetadata(string $encrypted): array
    {
        try {
            return json_decode(Crypt::decryptString($encrypted), true);
        } catch (DecryptException $e) {
            Log::error('Metadata decryption failed', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Create searchable hash (HMAC-SHA256)
     */
    public function createSearchHash(string $value): string
    {
        return hash_hmac('sha256', $value, config('app.key'));
    }

    /**
     * Search encrypted field by hash
     */
    public function searchEncryptedField(string $model, string $field, string $value)
    {
        $hash = $this->createSearchHash($value);
        $hashField = $field . '_hash';

        return $model::where($hashField, $hash)->first();
    }

    /**
     * Mask sensitive data for logs (PII protection)
     */
    public function maskForLogging(string $value, int $visibleChars = 4): string
    {
        if (strlen($value) <= $visibleChars) {
            return str_repeat('*', strlen($value));
        }

        $visible = substr($value, 0, $visibleChars);
        $masked = str_repeat('*', strlen($value) - $visibleChars);

        return $visible . $masked;
    }
}
```

**1.4. Usage Examples**

```php
// Creating a student with encrypted data
$student = Student::create([
    'first_name' => 'John',
    'last_name' => 'Doe',
    'nim' => '1234567890',        // Automatically encrypted
    'phone' => '+628123456789',   // Automatically encrypted
    'address' => 'Jl. Example 123', // Automatically encrypted
]);

// Retrieving (automatically decrypted)
$student = Student::find(1);
echo $student->nim; // Output: "1234567890" (decrypted)

// Searching by encrypted field (using hash)
$encryptionService = app(EncryptionService::class);
$phoneHash = $encryptionService->createSearchHash('+628123456789');
$student = Student::where('phone_hash', $phoneHash)->first();

// Logging with masked data
Log::info('Student registration', [
    'nim' => $encryptionService->maskForLogging($student->nim, 4),
    'phone' => $encryptionService->maskForLogging($student->phone, 4),
]);
// Output: "nim": "1234******", "phone": "+628********"
```

**1.5. Environment Configuration**

```bash
# .env - CRITICAL SECURITY SETTINGS

# Application Key (MUST be 32 bytes for AES-256)
# NEVER commit this to Git
# NEVER change in production (encrypted data will be unrecoverable)
APP_KEY=base64:RANDOM_32_BYTE_KEY_HERE

# Generate secure key
php artisan key:generate

# IMPORTANT: Backup APP_KEY securely
# - Store in password manager
# - Document in secure location
# - Keep offline backup

# TLS/SSL (ALWAYS use in production)
APP_URL=https://yourdomain.com
FORCE_HTTPS=true
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=strict
```

**1.6. Key Rotation Strategy**

```php
// app/Console/Commands/RotateEncryptionKey.php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Models\Student;
use App\Models\Institution;

class RotateEncryptionKey extends Command
{
    protected $signature = 'security:rotate-key {--old-key=}';
    protected $description = 'Rotate encryption key and re-encrypt all data';

    public function handle()
    {
        $oldKey = $this->option('old-key');

        if (!$oldKey) {
            $this->error('Old key is required. Use --old-key=');
            return 1;
        }

        $this->info('Starting encryption key rotation...');
        $this->warn('This operation may take a while for large datasets.');

        // Decrypt with old key and re-encrypt with new key
        DB::transaction(function () use ($oldKey) {
            $students = Student::all();

            foreach ($students as $student) {
                // Decrypt with old key
                config(['app.key' => $oldKey]);
                $nim = $student->nim;
                $phone = $student->phone;

                // Re-encrypt with new key
                config(['app.key' => env('APP_KEY')]);
                $student->nim = $nim;
                $student->phone = $phone;
                $student->save();
            }

            $this->info("Rotated keys for {$students->count()} students");
        });

        $this->info('Key rotation completed successfully!');
        return 0;
    }
}
```

---

### Enhancement 2: Input Sanitization & XSS Prevention

#### 🎯 Objective
Prevent XSS attacks by sanitizing all user input and escaping output properly.

#### 📋 Implementation

**2.1. Global Sanitization Middleware**

```php
// app/Http/Middleware/SanitizeInput.php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SanitizeInput
{
    /**
     * Fields yang tidak boleh di-sanitasi
     */
    protected $except = [
        'password',
        'password_confirmation',
        'current_password',
        '_token',
        'api_token',
    ];

    /**
     * Fields yang allow limited HTML (untuk rich text editors)
     */
    protected $richTextFields = [
        'description',
        'problem_description',
        'project_description',
        'solution_description',
    ];

    /**
     * Allowed HTML tags untuk rich text
     */
    protected $allowedTags = '<p><br><strong><em><u><ul><ol><li><a>';

    /**
     * Handle incoming request
     */
    public function handle(Request $request, Closure $next)
    {
        $input = $request->except($this->except);
        $sanitized = $this->sanitizeArray($input);
        $request->merge($sanitized);

        return $next($request);
    }

    /**
     * Recursively sanitize array
     */
    protected function sanitizeArray(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->sanitizeArray($value);
            } elseif (is_string($value)) {
                $data[$key] = $this->sanitizeString($key, $value);
            }
        }

        return $data;
    }

    /**
     * Sanitize individual string
     */
    protected function sanitizeString(string $key, string $value): string
    {
        // Allow limited HTML for rich text fields
        if (in_array($key, $this->richTextFields)) {
            // Strip dangerous tags but keep formatting
            $value = strip_tags($value, $this->allowedTags);

            // Remove event handlers (onclick, onload, etc.)
            $value = preg_replace('/\s*on\w+\s*=\s*["\'].*?["\']/i', '', $value);

            // Remove javascript: protocol
            $value = preg_replace('/javascript:/i', '', $value);

            return $value;
        }

        // For other fields, strip all HTML
        $value = strip_tags($value);

        // Remove null bytes
        $value = str_replace(chr(0), '', $value);

        // Remove control characters except newlines and tabs
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);

        // Normalize whitespace
        $value = preg_replace('/\s+/', ' ', $value);

        // Trim
        return trim($value);
    }
}
```

**2.2. Register Middleware**

```php
// bootstrap/app.php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\SanitizeInput;
use App\Http\Middleware\SecurityHeaders;

return Application::configure(basePath: dirname(__DIR__))
    ->withMiddleware(function (Middleware $middleware): void {
        // Global middleware
        $middleware->append([
            SecurityHeaders::class,
        ]);

        // Web middleware group
        $middleware->web(append: [
            SanitizeInput::class,
        ]);

        // API middleware group
        $middleware->api(append: [
            SanitizeInput::class,
        ]);
    })
    ->create();
```

**2.3. Request-Level Sanitization**

```php
// app/Http/Requests/InstitutionRegisterRequest.php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InstitutionRegisterRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'institution_name' => $this->sanitizeName($this->institution_name ?? ''),
            'pic_name' => $this->sanitizeName($this->pic_name ?? ''),
            'official_email' => strtolower(strip_tags($this->official_email ?? '')),
            'username' => strtolower(strip_tags($this->username ?? '')),
            'description' => $this->sanitizeHtml($this->description ?? ''),
            'website' => $this->sanitizeUrl($this->website ?? ''),
        ]);
    }

    /**
     * Sanitize name (only letters, spaces, dots, hyphens)
     */
    private function sanitizeName(string $name): string
    {
        $name = strip_tags($name);
        $name = preg_replace('/[^a-zA-Z\s.\'-]/u', '', $name);
        $name = preg_replace('/\s+/', ' ', $name);
        return ucwords(strtolower(trim($name)));
    }

    /**
     * Sanitize HTML content
     */
    private function sanitizeHtml(string $html): string
    {
        // Remove script tags
        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);

        // Remove event handlers
        $html = preg_replace('/\s*on\w+\s*=\s*["\'].*?["\']/i', '', $html);

        // Remove dangerous protocols
        $html = preg_replace('/(javascript|data|vbscript):/i', '', $html);

        // Allow only safe tags
        $html = strip_tags($html, '<p><br><strong><em><u><ul><ol><li>');

        return trim($html);
    }

    /**
     * Sanitize URL
     */
    private function sanitizeUrl(string $url): string
    {
        $url = strip_tags($url);
        $url = filter_var($url, FILTER_SANITIZE_URL);

        // Only allow http/https protocols
        if (!preg_match('/^https?:\/\//i', $url)) {
            return '';
        }

        return $url;
    }
}
```

**2.4. Output Escaping in Blade Templates**

```blade
{{-- resources/views/institution/show.blade.php --}}

{{-- ✅ SAFE: Automatically escaped --}}
<h1>{{ $institution->name }}</h1>
<p>{{ $institution->description }}</p>

{{-- ✅ SAFE: Escaped email --}}
<a href="mailto:{{ $institution->email }}">Contact</a>

{{-- ⚠️ DANGEROUS: Unescaped HTML --}}
{{-- Only use if content is from trusted source --}}
<div>{!! $institution->description !!}</div>

{{-- ✅ BEST PRACTICE: Use HTML Purifier --}}
<div>{{ Purifier::clean($institution->description) }}</div>

{{-- ✅ SAFE: Escaped in attributes --}}
<div data-name="{{ $institution->name }}"
     data-id="{{ $institution->id }}">
</div>

{{-- ✅ SAFE: JSON data for JavaScript --}}
<script>
    const institution = @json($institution);
    // Laravel automatically escapes and encodes
</script>
```

**2.5. Install and Configure HTML Purifier**

```bash
composer require mews/purifier
php artisan vendor:publish --provider="Mews\Purifier\PurifierServiceProvider"
```

```php
// config/purifier.php
<?php

return [
    'encoding'      => 'UTF-8',
    'finalize'      => true,
    'cachePath'     => storage_path('app/purifier'),
    'cacheFileMode' => 0755,
    'settings'      => [
        'default' => [
            'HTML.Doctype'             => 'HTML 4.01 Transitional',
            'HTML.Allowed'             => 'p,br,strong,em,u,a[href|title],ul,ol,li',
            'CSS.AllowedProperties'    => '',
            'AutoFormat.AutoParagraph' => false,
            'AutoFormat.RemoveEmpty'   => true,
            'Attr.AllowedFrameTargets' => ['_blank'],
        ],
        'strict' => [
            'HTML.Allowed' => 'p,br,strong,em',
        ],
    ],
];
```

```php
// Usage in controllers
use Mews\Purifier\Facades\Purifier;

$clean = Purifier::clean($dirtyHtml);
$clean = Purifier::clean($dirtyHtml, 'strict');
```

**2.6. XSS Testing**

```php
// tests/Feature/XSSPreventionTest.php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Institution;
use Illuminate\Foundation\Testing\RefreshDatabase;

class XSSPreventionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test XSS prevention in institution name
     */
    public function test_xss_script_tags_are_stripped_from_name()
    {
        $user = User::factory()->create(['user_type' => 'institution']);

        $xssPayloads = [
            '<script>alert("XSS")</script>Desa Test',
            '<img src=x onerror=alert("XSS")>',
            '<svg/onload=alert("XSS")>',
            'javascript:alert("XSS")',
            '<iframe src="javascript:alert(\'XSS\')">',
        ];

        foreach ($xssPayloads as $payload) {
            $response = $this->actingAs($user)->postJson('/api/institutions', [
                'institution_name' => $payload,
                'institution_type' => 'pemerintah_desa',
                'official_email' => 'test@example.com',
                'username' => 'testuser',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
                // ... other required fields
            ]);

            $institution = Institution::latest()->first();

            $this->assertStringNotContainsString('<script>', $institution->name);
            $this->assertStringNotContainsString('javascript:', $institution->name);
            $this->assertStringNotContainsString('onerror=', $institution->name);
            $this->assertStringNotContainsString('<iframe', $institution->name);
        }
    }

    /**
     * Test that safe HTML is preserved in description
     */
    public function test_safe_html_is_preserved_in_description()
    {
        $user = User::factory()->create(['user_type' => 'institution']);

        $safeHtml = '<p>This is <strong>safe</strong> content</p>';

        $response = $this->actingAs($user)->postJson('/api/institutions', [
            'description' => $safeHtml . '<script>alert("XSS")</script>',
            // ... other fields
        ]);

        $institution = Institution::latest()->first();

        // Safe HTML preserved
        $this->assertStringContainsString('<p>', $institution->description);
        $this->assertStringContainsString('<strong>', $institution->description);

        // Malicious script removed
        $this->assertStringNotContainsString('<script>', $institution->description);
    }
}
```

---

### Enhancement 3: Advanced File Upload Security

#### 🎯 Objective
Prevent malicious file uploads including executable files, viruses, and files with embedded code.

#### 📋 Implementation

**3.1. Enhanced File Validation Request**

```php
// app/Http/Requests/DocumentUploadRequest.php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;
use App\Services\FileSecurityService;

class DocumentUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->user_type === 'institution';
    }

    public function rules(): array
    {
        return [
            'official_letter' => [
                'required',
                'file',
                File::types(['pdf'])
                    ->max(5 * 1024) // 5MB
                    ->rules([
                        'mimes:pdf',
                        'mimetypes:application/pdf',
                        function ($attribute, $value, $fail) {
                            $this->validatePDFSecurity($value, $fail);
                        },
                    ]),
            ],
            'logo' => [
                'required',
                'file',
                File::image()
                    ->max(2 * 1024) // 2MB
                    ->dimensions([
                        'min_width' => 100,
                        'min_height' => 100,
                        'max_width' => 4000,
                        'max_height' => 4000,
                    ])
                    ->rules([
                        function ($attribute, $value, $fail) {
                            $this->validateImageSecurity($value, $fail);
                        },
                    ]),
            ],
            'pic_identity' => [
                'required',
                'file',
                File::image()
                    ->max(2 * 1024)
                    ->rules([
                        function ($attribute, $value, $fail) {
                            $this->validateImageSecurity($value, $fail);
                        },
                    ]),
            ],
            'npwp' => [
                'nullable',
                'file',
                File::types(['pdf', 'jpg', 'jpeg', 'png'])
                    ->max(3 * 1024),
            ],
        ];
    }

    /**
     * Validate PDF security (magic bytes, no JavaScript)
     */
    protected function validatePDFSecurity($file, $fail): void
    {
        // Check magic bytes
        $handle = fopen($file->getRealPath(), 'rb');
        $header = fread($handle, 4);
        fclose($handle);

        if ($header !== '%PDF') {
            $fail('Invalid PDF file format. File may be corrupted or malicious.');
            return;
        }

        // Check for embedded JavaScript (XSS vector)
        $content = file_get_contents($file->getRealPath(), false, null, 0, 100000);

        $dangerousPatterns = [
            '/\/JavaScript/i',
            '/\/JS\s/i',
            '/\/OpenAction/i',
            '/\/Launch/i',
            '/\/EmbeddedFile/i',
        ];

        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                $fail('PDF contains potentially malicious content (JavaScript or embedded files).');
                return;
            }
        }

        // Check file size consistency
        if (filesize($file->getRealPath()) !== $file->getSize()) {
            $fail('File size mismatch. File may be corrupted.');
            return;
        }
    }

    /**
     * Validate image security (magic bytes, no PHP code)
     */
    protected function validateImageSecurity($file, $fail): void
    {
        // Check magic bytes
        $handle = fopen($file->getRealPath(), 'rb');
        $header = fread($handle, 8);
        fclose($handle);

        $validHeaders = [
            'jpeg' => "\xFF\xD8\xFF",
            'png'  => "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A",
            'gif'  => "GIF89a",
        ];

        $isValid = false;
        foreach ($validHeaders as $type => $validHeader) {
            if (strpos($header, $validHeader) === 0) {
                $isValid = true;
                break;
            }
        }

        if (!$isValid) {
            $fail('Invalid image file format.');
            return;
        }

        // Verify with getimagesize (more thorough check)
        $imageInfo = @getimagesize($file->getRealPath());
        if ($imageInfo === false) {
            $fail('File is not a valid image or is corrupted.');
            return;
        }

        // Check for PHP code embedded in image
        $content = file_get_contents($file->getRealPath());

        $dangerousPatterns = [
            '/<\?php/i',
            '/<\?=/i',
            '/<script/i',
            '/<?xml/i',
        ];

        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                $fail('Image contains malicious code.');
                return;
            }
        }

        // Validate image dimensions match EXIF data
        if ($imageInfo[0] <= 0 || $imageInfo[1] <= 0) {
            $fail('Invalid image dimensions.');
            return;
        }
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            'official_letter.required' => 'Surat pengantar resmi wajib diunggah',
            'official_letter.mimes' => 'Surat pengantar harus berformat PDF',
            'official_letter.max' => 'Ukuran file surat pengantar maksimal 5MB',
            'logo.required' => 'Logo institusi wajib diunggah',
            'logo.image' => 'Logo harus berupa file gambar',
            'logo.max' => 'Ukuran logo maksimal 2MB',
            'pic_identity.required' => 'KTP penanggung jawab wajib diunggah',
            'npwp.max' => 'Ukuran file NPWP maksimal 3MB',
        ];
    }
}
```

**3.2. File Security Service**

```php
// app/Services/FileSecurityService.php
<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FileSecurityService
{
    /**
     * Scan file for malware
     */
    public function scanFile(UploadedFile $file): array
    {
        // Skip in development
        if (!app()->isProduction()) {
            Log::info('File scan skipped (development mode)', [
                'filename' => $file->getClientOriginalName(),
            ]);

            return [
                'safe' => true,
                'scanner' => 'skipped',
                'reason' => 'Development environment',
            ];
        }

        // Try ClamAV first (if available)
        if ($this->isClamAVAvailable()) {
            return $this->scanWithClamAV($file);
        }

        // Fallback to VirusTotal API
        if (config('services.virustotal.api_key')) {
            return $this->scanWithVirusTotal($file);
        }

        // No scanner available
        Log::warning('No antivirus scanner configured', [
            'filename' => $file->getClientOriginalName(),
        ]);

        return [
            'safe' => true,
            'scanner' => 'none',
            'warning' => 'No antivirus scanner configured',
        ];
    }

    /**
     * Check if ClamAV is installed and running
     */
    protected function isClamAVAvailable(): bool
    {
        try {
            $output = shell_exec('which clamscan 2>&1');
            return !empty($output);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Scan with ClamAV
     */
    protected function scanWithClamAV(UploadedFile $file): array
    {
        $filePath = escapeshellarg($file->getRealPath());
        $output = shell_exec("clamscan --no-summary {$filePath} 2>&1");

        $isSafe = strpos($output, 'OK') !== false;

        Log::info('ClamAV scan completed', [
            'filename' => $file->getClientOriginalName(),
            'safe' => $isSafe,
            'output' => $output,
        ]);

        return [
            'safe' => $isSafe,
            'scanner' => 'clamav',
            'details' => $output,
        ];
    }

    /**
     * Scan with VirusTotal API
     */
    protected function scanWithVirusTotal(UploadedFile $file): array
    {
        try {
            $apiKey = config('services.virustotal.api_key');

            // Upload file
            $response = Http::withHeaders([
                'x-apikey' => $apiKey,
            ])->attach(
                'file',
                file_get_contents($file->getRealPath()),
                $file->getClientOriginalName()
            )->post('https://www.virustotal.com/api/v3/files');

            if (!$response->successful()) {
                Log::error('VirusTotal upload failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [
                    'safe' => true,
                    'scanner' => 'virustotal',
                    'error' => 'API request failed',
                ];
            }

            $data = $response->json();
            $analysisId = $data['data']['id'];

            // Wait for analysis (simplified - use queue in production)
            sleep(15);

            // Get result
            $resultResponse = Http::withHeaders([
                'x-apikey' => $apiKey,
            ])->get("https://www.virustotal.com/api/v3/analyses/{$analysisId}");

            $result = $resultResponse->json();
            $stats = $result['data']['attributes']['stats'] ?? [];

            $isSafe = ($stats['malicious'] ?? 0) === 0 &&
                      ($stats['suspicious'] ?? 0) === 0;

            Log::info('VirusTotal scan completed', [
                'filename' => $file->getClientOriginalName(),
                'safe' => $isSafe,
                'stats' => $stats,
            ]);

            return [
                'safe' => $isSafe,
                'scanner' => 'virustotal',
                'stats' => $stats,
            ];

        } catch (\Exception $e) {
            Log::error('VirusTotal scan error', [
                'error' => $e->getMessage(),
                'filename' => $file->getClientOriginalName(),
            ]);

            return [
                'safe' => true,
                'scanner' => 'virustotal',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Sanitize filename (prevent directory traversal)
     */
    public function sanitizeFilename(string $filename): string
    {
        // Remove directory separators
        $filename = str_replace(['/', '\\', '..', "\0"], '', $filename);

        // Remove dangerous extensions
        $filename = preg_replace('/\.(php|phtml|php3|php4|php5|phar|exe|sh|bat|cmd)$/i', '.txt', $filename);

        // Keep only safe characters
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);

        // Limit length
        if (strlen($filename) > 200) {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $filename = substr($filename, 0, 190) . '.' . $extension;
        }

        return $filename;
    }

    /**
     * Generate secure random filename
     */
    public function generateSecureFilename(UploadedFile $file, string $prefix = ''): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $timestamp = now()->format('YmdHis');
        $random = Str::random(16);

        $filename = $prefix ? "{$prefix}_{$timestamp}_{$random}" : "{$timestamp}_{$random}";

        return "{$filename}.{$extension}";
    }

    /**
     * Validate file extension against mime type
     */
    public function validateExtension(UploadedFile $file): bool
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $mimeType = $file->getMimeType();

        $validCombinations = [
            'pdf' => ['application/pdf', 'application/x-pdf'],
            'jpg' => ['image/jpeg', 'image/pjpeg'],
            'jpeg' => ['image/jpeg', 'image/pjpeg'],
            'png' => ['image/png', 'image/x-png'],
            'gif' => ['image/gif'],
        ];

        if (!isset($validCombinations[$extension])) {
            return false;
        }

        return in_array($mimeType, $validCombinations[$extension]);
    }
}
```

**3.3. Usage in Controller**

```php
// app/Http/Controllers/API/DocumentVerificationController.php
<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentUploadRequest;
use App\Services\FileSecurityService;
use App\Services\SupabaseStorageService;
use App\Models\VerificationDocument;
use Illuminate\Http\JsonResponse;

class DocumentVerificationController extends Controller
{
    public function __construct(
        protected FileSecurityService $fileSecurityService,
        protected SupabaseStorageService $storageService
    ) {}

    /**
     * Upload verification documents
     */
    public function uploadDocuments(DocumentUploadRequest $request, int $institutionId): JsonResponse
    {
        $institution = $request->user()->institution;

        if (!$institution || $institution->id !== $institutionId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $uploadedDocuments = [];

        // Process each file
        $files = $request->allFiles();
        foreach ($files as $fieldName => $file) {
            // Scan for malware
            $scanResult = $this->fileSecurityService->scanFile($file);

            if (!$scanResult['safe']) {
                return response()->json([
                    'success' => false,
                    'message' => 'File contains malicious content and cannot be uploaded.',
                    'field' => $fieldName,
                    'scanner' => $scanResult['scanner'],
                    'details' => $scanResult['details'] ?? 'Malware detected',
                ], 422);
            }

            // Validate extension matches mime type
            if (!$this->fileSecurityService->validateExtension($file)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File extension does not match file type.',
                    'field' => $fieldName,
                ], 422);
            }

            // Generate secure filename
            $secureFilename = $this->fileSecurityService->generateSecureFilename($file, $fieldName);

            // Upload to Supabase
            $path = $this->storageService->uploadDocument(
                $file,
                $secureFilename,
                "institutions/{$institutionId}/verification"
            );

            // Save to database
            $document = VerificationDocument::create([
                'institution_id' => $institutionId,
                'document_type' => $this->getDocumentType($fieldName),
                'file_url' => $path,
                'file_name' => $secureFilename,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]);

            $uploadedDocuments[] = [
                'id' => $document->id,
                'type' => $document->document_type,
                'filename' => $document->file_name,
                'size' => $document->file_size,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Documents uploaded successfully',
            'data' => [
                'documents' => $uploadedDocuments,
                'institution_id' => $institutionId,
            ],
        ], 201);
    }

    /**
     * Map field name to document type
     */
    protected function getDocumentType(string $fieldName): string
    {
        return match($fieldName) {
            'official_letter' => VerificationDocument::TYPE_OFFICIAL_LETTER,
            'logo' => VerificationDocument::TYPE_LOGO,
            'pic_identity' => VerificationDocument::TYPE_PIC_IDENTITY,
            'npwp' => VerificationDocument::TYPE_NPWP,
            default => 'other',
        };
    }
}
```

**3.4. Configuration**

```bash
# .env
VIRUSTOTAL_API_KEY=your_virustotal_api_key_here

# Install ClamAV (Ubuntu/Debian)
sudo apt-get update
sudo apt-get install clamav clamav-daemon

# Start ClamAV daemon
sudo systemctl enable clamav-daemon
sudo systemctl start clamav-daemon

# Update virus definitions
sudo freshclam

# Test ClamAV
clamscan --version
```

```php
// config/services.php
return [
    // ... other services

    'virustotal' => [
        'api_key' => env('VIRUSTOTAL_API_KEY'),
        'timeout' => 30, // seconds
    ],

    'file_upload' => [
        'max_size' => [
            'pdf' => 5120, // 5MB in KB
            'image' => 2048, // 2MB in KB
        ],
        'allowed_mime_types' => [
            'pdf' => ['application/pdf'],
            'image' => ['image/jpeg', 'image/png', 'image/jpg'],
        ],
        'scan_enabled' => env('FILE_SCAN_ENABLED', true),
    ],
];
```

---

*[Content continues... Due to length, I'll create multiple parts. Would you like me to continue with Enhancements 4-12?]*

### Phase 1: MVP (Month 1-3)
- ✅ Basic registration flows
- ✅ AI document verification
- ✅ Simple matching algorithm
- ✅ Payment integration (Midtrans)

### Phase 2: AI Enhancement (Month 4-6)
- 🔄 Advanced ML matching model
- 🔄 AI chatbot with RAG
- 🔄 Questionnaire optimization
- 🔄 Dashboard analytics

### Phase 3: Scale (Month 7-9)
- 📅 Mobile app (React Native)
- 📅 Auto-moderation AI
- 📅 Multi-language support
- 📅 Advanced reporting

### Phase 4: Innovation (Month 10-12)
- 📅 Predictive analytics (success prediction)
- 📅 Smart scheduling/matching optimization
- 📅 Virtual project assistant
- 📅 Blockchain certificates

### Future AI Features to Consider

| Feature | Impact | Complexity | Priority |
|---------|--------|------------|----------|
| Resume Parser | High | Medium | High |
| Video Interview Analysis | Medium | High | Medium |
| Auto Report Generation | High | Medium | High |
| Sentiment Analysis (feedback) | Medium | Low | Medium |
| Fraud Detection (enhanced) | High | High | High |
| Project Success Prediction | High | High | Medium |
| Smart Notification Timing | Low | Low | Low |
| Auto Translation | Medium | Medium | Medium |

---

## 📊 Success Metrics

### KPIs to Track

**Institution Metrics:**
- Verification approval rate
- Time to activation (registration → active)
- Subscription conversion rate
- Problem posting rate
- Application received rate

**Student Metrics:**
- Questionnaire completion rate
- Match accuracy (applied vs recommended)
- Chat engagement rate
- Application success rate
- Project completion rate

**AI Performance:**
- Document verification accuracy
- Matching algorithm precision/recall
- Chatbot response quality (user ratings)
- False positive/negative rates
- Processing time

**Business Metrics:**
- Monthly Recurring Revenue (MRR)
- Customer Acquisition Cost (CAC)
- Lifetime Value (LTV)
- Churn rate
- Net Promoter Score (NPS)

---

## 📝 Implementation Notes

### Development Workflow

```bash
# 1. Setup environments
- Development (local)
- Staging (testing)
- Production (live)

# 2. Feature development
1. Create feature branch
2. Implement backend API
3. Implement AI service (if needed)
4. Write tests
5. Create PR
6. Code review
7. Deploy to staging
8. QA testing
9. Deploy to production

# 3. AI model lifecycle
1. Data collection
2. Model training
3. Evaluation
4. A/B testing
5. Deployment
6. Monitoring
7. Retraining (monthly/quarterly)
```

### Testing Strategy

**Unit Tests:**
- Laravel (PHPUnit)
- AI Service (Pytest)
- Frontend (Vitest)

**Integration Tests:**
- API endpoints
- Payment flow
- AI service integration

**E2E Tests:**
- Critical user flows
- Payment scenarios

**AI Model Tests:**
- Accuracy benchmarks
- Bias detection
- Performance tests

---

## 🗄️ Supabase Vector Database Setup

### Enabling pgvector Extension

Supabase mendukung pgvector extension untuk vector similarity search. Berikut cara setupnya:

#### 1. Enable pgvector di Supabase Dashboard

```sql
-- Jalankan di SQL Editor Supabase Dashboard
-- https://app.supabase.com/project/YOUR_PROJECT/sql

-- Enable pgvector extension
CREATE EXTENSION IF NOT EXISTS vector;

-- Verify extension installed
SELECT * FROM pg_extension WHERE extname = 'vector';
```

#### 2. Buat Tabel dengan Kolom Vector

```sql
-- Students table with AI profile vector
CREATE TABLE students (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT REFERENCES users(id),

    -- Basic info
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,

    -- AI Profile (1536 dimensions untuk Cohere Embed v3)
    ai_profile_vector VECTOR(1536),
    questionnaire_data JSONB,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Problems table with embedding
CREATE TABLE problems (
    id BIGSERIAL PRIMARY KEY,
    institution_id BIGINT REFERENCES institutions(id),

    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(100),

    -- AI Embedding (1536 dimensions untuk Cohere Embed v3)
    ai_embedding VECTOR(1536),
    required_skills JSONB,

    status VARCHAR(50) DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Knowledge base untuk RAG chatbot
CREATE TABLE knowledge_base (
    id BIGSERIAL PRIMARY KEY,

    -- Content
    content TEXT NOT NULL,
    content_type VARCHAR(50), -- 'problem_desc', 'institution_profile', 'faq', 'guide'

    -- Metadata
    source_id BIGINT, -- ID dari problem, institution, dll
    source_type VARCHAR(50),

    -- Vector embedding (1536 dimensions)
    embedding VECTOR(1536),

    -- Metadata for filtering
    metadata JSONB,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### 3. Buat Vector Indexes untuk Performance

```sql
-- Index untuk student profile similarity search
-- IVFFLAT: Approximate nearest neighbor (ANN) algorithm
-- lists = 100: Good for tables with ~100k rows
CREATE INDEX idx_students_vector
ON students
USING ivfflat (ai_profile_vector vector_cosine_ops)
WITH (lists = 100);

-- Index untuk problem similarity search
CREATE INDEX idx_problems_vector
ON problems
USING ivfflat (ai_embedding vector_cosine_ops)
WITH (lists = 100);

-- Index untuk knowledge base
CREATE INDEX idx_knowledge_base_vector
ON knowledge_base
USING ivfflat (embedding vector_cosine_ops)
WITH (lists = 100);

-- Composite index untuk filtered vector search
CREATE INDEX idx_knowledge_base_type_vector
ON knowledge_base (content_type, embedding);
```

**Note tentang IVFFLAT lists parameter:**
- lists = 100 bagus untuk 10k-100k rows
- lists = 500 untuk 100k-1M rows
- lists = 1000 untuk > 1M rows

#### 4. Vector Similarity Search Queries

```sql
-- 1. Find similar students (cosine similarity)
SELECT
    id,
    first_name,
    last_name,
    1 - (ai_profile_vector <=> $1::vector) AS similarity_score
FROM students
WHERE ai_profile_vector IS NOT NULL
ORDER BY ai_profile_vector <=> $1::vector
LIMIT 10;

-- 2. Find matching problems for a student
SELECT
    p.id,
    p.title,
    p.category,
    1 - (p.ai_embedding <=> $1::vector) AS match_score
FROM problems p
WHERE
    p.ai_embedding IS NOT NULL
    AND p.status = 'open'
ORDER BY p.ai_embedding <=> $1::vector
LIMIT 20;

-- 3. RAG: Find relevant documents for chatbot
SELECT
    kb.id,
    kb.content,
    kb.content_type,
    kb.metadata,
    1 - (kb.embedding <=> $1::vector) AS relevance_score
FROM knowledge_base kb
WHERE
    kb.embedding IS NOT NULL
    AND kb.content_type = ANY($2::text[]) -- Filter by content types
ORDER BY kb.embedding <=> $1::vector
LIMIT 5;

-- 4. Hybrid search (vector + full-text)
SELECT
    kb.id,
    kb.content,
    kb.content_type,
    1 - (kb.embedding <=> $1::vector) AS vector_score,
    ts_rank(to_tsvector('indonesian', kb.content), plainto_tsquery('indonesian', $2)) AS text_score,
    -- Combined score (weighted)
    (1 - (kb.embedding <=> $1::vector)) * 0.7 +
    ts_rank(to_tsvector('indonesian', kb.content), plainto_tsquery('indonesian', $2)) * 0.3 AS combined_score
FROM knowledge_base kb
WHERE
    kb.embedding IS NOT NULL
    AND (
        kb.embedding <=> $1::vector < 0.5 -- Similarity threshold
        OR to_tsvector('indonesian', kb.content) @@ plainto_tsquery('indonesian', $2)
    )
ORDER BY combined_score DESC
LIMIT 10;
```

**Distance operators di pgvector:**
- `<=>` : Cosine distance (0 = identical, 2 = opposite)
- `<->` : L2 distance (Euclidean)
- `<#>` : Inner product

**Convert to similarity score:**
- Cosine similarity = `1 - (vector1 <=> vector2)`
- Range: 0 (completely different) to 1 (identical)

#### 5. Laravel Integration dengan Supabase pgvector

```php
// config/database.php
'connections' => [
    'pgsql' => [
        'driver' => 'pgsql',
        'url' => env('DATABASE_URL'),
        'host' => env('DB_HOST', 'db.your-project.supabase.co'),
        'port' => env('DB_PORT', '5432'),
        'database' => env('DB_DATABASE', 'postgres'),
        'username' => env('DB_USERNAME', 'postgres'),
        'password' => env('DB_PASSWORD', ''),
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
        'schema' => 'public',
        'sslmode' => 'require',
    ],
],

// .env
DATABASE_URL=postgresql://postgres:YOUR_PASSWORD@db.your-project.supabase.co:5432/postgres
SUPABASE_URL=https://your-project.supabase.co
SUPABASE_ANON_KEY=your-anon-key
SUPABASE_SERVICE_KEY=your-service-role-key
```

#### 6. Helper Functions untuk Vector Operations

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class VectorSearchService
{
    /**
     * Convert array to PostgreSQL vector format
     */
    public function arrayToVector(array $embedding): string
    {
        return '[' . implode(',', $embedding) . ']';
    }

    /**
     * Find similar students based on profile vector
     */
    public function findSimilarStudents(array $profileVector, int $limit = 10): array
    {
        $vectorStr = $this->arrayToVector($profileVector);

        return DB::select("
            SELECT
                id,
                first_name,
                last_name,
                email,
                1 - (ai_profile_vector <=> ?::vector) AS similarity_score
            FROM students
            WHERE ai_profile_vector IS NOT NULL
            ORDER BY ai_profile_vector <=> ?::vector
            LIMIT ?
        ", [$vectorStr, $vectorStr, $limit]);
    }

    /**
     * Find matching problems for a student
     */
    public function findMatchingProblems(array $studentVector, int $limit = 20): array
    {
        $vectorStr = $this->arrayToVector($studentVector);

        return DB::select("
            SELECT
                p.id,
                p.title,
                p.description,
                p.category,
                i.name as institution_name,
                1 - (p.ai_embedding <=> ?::vector) AS match_score
            FROM problems p
            JOIN institutions i ON p.institution_id = i.id
            WHERE
                p.ai_embedding IS NOT NULL
                AND p.status = 'open'
            ORDER BY p.ai_embedding <=> ?::vector
            LIMIT ?
        ", [$vectorStr, $vectorStr, $limit]);
    }

    /**
     * RAG: Retrieve relevant documents for chatbot
     */
    public function retrieveRelevantDocs(
        array $queryVector,
        array $contentTypes = [],
        int $limit = 5
    ): array {
        $vectorStr = $this->arrayToVector($queryVector);

        $query = "
            SELECT
                kb.id,
                kb.content,
                kb.content_type,
                kb.source_id,
                kb.source_type,
                kb.metadata,
                1 - (kb.embedding <=> ?::vector) AS relevance_score
            FROM knowledge_base kb
            WHERE kb.embedding IS NOT NULL
        ";

        $params = [$vectorStr];

        if (!empty($contentTypes)) {
            $query .= " AND kb.content_type = ANY(?)";
            $params[] = '{' . implode(',', $contentTypes) . '}';
        }

        $query .= " ORDER BY kb.embedding <=> ?::vector LIMIT ?";
        $params[] = $vectorStr;
        $params[] = $limit;

        return DB::select($query, $params);
    }

    /**
     * Update student profile vector
     */
    public function updateStudentVector(int $studentId, array $vector): bool
    {
        $vectorStr = $this->arrayToVector($vector);

        return DB::update("
            UPDATE students
            SET
                ai_profile_vector = ?::vector,
                updated_at = NOW()
            WHERE id = ?
        ", [$vectorStr, $studentId]);
    }

    /**
     * Update problem embedding
     */
    public function updateProblemEmbedding(int $problemId, array $embedding): bool
    {
        $vectorStr = $this->arrayToVector($embedding);

        return DB::update("
            UPDATE problems
            SET
                ai_embedding = ?::vector,
                updated_at = NOW()
            WHERE id = ?
        ", [$vectorStr, $problemId]);
    }

    /**
     * Insert knowledge base entry with embedding
     */
    public function insertKnowledgeBase(
        string $content,
        string $contentType,
        array $embedding,
        ?int $sourceId = null,
        ?string $sourceType = null,
        ?array $metadata = null
    ): int {
        $vectorStr = $this->arrayToVector($embedding);

        $result = DB::selectOne("
            INSERT INTO knowledge_base (
                content,
                content_type,
                embedding,
                source_id,
                source_type,
                metadata
            )
            VALUES (?, ?, ?::vector, ?, ?, ?::jsonb)
            RETURNING id
        ", [
            $content,
            $contentType,
            $vectorStr,
            $sourceId,
            $sourceType,
            json_encode($metadata)
        ]);

        return $result->id;
    }
}
```

#### 7. Complete Flow: Generate & Store Embeddings

```php
<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Problem;
use Cohere\Client as CohereClient;

class EmbeddingService
{
    protected $cohere;
    protected $vectorSearch;

    public function __construct(CohereClient $cohere, VectorSearchService $vectorSearch)
    {
        $this->cohere = $cohere;
        $this->vectorSearch = $vectorSearch;
    }

    /**
     * Generate and store student profile embedding
     */
    public function generateStudentProfileEmbedding(Student $student): void
    {
        // Combine student data into text
        $profileText = $this->buildStudentProfileText($student);

        // Generate embedding via Cohere
        $response = $this->cohere->embed([
            'texts' => [$profileText],
            'model' => 'embed-english-v3.0',
            'input_type' => 'search_document'
        ]);

        $embedding = $response->embeddings[0]; // Array of 1536 floats

        // Store in Supabase
        $this->vectorSearch->updateStudentVector($student->id, $embedding);
    }

    /**
     * Generate and store problem embedding
     */
    public function generateProblemEmbedding(Problem $problem): void
    {
        // Combine problem data into text
        $problemText = $this->buildProblemText($problem);

        // Generate embedding via Cohere
        $response = $this->cohere->embed([
            'texts' => [$problemText],
            'model' => 'embed-english-v3.0',
            'input_type' => 'search_document'
        ]);

        $embedding = $response->embeddings[0];

        // Store in Supabase
        $this->vectorSearch->updateProblemEmbedding($problem->id, $embedding);
    }

    /**
     * Build student profile text for embedding
     */
    protected function buildStudentProfileText(Student $student): string
    {
        $questionnaire = $student->questionnaire_data ?? [];

        return implode('. ', array_filter([
            "Student: {$student->first_name} {$student->last_name}",
            "Major: {$student->major}",
            "Skills: " . implode(', ', $questionnaire['skills'] ?? []),
            "Interests: " . implode(', ', $questionnaire['interests'] ?? []),
            "Goals: " . ($questionnaire['primary_goal'] ?? ''),
        ]));
    }

    /**
     * Build problem text for embedding
     */
    protected function buildProblemText(Problem $problem): string
    {
        return implode('. ', array_filter([
            $problem->title,
            $problem->description,
            "Category: {$problem->category}",
            "Skills needed: " . implode(', ', $problem->required_skills ?? []),
        ]));
    }
}
```

#### 8. Monitoring Vector Index Performance

```sql
-- Check index size
SELECT
    schemaname,
    tablename,
    indexname,
    pg_size_pretty(pg_relation_size(indexrelid)) AS index_size
FROM pg_stat_user_indexes
WHERE indexname LIKE '%vector%';

-- Check index usage stats
SELECT
    schemaname,
    tablename,
    indexname,
    idx_scan AS index_scans,
    idx_tup_read AS tuples_read,
    idx_tup_fetch AS tuples_fetched
FROM pg_stat_user_indexes
WHERE indexname LIKE '%vector%';

-- Analyze table statistics (run periodically)
ANALYZE students;
ANALYZE problems;
ANALYZE knowledge_base;
```

---

## 🔑 AI API Configuration & Cost Management

### API Keys Required

```bash
# .env file
ANTHROPIC_API_KEY=sk-ant-...
COHERE_API_KEY=...

# API Endpoints
ANTHROPIC_BASE_URL=https://api.anthropic.com
COHERE_BASE_URL=https://api.cohere.ai
```

### Model Selection Strategy

```typescript
// Intelligent model routing based on query complexity
function selectClaudeModel(query: string, conversationHistory: Message[]): string {
  const complexityScore = assessQueryComplexity(query, conversationHistory);

  if (complexityScore > 7) {
    return 'claude-3-5-sonnet-20241022';  // Complex reasoning
  } else {
    return 'claude-3-haiku-20240307';     // Simple Q&A (cheaper)
  }
}

function assessQueryComplexity(query: string, history: Message[]): number {
  let score = 0;

  // Long query = more complex
  if (query.length > 200) score += 3;

  // Technical keywords
  if (/teknologi|implementasi|arsitektur|database/i.test(query)) score += 2;

  // Multi-part questions
  if (query.includes('?') && query.split('?').length > 2) score += 2;

  // Long conversation = needs context
  if (history.length > 5) score += 2;

  // Follow-up question
  if (/bagaimana|jelaskan|detail|contoh/i.test(query)) score += 2;

  return Math.min(10, score);
}
```

### Cost Optimization

**Claude API Pricing (2025):**
- Claude 3.5 Sonnet: ~$3 per 1M input tokens, ~$15 per 1M output tokens
- Claude 3 Haiku: ~$0.25 per 1M input tokens, ~$1.25 per 1M output tokens

**Cohere API Pricing (Trial → Paid):**
- Embed v3: ~$0.10 per 1M tokens (trial: free tier available)
- Rerank: ~$0.20 per 1M tokens (trial: limited searches)

**Monthly Budget Estimation:**

| Feature | Usage | Model | Est. Cost/Month |
|---------|-------|-------|-----------------|
| Document Verification | 100 institutions | Claude 3 Haiku | $5-10 |
| Student Matching | 500 students | Cohere Embed v3 | $10-20 |
| AI Chatbot | 10k messages | Claude mix (70% Haiku, 30% Sonnet) | $50-100 |
| Vector Search | 50k searches | Cohere Rerank | $15-30 |
| **Total** | | | **$80-160/month** |

**Cost Reduction Strategies:**

```typescript
// 1. Response Caching
const cache = new Map<string, {response: string, timestamp: number}>();

async function getCachedResponse(query: string): Promise<string | null> {
  const cacheKey = hashQuery(query);
  const cached = cache.get(cacheKey);

  if (cached && Date.now() - cached.timestamp < 3600000) { // 1 hour
    return cached.response;
  }
  return null;
}

// 2. Batch Processing
async function batchEmbeddings(texts: string[]): Promise<number[][]> {
  // Cohere allows up to 96 texts per request
  const batches = chunk(texts, 96);
  const results = await Promise.all(
    batches.map(batch => cohereClient.embed({
      texts: batch,
      model: 'embed-english-v3.0',
      input_type: 'search_document'
    }))
  );
  return results.flat();
}

// 3. Smart Context Window
function optimizeContext(documents: Document[], maxTokens: number = 4000): string {
  let context = '';
  let tokens = 0;

  for (const doc of documents) {
    const docTokens = estimateTokens(doc.content);
    if (tokens + docTokens > maxTokens) break;

    context += doc.content + '\n\n';
    tokens += docTokens;
  }

  return context;
}

// 4. Rate Limiting
const rateLimiter = {
  claude: {
    requestsPerMinute: 50,
    tokensPerMinute: 100000
  },
  cohere: {
    requestsPerMinute: 100,
    embedsPerMinute: 10000
  }
};
```

### Cohere Trial Limitations & Migration Plan

**Current Trial Limits:**
- Embed v3: 100 API calls/minute
- Rerank: 100 API calls/minute
- Total: Limited trial credits

**Migration Strategy When Trial Ends:**

```typescript
interface EmbeddingProvider {
  name: 'cohere' | 'openai' | 'sentence-transformers';
  costPerToken: number;
  maxBatchSize: number;
}

// Fallback providers
const embeddingProviders: EmbeddingProvider[] = [
  {
    name: 'cohere',
    costPerToken: 0.0001,
    maxBatchSize: 96
  },
  {
    name: 'openai',
    costPerToken: 0.00013,  // text-embedding-3-small
    maxBatchSize: 2048
  },
  {
    name: 'sentence-transformers',  // Self-hosted
    costPerToken: 0,  // Infrastructure cost only
    maxBatchSize: 32
  }
];

// Auto-switch based on budget
async function selectEmbeddingProvider(budget: number): Promise<EmbeddingProvider> {
  if (budget > 50) return embeddingProviders[0]; // Cohere (best quality)
  if (budget > 20) return embeddingProviders[1]; // OpenAI
  return embeddingProviders[2]; // Self-hosted (free but slower)
}
```

**Recommended Approach:**
1. **Phase 1 (MVP)**: Use Cohere trial for embeddings + Claude Haiku primarily
2. **Phase 2**: Monitor usage, optimize queries, implement caching
3. **Phase 3**: Evaluate if Cohere paid plan is worth it vs alternatives
4. **Phase 4**: Consider self-hosting embedding models if volume is high

### API Error Handling & Fallbacks

```typescript
// Robust API calling with retries and fallbacks
async function callClaudeWithFallback(
  prompt: string,
  preferredModel: string = 'claude-3-5-sonnet-20241022'
): Promise<string> {

  try {
    // Try primary model
    return await callClaude(prompt, preferredModel);
  } catch (error) {
    if (error.status === 429) {
      // Rate limit - wait and retry
      await sleep(2000);
      return await callClaude(prompt, preferredModel);
    }

    if (error.status === 529) {
      // Overloaded - fallback to Haiku
      console.warn('Claude Sonnet overloaded, falling back to Haiku');
      return await callClaude(prompt, 'claude-3-haiku-20240307');
    }

    // Other errors - return graceful degradation
    return "Maaf, AI sedang tidak tersedia. Silakan coba lagi nanti.";
  }
}

async function callCohereEmbedWithFallback(texts: string[]): Promise<number[][]> {
  try {
    return await cohereClient.embed({
      texts,
      model: 'embed-english-v3.0',
      input_type: 'search_document'
    });
  } catch (error) {
    if (error.message.includes('trial')) {
      // Trial expired - fallback to self-hosted
      console.warn('Cohere trial expired, using sentence-transformers');
      return await selfHostedEmbed(texts);
    }
    throw error;
  }
}
```

### Monitoring & Analytics

```typescript
interface AIUsageMetrics {
  service: 'claude' | 'cohere';
  model: string;
  feature: string;

  // Usage
  requestCount: number;
  inputTokens: number;
  outputTokens: number;

  // Cost
  estimatedCost: number;

  // Performance
  avgLatency: number;
  errorRate: number;

  // Time
  timestamp: Date;
  period: 'hourly' | 'daily' | 'monthly';
}

// Track usage in database
async function logAIUsage(metrics: AIUsageMetrics) {
  await db.aiUsageLogs.create({
    data: metrics
  });

  // Alert if approaching budget
  const monthlySpend = await calculateMonthlySpend();
  if (monthlySpend > MONTHLY_BUDGET * 0.8) {
    await sendAlert({
      type: 'budget_warning',
      message: `AI spending at ${monthlySpend}/${MONTHLY_BUDGET}`
    });
  }
}
```

---

## 🎓 Conclusion

Spesifikasi ini memberikan roadmap lengkap untuk mengintegrasikan AI ke dalam platform KKN-GO. Dengan pendekatan bertahap (MVP → Enhancement → Scale), development risk dapat diminimalkan sambil terus deliver value kepada users.

**Key Success Factors:**
1. ✅ **User-centric design** - AI harus membantu, bukan menyulitkan
2. ✅ **Transparent AI** - Selalu explain keputusan AI
3. ✅ **Privacy-first** - Protect user data
4. ✅ **Iterative improvement** - Continuously learn from user feedback
5. ✅ **Scalable architecture** - Design for growth

**Next Steps:**
1. Review dan approval spesifikasi ini
2. Setup development environment
3. Start with Phase 1 MVP
4. Collect early user feedback
5. Iterate and improve


# 🔧 Migration Fix Guide - Feature 1 AI Document Verification

## ❗ Problem
Database transaction stuck dalam failed state karena migration error sebelumnya.

## ✅ Solution Steps

### Step 1: Clear Database Transaction State

**Via Supabase Dashboard (Recommended):**

1. Buka Supabase Dashboard: https://app.supabase.com/project/zgpykwjzmiqxhweifmrn/sql
2. Masuk ke **SQL Editor**
3. Run query ini:

```sql
-- Terminate stuck transactions
SELECT pg_terminate_backend(pid)
FROM pg_stat_activity
WHERE state = 'idle in transaction (aborted)'
  AND datname = 'postgres';

-- Verify
SELECT pid, state, query
FROM pg_stat_activity
WHERE datname = 'postgres';
```

### Step 2: Run Migration Fresh

Setelah clear transaction state, run migration:

```bash
cd "c:\Users\HYPE R Series\OneDrive - Institut Teknologi Bandung\Documents\ITB\Semester 5\Lapangku"
php artisan migrate:fresh
```

### Step 3: (Optional) Seed Data if Needed

Jika butuh data dummy:

```bash
php artisan db:seed
```

---

## 📊 Verification

Setelah migration berhasil, verifikasi table sudah dibuat:

```sql
-- Check all tables
SELECT table_name
FROM information_schema.tables
WHERE table_schema = 'public'
ORDER BY table_name;

-- Verify verification_documents table
SELECT column_name, data_type
FROM information_schema.columns
WHERE table_name = 'verification_documents';
```

Expected tables yang harus ada:
- ✅ `verification_documents` (Feature 1 - NEW!)
- ✅ `institutions` (with AI verification fields - UPDATED!)
- ✅ `sessions`
- ✅ `users`
- ✅ `students`
- ✅ `problems`
- ✅ dan lainnya...

---

## 🧪 Testing Feature 1 After Migration Success

### 1. Start Development Server

```bash
php artisan serve
```

### 2. Test API Endpoints

#### A. Upload Documents (via Postman/Insomnia)

**Request:**
```
POST http://127.0.0.1:8000/api/institutions/1/documents
Authorization: Bearer {your_token}
Content-Type: multipart/form-data

Body (form-data):
- official_letter: [Select PDF file]
- logo: [Select Image file]
- pic_identity: [Select Image file - KTP]
- npwp: [Optional - Select PDF/Image]
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Documents uploaded successfully",
  "data": {
    "documents": [
      {
        "id": 1,
        "type": "official_letter",
        "file_name": "surat_pengantar.pdf"
      },
      {
        "id": 2,
        "type": "logo",
        "file_name": "logo_institusi.png"
      },
      {
        "id": 3,
        "type": "pic_identity",
        "file_name": "ktp_pic.jpg"
      }
    ],
    "institution_id": 1
  }
}
```

#### B. Trigger AI Verification

**Request:**
```
POST http://127.0.0.1:8000/api/ai/verify-documents
Authorization: Bearer {your_token}
Content-Type: application/json

{
  "institution_id": 1
}
```

**Expected Response:**
```json
{
  "success": true,
  "message": "Documents verified successfully",
  "data": {
    "verification_id": "ver_abc123",
    "status": "approved|needs_review|rejected",
    "score": 92.5,
    "confidence": 0.91,
    "reasoning": "All documents appear authentic. Logo matches official records...",
    "flags": [],
    "processed_at": "2025-01-15T10:30:00Z"
  }
}
```

#### C. Check Verification Status

**Request:**
```
GET http://127.0.0.1:8000/api/institutions/1/verification-status
Authorization: Bearer {your_token}
```

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "institution_id": 1,
    "verification_status": "pending_payment",
    "verification_score": 92.5,
    "verification_confidence": 0.91,
    "verified_at": "2025-01-15T10:30:00Z",
    "is_verified": true,
    "documents": [
      {
        "id": 1,
        "type": "official_letter",
        "file_name": "surat_pengantar.pdf",
        "ai_status": "approved",
        "ai_score": 95,
        "ai_processed_at": "2025-01-15T10:30:00Z"
      }
    ]
  }
}
```

#### D. Get Detailed Verification Result

**Request:**
```
GET http://127.0.0.1:8000/api/ai/verification/ver_abc123
Authorization: Bearer {your_token}
```

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "verification_id": "ver_abc123",
    "institution": {
      "id": 1,
      "name": "Desa Sukamaju",
      "type": "Desa",
      "verification_status": "pending_payment",
      "verification_score": 92.5,
      "verification_confidence": 0.91
    },
    "documents": [
      {
        "id": 1,
        "type": "official_letter",
        "file_name": "surat_pengantar.pdf",
        "ai_status": "approved",
        "ai_score": 95,
        "ai_confidence": 0.92,
        "ai_flags": [],
        "ai_extracted_data": {
          "institution_name": "Desa Sukamaju",
          "pic_name": "Budi Santoso",
          "issue_date": "2025-01-10"
        },
        "ai_reasoning": "Document appears authentic. Official letterhead present...",
        "ai_processed_at": "2025-01-15T10:30:00Z"
      }
    ]
  }
}
```

---

## 🎯 What to Look For in Test Results

### 1. **Approved Status** (Score ≥ 85, Confidence ≥ 0.8)
- Institution status: `pending_payment`
- Ready for subscription
- No flags or minor warnings only

### 2. **Needs Review Status** (60 ≤ Score < 85)
- Institution status: `needs_review`
- Has warnings/flags
- Requires human verification
- Check `ai_flags` for details

### 3. **Rejected Status** (Score < 60)
- Institution status: `rejected`
- Critical issues detected
- Check `ai_flags` for critical errors
- Cannot proceed without fixing documents

---

## 🐛 Troubleshooting

### Error: "No documents found for verification"
- Upload documents first via `/api/institutions/{id}/documents`

### Error: "Unauthorized"
- Check Bearer token is valid
- Check user owns the institution

### Error: "Claude API Error"
- Check `CLAUDE_API_KEY` in `.env`
- Check API key is valid: sk-ant-api03-...

### Error: "Failed to load document"
- Check Supabase storage credentials
- Check file exists in Supabase bucket

### AI Returns Low Scores
- Normal behavior for low-quality documents
- Try with clear, high-resolution images
- Ensure documents are authentic

---

## 📝 Notes

- **Cost**: Using Claude 3 Haiku (~$0.25 per 1M input tokens)
- **Performance**: ~5-10 seconds per document analysis
- **Supported Formats**:
  - Images: JPG, PNG
  - PDFs: Official letters, NPWP
- **Max File Size**:
  - PDFs: 5MB
  - Images: 2MB

---

## ✅ Success Criteria

Feature 1 working correctly when:
- ✅ Documents upload successfully to Supabase
- ✅ AI analyzes all document types
- ✅ Returns structured verification result with reasoning
- ✅ Updates institution status automatically
- ✅ Stores all results in database
- ✅ Provides detailed flags and extracted data

---

## 🚀 Next Steps After Feature 1 Works

1. **Feature 2**: AI Questionnaire & Project Matching (Student)
2. **Feature 3**: AI Chatbot Assistant (RAG)
3. **Feature 4**: AI Project Suggestion

---

## 📞 Support

If migration still fails after following this guide:
1. Check PostgreSQL logs in Supabase
2. Verify network connection
3. Try manual SQL table creation as last resort
