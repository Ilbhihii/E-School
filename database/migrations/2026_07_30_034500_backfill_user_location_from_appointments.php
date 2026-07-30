<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'country')) {
                $table->string('country', 100)->nullable();
            }

            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city', 100)->nullable();
            }

            if (!Schema::hasColumn('users', 'ip_address')) {
                $table->string('ip_address', 45)->nullable();
            }
        });

        if (!Schema::hasTable('test_appointments')) {
            return;
        }

        if (
            !Schema::hasColumn('test_appointments', 'country')
            || !Schema::hasColumn('test_appointments', 'city')
        ) {
            return;
        }

        DB::table('users')
            ->where('role', 'student')
            ->orderBy('id')
            ->chunkById(100, function ($users) {
                foreach ($users as $user) {
                    if (!empty($user->country) && !empty($user->city)) {
                        continue;
                    }

                    $appointment = DB::table('test_appointments')
                        ->whereRaw('LOWER(email) = ?', [
                            mb_strtolower(trim((string) $user->email)),
                        ])
                        ->where(function ($query) {
                            $query
                                ->whereNotNull('country')
                                ->orWhereNotNull('city');
                        })
                        ->orderByDesc('id')
                        ->first();

                    if (!$appointment) {
                        continue;
                    }

                    DB::table('users')
                        ->where('id', $user->id)
                        ->update([
                            'country' => $user->country
                                ?: $appointment->country,
                            'city' => $user->city
                                ?: $appointment->city,
                            'updated_at' => now(),
                        ]);
                }
            });
    }

    public function down(): void
    {
        /*
         * Aucun retour automatique : les colonnes peuvent déjà contenir
         * des données importantes créées par une migration précédente.
         */
    }
};
