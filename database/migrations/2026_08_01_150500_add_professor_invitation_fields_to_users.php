<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        if (
            !Schema::hasColumn(
                'users',
                'must_change_password'
            )
        ) {
            Schema::table(
                'users',
                function (Blueprint $table) {
                    $table->boolean(
                        'must_change_password'
                    )
                        ->default(false)
                        ->after('password');
                }
            );
        }

        if (
            !Schema::hasColumn(
                'users',
                'temporary_password_expires_at'
            )
        ) {
            Schema::table(
                'users',
                function (Blueprint $table) {
                    $table->timestamp(
                        'temporary_password_expires_at'
                    )
                        ->nullable()
                        ->after(
                            'must_change_password'
                        );
                }
            );
        }

        if (
            !Schema::hasColumn(
                'users',
                'temporary_password_sent_at'
            )
        ) {
            Schema::table(
                'users',
                function (Blueprint $table) {
                    $table->timestamp(
                        'temporary_password_sent_at'
                    )
                        ->nullable()
                        ->after(
                            'temporary_password_expires_at'
                        );
                }
            );
        }

        if (
            !Schema::hasColumn(
                'users',
                'password_changed_at'
            )
        ) {
            Schema::table(
                'users',
                function (Blueprint $table) {
                    $table->timestamp(
                        'password_changed_at'
                    )
                        ->nullable()
                        ->after(
                            'temporary_password_sent_at'
                        );
                }
            );
        }

        if (
            !Schema::hasColumn(
                'users',
                'created_by'
            )
        ) {
            Schema::table(
                'users',
                function (Blueprint $table) {
                    $table->unsignedBigInteger(
                        'created_by'
                    )
                        ->nullable()
                        ->index()
                        ->after('role');
                }
            );
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        $columns = [];

        foreach (
            [
                'must_change_password',
                'temporary_password_expires_at',
                'temporary_password_sent_at',
                'password_changed_at',
                'created_by',
            ] as $column
        ) {
            if (
                Schema::hasColumn(
                    'users',
                    $column
                )
            ) {
                $columns[] = $column;
            }
        }

        if (empty($columns)) {
            return;
        }

        Schema::table(
            'users',
            function (Blueprint $table) use (
                $columns
            ) {
                $table->dropColumn($columns);
            }
        );
    }
};
