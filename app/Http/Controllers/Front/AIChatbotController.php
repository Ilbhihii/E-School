<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIChatbotController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $userMessage = $request->input('message');
        $conversation = $request->input('conversation', []);

        try {
            $response = $this->callGemini($userMessage, $conversation);
            return response()->json([
                'success' => true,
                'message' => $response,
            ]);
        } catch (\Exception $e) {
            Log::error('AI Chatbot error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Désolé, une erreur est survenue. Veuillez réessayer.',
            ], 500);
        }
    }

    private function callGemini(string $userMessage, array $conversation): string
    {
        $apiKey = config('services.gemini.key');

        if (!$apiKey) {
            throw new \Exception('GEMINI_API_KEY is not configured.');
        }

        // Build the system instruction
        $systemInstruction = "Tu es un assistant virtuel de **Smart School Academy**, une plateforme éducative intelligente marocaine.

RÈGLES STRICTES :
- Réponds TOUJOURS en français, de façon chaleureuse et professionnelle.
- Sois concis (max 3-4 phrases sauf si une explication détaillée est nécessaire).
- Ne réponds qu'aux questions concernant la plateforme et l'éducation.

INFORMATIONS SUR LA PLATEFORME :
- **Smart School Academy** est une plateforme d'apprentissage en ligne marocaine.
- Services : Cours interactifs, sessions live, quiz/QCM, suivi personnalisé, supports PDF téléchargeables.
- Matières scolaires (français, maths, physique, etc.) et religieuses.
- Les étudiants passent un test de niveau pour être orientés.
- Rendez-vous disponible pour test de niveau personnalisé.
- Paiement sécurisé via PayPal et Stripe.
- Contact : Casablanca, Maroc — email: contact@e-school.com

QUAND ON TE DEMANDE DE T'AIDER :
- Propose un rendez-vous pour test de niveau : « Je vous propose de prendre rendez-vous pour un test de niveau personnalisé ! »
- Suggère l'inscription gratuite.

SOIS ACCUEILLANT ET SYMPA, mais reste professionnel.";

        // Build conversation contents
        $contents = [];

        // Add conversation history (last 10 messages max)
        $history = array_slice($conversation, -10);
        foreach ($history as $msg) {
            $role = isset($msg['role']) && $msg['role'] === 'assistant' ? 'model' : 'user';
            $contents[] = [
                'role' => $role,
                'parts' => [
                    ['text' => $msg['content'] ?? $msg['message'] ?? ''],
                ],
            ];
        }

        // Add current user message
        $contents[] = [
            'role' => 'user',
            'parts' => [
                ['text' => $userMessage],
            ],
        ];

        $model = 'gemini-2.0-flash';

        $response = Http::timeout(30)
            ->post('https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent?key=' . $apiKey, [
                'system_instruction' => [
                    'parts' => [['text' => $systemInstruction]],
                ],
                'contents' => $contents,
                'generationConfig' => [
                    'maxOutputTokens' => 300,
                    'temperature' => 0.7,
                ],
            ]);

        if (!$response->successful()) {
            $error = $response->json();
            $errorMsg = $error['error']['message'] ?? 'Erreur API Gemini';
            throw new \Exception('Gemini API error: ' . $errorMsg);
        }

        $data = $response->json();
        return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Désolé, je n\'ai pas compris. Pouvez-vous reformuler ?';
    }
}
