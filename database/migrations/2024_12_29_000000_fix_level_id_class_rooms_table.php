<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('class_rooms')) {
            return;
        }

        Schema::table('class_rooms', function (Blueprint $table) {
            if (!Schema::hasColumn('class_rooms', 'level_id')) {
                $table->foreignId('level_id')->nullable()->constrained()->onDelete('cascade')->after('name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('class_rooms', function (Blueprint $table) {
            if (Schema::hasColumn('class_rooms', 'level_id')) {
                $table->dropForeign(['level_id']);
                $table->dropColumn('level_id');
            }
        });
    }
};
?>

