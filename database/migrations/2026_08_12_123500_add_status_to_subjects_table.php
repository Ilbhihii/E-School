<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToSubjectsTable extends Migration
{
    public function up()
    {
        if (
            Schema::hasTable('subjects')
            && !Schema::hasColumn(
                'subjects',
                'status'
            )
        ) {
            Schema::table(
                'subjects',
                function (Blueprint $table) {
                    /*
                     * Les matières déjà existantes
                     * restent actives.
                     */
                    $table
                        ->string('status', 30)
                        ->default('active')
                        ->after('type')
                        ->index();
                }
            );
        }
    }

    public function down()
    {
        if (
            Schema::hasTable('subjects')
            && Schema::hasColumn(
                'subjects',
                'status'
            )
        ) {
            Schema::table(
                'subjects',
                function (Blueprint $table) {
                    $table->dropColumn(
                        'status'
                    );
                }
            );
        }
    }
}
