<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Province
 */
class Province extends Model
{
    use HasFactory;

    // PENTING: Specify connection ke Supabase PostgreSQL
    protected $connection = 'pgsql';

    protected $fillable = ['name', 'code'];

    public $timestamps = false;

    /**
     * relasi ke regencies
     */
    public function regencies()
    {
        return $this->hasMany(Regency::class);
    }

    /**
     * relasi ke problems
     */
    public function problems()
    {
        return $this->hasMany(Problem::class);
    }
}
