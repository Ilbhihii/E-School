<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('student_payments')) return;
        Schema::table('student_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('student_payments', 'payment_provider')) {
                $table->string('payment_provider', 40)->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('student_payments', 'provider_reference')) {
                $table->string('provider_reference', 191)->nullable()->after('payment_provider');
                $table->unique(['payment_provider', 'provider_reference'], 'student_payments_provider_reference_unique');
            }
        });
    }
    public function down(): void
    {
        if (!Schema::hasTable('student_payments')) return;
        Schema::table('student_payments', function (Blueprint $table) {
            if (Schema::hasColumn('student_payments', 'provider_reference')) {
                $table->dropUnique('student_payments_provider_reference_unique');
                $table->dropColumn('provider_reference');
            }
            if (Schema::hasColumn('student_payments', 'payment_provider')) $table->dropColumn('payment_provider');
        });
    }
};
