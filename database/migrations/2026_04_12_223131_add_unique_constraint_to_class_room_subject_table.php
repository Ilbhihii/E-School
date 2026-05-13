<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class_room_subject', function (Blueprint $table) {
            $table->dropPrimary('id');
            $table->dropColumn('id');
            $table->foreignId('class_room_id')->change()->constrained('class_rooms')->cascadeOnDelete();
            $table->foreignId('subject_id')->change()->constrained('subjects')->cascadeOnDelete();
            $table->primary(['class_room_id', 'subject_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('class_room_subject', function (Blueprint $table) {
            $table->dropForeign(['class_room_id']);
            $table->dropForeign(['subject_id']);
            $table->dropPrimary(['class_room_id', 'subject_id']);
            $table->dropTimestamps();
            $table->id()->change();
        });
    }
};

