<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('test_appointments')) {
            return;
        }

        $columns = [
            'payment_plan_name' => !Schema::hasColumn(
                'test_appointments',
                'payment_plan_name'
            ),
            'payment_duration_months' => !Schema::hasColumn(
                'test_appointments',
                'payment_duration_months'
            ),
            'payment_amount_minor' => !Schema::hasColumn(
                'test_appointments',
                'payment_amount_minor'
            ),
            'payment_currency' => !Schema::hasColumn(
                'test_appointments',
                'payment_currency'
            ),
            'payment_currency_symbol' => !Schema::hasColumn(
                'test_appointments',
                'payment_currency_symbol'
            ),
        ];

        if (!in_array(true, $columns, true)) {
            return;
        }

        Schema::table(
            'test_appointments',
            function (Blueprint $table) use ($columns) {
                if ($columns['payment_plan_name']) {
                    $table->string('payment_plan_name', 120)
                        ->nullable()
                        ->after('payment_plan');
                }

                if ($columns['payment_duration_months']) {
                    $table->unsignedTinyInteger(
                        'payment_duration_months'
                    )
                        ->nullable()
                        ->after('payment_plan_name');
                }

                if ($columns['payment_amount_minor']) {
                    $table->unsignedBigInteger(
                        'payment_amount_minor'
                    )
                        ->nullable()
                        ->after('payment_duration_months');
                }

                if ($columns['payment_currency']) {
                    $table->string('payment_currency', 10)
                        ->nullable()
                        ->after('payment_amount_minor');
                }

                if ($columns['payment_currency_symbol']) {
                    $table->string(
                        'payment_currency_symbol',
                        10
                    )
                        ->nullable()
                        ->after('payment_currency');
                }
            }
        );
    }

    public function down(): void
    {
        if (!Schema::hasTable('test_appointments')) {
            return;
        }

        $drop = [];

        foreach (
            [
                'payment_currency_symbol',
                'payment_currency',
                'payment_amount_minor',
                'payment_duration_months',
                'payment_plan_name',
            ] as $column
        ) {
            if (Schema::hasColumn('test_appointments', $column)) {
                $drop[] = $column;
            }
        }

        if (empty($drop)) {
            return;
        }

        Schema::table(
            'test_appointments',
            function (Blueprint $table) use ($drop) {
                $table->dropColumn($drop);
            }
        );
    }
};
