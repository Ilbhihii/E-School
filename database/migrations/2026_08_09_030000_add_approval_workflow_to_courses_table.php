<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('courses')) {
            return;
        }

        if (
            !Schema::hasColumn(
                'courses',
                'approval_status'
            )
        ) {
            Schema::table(
                'courses',
                function (Blueprint $table) {
                    $table
                        ->string(
                            'approval_status',
                            20
                        )
                        ->default('approved')
                        ->after('user_id');

                    $table->index(
                        'approval_status',
                        'courses_approval_status_idx'
                    );
                }
            );
        }

        if (
            !Schema::hasColumn(
                'courses',
                'submitted_at'
            )
        ) {
            Schema::table(
                'courses',
                function (Blueprint $table) {
                    $table
                        ->timestamp('submitted_at')
                        ->nullable()
                        ->after('approval_status');
                }
            );
        }

        if (
            !Schema::hasColumn(
                'courses',
                'reviewed_by'
            )
        ) {
            Schema::table(
                'courses',
                function (Blueprint $table) {
                    $table
                        ->foreignId('reviewed_by')
                        ->nullable()
                        ->after('submitted_at')
                        ->constrained('users')
                        ->nullOnDelete();
                }
            );
        }

        if (
            !Schema::hasColumn(
                'courses',
                'reviewed_at'
            )
        ) {
            Schema::table(
                'courses',
                function (Blueprint $table) {
                    $table
                        ->timestamp('reviewed_at')
                        ->nullable()
                        ->after('reviewed_by');
                }
            );
        }

        if (
            !Schema::hasColumn(
                'courses',
                'rejection_reason'
            )
        ) {
            Schema::table(
                'courses',
                function (Blueprint $table) {
                    $table
                        ->text('rejection_reason')
                        ->nullable()
                        ->after('reviewed_at');
                }
            );
        }

        /*
         * Tous les cours historiques restent publiés.
         */
        DB::table('courses')
            ->whereNull('approval_status')
            ->update([
                'approval_status' => 'approved',
            ]);
    }

    public function down(): void
    {
        if (!Schema::hasTable('courses')) {
            return;
        }

        /*
         * Rollback volontairement conservateur :
         * on supprime d'abord la FK puis les colonnes.
         */
        if (
            Schema::hasColumn(
                'courses',
                'reviewed_by'
            )
        ) {
            Schema::table(
                'courses',
                function (Blueprint $table) {
                    $table
                        ->dropConstrainedForeignId(
                            'reviewed_by'
                        );
                }
            );
        }

        foreach (
            [
                'rejection_reason',
                'reviewed_at',
                'submitted_at',
            ] as $column
        ) {
            if (
                Schema::hasColumn(
                    'courses',
                    $column
                )
            ) {
                Schema::table(
                    'courses',
                    function (
                        Blueprint $table
                    ) use ($column) {
                        $table->dropColumn(
                            $column
                        );
                    }
                );
            }
        }

        if (
            Schema::hasColumn(
                'courses',
                'approval_status'
            )
        ) {
            Schema::table(
                'courses',
                function (Blueprint $table) {
                    $table->dropIndex(
                        'courses_approval_status_idx'
                    );

                    $table->dropColumn(
                        'approval_status'
                    );
                }
            );
        }
    }
};
