<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lives', function (Blueprint $table) {
            if (!Schema::hasColumn('lives', 'provider')) {
                $table->string('provider', 30)->nullable()->after('stream_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lives', function (Blueprint $table) {
            if (Schema::hasColumn('lives', 'provider')) {
                $table->dropColumn('provider');
            }
        });
    }
};
