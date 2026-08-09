<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('class_slots')) {
            Schema::create(
                'class_slots',
                function (Blueprint $table) {
                    $table->id();

                    $table
                        ->foreignId('subject_id')
                        ->constrained('subjects')
                        ->cascadeOnDelete();

                    $table
                        ->foreignId('level_id')
                        ->constrained('levels')
                        ->cascadeOnDelete();

                    $table
                        ->foreignId('class_id')
                        ->constrained('class_rooms')
                        ->cascadeOnDelete();

                    $table->string('code', 20);
                    $table
                        ->unsignedTinyInteger('position')
                        ->default(1);

                    $table
                        ->boolean('is_active')
                        ->default(true);

                    $table->timestamps();

                    $table->unique(
                        [
                            'subject_id',
                            'level_id',
                            'class_id',
                            'code',
                        ],
                        'class_slots_path_code_unique'
                    );
                }
            );
        }

        $this->backfillExistingClasses();
    }

    public function down(): void
    {
        Schema::dropIfExists('class_slots');
    }

    private function backfillExistingClasses(): void
    {
        if (
            !Schema::hasTable('class_room_subject')
            || !Schema::hasTable('class_rooms')
            || !Schema::hasTable('levels')
        ) {
            return;
        }

        $rows = DB::table('class_room_subject')
            ->join(
                'class_rooms',
                'class_room_subject.class_room_id',
                '=',
                'class_rooms.id'
            )
            ->join(
                'levels',
                'class_rooms.level_id',
                '=',
                'levels.id'
            )
            ->select([
                'class_room_subject.subject_id',
                'class_rooms.id as class_id',
                'class_rooms.name as class_name',
                'levels.id as level_id',
            ])
            ->get();

        foreach ($rows as $row) {
            $prefix = $this->prefixForClass(
                (string) $row->class_name
            );

            foreach (range(1, 4) as $number) {
                DB::table('class_slots')
                    ->updateOrInsert(
                        [
                            'subject_id' =>
                                (int) $row->subject_id,
                            'level_id' =>
                                (int) $row->level_id,
                            'class_id' =>
                                (int) $row->class_id,
                            'code' =>
                                $prefix . $number,
                        ],
                        [
                            'position' => $number,
                            'is_active' => true,
                            'updated_at' => now(),
                            'created_at' => now(),
                        ]
                    );
            }
        }
    }

    private function prefixForClass(
        string $name
    ): string {
        $normalized = Str::lower(
            Str::ascii(
                trim($name)
            )
        );

        return match (true) {
            str_contains(
                $normalized,
                'debutant'
            ) => 'D',

            str_contains(
                $normalized,
                'intermediaire'
            ) => 'I',

            str_contains(
                $normalized,
                'avance'
            ) => 'A',

            default => 'G',
        };
    }
};
