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
        // 1. Découverte de l'alphabet / Débutant
        [
            'level_name'     => 'Découverte de l’alphabet',
            'class_name'     => 'Débutant',
            'title'          => 'Lecture des lettres et des mots simples',
            'instructions'   => 'Lisez lentement les lettres, les mots, puis la phrase. Prononcez chaque lettre distinctement.',
            'reading_text'   => 'أَ – بَ – تَ – ثَ – جَ – حَ – خَ – دَ – ذَ – رَ – زَ – سَ – شَ – صَ – ضَ – طَ – ظَ – عَ – غَ – فَ – قَ – كَ – لَ – مَ – نَ – هَ – وَ – يَ

كِتَاب – مَدْرَسَة – قَلَم – بَاب – نَافِذَة

جَلَسَ الْوَلَدُ عَلَى الْكُرْسِيِّ وَقَرَأَ الدَّرْسَ.',
            'maximum_duration' => 60,
        ],
        // 2. Lecture et communication / Débutant
        [
            'level_name'     => 'Lecture et communication',
            'class_name'     => 'Débutant',
            'title'          => 'Présentation personnelle',
            'instructions'   => 'Lisez le texte à voix haute en articulant bien. Faites attention aux voyelles courtes.',
            'reading_text'   => 'السَّلَامُ عَلَيْكُمْ. أَنَا تِلْمِيذٌ فِي الصَّفِّ الْأَوَّلِ. أَسْكُنُ فِي مَدِينَةِ الرِّبَاطِ. أُحِبُّ الْقِرَاءَةَ وَالْكِتَابَةَ. أَذْهَبُ إِلَى الْمَدْرَسَةِ كُلَّ يَوْمٍ. هَذِهِ حَقِيبَتِي وَهَذَا قَلَمِي.',
            'maximum_duration' => 90,
        ],
        // 3. Lecture et communication / Intermédiaire
        [
            'level_name'     => 'Lecture et communication',
            'class_name'     => 'Intermédiaire',
            'title'          => 'Ma journée quotidienne',
            'instructions'   => 'Lisez le texte avec fluidité. Respectez la ponctuation et le rythme de la lecture.',
            'reading_text'   => 'أَسْتَيْقِظُ صَبَاحًا مُبَكِّرًا، ثُمَّ أَغْسِلُ وَجْهِي وَأَتَيَضَّأُ. بَعْدَ ذَلِكَ أُصَلِّي صَلَاةَ الْفَجْرِ. أَتَنَاوَلُ فَطُورِي وَأَشْرَبُ كَأْسَ حَلِيبٍ. ثُمَّ أَذْهَبُ إِلَى الْمَدْرَسَةِ مَاشِيًا مَعَ أَصْدِقَائِي. فِي الْمَدْرَسَةِ نَدْرُسُ اللُّغَةَ الْعَرَبِيَّةَ وَالرِّيَاضِيَّاتِ وَالْعُلُومَ. أَعُودُ إِلَى الْبَيْتِ ظُهْرًا وَأَتَغَدَّى مَعَ عَائِلَتِي. بَعْدَ الظُّهْرِ أَحْفَظُ دُرُوسِي وَأَلْعَبُ مَعَ أَخِي الصَّغِيرِ.',
            'maximum_duration' => 120,
        ],
        // 4. Lecture et communication / Avancée
        [
            'level_name'     => 'Lecture et communication',
            'class_name'     => 'Avancée',
            'title'          => 'وصف رحلة',
            'instructions'   => 'Lisez le texte avec une diction claire et une intonation expressive. Faites vivre le récit.',
            'reading_text'   => 'فِي الْأُسْبُوعِ الْمَاضِي، قَامَتْ مَدْرَسَتُنَا بِرِحْلَةٍ إِلَى الْمُتْحَفِ الْوَطَنِيِّ. انْطَلَقْنَا فِي الصَّبَاحِ الْبَاكِرِ بِالْحَافِلَةِ، وَكَانَ الطَّقْسُ جَمِيلًا وَالشَّمْسُ مُشْرِقَةً. عِنْدَ وُصُولِنَا، اسْتَقْبَلَنَا مُرْشِدٌ لَطِيفٌ وَأَخَذَنَا فِي جَوْلَةٍ رَائِعَةٍ. رَأَيْنَا آثَارًا قَدِيمَةً وَأَدَوَاتٍ تَارِيخِيَّةً مُثِيرَةً. كَتَبْتُ مُلَاحَظَاتِي فِي كُرَّاسَتِي لِأَسْتَعْمِلَهَا فِي مَشْرُوعِي الْمَدْرَسِيِّ. كَانَتْ رِحْلَةً مُمْتِعَةً وَمُفِيدَةً جِدًّا.',
            'maximum_duration' => 150,
        ],
        // 5. Maîtrise intermédiaire / Intermédiaire
        [
            'level_name'     => 'Maîtrise intermédiaire',
            'class_name'     => 'Intermédiaire',
            'title'          => 'قصة قصيرة',
            'instructions'   => 'Lisez cette histoire courte avec émotion. Changez le ton selon les personnages et les événements.',
            'reading_text'   => 'كَانَ يَا مَا كَانَ فِي قَدِيمِ الزَّمَانِ، فِي قَرْيَةٍ صَغِيرَةٍ، عَاشَ فَلَّاحٌ طَيِّبٌ مَعَ أَوْلَادِهِ الثَّلَاثَةِ. كَانَ الْفَلَّاحُ يَعْمَلُ كُلَّ يَوْمٍ فِي حَقْلِهِ بِجِدٍّ وَنَشَاطٍ. وَفِي يَوْمٍ مِنَ الْأَيَّامِ، وَجَدَ الْفَلَّاحُ كَنْزًا ثَمِينًا مَدْفُونًا تَحْتَ شَجَرَةِ زَيْتُونٍ عَتِيقَةٍ. فَرِحَ الْأَوْلَادُ كَثِيرًا، وَقَرَّرُوا أَنْ يُوَزِّعُوا الْكَنْزَ عَلَى فُقَرَاءِ الْقَرْيَةِ. فَأَحَبَّهُمُ الْجَمِيعُ وَأَصْبَحُوا أَعِزَّاءَ فِي قُلُوبِ النَّاسِ.',
            'maximum_duration' => 150,
        ],
        // 6. Expression écrite et orale / Débutant
        [
            'level_name'     => 'Expression écrite et orale',
            'class_name'     => 'Débutant',
            'title'          => 'أفراد العائلة',
            'instructions'   => 'Lisez le texte en nommant chaque membre de la famille. Essayez de décrire brièvement chaque personne.',
            'reading_text'   => 'هَذِهِ عَائِلَتِي. أَبِي طَبِيبٌ يُحِبُّ الْعَمَلَ. أُمِّي رَبَّةُ بَيْتٍ طَيِّبَةٌ. لِي أَخٌ كَبِيرٌ اسْمُهُ أَحْمَدُ وَأُخْتٌ صَغِيرَةٌ اسْمُهَا مَرْيَمُ. أَحْمَدُ يَدْرُسُ فِي الْجَامِعَةِ أَمَّا مَرْيَمُ فَلَا تَزَالُ صَغِيرَةً وَتَذْهَبُ إِلَى الْحَضَانَةِ. أَنَا فَرْحَانُ بِعَائِلَتِي وَأُحِبُّهُمْ كَثِيرًا.',
            'maximum_duration' => 90,
        ],
        // 7. Expression écrite et orale / Intermédiaire
        [
            'level_name'     => 'Expression écrite et orale',
            'class_name'     => 'Intermédiaire',
            'title'          => 'هواياتي المفضلة',
            'instructions'   => 'Lisez le texte en montrant de l\'enthousiasme pour les loisirs décrits. Variez le rythme de lecture.',
            'reading_text'   => 'مِنْ هِوَايَاتِي الْمُفَضَّلَةِ قِرَاءَةُ الْكُتُبِ وَرَسْمُ اللَّوْحَاتِ وَمُمَارَسَةُ الرِّيَاضَةِ. أَقْرَأُ كُلَّ يَوْمٍ قَبْلَ النَّوْمِ نَحْوَ ثَلَاثِينَ دَقِيقَةً. أَحَبُّ كُتُبَ الْمَغَامَرَاتِ وَالْقِصَصِ الْعِلْمِيَّةِ. أَمَّا الرَّسْمُ فَأُفَضِّلُ رَسْمَ الْمَنَاظِرِ الطَّبِيعِيَّةِ وَالْبَحْرِ وَالْجِبَالِ. بِالنِّسْبَةِ لِلرِّيَاضَةِ فَأَلْعَبُ كُرَةَ الْقَدَمِ مَعَ أَصْدِقَائِي كُلَّ جُمُعَةٍ. الْهِوَايَاتُ تُسَاعِدُنِي عَلَى الِاسْتِرْخَاءِ وَتَطْوِيرِ مَهَارَاتِي.',
            'maximum_duration' => 120,
        ],
        // 8. Expression écrite et orale / Avancée
        [
            'level_name'     => 'Expression écrite et orale',
            'class_name'     => 'Avancée',
            'title'          => 'العلم وأهميته في حياتنا',
            'instructions'   => 'Lisez ce texte argumentatif avec conviction et clarté. Marquez bien les pauses et l\'intonation.',
            'reading_text'   => 'لِلْعِلْمِ أَهَمِّيَّةٌ كَبِيرَةٌ فِي حَيَاتِنَا، فَهُوَ نُورٌ يُنِيرُ الطَّرِيقَ وَيَفْتَحُ آفَاقًا جَدِيدَةً لِلْمُجْتَمَعَاتِ. بِالْعِلْمِ نَبْنِي الْحَضَارَاتِ وَنُطَوِّرُ الْعُلُومَ وَنُحَقِّقُ التَّقَدُّمَ. لَا يَقْتَصِرُ الْعِلْمُ عَلَى الْمَدَارِسِ وَالْجَامِعَاتِ فَحَسْبُ، بَلْ هُوَ رِحْلَةٌ مُسْتَمِرَّةٌ طُولَ الْحَيَاةِ. كُلُّ مَا نَرَاهُ حَوْلَنَا مِنَ التِّقْنِيَةِ وَالطِّبِّ وَالْهَنْدَسَةِ هُوَ ثَمَرَةُ الْعِلْمِ وَالْبَحْثِ. لِذَلِكَ يَجِبُ عَلَيْنَا أَنْ نُقَدِّرَ الْعِلْمَ وَالْعُلَمَاءَ وَنَسْعَى لِاكْتِسَابِ الْمَعْرِفَةِ بِاجْتِهَادٍ وَإِصْرَارٍ. فَالْعِلْمُ قُوَّةٌ وَالْجَهْلُ ضَعْفٌ.',
            'maximum_duration' => 180,
        ],
    ];

    public function run(): void
    {
        $arabicSubject = Subject::where('name', 'Arabe')->firstOrFail();

        foreach ($this->tests as $test) {
            $level = Level::query()
                ->where('subject_id', $arabicSubject->id)
                ->where(function ($query) use ($test) {
                    $partial = mb_substr($test['level_name'], 0, 15);
                    $query
                        ->where('name', 'like', '%' . $partial . '%')
                        ->orWhereRaw('LOWER(name) LIKE ?', ['%' . mb_strtolower($partial) . '%']);
                })
                ->first();

            if (!$level) {
                $this->command->warn("Niveau '{$test['level_name']}' non trouvé pour l'Arabe. Création...");
                $level = Level::create([
                    'name'        => $test['level_name'],
                    'description' => 'Niveau pour le test vocal d\'arabe',
                    'subject_id'  => $arabicSubject->id,
                    'order'       => Level::where('subject_id', $arabicSubject->id)->max('order') + 1,
                ]);
            }

            $class = ClassRoom::query()
                ->where('level_id', $level->id)
                ->where('name', $test['class_name'])
                ->first();

            if (!$class) {
                $this->command->warn("Classe '{$test['class_name']}' non trouvée pour le niveau '{$test['level_name']}'. Création...");
                $class = ClassRoom::create([
                    'name'     => $test['class_name'],
                    'level_id' => $level->id,
                ]);
            }

            // Toujours s'assurer que la classe est liée à la matière
            $class->subjects()->syncWithoutDetaching([$arabicSubject->id]);

            VocalTestPrompt::updateOrCreate(
                [
                    'subject_id' => $arabicSubject->id,
                    'level_id'   => $level->id,
                    'class_id'   => $class->id,
                ],
                [
                    'title'            => $test['title'],
                    'instructions'     => $test['instructions'],
                    'reading_text'     => $test['reading_text'],
                    'maximum_duration' => $test['maximum_duration'],
                    'is_active'        => true,
                ]
            );

            $this->command->info("✓ Test vocal créé : {$test['title']} ({$test['level_name']} / {$test['class_name']})");
        }

        $this->command->info('');
        $this->command->info('✅ ' . count($this->tests) . ' tests vocaux arabes créés avec succès !');
    }
}
