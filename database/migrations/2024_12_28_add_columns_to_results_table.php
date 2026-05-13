<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('results')) {
            return;
        }
        Schema::table('results', function (Blueprint $table) {
            $table->integer('total_questions')->default(0)->after('score');
            $table->decimal('percentage', 5, 2)->default(0)->after('total_questions');
            $table->json('answers')->nullable()->after('percentage');
        });
    }

    public function down()
    {
        if (!Schema::hasTable('results')) {
            return;
        }
        Schema::table('results', function (Blueprint $table) {
            $table->dropColumn(['total_questions', 'percentage', 'answers']);
        });
    }
};

