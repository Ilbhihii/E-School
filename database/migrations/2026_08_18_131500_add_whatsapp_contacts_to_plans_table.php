<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddWhatsappContactsToPlansTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('plans')) {
            return;
        }

        if (!Schema::hasColumn('plans', 'whatsapp_france')) {
            Schema::table('plans', function (Blueprint $table) {
                $table->string('whatsapp_france', 30)
                    ->nullable()
                    ->after('paypal_url');
            });
        }

        if (!Schema::hasColumn('plans', 'whatsapp_maroc')) {
            Schema::table('plans', function (Blueprint $table) {
                $table->string('whatsapp_maroc', 30)
                    ->nullable()
                    ->after('whatsapp_france');
            });
        }

        if (!Schema::hasColumn('plans', 'whatsapp_message')) {
            Schema::table('plans', function (Blueprint $table) {
                $table->string('whatsapp_message', 500)
                    ->nullable()
                    ->after('whatsapp_maroc');
            });
        }

        DB::table('plans')
            ->whereNull('whatsapp_france')
            ->update([
                'whatsapp_france' => '+33 7 60 96 12 74',
            ]);

        DB::table('plans')
            ->whereNull('whatsapp_maroc')
            ->update([
                'whatsapp_maroc' => '+212 6 65 72 99 77',
            ]);

        DB::table('plans')
            ->whereNull('whatsapp_message')
            ->update([
                'whatsapp_message' =>
                    'Bonjour, je souhaite envoyer mon reçu de paiement pour l’offre {offre}. '
                    . 'Référence : {reference}. Montant : {montant} {devise}. '
                    . 'Je joins le reçu à ce message.',
            ]);
    }

    public function down()
    {
        if (!Schema::hasTable('plans')) {
            return;
        }

        foreach (
            [
                'whatsapp_message',
                'whatsapp_maroc',
                'whatsapp_france',
            ] as $column
        ) {
            if (Schema::hasColumn('plans', $column)) {
                Schema::table('plans', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
}
