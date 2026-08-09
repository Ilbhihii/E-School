@props([
    'items' => collect(),
    'title' => 'Créneaux des classes',
    'showTeacher' => false,
])

@php
    $sourceItems = collect($items ?? []);

    $normalizedRows = $sourceItems
        ->map(function ($item) {
            $isArray = is_array($item);

            $scheduleId = $isArray
                ? ($item['schedule_id'] ?? $item['id'] ?? null)
                : ($item->id ?? null);

            $startObject = $isArray
                ? ($item['start'] ?? null)
                : ($item->start_time ?? null);

            $endObject = $isArray
                ? ($item['end'] ?? null)
                : ($item->end_time ?? null);

            $dayOfWeek = $isArray
                ? ($item['day_of_week'] ?? null)
                : ($item->day_of_week ?? null);

            if (!$dayOfWeek && $startObject instanceof \Carbon\CarbonInterface) {
                $dayOfWeek = $startObject->dayOfWeekIso;
            }

            $dayLabel = $isArray
                ? ($item['day_label'] ?? null)
                : ($item->day_label ?? null);

            if (!$dayLabel && $startObject instanceof \Carbon\CarbonInterface) {
                $dayLabel = ucfirst($startObject->copy()->locale('fr')->isoFormat('dddd'));
            }

            $dayLabel = $dayLabel ?: ([
                1 => 'Lundi',
                2 => 'Mardi',
                3 => 'Mercredi',
                4 => 'Jeudi',
                5 => 'Vendredi',
                6 => 'Samedi',
                7 => 'Dimanche',
            ][(int) $dayOfWeek] ?? 'Jour');

            $startLabel = $isArray
                ? ($item['start_label'] ?? null)
                : null;

            $endLabel = $isArray
                ? ($item['end_label'] ?? null)
                : null;

            if (!$startLabel && $startObject) {
                try {
                    $startLabel = \Carbon\Carbon::parse($startObject)->format('H:i');
                } catch (\Throwable $e) {
                    $startLabel = null;
                }
            }

            if (!$endLabel && $endObject) {
                try {
                    $endLabel = \Carbon\Carbon::parse($endObject)->format('H:i');
                } catch (\Throwable $e) {
                    $endLabel = null;
                }
            }

            if ((!$startLabel || !$endLabel) && $isArray && !empty($item['time_label'])) {
                $parts = preg_split('/\s*[–-]\s*/u', (string) $item['time_label']);
                $startLabel = $startLabel ?: ($parts[0] ?? null);
                $endLabel = $endLabel ?: ($parts[1] ?? null);
            }

            $subjectId = $isArray
                ? ($item['subject_id'] ?? null)
                : ($item->subject_id ?? null);

            $subjectName = $isArray
                ? ($item['subject'] ?? 'Matière')
                : ($item->subjectModel?->name ?? $item->subject ?? 'Matière');

            $levelId = $isArray
                ? ($item['level_id'] ?? null)
                : ($item->level_id ?? $item->classRoom?->level_id ?? null);

            $levelName = $isArray
                ? ($item['level'] ?? 'Niveau')
                : ($item->level?->name ?? $item->classRoom?->level?->name ?? 'Niveau');

            $classId = $isArray
                ? ($item['class_id'] ?? null)
                : ($item->class_id ?? null);

            $className = $isArray
                ? ($item['class_name'] ?? 'Classe')
                : ($item->classRoom?->name ?? 'Classe');

            $teacher = $isArray
                ? ($item['teacher'] ?? null)
                : ($item->prof?->name ?? null);

            if (!$subjectName || !$levelName || !$className || !$startLabel || !$endLabel) {
                return null;
            }

            $subjectKey = $subjectId
                ? 'subject-' . $subjectId
                : 'subject-' . md5(mb_strtolower((string) $subjectName));

            $levelKey = $levelId
                ? 'level-' . $levelId
                : 'level-' . md5(mb_strtolower((string) $levelName));

            $classKey = $classId
                ? 'class-' . $classId
                : 'class-' . md5(mb_strtolower((string) $className));

            $slotKey = sprintf('%02d|%s|%s', (int) $dayOfWeek, $startLabel, $endLabel);

            $uniqueKey = $scheduleId
                ? 'schedule-' . $scheduleId
                : implode('|', [$subjectKey, $levelKey, $classKey, $slotKey]);

            $shortClassName = preg_replace('/^\s*classe\s+/iu', '', (string) $className);

            return [
                'unique_key' => $uniqueKey,
                'schedule_id' => $scheduleId,
                'subject_key' => $subjectKey,
                'subject_name' => $subjectName,
                'level_key' => $levelKey,
                'level_name' => $levelName,
                'class_key' => $classKey,
                'class_name' => $className,
                'class_short_name' => $shortClassName,
                'day_of_week' => (int) $dayOfWeek,
                'day_label' => $dayLabel,
                'start_label' => $startLabel,
                'end_label' => $endLabel,
                'slot_key' => $slotKey,
                'teacher' => $teacher,
            ];
        })
        ->filter()
        ->unique('unique_key')
        ->values();

    $levelRank = function (string $name): int {
        $normalized = mb_strtolower(\Illuminate\Support\Str::ascii($name));

        if (str_contains($normalized, 'debut')) {
            return 10;
        }

        if (str_contains($normalized, 'intermedia')) {
            return 20;
        }

        if (str_contains($normalized, 'avance')) {
            return 30;
        }

        return 100;
    };

    $sections = $normalizedRows
        ->groupBy(fn (array $row) => $row['subject_key'] . '|day-' . $row['day_of_week'])
        ->map(function ($rows) use ($levelRank) {
            $first = $rows->first();

            $levels = $rows
                ->groupBy('level_key')
                ->map(function ($levelRows) use ($levelRank) {
                    $level = $levelRows->first();

                    return [
                        'key' => $level['level_key'],
                        'name' => $level['level_name'],
                        'rank' => $levelRank($level['level_name']),
                    ];
                })
                ->sortBy(fn (array $level) => sprintf('%03d|%s', $level['rank'], mb_strtolower($level['name'])))
                ->values();

            $slots = $rows
                ->groupBy('slot_key')
                ->map(function ($slotRows) {
                    $slot = $slotRows->first();

                    return [
                        'key' => $slot['slot_key'],
                        'start' => $slot['start_label'],
                        'end' => $slot['end_label'],
                    ];
                })
                ->sortBy('start')
                ->values();

            return [
                'subject' => $first['subject_name'],
                'day' => $first['day_label'],
                'day_order' => $first['day_of_week'],
                'levels' => $levels,
                'slots' => $slots,
                'rows' => $rows,
            ];
        })
        ->sortBy(fn (array $section) => sprintf('%02d|%s', $section['day_order'], mb_strtolower($section['subject'])))
        ->values();
@endphp

@if($sections->isNotEmpty())
    <section class="schedule-slot-matrix-shell">
        <div class="schedule-slot-matrix-heading">
            <div>
                <span class="schedule-slot-matrix-kicker">
                    <i class="bi bi-grid-3x3-gap-fill"></i>
                    Vue par créneaux
                </span>
                <h3>{{ $title }}</h3>
                <p>
                    Les classes sont regroupées directement par horaire.
                    Aucun bloc « pause » n’est affiché.
                </p>
            </div>
        </div>

        <div class="schedule-slot-matrix-sections">
            @foreach($sections as $section)
                <article class="schedule-slot-matrix-card">
                    <header class="schedule-slot-matrix-card-head">
                        <div>
                            <span>{{ $section['subject'] }}</span>
                            <strong>
                                <i class="bi bi-calendar3"></i>
                                Créneaux du {{ mb_strtolower($section['day']) }}
                            </strong>
                        </div>

                        <span class="schedule-slot-matrix-count">
                            {{ $section['slots']->count() }}
                            créneau{{ $section['slots']->count() > 1 ? 'x' : '' }}
                        </span>
                    </header>

                    <div class="schedule-slot-matrix-scroll">
                        <table class="schedule-slot-matrix-table">
                            <thead>
                                <tr>
                                    <th class="schedule-slot-matrix-time-head">
                                        Horaire
                                    </th>

                                    @foreach($section['levels'] as $levelIndex => $level)
                                        <th>
                                            <span class="schedule-slot-matrix-level schedule-slot-matrix-level-{{ ($levelIndex % 3) + 1 }}">
                                                @if($levelIndex % 3 === 0)
                                                    <i class="bi bi-person-fill"></i>
                                                @elseif($levelIndex % 3 === 1)
                                                    <i class="bi bi-journal-check"></i>
                                                @else
                                                    <i class="bi bi-star-fill"></i>
                                                @endif
                                                {{ $level['name'] }}
                                            </span>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($section['slots'] as $slotIndex => $slot)
                                    <tr>
                                        <th class="schedule-slot-matrix-time">
                                            <span>Créneau {{ $slotIndex + 1 }}</span>
                                            <strong>{{ $slot['start'] }}</strong>
                                            <small>à {{ $slot['end'] }}</small>
                                        </th>

                                        @foreach($section['levels'] as $level)
                                            @php
                                                $cellRows = $section['rows']
                                                    ->where('slot_key', $slot['key'])
                                                    ->where('level_key', $level['key'])
                                                    ->sortBy('class_name')
                                                    ->values();
                                            @endphp

                                            <td>
                                                @if($cellRows->isNotEmpty())
                                                    <div class="schedule-slot-matrix-classes">
                                                        @foreach($cellRows as $cellRow)
                                                            <div class="schedule-slot-matrix-class">
                                                                <strong>{{ $cellRow['class_short_name'] }}</strong>
                                                                <small>{{ $level['name'] }}</small>

                                                                @if($showTeacher && !empty($cellRow['teacher']))
                                                                    <span>
                                                                        <i class="bi bi-person-video3"></i>
                                                                        {{ $cellRow['teacher'] }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="schedule-slot-matrix-empty">—</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endif

@once
    <style>
        .schedule-slot-matrix-shell {
            margin: 20px 0;
        }

        .schedule-slot-matrix-heading {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 12px;
        }

        .schedule-slot-matrix-kicker {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 5px;
            color: #60a5fa;
            font-size: .66rem;
            font-weight: 850;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .schedule-slot-matrix-heading h3 {
            margin: 0;
            color: #f8fafc;
            font-size: 1.04rem;
            font-weight: 850;
        }

        .schedule-slot-matrix-heading p {
            margin: 5px 0 0;
            color: #64748b;
            font-size: .72rem;
        }

        .schedule-slot-matrix-sections {
            display: grid;
            gap: 14px;
        }

        .schedule-slot-matrix-card {
            overflow: hidden;
            border: 1px solid rgba(148, 163, 184, .13);
            border-radius: 16px;
            background:
                linear-gradient(180deg, rgba(16, 27, 46, .96), rgba(10, 20, 36, .96));
            box-shadow: 0 16px 42px rgba(0, 0, 0, .14);
        }

        .schedule-slot-matrix-card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 14px 16px;
            border-bottom: 1px solid rgba(148, 163, 184, .1);
            background: rgba(2, 6, 23, .12);
        }

        .schedule-slot-matrix-card-head > div {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .schedule-slot-matrix-card-head > div > span {
            color: #94a3b8;
            font-size: .6rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .schedule-slot-matrix-card-head strong {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: #e2e8f0;
            font-size: .8rem;
        }

        .schedule-slot-matrix-card-head strong i {
            color: #60a5fa;
        }

        .schedule-slot-matrix-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 30px;
            padding: 5px 9px;
            border: 1px solid rgba(96, 165, 250, .15);
            border-radius: 9px;
            color: #93c5fd;
            background: rgba(37, 99, 235, .08);
            font-size: .58rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .schedule-slot-matrix-scroll {
            overflow-x: auto;
        }

        .schedule-slot-matrix-table {
            width: 100%;
            min-width: 690px;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .schedule-slot-matrix-table th,
        .schedule-slot-matrix-table td {
            border-right: 1px solid rgba(148, 163, 184, .08);
            border-bottom: 1px solid rgba(148, 163, 184, .08);
        }

        .schedule-slot-matrix-table th:last-child,
        .schedule-slot-matrix-table td:last-child {
            border-right: 0;
        }

        .schedule-slot-matrix-table tbody tr:last-child th,
        .schedule-slot-matrix-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .schedule-slot-matrix-table thead th {
            padding: 9px;
            background: rgba(2, 6, 23, .22);
            text-align: center;
        }

        .schedule-slot-matrix-time-head {
            width: 112px;
            color: #64748b;
            font-size: .58rem;
            font-weight: 850;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .schedule-slot-matrix-level {
            display: inline-flex;
            min-height: 31px;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            padding: 6px 9px;
            border-radius: 9px;
            color: #fff;
            font-size: .62rem;
            font-weight: 850;
            text-transform: uppercase;
        }

        .schedule-slot-matrix-level-1 {
            background: linear-gradient(135deg, #f97316, #ea580c);
        }

        .schedule-slot-matrix-level-2 {
            background: linear-gradient(135deg, #1d4ed8, #3730a3);
        }

        .schedule-slot-matrix-level-3 {
            background: linear-gradient(135deg, #f59e0b, #ea580c);
        }

        .schedule-slot-matrix-time {
            width: 112px;
            padding: 12px 9px;
            background: rgba(2, 6, 23, .12);
            text-align: center;
            vertical-align: middle;
        }

        .schedule-slot-matrix-time span,
        .schedule-slot-matrix-time strong,
        .schedule-slot-matrix-time small {
            display: block;
        }

        .schedule-slot-matrix-time span {
            margin-bottom: 4px;
            color: #64748b;
            font-size: .52rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        .schedule-slot-matrix-time strong {
            color: #f8fafc;
            font-size: .74rem;
        }

        .schedule-slot-matrix-time small {
            margin-top: 2px;
            color: #94a3b8;
            font-size: .58rem;
        }

        .schedule-slot-matrix-table td {
            padding: 10px;
            vertical-align: middle;
            text-align: center;
        }

        .schedule-slot-matrix-classes {
            display: grid;
            gap: 6px;
        }

        .schedule-slot-matrix-class {
            display: flex;
            min-height: 64px;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 2px;
            padding: 8px;
            border: 1px solid rgba(148, 163, 184, .09);
            border-radius: 10px;
            background: rgba(255, 255, 255, .025);
        }

        .schedule-slot-matrix-class strong {
            color: #f1f5f9;
            font-size: .76rem;
            font-weight: 900;
        }

        .schedule-slot-matrix-class small {
            color: #94a3b8;
            font-size: .54rem;
        }

        .schedule-slot-matrix-class span {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-top: 3px;
            color: #60a5fa;
            font-size: .52rem;
        }

        .schedule-slot-matrix-empty {
            color: #475569;
            font-size: .8rem;
        }

        html.light-mode .schedule-slot-matrix-heading h3,
        html.light-mode .schedule-slot-matrix-card-head strong,
        html.light-mode .schedule-slot-matrix-time strong,
        html.light-mode .schedule-slot-matrix-class strong {
            color: #0f172a;
        }

        html.light-mode .schedule-slot-matrix-card {
            border-color: #e2e8f0;
            background: #fff;
            box-shadow: 0 14px 35px rgba(15, 23, 42, .08);
        }

        html.light-mode .schedule-slot-matrix-card-head,
        html.light-mode .schedule-slot-matrix-table thead th,
        html.light-mode .schedule-slot-matrix-time {
            background: #f8fafc;
        }

        html.light-mode .schedule-slot-matrix-table th,
        html.light-mode .schedule-slot-matrix-table td {
            border-color: #e2e8f0;
        }

        html.light-mode .schedule-slot-matrix-class {
            border-color: #e2e8f0;
            background: #fff;
        }

        @media (max-width: 700px) {
            .schedule-slot-matrix-heading {
                align-items: flex-start;
                flex-direction: column;
            }

            .schedule-slot-matrix-card-head {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>
@endonce
