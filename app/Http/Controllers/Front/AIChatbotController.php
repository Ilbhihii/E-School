<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\OpenAIVisitorChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AIChatbotController extends Controller
{
    /**
     * Chatbot réservé aux visiteurs non connectés.
     */
    public function chat(
        Request $request,
        OpenAIVisitorChatService $chatService
    ) {
        /*
         * Un étudiant, professeur ou administrateur connecté
         * ne peut pas utiliser cette route.
         */
        if (Auth::check()) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Le chatbot est réservé aux visiteurs du site.',
            ], 403);
        }

        try {
            $validated = $request->validate([
                'message' => [
                    'required',
                    'string',
                    'max:1200',
                ],
                'conversation' => [
                    'nullable',
                    'array',
                    'max:12',
                ],
                'conversation.*.role' => [
                    'required_with:conversation',
                    'in:user,assistant',
                ],
                'conversation.*.content' => [
                    'required_with:conversation',
                    'string',
                    'max:1600',
                ],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Veuillez écrire une question valide.',
                'errors' => $e->errors(),
            ], 422);
        }

        try {
            $answer = $chatService->reply(
                $validated['message'],
                $validated['conversation'] ?? []
            );

            return response()->json([
                'success' => true,
                'message' => $answer,
            ]);
        } catch (\Throwable $e) {
            Log::error(
                'Visitor OpenAI chatbot error',
                [
                    'message' => $e->getMessage(),
                    'exception' => get_class($e),
                ]
            );

            return response()->json([
                'success' => false,
                'message' =>
                    'L’assistant est momentanément indisponible. '
                    . 'Vous pouvez nous contacter directement '
                    . 'à contact.smartschoolacademy@gmail.com.',
            ], 503);
        }
    }
}
