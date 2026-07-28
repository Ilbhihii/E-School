<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use App\Models\Level;
use App\Models\Subject;
use App\Models\VocalTestPrompt;
use Illuminate\Database\Seeder;

class ArabicVocalTestPromptSeeder extends Seeder
{
    private array $tests = [
        [
            'level_name' => VocalTestPrompt::ARABIC_READING_WRITING,
            'class_name' => VocalTestPrompt::CLASS_INTERMEDIATE,
            'title' => 'Lecture et compréhension d’un texte',
            'instructions' => 'Lisez le texte avec une prononciation claire, un rythme régulier et des pauses correctes.',
            'reading_text' => 'مِنْ هِوَايَاتِي الْمُفَضَّلَةِ قِرَاءَةُ الْكُتُبِ وَرَسْمُ اللَّوْحَاتِ وَمُمَارَسَةُ الرِّيَاضَةِ. أَقْرَأُ كُلَّ يَوْمٍ قَبْلَ النَّوْمِ، وَأُحَاوِلُ أَنْ أَكْتُبَ مَا تَعَلَّمْتُهُ فِي دَفْتَرِي.',
            'maximum_duration' => 120,
        ],
        [
            'level_name' => VocalTestPrompt::ARABIC_READING_WRITING,
            'class_name' => VocalTestPrompt::CLASS_ADVANCED,
            'title' => 'Lecture expressive avancée',
            'instructions' => 'Lisez ce texte argumentatif avec clarté, conviction et une intonation adaptée.',
            'reading_text' => 'لِلْعِلْمِ أَهَمِّيَّةٌ كَبِيرَةٌ فِي حَيَاتِنَا، فَهُوَ نُورٌ يُنِيرُ الطَّرِيقَ وَيَفْتَحُ آفَاقًا جَدِيدَةً لِلْمُجْتَمَعَاتِ. بِالْعِلْمِ نَبْنِي الْحَضَارَاتِ وَنُحَقِّقُ التَّقَدُّمَ.',
            'maximum_duration' => 150,
        ],
        [
            'level_name' => VocalTestPrompt::ARABIC_COMMUNICATION,
            'class_name' => VocalTestPrompt::CLASS_INTERMEDIATE,
            'title' => 'Communication dans la vie quotidienne',
            'instructions' => 'Lisez naturellement, comme si vous racontiez votre journée à une autre personne.',
            'reading_text' => 'أَسْتَيْقِظُ صَبَاحًا مُبَكِّرًا، ثُمَّ أَغْسِلُ وَجْهِي وَأَتَنَاوَلُ فَطُورِي. بَعْدَ ذَلِكَ أَذْهَبُ إِلَى الْمَدْرَسَةِ مَعَ أَصْدِقَائِي، وَفِي الْمَسَاءِ أُرَاجِعُ دُرُوسِي وَأَتَحَدَّثُ مَعَ عَائِلَتِي.',
            'maximum_duration' => 120,
        ],
        [
            'level_name' => VocalTestPrompt::ARABIC_COMMUNICATION,
            'class_name' => VocalTestPrompt::CLASS_ADVANCED,
            'title' => 'Récit et communication avancée',
            'instructions' => 'Lisez avec une diction claire et une intonation expressive. Faites vivre le récit.',
            'reading_text' => 'فِي الْأُسْبُوعِ الْمَاضِي، قَامَتْ مَدْرَسَتُنَا بِرِحْلَةٍ إِلَى الْمُتْحَفِ الْوَطَنِيِّ. انْطَلَقْنَا فِي الصَّبَاحِ الْبَاكِرِ، وَعِنْدَ وُصُولِنَا اسْتَقْبَلَنَا مُرْشِدٌ وَأَخَذَنَا فِي جَوْلَةٍ مُفِيدَةٍ.',
            'maximum_duration' => 150,
        ],
    ];

    public function run(): void
    {
        $subject = Subject::where('name', 'Arabe')->firstOrFail();

        // Désactiver les anciens tests, y compris tous les tests Débutant.
        VocalTestPrompt::where('subject_id', $subject->id)
            ->update(['is_active' => false]);

        foreach ($this->tests as $test) {
            $level = Level::firstOrCreate(
                [
                    'subject_id' => $subject->id,
                    'name' => $test['level_name'],
                ],
                [
                    'description' => 'Parcours d’apprentissage de l’arabe.',
                    'order' => $test['level_name']
                        === VocalTestPrompt::ARABIC_READING_WRITING
                            ? 1
                            : 2,
                ]
            );

            $classRoom = ClassRoom::firstOrCreate([
                'level_id' => $level->id,
                'name' => $test['class_name'],
            ]);

            $classRoom->subjects()->syncWithoutDetaching([
                $subject->id,
            ]);

            VocalTestPrompt::updateOrCreate(
                [
                    'subject_id' => $subject->id,
                    'level_id' => $level->id,
                    'class_id' => $classRoom->id,
                ],
                [
                    'title' => $test['title'],
                    'instructions' => $test['instructions'],
                    'reading_text' => $test['reading_text'],
                    'test_mode' => VocalTestPrompt::MODE_READING,
                    'preparation_seconds' => 0,
                    'maximum_duration' => $test['maximum_duration'],
                    'hide_text_during_recording' => false,
                    'is_active' => true,
                ]
            );
        }

        $this->command->info(
            '4 tests d’Arabe créés. Aucun test n’est créé pour Débutant.'
        );
    }
}
