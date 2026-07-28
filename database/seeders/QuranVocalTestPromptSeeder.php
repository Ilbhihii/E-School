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
        // 1. Apprendre les règles / Débutant
        [
            'level_name'     => 'Apprendre les règles',
            'class_name'     => 'Débutant',
            'title'          => 'Lecture guidée de la sourate Al-Fatiha',
            'instructions'   => 'Lisez la sourate Al-Fatiha en appliquant les règles de base du tajwid : prolongement (madd), nasalisation (ghunna), et prononciation claire de chaque lettre.',
            'reading_text'   => 'بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ ﴿١﴾ الْحَمْدُ لِلَّهِ رَبِّ الْعَالَمِينَ ﴿٢﴾ الرَّحْمَنِ الرَّحِيمِ ﴿٣﴾ مَالِكِ يَوْمِ الدِّينِ ﴿٤﴾ إِيَّاكَ نَعْبُدُ وَإِيَّاكَ نَسْتَعِينُ ﴿٥﴾ اهْدِنَا الصِّرَاطَ الْمُسْتَقِيمَ ﴿٦﴾ صِرَاطَ الَّذِينَ أَنْعَمْتَ عَلَيْهِمْ غَيْرِ الْمَغْضُوبِ عَلَيْهِمْ وَلَا الضَّالِّينَ ﴿٧﴾',
            'test_mode'               => 'tajwid',
            'preparation_seconds'     => 0,
            'maximum_duration'        => 90,
            'hide_text_during_recording' => false,
        ],
        // 2. Apprendre les règles / Intermédiaire
        [
            'level_name'     => 'Apprendre les règles',
            'class_name'     => 'Intermédiaire',
            'title'          => 'Application des règles dans la sourate Al-Falaq',
            'instructions'   => 'Récitez la sourate Al-Falaq en appliquant consciencieusement les règles du tajwid : madd, qalqalah, idgham, et ikhfa. Faites attention aux arrêts et aux reprises.',
            'reading_text'   => 'بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ ﴿١﴾ قُلْ أَعُوذُ بِرَبِّ الْفَلَقِ ﴿٢﴾ مِن شَرِّ مَا خَلَقَ ﴿٣﴾ وَمِن شَرِّ غَاسِقٍ إِذَا وَقَبَ ﴿٤﴾ وَمِن شَرِّ النَّفَّاثَاتِ فِي الْعُقَدِ ﴿٥﴾ وَمِن شَرِّ حَاسِدٍ إِذَا حَسَدَ ﴿٦﴾',
            'test_mode'               => 'tajwid',
            'preparation_seconds'     => 0,
            'maximum_duration'        => 90,
            'hide_text_during_recording' => false,
        ],
        // 3. Apprendre les règles / Avancée
        [
            'level_name'     => 'Apprendre les règles',
            'class_name'     => 'Avancée',
            'title'          => 'Lecture avancée avec règles de tajwid',
            'instructions'   => 'Lisez ces versets de la sourate Al-Ikhlas et des deux premiers versets de la sourate Al-Masad avec une application complète et précise des règles avancées du tajwid. Soignez votre prononciation et votre rythme.',
            'reading_text'   => 'بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ ﴿١﴾ قُلْ هُوَ اللَّهُ أَحَدٌ ﴿٢﴾ اللَّهُ الصَّمَدُ ﴿٣﴾ لَمْ يَلِدْ وَلَمْ يُولَدْ ﴿٤﴾ وَلَمْ يَكُن لَّهُ كُفُوًا أَحَدٌ ﴿٥﴾

بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ ﴿١﴾ تَبَّتْ يَدَا أَبِي لَهَبٍ وَتَبَّ ﴿٢﴾ مَا أَغْنَىٰ عَنْهُ مَالُهُ وَمَا كَسَبَ ﴿٣﴾',
            'test_mode'               => 'tajwid',
            'preparation_seconds'     => 0,
            'maximum_duration'        => 120,
            'hide_text_during_recording' => false,
        ],
        // 4. Tajwid et Hifd / Débutant
        [
            'level_name'     => 'Tajwid et Hifd',
            'class_name'     => 'Débutant',
            'title'          => 'Récitation de mémoire de la sourate Al-Ikhlas',
            'instructions'   => 'Récitez la sourate Al-Ikhlas de mémoire en appliquant les règles de tajwid.',
            'reading_text'   => 'بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ ﴿١﴾ قُلْ هُوَ اللَّهُ أَحَدٌ ﴿٢﴾ اللَّهُ الصَّمَدُ ﴿٣﴾ لَمْ يَلِدْ وَلَمْ يُولَدْ ﴿٤﴾ وَلَمْ يَكُن لَّهُ كُفُوًا أَحَدٌ ﴿٥﴾',
            'test_mode'               => 'hifd',
            'preparation_seconds'     => 20,
            'maximum_duration'        => 60,
            'hide_text_during_recording' => true,
        ],
        // 5. Tajwid et Hifd / Intermédiaire
        [
            'level_name'     => 'Tajwid et Hifd',
            'class_name'     => 'Intermédiaire',
            'title'          => 'Récitation de mémoire des sourates Al-Falaq et An-Nas',
            'instructions'   => 'Récitez ces deux sourates de mémoire en respectant les règles du tajwid.',
            'reading_text'   => 'بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ ﴿١﴾ قُلْ أَعُوذُ بِرَبِّ الْفَلَقِ ﴿٢﴾ مِن شَرِّ مَا خَلَقَ ﴿٣﴾ وَمِن شَرِّ غَاسِقٍ إِذَا وَقَبَ ﴿٤﴾ وَمِن شَرِّ النَّفَّاثَاتِ فِي الْعُقَدِ ﴿٥﴾ وَمِن شَرِّ حَاسِدٍ إِذَا حَسَدَ ﴿٦﴾

بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ ﴿١﴾ قُلْ أَعُوذُ بِرَبِّ النَّاسِ ﴿٢﴾ مَلِكِ النَّاسِ ﴿٣﴾ إِلَٰهِ النَّاسِ ﴿٤﴾ مِن شَرِّ الْوَسْوَاسِ الْخَنَّاسِ ﴿٥﴾ الَّذِي يُوَسْوِسُ فِي صُدُورِ النَّاسِ ﴿٦﴾ مِنَ الْجِنَّةِ وَالنَّاسِ ﴿٧﴾',
            'test_mode'               => 'hifd',
            'preparation_seconds'     => 30,
            'maximum_duration'        => 120,
            'hide_text_during_recording' => true,
        ],
        // 6. Tajwid et Hifd / Avancée
        [
            'level_name'     => 'Tajwid et Hifd',
            'class_name'     => 'Avancée',
            'title'          => 'Récitation de mémoire des premiers versets de la sourate Al-Mulk',
            'instructions'   => 'Récitez ces versets de mémoire avec une application rigoureuse des règles avancées du tajwid.',
            'reading_text'   => 'بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ ﴿١﴾ تَبَارَكَ الَّذِي بِيَدِهِ الْمُلْكُ وَهُوَ عَلَىٰ كُلِّ شَيْءٍ قَدِيرٌ ﴿٢﴾ الَّذِي خَلَقَ الْمَوْتَ وَالْحَيَاةَ لِيَبْلُوَكُمْ أَيُّكُمْ أَحْسَنُ عَمَلًا ۚ وَهُوَ الْعَزِيزُ الْغَفُورُ ﴿٣﴾ الَّذِي خَلَقَ سَبْعَ سَمَاوَاتٍ طِبَاقًا ۖ مَّا تَرَىٰ فِي خَلْقِ الرَّحْمَٰنِ مِن تَفَاوُتٍ ۖ فَارْجِعِ الْبَصَرَ هَلْ تَرَىٰ مِن فُطُورٍ ﴿٤﴾',
            'test_mode'               => 'hifd',
            'preparation_seconds'     => 45,
            'maximum_duration'        => 180,
            'hide_text_during_recording' => true,
        ],
    ];

    public function run(): void
    {
        $quran = Subject::where('name', 'Coran')->firstOrFail();

        foreach ($this->tests as $test) {
            $level = Level::query()
                ->where('subject_id', $quran->id)
                ->where(function ($query) use ($test) {
                    $partial = mb_substr($test['level_name'], 0, 15);
                    $query->where('name', 'like', '%' . $partial . '%')
                          ->orWhereRaw('LOWER(name) LIKE ?', ['%' . mb_strtolower($partial) . '%']);
                })
                ->first();

            if (!$level) {
                $this->command->warn("Niveau '{$test['level_name']}' non trouvé pour le Coran. Création...");
                $level = Level::create([
                    'name'        => $test['level_name'],
                    'description' => 'Niveau pour le test vocal du Coran',
                    'subject_id'  => $quran->id,
                    'order'       => Level::where('subject_id', $quran->id)->max('order') + 1,
                ]);
            }

            $class = ClassRoom::query()
                ->where('level_id', $level->id)
                ->where('name', $test['class_name'])
                ->first();

            if (!$class) {
                $this->command->warn("Classe '{$test['class_name']}' non trouvée pour '{$test['level_name']}'. Création...");
                $class = ClassRoom::create([
                    'name'     => $test['class_name'],
                    'level_id' => $level->id,
                ]);
            }

            // Toujours s'assurer que la classe est liée à la matière
            $class->subjects()->syncWithoutDetaching([$quran->id]);

            VocalTestPrompt::updateOrCreate(
                [
                    'subject_id' => $quran->id,
                    'level_id'   => $level->id,
                    'class_id'   => $class->id,
                ],
                [
                    'title'                      => $test['title'],
                    'instructions'               => $test['instructions'],
                    'reading_text'               => $test['reading_text'],
                    'test_mode'                  => $test['test_mode'],
                    'preparation_seconds'        => $test['preparation_seconds'],
                    'maximum_duration'           => $test['maximum_duration'],
                    'hide_text_during_recording' => $test['hide_text_during_recording'],
                    'is_active'                  => true,
                ]
            );

            $this->command->info("✓ Test Coran créé : {$test['title']} ({$test['level_name']} / {$test['class_name']}) [{$test['test_mode']}]");
        }

        $this->command->info('');
        $this->command->info('✅ ' . count($this->tests) . ' tests vocaux du Coran créés avec succès !');
    }
}
