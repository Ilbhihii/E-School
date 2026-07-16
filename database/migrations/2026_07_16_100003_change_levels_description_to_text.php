<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('levels', function (Blueprint $table) {
            // MySQL / MariaDB : change string → text
            \DB::statement('ALTER TABLE levels MODIFY description TEXT NULL');
        });
    }

    public function down(): void
    {
        Schema::table('levels', function (Blueprint $table) {
            \DB::statement('ALTER TABLE levels MODIFY description VARCHAR(255) NULL');
        });
    }
};
