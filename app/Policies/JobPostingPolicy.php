<?php

namespace App\Policies;

use App\Models\User;
use App\Models\JobPosting;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Policy untuk autorisasi JobPosting
 * Memastikan hanya company owner yang bisa manage job postings mereka
 */
class JobPostingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine apakah user bisa melihat daftar job postings
     */
    public function viewAny(User $user): bool
    {
        // hanya company yang bisa melihat job postings management
        return $user->isCompany();
    }

    /**
     * Determine apakah user bisa melihat job posting tertentu
     */
    public function view(User $user, JobPosting $jobPosting): bool
    {
        // user harus company dan job posting harus milik company tersebut
        return $user->isCompany() &&
               $user->company &&
               $user->company->id === $jobPosting->company_id;
    }

    /**
     * Determine apakah user bisa membuat job posting
     */
    public function create(User $user): bool
    {
        // hanya company yang bisa create job posting
        return $user->isCompany() && $user->company !== null;
    }

    /**
     * Determine apakah user bisa update job posting tertentu
     */
    public function update(User $user, JobPosting $jobPosting): bool
    {
        // user harus company dan job posting harus milik company tersebut
        return $user->isCompany() &&
               $user->company &&
               $user->company->id === $jobPosting->company_id;
    }

    /**
     * Determine apakah user bisa delete job posting tertentu
     */
    public function delete(User $user, JobPosting $jobPosting): bool
    {
        // user harus company dan job posting harus milik company tersebut
        return $user->isCompany() &&
               $user->company &&
               $user->company->id === $jobPosting->company_id;
    }

    /**
     * Determine apakah user bisa restore deleted job posting
     */
    public function restore(User $user, JobPosting $jobPosting): bool
    {
        // user harus company dan job posting harus milik company tersebut
        return $user->isCompany() &&
               $user->company &&
               $user->company->id === $jobPosting->company_id;
    }

    /**
     * Determine apakah user bisa permanently delete job posting
     */
    public function forceDelete(User $user, JobPosting $jobPosting): bool
    {
        // user harus company dan job posting harus milik company tersebut
        return $user->isCompany() &&
               $user->company &&
               $user->company->id === $jobPosting->company_id;
    }
}
