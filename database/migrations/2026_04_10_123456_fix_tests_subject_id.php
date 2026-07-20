<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
            DB::statement('CREATE TABLE tests_new (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                title VARCHAR NOT NULL,
                subject_id INTEGER NULL,
                duration INTEGER NOT NULL DEFAULT 60,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                FOREIGN KEY(subject_id) REFERENCES subjects(id) ON DELETE CASCADE
            )');
            DB::statement('INSERT INTO tests_new (id, title, subject_id, duration, created_at, updated_at)
                SELECT tests.id, tests.title, courses.subject_id, tests.duration, tests.created_at, tests.updated_at
                FROM tests LEFT JOIN courses ON courses.id = tests.course_id');
            DB::statement('DROP TABLE tests');
            DB::statement('ALTER TABLE tests_new RENAME TO tests');
            DB::statement('PRAGMA foreign_keys = ON');

            return;
        }

        // Add subject_id column
        if (!Schema::hasColumn('tests', 'subject_id')) {
            Schema::table('tests', function (Blueprint $table) {
                $table->unsignedBigInteger('subject_id')->nullable()->after('course_id');
            });
        }

        // Backfill subject_id from courses.subject_id
        DB::statement('
            UPDATE tests 
            SET subject_id = (
                SELECT c.subject_id 
                FROM courses c 
                WHERE c.id = tests.course_id
            )
            WHERE tests.course_id IS NOT NULL
                AND tests.subject_id IS NULL
        ');

        // Drop course_id FK if exists
        Schema::table('tests', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropColumn('course_id');
        });

        // Add proper FK on subject_id
        Schema::table('tests', function (Blueprint $table) {
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
        });
    }

    public function down()
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
            DB::statement('CREATE TABLE tests_old (
                id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                title VARCHAR NOT NULL,
                course_id INTEGER NULL,
                duration INTEGER NOT NULL DEFAULT 60,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                FOREIGN KEY(course_id) REFERENCES courses(id) ON DELETE CASCADE
            )');
            DB::statement('INSERT INTO tests_old (id, title, duration, created_at, updated_at)
                SELECT id, title, duration, created_at, updated_at FROM tests');
            DB::statement('DROP TABLE tests');
            DB::statement('ALTER TABLE tests_old RENAME TO tests');
            DB::statement('PRAGMA foreign_keys = ON');

            return;
        }

        Schema::table('tests', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
        });

        Schema::table('tests', function (Blueprint $table) {
            $table->unsignedBigInteger('course_id')->after('title');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });

        Schema::table('tests', function (Blueprint $table) {
            $table->dropColumn('subject_id');
        });
    }
};
