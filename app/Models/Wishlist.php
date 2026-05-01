<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Wishlist
 * 
 * representasi wishlist/bookmark problem oleh mahasiswa
 */
class Wishlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'problem_id',
        'notes',
    ];

    /**
     * relasi ke student
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * relasi ke problem
     */
    public function problem()
    {
        return $this->belongsTo(Problem::class);
    }
}