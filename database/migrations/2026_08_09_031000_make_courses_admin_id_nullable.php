<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            !Schema::hasTable('courses')
            || !Schema::hasColumn(
                'courses',
                'admin_id'
            )
        ) {
            return;
        }

        /*
         * Un cours peut maintenant être créé par :
         *
         * - un administrateur :
         *   admin_id = ID de l'admin
         *
         * - un professeur :
         *   admin_id = NULL
         *   user_id  = ID du professeur
         *
         * La clé étrangère admin_id reste en place.
         * Seule la colonne devient nullable.
         */
        Schema::table(
            'courses',
            function (Blueprint $table) {
                $table
                    ->unsignedBigInteger('admin_id')
                    ->nullable()
                    ->change();
            }
        );
    }

    public function down(): void
    {
        /*
         * Rollback volontairement conservateur.
         *
         * Après l'activation des propositions professeur,
         * des cours peuvent légitimement avoir admin_id = NULL.
         * Remettre la colonne en NOT NULL provoquerait une erreur.
         */
    }
};
