<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Problem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * WishlistController
 * 
 * handle save/bookmark problems oleh mahasiswa
 */
class WishlistController extends Controller
{
    /**
     * tampilkan halaman wishlist
     */
    public function index()
    {
        $student = Auth::user()->student;
        
        $wishlists = Wishlist::with(['problem.institution', 'problem.province', 'problem.regency', 'problem.images'])
                            ->where('student_id', $student->id)
                            ->latest()
                            ->paginate(12);
        
        return view('student.wishlist.index', compact('wishlists'));
    }
    
    /**
     * toggle save/unsave problem
     */
    public function toggle(Request $request, $problemId)
    {
        $student = Auth::user()->student;
        $problem = Problem::findOrFail($problemId);
        
        // cek apakah sudah ada di wishlist
        $wishlist = Wishlist::where('student_id', $student->id)
                           ->where('problem_id', $problemId)
                           ->first();
        
        if ($wishlist) {
            // unsave: hapus dari wishlist
            $wishlist->delete();
            
            return response()->json([
                'success' => true,
                'saved' => false,
                'message' => 'Dihapus dari wishlist'
            ]);
        } else {
            // save: tambahkan ke wishlist
            Wishlist::create([
                'student_id' => $student->id,
                'problem_id' => $problemId,
                'notes' => $request->notes ?? null,
            ]);
            
            return response()->json([
                'success' => true,
                'saved' => true,
                'message' => 'Ditambahkan ke wishlist'
            ]);
        }
    }
    
    /**
     * ✅ TAMBAHAN: remove wishlist by wishlist id
     * method ini dipanggil dari halaman wishlist index
     */
    public function remove($wishlistId)
    {
        $student = Auth::user()->student;
        
        // cari wishlist berdasarkan id dan pastikan milik student yang login
        $wishlist = Wishlist::where('id', $wishlistId)
                           ->where('student_id', $student->id)
                           ->first();
        
        if (!$wishlist) {
            return response()->json([
                'success' => false,
                'message' => 'Wishlist tidak ditemukan'
            ], 404);
        }
        
        $wishlist->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Berhasil dihapus dari wishlist'
        ]);
    }
    
    /**
     * cek apakah problem sudah disave
     */
    public function check($problemId)
    {
        $student = Auth::user()->student;
        
        $saved = Wishlist::where('student_id', $student->id)
                        ->where('problem_id', $problemId)
                        ->exists();
        
        return response()->json(['saved' => $saved]);
    }
    
    /**
     * update notes di wishlist
     */
    public function updateNotes(Request $request, $problemId)
    {
        $student = Auth::user()->student;
        
        $request->validate([
            'notes' => 'nullable|string|max:500'
        ]);
        
        $wishlist = Wishlist::where('student_id', $student->id)
                           ->where('problem_id', $problemId)
                           ->firstOrFail();
        
        $wishlist->update(['notes' => $request->notes]);
        
        return response()->json([
            'success' => true,
            'message' => 'Catatan berhasil diperbarui'
        ]);
    }
}