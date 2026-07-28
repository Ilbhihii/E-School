<?php

use App\Models\VocalTestSubmission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'vocal_test_submissions',
            function (Blueprint $table) {
                if (
                    !Schema::hasColumn(
                        'vocal_test_submissions',
                        'submission_type'
                    )
                ) {
                    $table->string('submission_type', 30)
                        ->default(VocalTestSubmission::TYPE_AUDIO)
                        ->after('test_mode');
                }

                if (
                    !Schema::hasColumn(
                        'vocal_test_submissions',
                        'answer_data'
                    )
                ) {
                    $table->json('answer_data')
                        ->nullable()
                        ->after('submission_type');
                }

                if (
                    !Schema::hasColumn(
                        'vocal_test_submissions',
                        'auto_correct_count'
                    )
                ) {
                    $table->unsignedTinyInteger(
                        'auto_correct_count'
                    )
                        ->nullable()
                        ->after('answer_data');
                }

                if (
                    !Schema::hasColumn(
                        'vocal_test_submissions',
                        'auto_total_questions'
                    )
                ) {
                    $table->unsignedTinyInteger(
                        'auto_total_questions'
                    )
                        ->nullable()
                        ->after('auto_correct_count');
                }
            }
        );

        /*
         * Les exercices de complétion ne possèdent aucun fichier audio.
         * La colonne audio_path était obligatoire dans l'ancienne table.
         */
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement(
                'ALTER TABLE vocal_test_submissions '
                . 'MODIFY audio_path VARCHAR(255) NULL'
            );
        } elseif ($driver === 'pgsql') {
            DB::statement(
                'ALTER TABLE vocal_test_submissions '
                . 'ALTER COLUMN audio_path DROP NOT NULL'
            );
        }
    }

    public function down(): void
    {
        /*
         * Supprimer les exercices sans audio avant de restaurer
         * la contrainte NOT NULL de l'ancien schéma.
         */
        DB::table('vocal_test_submissions')
            ->where(
                'submission_type',
                VocalTestSubmission::TYPE_COMPLETION
            )
            ->delete();

        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement(
                'ALTER TABLE vocal_test_submissions '
                . 'MODIFY audio_path VARCHAR(255) NOT NULL'
            );
        } elseif ($driver === 'pgsql') {
            DB::statement(
                'ALTER TABLE vocal_test_submissions '
                . 'ALTER COLUMN audio_path SET NOT NULL'
            );
        }

        Schema::table(
            'vocal_test_submissions',
            function (Blueprint $table) {
                $columns = [
                    'submission_type',
                    'answer_data',
                    'auto_correct_count',
                    'auto_total_questions',
                ];

                foreach ($columns as $column) {
                    if (
                        Schema::hasColumn(
                            'vocal_test_submissions',
                            $column
                        )
                    ) {
                        $table->dropColumn($column);
                    }
                }
            }
        );
    }
};