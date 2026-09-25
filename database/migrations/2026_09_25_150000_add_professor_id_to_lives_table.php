<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            Schema::hasTable('lives')
            && !Schema::hasColumn(
                'lives',
                'professor_id'
            )
        ) {
            Schema::table(
                'lives',
                function (Blueprint $table) {
                    $table
                        ->foreignId('professor_id')
                        ->nullable()
                        ->after('user_id')
                        ->constrained('users')
                        ->nullOnDelete();
                }
            );
        }
    }

    public function down(): void
    {
        if (
            Schema::hasTable('lives')
            && Schema::hasColumn(
                'lives',
                'professor_id'
            )
        ) {
            Schema::table(
                'lives',
                function (Blueprint $table) {
                    $table->dropConstrainedForeignId(
                        'professor_id'
                    );
                }
            );
        }
    }
};