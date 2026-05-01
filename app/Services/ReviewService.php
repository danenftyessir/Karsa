<?php

namespace App\Services;

use App\Models\Review;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

/**
 * service untuk mengelola reviews
 * handle business logic untuk rating dan review system
 */
class ReviewService
{
    /**
     * dapatkan proyek yang perlu direview oleh institution
     */
    public function getPendingReviews($institutionId, $limit = 10)
    {
        return Project::where('institution_id', $institutionId)
                     ->where('status', 'completed')
                     ->whereNull('rating')
                     ->whereNull('institution_review')
                     ->with(['student.user', 'student.university', 'problem'])
                     ->latest('actual_end_date')
                     ->limit($limit)
                     ->get();
    }

    /**
     * buat review untuk proyek
     */
    public function createReview($projectId, $institutionId, $data)
    {
        DB::beginTransaction();
        
        try {
            // cek apakah proyek milik institution ini
            $project = Project::where('id', $projectId)
                             ->where('institution_id', $institutionId)
                             ->firstOrFail();

            // update project dengan rating dan review
            $project->update([
                'rating' => $data['rating'],
                'institution_review' => $data['review'],
                'reviewed_at' => now(),
            ]);

            // buat entry di tabel reviews
            $review = Review::create([
                'project_id' => $project->id,
                'reviewer_id' => auth()->id(), // user_id dari institution
                'reviewee_id' => $project->student->user_id, // user_id dari student
                'type' => 'institution_to_student',
                'rating' => $data['rating'],
                'review_text' => $data['review'],
                'professionalism_rating' => $data['professionalism_rating'] ?? null,
                'communication_rating' => $data['communication_rating'] ?? null,
                'quality_rating' => $data['quality_rating'] ?? null,
                'timeliness_rating' => $data['timeliness_rating'] ?? null,
                'strengths' => $data['strengths'] ?? null,
                'improvements' => $data['improvements'] ?? null,
                'is_public' => $data['is_public'] ?? true,
            ]);

            DB::commit();
            
            return $review;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * update review yang sudah ada
     */
    public function updateReview($reviewId, $institutionId, $data)
    {
        DB::beginTransaction();
        
        try {
            $review = Review::whereHas('project', function($q) use ($institutionId) {
                            $q->where('institution_id', $institutionId);
                        })
                        ->where('id', $reviewId)
                        ->where('type', 'institution_to_student')
                        ->firstOrFail();

            $review->update([
                'rating' => $data['rating'],
                'review_text' => $data['review'],
                'professionalism_rating' => $data['professionalism_rating'] ?? $review->professionalism_rating,
                'communication_rating' => $data['communication_rating'] ?? $review->communication_rating,
                'quality_rating' => $data['quality_rating'] ?? $review->quality_rating,
                'timeliness_rating' => $data['timeliness_rating'] ?? $review->timeliness_rating,
                'strengths' => $data['strengths'] ?? $review->strengths,
                'improvements' => $data['improvements'] ?? $review->improvements,
            ]);

            // update juga di project
            $review->project->update([
                'rating' => $data['rating'],
                'institution_review' => $data['review'],
                'reviewed_at' => now(),
            ]);

            DB::commit();
            
            return $review;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * dapatkan review terbaru yang diberikan institution
     */
    public function getRecentInstitutionReviews($institutionId, $limit = 5)
    {
        return Review::with(['project.problem', 'reviewee', 'project.student.user', 'project.student.university'])
                    ->where('type', 'institution_to_student')
                    ->whereHas('project', function($q) use ($institutionId) {
                        $q->where('institution_id', $institutionId);
                    })
                    ->latest()
                    ->limit($limit)
                    ->get();
    }

    /**
     * dapatkan statistik review untuk institution
     */
    public function getInstitutionReviewStats($institutionId)
    {
        $reviewsQuery = Review::where('type', 'institution_to_student')
                             ->whereHas('project', function($q) use ($institutionId) {
                                 $q->where('institution_id', $institutionId);
                             });

        $totalReviews = $reviewsQuery->count();
        $avgRating = $reviewsQuery->avg('rating') ?? 0;

        // distribusi rating
        $ratingDistribution = [
            5 => (clone $reviewsQuery)->where('rating', 5)->count(),
            4 => (clone $reviewsQuery)->where('rating', 4)->count(),
            3 => (clone $reviewsQuery)->where('rating', 3)->count(),
            2 => (clone $reviewsQuery)->where('rating', 2)->count(),
            1 => (clone $reviewsQuery)->where('rating', 1)->count(),
        ];

        return [
            'total_reviews' => $totalReviews,
            'average_rating' => round($avgRating, 2),
            'rating_distribution' => $ratingDistribution,
        ];
    }

    /**
     * dapatkan reviews untuk student (untuk portfolio)
     */
    public function getStudentReviews($studentId)
    {
        return Review::where('type', 'institution_to_student')
                    ->whereHas('project', function($q) use ($studentId) {
                        $q->where('student_id', $studentId);
                    })
                    ->where('rating', '>=', 4) // hanya tampilkan review positif
                    ->where('is_public', true)
                    ->with(['project.problem', 'project.institution', 'reviewer'])
                    ->latest()
                    ->get();
    }

    /**
     * cek apakah proyek sudah direview
     */
    public function isProjectReviewed($projectId)
    {
        return Review::where('project_id', $projectId)
                    ->where('type', 'institution_to_student')
                    ->exists();
    }

    /**
     * dapatkan average rating untuk student dari semua reviews
     */
    public function getStudentAverageRating($studentId)
    {
        return Review::where('type', 'institution_to_student')
                    ->whereHas('project', function($q) use ($studentId) {
                        $q->where('student_id', $studentId);
                    })
                    ->avg('rating') ?? 0;
    }

    /**
     * buat review untuk institution (oleh student)
     */
    public function createInstitutionReview($projectId, $studentId, $data)
    {
        DB::beginTransaction();
        
        try {
            $project = Project::where('id', $projectId)
                             ->where('student_id', $studentId)
                             ->where('status', 'completed')
                             ->firstOrFail();

            // cek apakah sudah pernah review
            $existingReview = Review::where('project_id', $projectId)
                                   ->where('type', 'student_to_institution')
                                   ->where('reviewer_id', auth()->id())
                                   ->exists();

            if ($existingReview) {
                throw new \Exception('Anda sudah memberikan review untuk proyek ini.');
            }

            // buat review
            $review = Review::create([
                'project_id' => $project->id,
                'reviewer_id' => auth()->id(), // user_id dari student
                'reviewee_id' => $project->institution->user_id, // user_id dari institution
                'type' => 'student_to_institution',
                'rating' => $data['rating'],
                'review_text' => $data['review'],
                'strengths' => $data['strengths'] ?? null,
                'improvements' => $data['improvements'] ?? null,
                'is_public' => $data['is_public'] ?? true,
            ]);

            DB::commit();
            
            return $review;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * dapatkan reviews yang diterima oleh institution (dari students)
     */
    public function getInstitutionReceivedReviews($institutionId, $limit = null)
    {
        $query = Review::with(['project.problem', 'reviewer', 'project.student.user'])
                      ->where('type', 'student_to_institution')
                      ->whereHas('project', function($q) use ($institutionId) {
                          $q->where('institution_id', $institutionId);
                      })
                      ->where('is_public', true)
                      ->latest();

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * response ke review yang diterima
     */
    public function respondToReview($reviewId, $institutionId, $response)
    {
        $review = Review::whereHas('project', function($q) use ($institutionId) {
                        $q->where('institution_id', $institutionId);
                    })
                    ->where('id', $reviewId)
                    ->where('type', 'student_to_institution')
                    ->firstOrFail();

        $review->update([
            'response' => $response,
            'responded_at' => now(),
        ]);

        return $review;
    }
}