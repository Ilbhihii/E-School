<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->enableGuestFlow(
            'vocal_test_submissions'
        );

        $this->enableGuestFlow(
            'high_school_test_submissions'
        );
    }

    public function down(): void
    {
        foreach (
            [
                'vocal_test_submissions',
                'high_school_test_submissions',
            ] as $tableName
        ) {
            if (
                !Schema::hasTable($tableName)
                || !Schema::hasColumn(
                    $tableName,
                    'guest_token'
                )
            ) {
                continue;
            }

            Schema::table(
                $tableName,
                function (Blueprint $table) {
                    $table->dropUnique([
                        'guest_token',
                    ]);

                    $table->dropColumn(
                        'guest_token'
                    );
                }
            );
        }

        /*
         * user_id reste nullable lors d’un rollback afin de ne pas
         * supprimer les tests déjà envoyés par des visiteurs.
         */
    }

    private function enableGuestFlow(
        string $tableName
    ): void {
        if (!Schema::hasTable($tableName)) {
            return;
        }

        if (
            Schema::hasColumn(
                $tableName,
                'user_id'
            )
        ) {
            Schema::table(
                $tableName,
                function (Blueprint $table) {
                    $table->dropForeign([
                        'user_id',
                    ]);
                }
            );

            Schema::table(
                $tableName,
                function (Blueprint $table) {
                    $table
                        ->unsignedBigInteger(
                            'user_id'
                        )
                        ->nullable()
                        ->change();

                    $table
                        ->foreign('user_id')
                        ->references('id')
                        ->on('users')
                        ->nullOnDelete();
                }
            );
        }

        if (
            !Schema::hasColumn(
                $tableName,
                'guest_token'
            )
        ) {
            Schema::table(
                $tableName,
                function (Blueprint $table) {
                    $table->uuid('guest_token')
                        ->nullable()
                        ->unique()
                        ->after('user_id');
                }
            );
        }
    }
};
