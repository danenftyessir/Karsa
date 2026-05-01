<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * model ProblemImage
 * untuk menyimpan gambar-gambar problem/masalah
 * 
 * CATATAN PENTING: file gambar disimpan di Supabase Storage
 * - Bucket: "karsa storage"
 * - Path: problems/FILENAME.jpg
 * - Akses: Public URL via supabase_url() helper
 * 
 */
class ProblemImage extends Model
{
    protected $fillable = [
        'problem_id',
        'image_path',
        'caption',
        'order',
        'is_cover', 
    ];

    protected $casts = [
        'order' => 'integer',
        'is_cover' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // relasi ke problem
    public function problem(): BelongsTo
    {
        return $this->belongsTo(Problem::class);
    }

    /**
     * accessor untuk mendapatkan URL publik gambar dari Supabase
     * 
     * usage di blade: 
     * <img src="{{ $problemImage->image_url }}" alt="{{ $problemImage->caption }}">
     * 
     * atau untuk problem dengan images:
     * @foreach($problem->images as $image)
     *     <img src="{{ $image->image_url }}" alt="{{ $image->caption }}">
     * @endforeach
     * 
     * @return string URL publik gambar
     */
    public function getImageUrlAttribute(): string
    {
        // gunakan helper supabase_url untuk generate public URL
        return supabase_url($this->image_path);
    }

    /**
     * accessor untuk mendapatkan thumbnail URL (opsional, jika ada sistem thumbnail)
     * untuk saat ini return URL yang sama dengan image_url
     * 
     * usage di blade: {{ $problemImage->thumbnail_url }}
     * 
     * @return string URL thumbnail
     */
    public function getThumbnailUrlAttribute(): string
    {
        // untuk saat ini, return URL yang sama
        // nanti bisa dimodifikasi jika ada sistem thumbnail terpisah
        return $this->image_url;
    }

    /**
     * scope untuk mengurutkan berdasarkan order
     * 
     * usage: ProblemImage::ordered()->get()
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }
}