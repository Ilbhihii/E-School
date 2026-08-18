<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddShortDurationPricesToPlansTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('plans')) {
            return;
        }

        if (!Schema::hasColumn('plans', 'price_1_month_minor')) {
            Schema::table('plans', function (Blueprint $table) {
                $table->unsignedBigInteger('price_1_month_minor')
                    ->nullable()
                    ->after('amount_minor');
            });
        }

        if (!Schema::hasColumn('plans', 'price_2_month_minor')) {
            Schema::table('plans', function (Blueprint $table) {
                $table->unsignedBigInteger('price_2_month_minor')
                    ->nullable()
                    ->after('price_1_month_minor');
            });
        }

        if (!Schema::hasColumn('plans', 'price_3_month_minor')) {
            Schema::table('plans', function (Blueprint $table) {
                $table->unsignedBigInteger('price_3_month_minor')
                    ->nullable()
                    ->after('price_2_month_minor');
            });
        }

        if (!Schema::hasColumn('plans', 'price_4_month_minor')) {
            Schema::table('plans', function (Blueprint $table) {
                $table->unsignedBigInteger('price_4_month_minor')
                    ->nullable()
                    ->after('price_3_month_minor');
            });
        }

        if (Schema::hasColumn('plans', 'whatsapp_message')) {
            DB::table('plans')
                ->where(
                    'whatsapp_message',
                    'Bonjour, je souhaite envoyer mon reçu de paiement pour l’offre {offre}. Référence : {reference}. Montant : {montant} {devise}. Je joins le reçu à ce message.'
                )
                ->update([
                    'whatsapp_message' =>
                        'Bonjour, je souhaite envoyer mon reçu de paiement pour l’offre {offre}. '
                        . 'Durée : {duree}. Référence : {reference}. '
                        . 'Montant : {montant} {devise}. Je joins le reçu à ce message.',
                ]);
        }
    }

    public function down()
    {
        if (!Schema::hasTable('plans')) {
            return;
        }

        foreach (
            [
                'price_4_month_minor',
                'price_3_month_minor',
                'price_2_month_minor',
                'price_1_month_minor',
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
