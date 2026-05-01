<?php

namespace App\Policies;

use App\Models\User;
use App\Models\JobApplication;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Policy untuk autorisasi JobApplication
 * Memastikan hanya company owner yang bisa view/manage applications untuk job mereka
 */
class JobApplicationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine apakah user bisa melihat daftar applications
     */
    public function viewAny(User $user): bool
    {
        // hanya company yang bisa melihat applications
        return $user->isCompany();
    }

    /**
     * Determine apakah user bisa melihat application tertentu
     */
    public function view(User $user, JobApplication $application): bool
    {
        // user harus company dan application harus untuk job posting milik company tersebut
        return $user->isCompany() &&
               $user->company &&
               $application->jobPosting &&
               $application->jobPosting->company_id === $user->company->id;
    }

    /**
     * Determine apakah user bisa update status application
     */
    public function updateStatus(User $user, JobApplication $application): bool
    {
        // user harus company dan application harus untuk job posting milik company tersebut
        return $user->isCompany() &&
               $user->company &&
               $application->jobPosting &&
               $application->jobPosting->company_id === $user->company->id;
    }

    /**
     * Determine apakah user bisa add rating ke application
     */
    public function addRating(User $user, JobApplication $application): bool
    {
        // user harus company dan application harus untuk job posting milik company tersebut
        return $user->isCompany() &&
               $user->company &&
               $application->jobPosting &&
               $application->jobPosting->company_id === $user->company->id;
    }

    /**
     * Determine apakah user bisa add notes ke application
     */
    public function addNotes(User $user, JobApplication $application): bool
    {
        // user harus company dan application harus untuk job posting milik company tersebut
        return $user->isCompany() &&
               $user->company &&
               $application->jobPosting &&
               $application->jobPosting->company_id === $user->company->id;
    }
}
