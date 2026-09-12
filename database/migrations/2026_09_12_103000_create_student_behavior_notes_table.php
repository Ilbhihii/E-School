<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentBehaviorNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * Certaines anciennes bases Smart School ne permettent pas
     * d'ajouter proprement des contraintes FOREIGN KEY vers users.
     * Les relations restent gérées par Eloquent et la validation.
     */
    public function up()
    {
        /*
         * La première tentative MySQL peut avoir créé la table avant
         * d'échouer pendant l'ajout de la première clé étrangère.
         * Le module étant nouveau, on nettoie cette table partielle.
         */
        if (Schema::hasTable('student_behavior_notes')) {
            Schema::drop('student_behavior_notes');
        }

        Schema::create(
            'student_behavior_notes',
            function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('professor_id');
                $table->unsignedBigInteger('student_id');
                $table->unsignedBigInteger('subject_id');
                $table->unsignedBigInteger('level_id');
                $table->unsignedBigInteger('class_room_id');

                $table->string('type', 20);
                $table->unsignedTinyInteger('points');
                $table->text('note');
                $table->date('noted_at');
                $table->timestamps();

                $table->index(
                    'professor_id',
                    'behavior_notes_professor_idx'
                );

                $table->index(
                    'student_id',
                    'behavior_notes_student_idx'
                );

                $table->index(
                    'subject_id',
                    'behavior_notes_subject_idx'
                );

                $table->index(
                    'level_id',
                    'behavior_notes_level_idx'
                );

                $table->index(
                    'class_room_id',
                    'behavior_notes_class_idx'
                );

                $table->index(
                    [
                        'professor_id',
                        'subject_id',
                        'level_id',
                        'class_room_id',
                    ],
                    'behavior_notes_prof_path_idx'
                );

                $table->index(
                    [
                        'student_id',
                        'noted_at',
                    ],
                    'behavior_notes_student_date_idx'
                );
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists(
            'student_behavior_notes'
        );
    }
}
