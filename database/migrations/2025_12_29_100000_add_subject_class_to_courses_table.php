<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('courses')) return;
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'subject_id')) {
                // subject existe déjà à ce stade ; conserver la contrainte.
                $table->foreignId('subject_id')->constrained()->cascadeOnDelete()->after('description');
            }
            if (!Schema::hasColumn('courses', 'class_id')) {
                // class_rooms est créée plus tard : ne pas créer une FK prématurée.
                $table->unsignedBigInteger('class_id')->nullable()->after('subject_id');
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('courses')) return;
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'class_id')) $table->dropColumn('class_id');
            if (Schema::hasColumn('courses', 'subject_id')) {
                try { $table->dropForeign(['subject_id']); } catch (\Throwable $e) {}
                $table->dropColumn('subject_id');
            }
        });
    }
};
