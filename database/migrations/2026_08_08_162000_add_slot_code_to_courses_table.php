<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSlotCodeToCoursesTable extends Migration
{
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'slot_code')) {
                $table->string('slot_code', 20)
                    ->nullable()
                    ->after('class_id')
                    ->index();
            }
        });
    }

    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'slot_code')) {
                $table->dropIndex(['slot_code']);
                $table->dropColumn('slot_code');
            }
        });
    }
}
