<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * SavedTalent Model (Pivot Table)
 *
 * PENTING: Model ini menggunakan Supabase PostgreSQL sebagai database
 * SEMUA operasi CRUD langsung ke Supabase, BUKAN local database
 */
class SavedTalent extends Model
{
    use HasFactory;

    // Specify connection to Supabase PostgreSQL
    protected $connection = 'pgsql';
    protected $table = 'saved_talents';

    protected $fillable = [
        'company_id',
        'user_id',
        'category',
        'notes',
        'saved_at',
    ];

    protected $casts = [
        'saved_at' => 'datetime',
    ];

    // ========================================================================
    // RELATIONSHIPS
    // ========================================================================

    /**
     * Saved talent belongs to a company
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Saved talent belongs to a user (talent)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ========================================================================
    // SCOPES
    // ========================================================================

    /**
     * Scope: Filter by company
     */
    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope: Filter by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope: Filter by talent
     */
    public function scopeByTalent($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
