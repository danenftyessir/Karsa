<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\ChatbotService;
use App\Models\ChatConversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * ChatbotController
 *
 * Handle chatbot interactions untuk mahasiswa
 */
class ChatbotController extends Controller
{
    protected $chatbotService;

    public function __construct(ChatbotService $chatbotService)
    {
        $this->middleware('auth');
        $this->middleware('student');
        $this->chatbotService = $chatbotService;
    }

    /**
     * Display chatbot interface
     */
    public function index()
    {
        $user = Auth::user();
        $conversations = $this->chatbotService->getUserConversations($user, 10);

        return view('student.chatbot.index', compact('conversations'));
    }

    /**
     * Show specific conversation
     */
    public function show($id)
    {
        $user = Auth::user();
        $conversation = $this->chatbotService->getConversation($id, $user);

        if (!$conversation) {
            return redirect()->route('student.chatbot.index')
                ->with('error', 'Percakapan tidak ditemukan.');
        }

        $conversations = $this->chatbotService->getUserConversations($user, 10);

        return view('student.chatbot.show', compact('conversation', 'conversations'));
    }

    /**
     * Create new conversation
     */
    public function create()
    {
        try {
            $user = Auth::user();
            $conversation = $this->chatbotService->createConversation($user);

            return redirect()->route('student.chatbot.show', $conversation->id)
                ->with('success', 'Percakapan baru dibuat.');

        } catch (\Exception $e) {
            Log::error('Failed to create conversation', [
                'error' => $e->getMessage()
            ]);

            return redirect()->route('student.chatbot.index')
                ->with('error', 'Gagal membuat percakapan baru.');
        }
    }

    /**
     * Send message (API endpoint)
     */
    public function sendMessage(Request $request)
    {
        try {
            $validated = $request->validate([
                'conversation_id' => 'required|exists:chat_conversations,id',
                'message' => 'required|string|max:5000',
                'filters' => 'nullable|array',
                'filters.categories' => 'nullable|array',
                'filters.year' => 'nullable|integer',
                'filters.province_id' => 'nullable|exists:provinces,id',
                'filters.regency_id' => 'nullable|exists:regencies,id',
            ]);

            $user = Auth::user();
            $conversation = ChatConversation::where('id', $validated['conversation_id'])
                ->where('user_id', $user->id)
                ->first();

            if (!$conversation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Percakapan tidak ditemukan.'
                ], 404);
            }

            $result = $this->chatbotService->sendMessage(
                $conversation,
                $validated['message'],
                $validated['filters'] ?? []
            );

            return response()->json($result);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid.',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Failed to send message', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengirim pesan.'
            ], 500);
        }
    }

    /**
     * Get conversations list (API endpoint)
     */
    public function getConversations(Request $request)
    {
        try {
            $user = Auth::user();
            $conversations = $this->chatbotService->getUserConversations($user, 20);

            return response()->json([
                'success' => true,
                'conversations' => $conversations->items(),
                'pagination' => [
                    'current_page' => $conversations->currentPage(),
                    'last_page' => $conversations->lastPage(),
                    'per_page' => $conversations->perPage(),
                    'total' => $conversations->total(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get conversations', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil daftar percakapan.'
            ], 500);
        }
    }

    /**
     * Archive conversation
     */
    public function archive($id)
    {
        try {
            $user = Auth::user();
            $conversation = ChatConversation::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$conversation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Percakapan tidak ditemukan.'
                ], 404);
            }

            $this->chatbotService->archiveConversation($conversation);

            return response()->json([
                'success' => true,
                'message' => 'Percakapan berhasil diarsipkan.'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to archive conversation', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengarsipkan percakapan.'
            ], 500);
        }
    }

    /**
     * Delete conversation
     */
    public function destroy($id)
    {
        try {
            $user = Auth::user();
            $conversation = ChatConversation::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$conversation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Percakapan tidak ditemukan.'
                ], 404);
            }

            $this->chatbotService->deleteConversation($conversation);

            return response()->json([
                'success' => true,
                'message' => 'Percakapan berhasil dihapus.'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to delete conversation', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus percakapan.'
            ], 500);
        }
    }
}
