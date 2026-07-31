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

        $needsPaymentPlan = !Schema::hasColumn(
            'test_appointments',
            'payment_plan'
        );

        $needsPaymentInvitedAt = !Schema::hasColumn(
            'test_appointments',
            'payment_invited_at'
        );

        $needsPaymentInvitationCount = !Schema::hasColumn(
            'test_appointments',
            'payment_invitation_count'
        );

        if (
            !$needsPaymentPlan
            && !$needsPaymentInvitedAt
            && !$needsPaymentInvitationCount
        ) {
            return;
        }

        Schema::table(
            'test_appointments',
            function (Blueprint $table) use (
                $needsPaymentPlan,
                $needsPaymentInvitedAt,
                $needsPaymentInvitationCount
            ) {
                if ($needsPaymentPlan) {
                    $table->string('payment_plan', 50)
                        ->nullable()
                        ->after('notes');
                }

                if ($needsPaymentInvitedAt) {
                    $table->timestamp('payment_invited_at')
                        ->nullable()
                        ->after(
                            $needsPaymentPlan
                                ? 'payment_plan'
                                : 'notes'
                        );
                }

                if ($needsPaymentInvitationCount) {
                    $table->unsignedInteger(
                        'payment_invitation_count'
                    )
                        ->default(0)
                        ->after(
                            $needsPaymentInvitedAt
                                ? 'payment_invited_at'
                                : (
                                    $needsPaymentPlan
                                        ? 'payment_plan'
                                        : 'notes'
                                )
                        );
                }
            }
        );
    }

    public function down(): void
    {
        if (!Schema::hasTable('test_appointments')) {
            return;
        }

        $columns = [];

        foreach (
            [
                'payment_invitation_count',
                'payment_invited_at',
                'payment_plan',
            ] as $column
        ) {
            if (Schema::hasColumn('test_appointments', $column)) {
                $columns[] = $column;
            }
        }

        if (empty($columns)) {
            return;
        }

        Schema::table(
            'test_appointments',
            function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            }
        );
    }
};
