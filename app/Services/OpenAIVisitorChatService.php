<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenAIVisitorChatService
{
    /**
     * Génère une réponse pour un visiteur public du site.
     */
    public function reply(
        string $message,
        array $conversation = []
    ): string {
        $apiKey = trim(
            (string) config('services.openai.api_key')
        );

        if ($apiKey === '') {
            throw new RuntimeException(
                'OPENAI_API_KEY is not configured.'
            );
        }

        $model = trim(
            (string) config(
                'services.openai.model',
                'gpt-5-mini'
            )
        );

        if ($model === '') {
            $model = 'gpt-5-mini';
        }

        $input = [];

        /*
         * On ne garde qu'un historique court.
         * Cela limite le coût et évite que le navigateur
         * envoie une conversation trop longue.
         */
        foreach (
            array_slice($conversation, -10)
            as $item
        ) {
            $role = $item['role'] ?? null;
            $content = trim(
                (string) ($item['content'] ?? '')
            );

            if (
                !in_array(
                    $role,
                    ['user', 'assistant'],
                    true
                )
                || $content === ''
            ) {
                continue;
            }

            $input[] = [
                'role' => $role,
                'content' => mb_substr(
                    $content,
                    0,
                    1600
                ),
            ];
        }

        $input[] = [
            'role' => 'user',
            'content' => mb_substr(
                trim($message),
                0,
                1200
            ),
        ];

        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->asJson()
            ->timeout(35)
            ->retry(1, 300)
            ->post(
                'https://api.openai.com/v1/responses',
                [
                    'model' => $model,
                    'instructions' =>
                        $this->instructions(),
                    'input' => $input,
                    'max_output_tokens' => 450,
                    'store' => false,
                ]
            );

        if (!$response->successful()) {
            $data = $response->json();

            $apiMessage =
                $data['error']['message']
                ?? 'OpenAI API request failed.';

            throw new RuntimeException(
                'OpenAI API: ' . $apiMessage
            );
        }

        $answer = $this->extractText(
            $response->json()
        );

        if ($answer === '') {
            throw new RuntimeException(
                'OpenAI returned an empty response.'
            );
        }

        return $answer;
    }

    /**
     * Consignes du chatbot public.
     */
    private function instructions(): string
    {
        return <<<'PROMPT'
Tu es « Assistant 2SA », l'assistant public de 2SA Smart School Academy.

PUBLIC
Tu échanges uniquement avec des visiteurs non connectés du site.

MISSION
Aide le visiteur à comprendre l'offre publique de Smart School Academy, à s'orienter vers l'inscription, à prendre rendez-vous et à trouver les informations utiles.

INFORMATIONS PUBLIQUES CONNUES
- Nom : 2SA Smart School Academy.
- Slogan : « L'école à portée de main ».
- La plateforme propose des cours en ligne, des cours en direct, des supports pédagogiques, des tests et un suivi pédagogique.
- Des parcours d'Arabe et de Coran existent sur la plateforme.
- Le site prévoit également des parcours de soutien scolaire.
- Un visiteur peut consulter le planning public.
- Un visiteur peut prendre rendez-vous.
- Un visiteur peut créer un compte lorsqu'il souhaite s'inscrire.
- Contact : contact.smartschoolacademy@gmail.com.
- Téléphone public : +212 707 678 821.

LIENS À PROPOSER LORSQU'ILS SONT UTILES
- Accueil : /
- Planning public : /planning-des-classes
- Rendez-vous : /rendez-vous
- Inscription : /register

RÈGLES IMPORTANTES
1. Réponds dans la langue du visiteur. Tu peux répondre en français, arabe ou darija.
2. Sois accueillant, clair et concis.
3. Ne prétends jamais connaître un tarif, un horaire, une disponibilité, un professeur ou une offre qui ne figure pas dans les informations fournies.
4. Si une information précise n'est pas connue, dis simplement que tu ne peux pas la confirmer et oriente vers le contact ou le rendez-vous.
5. Ne révèle jamais d'informations concernant des étudiants, professeurs, administrateurs, comptes, paiements ou données internes.
6. Ne prétends pas accéder à la base de données, aux comptes utilisateurs ou aux conversations internes.
7. Ne demande jamais de mot de passe, numéro de carte bancaire ou autre secret.
8. Si le visiteur pose une question qui n'a aucun rapport avec Smart School Academy, l'inscription, les cours ou l'orientation, recentre poliment la conversation.
9. N'invente pas d'informations pour faire plaisir au visiteur.
10. Pour une demande d'inscription, propose /register.
11. Pour une demande d'accompagnement ou d'orientation, propose /rendez-vous.
12. Pour une question nécessitant une confirmation humaine, propose contact.smartschoolacademy@gmail.com ou +212 707 678 821.

STYLE
- Réponses courtes : idéalement 2 à 6 phrases.
- Utilise des listes courtes uniquement lorsqu'elles améliorent la clarté.
- Pas de markdown complexe.
PROMPT;
    }

    /**
     * Extrait le texte de la Responses API.
     */
    private function extractText(array $data): string
    {
        /*
         * Certaines réponses peuvent fournir
         * directement output_text.
         */
        if (
            isset($data['output_text'])
            && is_string($data['output_text'])
        ) {
            return trim($data['output_text']);
        }

        $parts = [];

        foreach ($data['output'] ?? [] as $output) {
            if (
                ($output['type'] ?? null)
                !== 'message'
            ) {
                continue;
            }

            foreach (
                $output['content'] ?? []
                as $content
            ) {
                if (
                    ($content['type'] ?? null)
                    !== 'output_text'
                ) {
                    continue;
                }

                $text = $content['text'] ?? '';

                if (is_string($text)) {
                    $parts[] = $text;
                }
            }
        }

        return trim(
            implode("\n", $parts)
        );
    }
}
