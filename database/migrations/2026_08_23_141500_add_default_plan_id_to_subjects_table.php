<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AddDefaultPlanIdToSubjectsTable extends Migration
{
    public function up()
    {
        if (
            !Schema::hasTable('subjects')
            || !Schema::hasTable('plans')
        ) {
            return;
        }

        if (!Schema::hasColumn('subjects', 'default_plan_id')) {
            Schema::table('subjects', function (Blueprint $table) {
                $table->unsignedBigInteger('default_plan_id')
                    ->nullable();

                $table->index('default_plan_id');

                $table->foreign('default_plan_id')
                    ->references('id')
                    ->on('plans')
                    ->onDelete('set null');
            });
        }

        /*
         * Association initiale pour les parcours déjà utilisés :
         * - Arabe / Coran -> Premium
         * - Soutien Lycée -> offre Soutien Lycée
         *
         * Les nouvelles matières seront associées depuis l'admin Plans.
         */
        $premiumId = DB::table('plans')
            ->where('code', 'premium')
            ->value('id');

        $highSchoolId = DB::table('plans')
            ->where('code', 'soutien_lycee')
            ->value('id');

        if (!$highSchoolId) {
            $highSchoolId = DB::table('plans')
                ->where('restricted_to_high_school', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->value('id');
        }

        DB::table('subjects')
            ->select(['id', 'name', 'default_plan_id'])
            ->orderBy('id')
            ->get()
            ->each(function ($subject) use (
                $premiumId,
                $highSchoolId
            ) {
                if (!empty($subject->default_plan_id)) {
                    return;
                }

                $normalized = Str::lower(
                    Str::ascii(
                        trim((string) $subject->name)
                    )
                );

                $planId = null;

                if (
                    in_array(
                        $normalized,
                        ['arabe', 'coran'],
                        true
                    )
                ) {
                    $planId = $premiumId;
                } elseif ($normalized === 'soutien lycee') {
                    $planId = $highSchoolId;
                }

                if ($planId) {
                    DB::table('subjects')
                        ->where('id', $subject->id)
                        ->update([
                            'default_plan_id' => $planId,
                        ]);
                }
            });
    }

    public function down()
    {
        if (
            !Schema::hasTable('subjects')
            || !Schema::hasColumn('subjects', 'default_plan_id')
        ) {
            return;
        }

        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeign(['default_plan_id']);
            $table->dropIndex(['default_plan_id']);
            $table->dropColumn('default_plan_id');
        });
    }
}
