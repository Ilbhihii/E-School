<?php

use App\Services\PedagogicalScopeCodeRebuildService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            !Schema::hasTable('prof_assignments')
            || !Schema::hasTable('pedagogical_time_slots')
            || !Schema::hasTable('class_slots')
        ) {
            return;
        }

        /*
         * V3 : applique aux professeurs la même règle que les étudiants :
         * jour + heure -> rang chronologique -> groupe automatique.
         *
         * Le service recalcule aussi les codes dépendants afin de garder
         * les différents espaces cohérents après un décalage d'horaire.
         */
        app(
            PedagogicalScopeCodeRebuildService::class
        )->rebuildAll();
    }

    public function down(): void
    {
        /*
         * Migration métier non destructive : on ne restaure pas les
         * anciens groupes choisis manuellement.
         */
    }
};
