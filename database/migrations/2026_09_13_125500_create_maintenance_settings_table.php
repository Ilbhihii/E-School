<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('maintenance_settings', function (Blueprint $table) {
            $table->id();

            $table->boolean('announcement_enabled')
                ->default(false);

            $table->boolean('blocking_enabled')
                ->default(false);

            $table->string('message', 500)
                ->nullable();

            $table->timestamp('start_at')
                ->nullable();

            $table->timestamp('end_at')
                ->nullable();

            $table->timestamps();
        });

        DB::table('maintenance_settings')->insert([
            'id' => 1,
            'announcement_enabled' => false,
            'blocking_enabled' => false,
            'message' =>
                'Une maintenance de la plateforme est programmée.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('maintenance_settings');
    }
}
