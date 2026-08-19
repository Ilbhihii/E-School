<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFamilyPackToPlansTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('plans')) {
            return;
        }

        if (!Schema::hasColumn('plans', 'is_family_pack')) {
            Schema::table('plans', function (Blueprint $table) {
                $table->boolean('is_family_pack')
                    ->default(false)
                    ->after('restricted_to_high_school');
            });
        }

        if (!Schema::hasColumn('plans', 'family_members')) {
            Schema::table('plans', function (Blueprint $table) {
                $table->unsignedTinyInteger('family_members')
                    ->nullable()
                    ->after('is_family_pack');
            });
        }
    }

    public function down()
    {
        if (!Schema::hasTable('plans')) {
            return;
        }

        if (Schema::hasColumn('plans', 'family_members')) {
            Schema::table('plans', function (Blueprint $table) {
                $table->dropColumn('family_members');
            });
        }

        if (Schema::hasColumn('plans', 'is_family_pack')) {
            Schema::table('plans', function (Blueprint $table) {
                $table->dropColumn('is_family_pack');
            });
        }
    }
}
