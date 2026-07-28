<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class VocalTestPrompt extends Model
{
    public const MODE_READING = 'reading';
    public const MODE_TAJWID = 'tajwid';
    public const MODE_HIFD = 'hifd';

    public const ARABIC_READING_WRITING = 'Lecture & Écriture';
    public const ARABIC_COMMUNICATION = 'Communication';
    public const QURAN_LEARNING_TAJWID = 'Apprentissage & Tajwid';

    public const CLASS_BEGINNER = 'Débutant';
    public const CLASS_INTERMEDIATE = 'Intermédiaire';
    public const CLASS_ADVANCED = 'Avancé';

    protected $fillable = [
        'subject_id',
        'level_id',
        'class_id',
        'title',
        'instructions',
        'reading_text',
        'test_mode',
        'preparation_seconds',
        'maximum_duration',
        'hide_text_during_recording',
        'is_active',
    ];

    protected $casts = [
        'preparation_seconds' => 'integer',
        'maximum_duration' => 'integer',
        'hide_text_during_recording' => 'boolean',
        'is_active' => 'boolean',
    ];

    public static function getModes(): array
    {
        return [
            self::MODE_READING => 'Lecture',
            self::MODE_TAJWID => 'Tajwid',
            self::MODE_HIFD => 'Hifd (Mémorisation)',
        ];
    }

    /**
     * Retourne les parcours autorisés par matière selon la structure validée.
     */
    public static function pathNamesForSubject(Subject $subject): array
    {
        $subjectName = self::normalizePathName($subject->name);

        if ($subjectName === 'arabe') {
            return [
                self::ARABIC_READING_WRITING,
                self::ARABIC_COMMUNICATION,
            ];
        }

        if (in_array($subjectName, ['coran', 'quran', 'القران'], true)) {
            return [
                self::QURAN_LEARNING_TAJWID,
            ];
        }

        return [];
    }

    /**
     * Les trois classes autorisées à l'intérieur de chaque parcours.
     */
    public static function allowedClassNames(): array
    {
        return [
            self::CLASS_BEGINNER,
            self::CLASS_INTERMEDIATE,
            self::CLASS_ADVANCED,
        ];
    }

    public static function isSupportedLevel(Subject $subject, Level $level): bool
    {
        if ((int) $level->subject_id !== (int) $subject->id) {
            return false;
        }

        $allowedLevels = array_map(
            [self::class, 'normalizePathName'],
            self::pathNamesForSubject($subject)
        );

        return in_array(
            self::normalizePathName($level->name),
            $allowedLevels,
            true
        );
    }

    public static function isSupportedPath(
        Subject $subject,
        Level $level,
        ClassRoom $classRoom
    ): bool {
        if (!self::isSupportedLevel($subject, $level)) {
            return false;
        }

        if ((int) $classRoom->level_id !== (int) $level->id) {
            return false;
        }

        $allowedClasses = array_map(
            [self::class, 'normalizePathName'],
            self::allowedClassNames()
        );

        return in_array(
            self::normalizePathName($classRoom->name),
            $allowedClasses,
            true
        );
    }

    /**
     * Première remarque :
     * aucun test vocal pour les deux parcours d'Arabe en classe Débutant.
     */
    public static function isExcludedPath(
        Subject $subject,
        Level $level,
        ClassRoom $classRoom
    ): bool {
        if (!self::isSupportedPath($subject, $level, $classRoom)) {
            return false;
        }

        if (self::normalizePathName($subject->name) !== 'arabe') {
            return false;
        }

        return self::normalizePathName($classRoom->name) === 'debutant';
    }

    public static function activeForPath(
        Subject $subject,
        Level $level,
        ClassRoom $classRoom
    ): ?self {
        if (
            !self::isSupportedPath($subject, $level, $classRoom)
            || self::isExcludedPath($subject, $level, $classRoom)
        ) {
            return null;
        }

        return self::query()
            ->where('subject_id', $subject->id)
            ->where('level_id', $level->id)
            ->where('class_id', $classRoom->id)
            ->where('is_active', true)
            ->first();
    }

    public static function requiresVocalTest(
        Subject $subject,
        Level $level,
        ClassRoom $classRoom
    ): bool {
        return self::activeForPath($subject, $level, $classRoom) !== null;
    }

    public static function normalizePathName(?string $value): string
    {
        $value = str_replace(
            ['’', "'", '-', '_', '&'],
            [' ', ' ', ' ', ' ', ' et '],
            (string) $value
        );

        $value = Str::lower(Str::ascii($value));

        return trim((string) preg_replace('/\s+/', ' ', $value));
    }

    /**
     * Le niveau Coran avancé utilise un exercice interactif de complétion
     * au lieu d'un enregistrement vocal.
     */
    public static function isInteractiveCompletionPath(
        Subject $subject,
        Level $level,
        ClassRoom $classRoom,
        ?self $prompt = null
    ): bool {
        if (!self::isSupportedPath($subject, $level, $classRoom)) {
            return false;
        }

        $isQuran = in_array(
            self::normalizePathName($subject->name),
            ['coran', 'quran', 'القران'],
            true
        );

        $isTajwidPath =
            self::normalizePathName($level->name)
            === self::normalizePathName(self::QURAN_LEARNING_TAJWID);

        $isAdvanced =
            self::normalizePathName($classRoom->name)
            === self::normalizePathName(self::CLASS_ADVANCED);

        $promptIsCompatible =
            $prompt === null
            || $prompt->test_mode === self::MODE_HIFD;

        return $isQuran
            && $isTajwidPath
            && $isAdvanced
            && $promptIsCompatible;
    }

    /**
     * Définition centralisée de l'exercice à trous.
     *
     * Les choix contiennent les quatre réponses correctes et quatre
     * propositions supplémentaires.
     */
    public static function completionDefinition(): array
    {
        return [
            'expected_answers' => [
                'سَمَاوَاتٍ',
                'تَفَاوُتٍ',
                'كَرَّتَيْنِ',
                'خَاسِئًا',
            ],

            'choices' => [
                'سَمَاوَاتٍ',
                'تَفَاوُتٍ',
                'كَرَّتَيْنِ',
                'خَاسِئًا',
                'نُجُومٍ',
                'اخْتِلَافٍ',
                'مَرَّتَيْنِ',
                'حَسِيرًا',
            ],

            'verses' => [
                [
                    ['text' => 'الَّذِي خَلَقَ سَبْعَ '],
                    ['slot' => 0],
                    ['text' => ' طِبَاقًا ۖ مَّا تَرَىٰ فِي خَلْقِ الرَّحْمَٰنِ مِن '],
                    ['slot' => 1],
                    ['text' => ' ۖ فَارْجِعِ الْبَصَرَ هَلْ تَرَىٰ مِن فُطُورٍ ﴿٣﴾'],
                ],
                [
                    ['text' => 'ثُمَّ ارْجِعِ الْبَصَرَ '],
                    ['slot' => 2],
                    ['text' => ' يَنْقَلِبْ إِلَيْكَ الْبَصَرُ '],
                    ['slot' => 3],
                    ['text' => ' وَهُوَ حَسِيرٌ ﴿٤﴾'],
                ],
            ],
        ];
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(
            VocalTestSubmission::class,
            'vocal_test_prompt_id'
        );
    }
}