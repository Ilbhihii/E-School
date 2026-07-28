<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use App\Models\Level;
use App\Models\Subject;
use App\Models\VocalTestPrompt;
use Illuminate\Database\Seeder;

class QuranVocalTestPromptSeeder extends Seeder
{
    private array $tests = [
        [
            'class_name' => VocalTestPrompt::CLASS_BEGINNER,
            'title' => 'Lecture guidée de la sourate Al-Fatiha',
            'instructions' => 'Lisez la sourate Al-Fatiha en appliquant les règles de base du tajwid.',
            'reading_text' => 'بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ ﴿١﴾ الْحَمْدُ لِلَّهِ رَبِّ الْعَالَمِينَ ﴿٢﴾ الرَّحْمَنِ الرَّحِيمِ ﴿٣﴾ مَالِكِ يَوْمِ الدِّينِ ﴿٤﴾ إِيَّاكَ نَعْبُدُ وَإِيَّاكَ نَسْتَعِينُ ﴿٥﴾ اهْدِنَا الصِّرَاطَ الْمُسْتَقِيمَ ﴿٦﴾ صِرَاطَ الَّذِينَ أَنْعَمْتَ عَلَيْهِمْ غَيْرِ الْمَغْضُوبِ عَلَيْهِمْ وَلَا الضَّالِّينَ ﴿٧﴾',
            'test_mode' => VocalTestPrompt::MODE_TAJWID,
            'preparation_seconds' => 0,
            'maximum_duration' => 90,
            'hide_text_during_recording' => false,
        ],
        [
            'class_name' => VocalTestPrompt::CLASS_INTERMEDIATE,
            'title' => 'Lecture de deux versets avec les règles du tajwid',
            'instructions' => 'Lisez les deux versets affichés. Le but n’est pas une récitation parfaite : nous vérifions surtout que vous savez lire, respecter les prolongements (madd), reconnaître le soukoun et vous arrêter correctement. Quelques erreurs sont acceptées.',
            'reading_text' => 'تَبَارَكَ الَّذِي بِيَدِهِ الْمُلْكُ وَهُوَ عَلَىٰ كُلِّ شَيْءٍ قَدِيرٌ ﴿١﴾

الَّذِي خَلَقَ الْمَوْتَ وَالْحَيَاةَ لِيَبْلُوَكُمْ أَيُّكُمْ أَحْسَنُ عَمَلًا ۚ وَهُوَ الْعَزِيزُ الْغَفُورُ ﴿٢﴾',
            'test_mode' => VocalTestPrompt::MODE_TAJWID,
            'preparation_seconds' => 0,
            'maximum_duration' => 90,
            'hide_text_during_recording' => false,
        ],
        [
            'class_name' => VocalTestPrompt::CLASS_ADVANCED,
            'title' => 'Compléter les mots manquants dans deux versets',
            'instructions' => 'Choisissez les mots corrects parmi les cartes proposées. Cliquez sur une carte pour l’ajouter automatiquement au premier espace vide, ou glissez-la vers l’espace de votre choix. Chaque carte ne peut être utilisée qu’une seule fois.',
            'reading_text' => 'الَّذِي خَلَقَ سَبْعَ ــــــــــــــ طِبَاقًا ۖ مَّا تَرَىٰ فِي خَلْقِ الرَّحْمَٰنِ مِن ــــــــــــــ ۖ فَارْجِعِ الْبَصَرَ هَلْ تَرَىٰ مِن فُطُورٍ ﴿٣﴾

ثُمَّ ارْجِعِ الْبَصَرَ ــــــــــــــ يَنْقَلِبْ إِلَيْكَ الْبَصَرُ ــــــــــــــ وَهُوَ حَسِيرٌ ﴿٤﴾',
            'test_mode' => VocalTestPrompt::MODE_HIFD,
            'preparation_seconds' => 0,
            'maximum_duration' => 120,
            'hide_text_during_recording' => false,
        ],
    ];

    public function run(): void
    {
        $subject = Subject::where('name', 'Coran')->firstOrFail();

        VocalTestPrompt::where('subject_id', $subject->id)
            ->update(['is_active' => false]);

        $level = Level::firstOrCreate(
            [
                'subject_id' => $subject->id,
                'name' => VocalTestPrompt::QURAN_LEARNING_TAJWID,
            ],
            [
                'description' => 'Comprendre, appliquer les règles du tajwid et mémoriser.',
                'order' => 1,
            ]
        );

        foreach ($this->tests as $test) {
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
                    'test_mode' => $test['test_mode'],
                    'preparation_seconds' => $test['preparation_seconds'],
                    'maximum_duration' => $test['maximum_duration'],
                    'hide_text_during_recording' => $test['hide_text_during_recording'],
                    'is_active' => true,
                ]
            );
        }

        $this->command->info(
            '3 tests du Coran créés pour Apprentissage & Tajwid.'
        );
    }
}