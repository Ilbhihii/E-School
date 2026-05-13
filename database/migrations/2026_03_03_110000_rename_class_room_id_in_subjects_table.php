<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameClassRoomIdInSubjectsTable extends Migration
{
    public function up()
    {
        // add new column then copy data, drop old, create FK
        Schema::table('subjects', function (Blueprint $table) {
            if (! Schema::hasColumn('subjects', 'class_id')) {
                $table->unsignedBigInteger('class_id')->nullable()->after('name');
            }
        });

// migrate existing values
        if (Schema::hasColumn('subjects', 'class_room_id')) {
            \DB::table('subjects')
                ->whereNotNull('class_room_id')
                ->update(['class_id' => \DB::raw('class_room_id')]);
        }


        Schema::table('subjects', function (Blueprint $table) {
            if (Schema::hasColumn('subjects', 'class_room_id')) {
                // drop foreign then column
                $table->dropForeign(['class_room_id']);
                $table->dropColumn('class_room_id');
            }
            if (! Schema::hasColumn('subjects', 'class_id')) {
                // should not happen but just in case
                $table->unsignedBigInteger('class_id')->nullable();
            }
            $table->foreign('class_id')
                  ->references('id')
                  ->on('class_rooms')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('subjects', function (Blueprint $table) {
            if (Schema::hasColumn('subjects', 'class_id')) {
                $table->dropForeign(['class_id']);
                $table->unsignedBigInteger('class_room_id')->nullable()->after('name');
            }
        });

        \DB::table('subjects')
            ->whereNotNull('class_id')
            ->update(['class_room_id' => \DB::raw('class_id')]);

        Schema::table('subjects', function (Blueprint $table) {
            if (Schema::hasColumn('subjects', 'class_id')) {
                $table->dropColumn('class_id');
            }
            if (Schema::hasColumn('subjects', 'class_room_id')) {
                $table->foreign('class_room_id')
                      ->references('id')
                      ->on('class_rooms')
                      ->onDelete('cascade');
            }
        });
    }
}
