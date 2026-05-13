<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->unsignedBigInteger('create_by')->nullable()->after('duration');
            $table->foreign('create_by')->references('id')->on('users')->onDelete('cascade');
            $table->boolean('is_ai_generated')->default(false)->after('create_by');
        });
    }

    public function down()
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->dropForeign(['create_by']);
            $table->dropColumn(['create_by', 'is_ai_generated']);
        });
    }
};
?>

