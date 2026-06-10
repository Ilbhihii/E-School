<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('results', function (Blueprint $table) {
            if (!Schema::hasColumn('results', 'total_questions')) {
                $table->integer('total_questions')->default(0)->after('score');
            }
            if (!Schema::hasColumn('results', 'percentage')) {
                $table->decimal('percentage', 5, 2)->default(0)->after('total_questions');
            }
            if (!Schema::hasColumn('results', 'answers')) {
                $table->json('answers')->nullable()->after('percentage');
            }
        });
    }

    public function down()
    {
        Schema::table('results', function (Blueprint $table) {
            if (Schema::hasColumn('results', 'total_questions')) {
                $table->dropColumn(['total_questions', 'percentage', 'answers']);
            }
        });
    }
};
?>

