<?php

namespace App\Services;

use App\Models\Question;
use App\Models\Answer;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;
use PhpOffice\PhpWord\IOFactory;

class QCMGenerator
{
    /**
     * Extrait le texte d'un fichier
     */
    public function extractTextFromFile($file)
    {
        try {
            $extension = $file->getClientOriginalExtension();
            $text = '';

            if ($extension === 'pdf') {
                $parser = new Parser();
                $pdf = $parser->parseFile($file);
                $text = $pdf->getText();
            } elseif (in_array($extension, ['doc', 'docx'])) {
                $phpWord = IOFactory::load($file);
                foreach ($phpWord->getSections() as $section) {
                    foreach ($section->getElements() as $element) {
                        if (method_exists($element, 'getText')) {
                            $text .= $element->getText() . "\n";
                        }
                    }
                }
            } elseif ($extension === 'txt') {
                $text = file_get_contents($file);
            }

            return trim($text);
        } catch (\Exception $e) {
            Log::error('File extraction error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Génère un QCM intelligent à partir du texte
     */
    public function generateFromText($text)
    {
        $questions = [];
        
        // Nettoyer et préparer le texte
        $text = preg_replace('/\s+/', ' ', $text);
        $sentences = $this->extractImportantSentences($text);
        
        if (empty($sentences)) {
            return null;
        }
        
        foreach ($sentences as $index => $sentence) {
            // Extraire les mots-clés de la phrase
            $keywords = $this->extractKeywords($sentence);
            
            if (empty($keywords)) continue;
            
            // Générer la question
            $questionText = $this->generateQuestion($sentence, $keywords);
            
            // Générer les options (une correcte, trois incorrectes)
            $options = $this->generateOptions($sentence, $keywords);
            
            // Mélanger les options pour éviter les patterns
            $correctIndex = 0; // Index de la bonne réponse après mélange
            $shuffledOptions = $this->shuffleWithTracking($options, $correctIndex);
            
            $questions[] = [
                'text' => $questionText,
                'options' => $shuffledOptions['options'],
                'correct' => [$shuffledOptions['correct_index']]
            ];
            
            // Limiter à 10 questions
            if (count($questions) >= 10) break;
        }
        
        return $questions;
    }
    
    /**
     * Extrait les phrases importantes du texte
     */
    private function extractImportantSentences($text)
    {
        // Découper en phrases
        $sentences = preg_split('/[.!?]+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        
        // Filtrer les phrases trop courtes
        $sentences = array_filter($sentences, function($s) {
            return strlen(trim($s)) > 30;
        });
        
        // Retirer les doublons
        $sentences = array_unique($sentences);
        
        // Réindexer
        return array_values($sentences);
    }
    
    /**
     * Extrait les mots-clés d'une phrase
     */
    private function extractKeywords($sentence)
    {
        // Mots vides à ignorer
        $stopWords = ['le', 'la', 'les', 'un', 'une', 'des', 'et', 'ou', 'mais', 'donc', 'car', 'ce', 'cet', 'cette', 'ces', 'mon', 'ton', 'son', 'notre', 'votre', 'leur', 'dans', 'sur', 'sous', 'avec', 'sans', 'par', 'pour'];
        
        $words = explode(' ', strtolower($sentence));
        $keywords = array_filter($words, function($word) use ($stopWords) {
            return strlen($word) > 3 && !in_array($word, $stopWords);
        });
        
        // Prendre les 3-5 premiers mots-clés
        return array_slice(array_unique($keywords), 0, 5);
    }
    
    /**
     * Génère une question à partir de la phrase
     */
    private function generateQuestion($sentence, $keywords)
    {
        $templates = [
            "Selon le texte, que signifie : \"%s\" ?",
            "D'après le contenu, \"%s\" fait référence à quoi ?",
            "Quelle affirmation concernant \"%s\" est correcte ?",
            "Le texte indique que \"%s\" :",
            "Parmi les propositions suivantes, laquelle correspond à \"%s\" ?"
        ];
        
        $keyPhrase = implode(' ', array_slice($keywords, 0, 3));
        $template = $templates[array_rand($templates)];
        
        return sprintf($template, $keyPhrase);
    }
    
    /**
     * Génère les options pour une question
     */
    private function generateOptions($sentence, $keywords)
    {
        // Option correcte (basée sur la phrase originale)
        $correctOption = $this->truncateText($sentence, 80);
        
        // Générer des options incorrectes
        $incorrectOptions = [];
        
        // Option 1: Contradiction
        $incorrectOptions[] = $this->generateContradiction($sentence);
        
        // Option 2: Généralisation excessive
        $incorrectOptions[] = $this->generateOvergeneralization($sentence);
        
        // Option 3: Option neutre
        $incorrectOptions[] = "Information non mentionnée dans le texte";
        
        // Mélanger les options
        $allOptions = array_merge([$correctOption], $incorrectOptions);
        shuffle($allOptions);
        
        return $allOptions;
    }
    
    /**
     * Génère une contradiction de la phrase originale
     */
    private function generateContradiction($sentence)
    {
        $negations = ['ne...pas', 'ne...jamais', 'sans', 'sauf'];
        $prefixes = ['Contrairement à', 'À l\'opposé de', 'L\'inverse de'];
        
        $prefix = $prefixes[array_rand($prefixes)];
        $negation = $negations[array_rand($negations)];
        
        return $prefix . ' ' . $this->truncateText($sentence, 60) . ' ' . $negation;
    }
    
    /**
     * Génère une généralisation excessive
     */
    private function generateOvergeneralization($sentence)
    {
        $generalizers = ['Toujours', 'Jamais', 'Dans tous les cas', 'Systématiquement', 'À chaque fois'];
        
        $generalizer = $generalizers[array_rand($generalizers)];
        
        return $generalizer . ', ' . $this->truncateText($sentence, 60);
    }
    
    /**
     * Mélange les options et garde trace de l'index correct
     */
    private function shuffleWithTracking($options, $correctIndex)
    {
        $correctValue = $options[$correctIndex];
        shuffle($options);
        
        $newCorrectIndex = array_search($correctValue, $options);
        
        return [
            'options' => $options,
            'correct_index' => $newCorrectIndex
        ];
    }
    
    /**
     * Tronque un texte à une longueur maximale
     */
    private function truncateText($text, $maxLength)
    {
        if (strlen($text) <= $maxLength) {
            return $text;
        }
        
        return substr($text, 0, $maxLength) . '...';
    }
    
    /**
     * Sauvegarde les questions dans la base de données
     */
    public function saveToTest($questionsData, $testId)
    {
        if (empty($questionsData)) {
            return false;
        }
        
        foreach ($questionsData as $qData) {
            if (!isset($qData['text']) || !isset($qData['options'])) {
                continue;
            }
            
            $question = Question::create([
                'test_id' => $testId,
                'question' => $qData['text']
            ]);
            
            foreach ($qData['options'] as $index => $optionText) {
                Answer::create([
                    'question_id' => $question->id,
                    'answer' => $optionText,
                    'is_correct' => in_array($index, $qData['correct'] ?? [])
                ]);
            }
        }
        
        return true;
    }
}