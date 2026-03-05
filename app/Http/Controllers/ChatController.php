<?php

namespace App\Http\Controllers;

use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChatController extends Controller
{
    /**
     * Chat sayfasını göster
     */
    public function index()
    {
        return view('chat.index');
    }

    /**
     * Gemini API'ye mesaj gönder (streaming SSE)
     */
    public function send(Request $request)
    {
        $request->validate([
            'messages' => 'required|array|min:1',
            'messages.*.role' => 'required|in:user,assistant',
            'messages.*.content' => 'required|string|max:10000',
        ]);

        $messages = $request->input('messages');

        // API key kontrolü
        if (empty(config('services.gemini.api_key'))) {
            return response()->json([
                'error' => 'Gemini API anahtarı yapılandırılmamış. Lütfen .env dosyasına GEMINI_API_KEY ekleyin.',
            ], 500);
        }

        $gemini = new GeminiService();

        $response = new StreamedResponse(function () use ($gemini, $messages) {
            // Output buffer'ı tamamen kapat — gerçek anlık streaming için zorunlu
            while (ob_get_level()) {
                ob_end_clean();
            }

            try {
                // streamToOutput(): her Gemini chunk'ı anında SSE olarak basar
                $gemini->streamToOutput($messages);
            } catch (\Exception $e) {
                Log::error('Chat Stream Hatası', ['message' => $e->getMessage()]);
                echo "data: " . json_encode(['error' => 'Bir hata oluştu']) . "\n\n";
                flush();
            }

            echo "data: [DONE]\n\n";
            flush();
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');
        $response->headers->set('X-Accel-Buffering', 'no');

        return $response;
    }

    /**
     * Gemini API'ye mesaj gönder (normal, stream olmadan)
     */
    public function sendSync(Request $request)
    {
        $request->validate([
            'messages' => 'required|array|min:1',
            'messages.*.role' => 'required|in:user,assistant',
            'messages.*.content' => 'required|string|max:10000',
        ]);

        $messages = $request->input('messages');

        if (empty(config('services.gemini.api_key'))) {
            return response()->json([
                'error' => 'Gemini API anahtarı yapılandırılmamış.',
            ], 500);
        }

        $gemini = new GeminiService();
        $reply = $gemini->sendMessage($messages);

        if ($reply === null) {
            return response()->json([
                'error' => 'Yanıt alınamadı. Lütfen tekrar deneyin.',
            ], 500);
        }

        return response()->json([
            'reply' => $reply,
        ]);
    }
}
