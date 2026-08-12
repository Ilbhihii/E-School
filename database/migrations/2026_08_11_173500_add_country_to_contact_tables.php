<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCountryToContactTables extends Migration
{
    public function up()
    {
        if (
            Schema::hasTable('contact_leads')
            && !Schema::hasColumn('contact_leads', 'country')
        ) {
            Schema::table('contact_leads', function (Blueprint $table) {
                $table->string('country', 100)
                    ->nullable()
                    ->after('phone_normalized');

                $table->index('country');
            });
        }

        if (
            Schema::hasTable('contact_requests')
            && !Schema::hasColumn('contact_requests', 'country')
        ) {
            Schema::table('contact_requests', function (Blueprint $table) {
                $table->string('country', 100)
                    ->nullable()
                    ->after('phone');
            });
        }
    }

    public function down()
    {
        if (
            Schema::hasTable('contact_requests')
            && Schema::hasColumn('contact_requests', 'country')
        ) {
            Schema::table('contact_requests', function (Blueprint $table) {
                $table->dropColumn('country');
            });
        }

        if (
            Schema::hasTable('contact_leads')
            && Schema::hasColumn('contact_leads', 'country')
        ) {
            Schema::table('contact_leads', function (Blueprint $table) {
                $table->dropIndex(['country']);
                $table->dropColumn('country');
            });
        }
    }
}
