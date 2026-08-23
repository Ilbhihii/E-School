<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            Schema::hasTable('class_rooms')
            && !Schema::hasColumn('class_rooms', 'admission_mode')
        ) {
            Schema::table('class_rooms', function (Blueprint $table) {
                /*
                 * NULL conserve le comportement historique des classes
                 * déjà existantes. Les nouvelles classes créées depuis
                 * l'administration reçoivent contact ou vocal_test.
                 */
                $table->string('admission_mode', 20)
                    ->nullable();

                $table->index(
                    'admission_mode',
                    'class_rooms_admission_mode_index'
                );
            });
        }
    }

    public function down(): void
    {
        if (
            Schema::hasTable('class_rooms')
            && Schema::hasColumn('class_rooms', 'admission_mode')
        ) {
            Schema::table('class_rooms', function (Blueprint $table) {
                $table->dropIndex(
                    'class_rooms_admission_mode_index'
                );
                $table->dropColumn('admission_mode');
            });
        }
    }
};
