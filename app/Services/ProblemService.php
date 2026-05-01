<?php

namespace App\Services;

use App\Models\Problem;
use App\Models\ProblemImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * service untuk handle business logic terkait problems
 */
class ProblemService
{
    /**
     * buat problem baru dengan validasi dan business logic
     */
    public function createProblem(array $data, $institutionId)
    {
        try {
            DB::beginTransaction();

            // hitung durasi dalam bulan
            $startDate = \Carbon\Carbon::parse($data['start_date']);
            $endDate = \Carbon\Carbon::parse($data['end_date']);
            $durationMonths = $startDate->diffInMonths($endDate);

            // buat problem
            $problem = Problem::create([
                'institution_id' => $institutionId,
                'title' => $data['title'],
                'description' => $data['description'],
                'background' => $data['background'] ?? null,
                'objectives' => $data['objectives'] ?? null,
                'scope' => $data['scope'] ?? null,
                'province_id' => $data['province_id'],
                'regency_id' => $data['regency_id'],
                'village' => $data['village'] ?? null,
                'detailed_location' => $data['detailed_location'] ?? null,
                'sdg_categories' => json_encode($data['sdg_categories']),
                'required_students' => $data['required_students'],
                'required_skills' => json_encode($data['required_skills']),
                'required_majors' => isset($data['required_majors']) ? json_encode($data['required_majors']) : null,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'application_deadline' => $data['application_deadline'],
                'duration_months' => $durationMonths,
                'difficulty_level' => $data['difficulty_level'],
                'expected_outcomes' => $data['expected_outcomes'] ?? null,
                'deliverables' => isset($data['deliverables']) ? json_encode($data['deliverables']) : null,
                'facilities_provided' => isset($data['facilities_provided']) ? json_encode($data['facilities_provided']) : null,
                'status' => $data['status'] ?? 'draft',
            ]);

            DB::commit();
            return $problem;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * update problem dengan validasi business rules
     */
    public function updateProblem(Problem $problem, array $data)
    {
        // validasi: tidak bisa update jika sudah ada accepted applications
        if ($problem->applications()->where('status', 'accepted')->exists()) {
            throw new \Exception('Tidak dapat mengupdate masalah yang sudah memiliki aplikasi yang diterima.');
        }

        try {
            DB::beginTransaction();

            // hitung durasi dalam bulan
            $startDate = \Carbon\Carbon::parse($data['start_date']);
            $endDate = \Carbon\Carbon::parse($data['end_date']);
            $durationMonths = $startDate->diffInMonths($endDate);

            $problem->update([
                'title' => $data['title'],
                'description' => $data['description'],
                'background' => $data['background'] ?? null,
                'objectives' => $data['objectives'] ?? null,
                'scope' => $data['scope'] ?? null,
                'province_id' => $data['province_id'],
                'regency_id' => $data['regency_id'],
                'village' => $data['village'] ?? null,
                'detailed_location' => $data['detailed_location'] ?? null,
                'sdg_categories' => json_encode($data['sdg_categories']),
                'required_students' => $data['required_students'],
                'required_skills' => json_encode($data['required_skills']),
                'required_majors' => isset($data['required_majors']) ? json_encode($data['required_majors']) : null,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'application_deadline' => $data['application_deadline'],
                'duration_months' => $durationMonths,
                'difficulty_level' => $data['difficulty_level'],
                'expected_outcomes' => $data['expected_outcomes'] ?? null,
                'deliverables' => isset($data['deliverables']) ? json_encode($data['deliverables']) : null,
                'facilities_provided' => isset($data['facilities_provided']) ? json_encode($data['facilities_provided']) : null,
                'status' => $data['status'] ?? $problem->status,
            ]);

            DB::commit();
            return $problem;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * hapus problem dengan validasi
     */
    public function deleteProblem(Problem $problem)
    {
        // validasi: tidak bisa hapus jika sudah ada applications
        if ($problem->applications()->exists()) {
            throw new \Exception('Tidak dapat menghapus masalah yang sudah memiliki aplikasi.');
        }

        try {
            DB::beginTransaction();

            // hapus semua images
            foreach ($problem->images as $image) {
                Storage::disk('public')->delete($image->image_path);
                $image->delete();
            }

            // hapus problem
            $problem->delete();

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * upload images untuk problem
     */
    public function uploadImages(Problem $problem, array $images)
    {
        $uploadedImages = [];

        foreach ($images as $image) {
            $path = $image->store('problems', 'public');
            
            $problemImage = ProblemImage::create([
                'problem_id' => $problem->id,
                'image_path' => $path,
            ]);

            $uploadedImages[] = $problemImage;
        }

        return $uploadedImages;
    }

    /**
     * hapus image dari problem
     */
    public function deleteImage(ProblemImage $image)
    {
        Storage::disk('public')->delete($image->image_path);
        $image->delete();
        
        return true;
    }

    /**
     * toggle problem status (draft/open/closed)
     */
    public function toggleStatus(Problem $problem, string $newStatus)
    {
        $validStatuses = ['draft', 'open', 'closed', 'in_progress', 'completed'];
        
        if (!in_array($newStatus, $validStatuses)) {
            throw new \Exception('Status tidak valid.');
        }

        // business rules untuk perubahan status
        if ($newStatus === 'open') {
            // validasi: harus lengkap semua field
            if (!$problem->title || !$problem->description || !$problem->province_id) {
                throw new \Exception('Data masalah belum lengkap. Pastikan semua field terisi sebelum mempublikasikan.');
            }
        }

        if ($newStatus === 'closed') {
            // notifikasi ke semua pending applications
            // TODO: implement notification
        }

        $problem->update(['status' => $newStatus]);
        
        return $problem;
    }

    /**
     * increment views count
     */
    public function incrementViews(Problem $problem)
    {
        $problem->increment('views_count');
    }

    /**
     * get problem statistics untuk dashboard
     */
    public function getProblemStatistics($institutionId)
    {
        return [
            'total' => Problem::where('institution_id', $institutionId)->count(),
            'draft' => Problem::where('institution_id', $institutionId)->where('status', 'draft')->count(),
            'open' => Problem::where('institution_id', $institutionId)->where('status', 'open')->count(),
            'in_progress' => Problem::where('institution_id', $institutionId)->where('status', 'in_progress')->count(),
            'completed' => Problem::where('institution_id', $institutionId)->where('status', 'completed')->count(),
            'closed' => Problem::where('institution_id', $institutionId)->where('status', 'closed')->count(),
            'total_views' => Problem::where('institution_id', $institutionId)->sum('views_count'),
            'total_applications' => Problem::where('institution_id', $institutionId)->sum('applications_count'),
            'urgent_count' => Problem::where('institution_id', $institutionId)->where('is_urgent', true)->count(),
        ];
    }

    /**
     * get trending problems (most viewed/applied dalam 30 hari terakhir)
     */
    public function getTrendingProblems($institutionId, $limit = 5)
    {
        return Problem::where('institution_id', $institutionId)
            ->where('created_at', '>=', now()->subDays(30))
            ->orderBy('views_count', 'desc')
            ->orderBy('applications_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * check apakah problem sudah penuh (accepted students >= required students)
     */
    public function isFull(Problem $problem)
    {
        return $problem->accepted_students >= $problem->required_students;
    }

    /**
     * get remaining slots
     */
    public function getRemainingSlots(Problem $problem)
    {
        return max(0, $problem->required_students - $problem->accepted_students);
    }

    /**
     * duplicate problem (untuk membuat problem baru berdasarkan yang sudah ada)
     */
    public function duplicateProblem(Problem $problem)
    {
        try {
            DB::beginTransaction();

            $newProblem = $problem->replicate();
            $newProblem->status = 'draft';
            $newProblem->views_count = 0;
            $newProblem->applications_count = 0;
            $newProblem->accepted_students = 0;
            $newProblem->title = $problem->title . ' (Copy)';
            $newProblem->save();

            // copy images
            foreach ($problem->images as $image) {
                $newPath = str_replace('.', '_copy.', $image->image_path);
                Storage::disk('public')->copy($image->image_path, $newPath);
                
                ProblemImage::create([
                    'problem_id' => $newProblem->id,
                    'image_path' => $newPath,
                ]);
            }

            DB::commit();
            return $newProblem;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}