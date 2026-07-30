<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'high_school_test_submissions',
            function (Blueprint $table) {
                if (
                    !Schema::hasColumn(
                        'high_school_test_submissions',
                        'reviewed_by'
                    )
                ) {
                    $table->foreignId('reviewed_by')
                        ->nullable()
                        ->after('teacher_comment')
                        ->constrained('users')
                        ->nullOnDelete();
                }

                if (
                    !Schema::hasColumn(
                        'high_school_test_submissions',
                        'image_annotations'
                    )
                ) {
                    $table->json('image_annotations')
                        ->nullable()
                        ->after('reviewed_by');
                }

                if (
                    !Schema::hasColumn(
                        'high_school_test_submissions',
                        'access_granted_at'
                    )
                ) {
                    $table->timestamp('access_granted_at')
                        ->nullable()
                        ->after('reviewed_at');
                }
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'high_school_test_submissions',
            function (Blueprint $table) {
                if (
                    Schema::hasColumn(
                        'high_school_test_submissions',
                        'reviewed_by'
                    )
                ) {
                    $table->dropForeign(['reviewed_by']);
                    $table->dropColumn('reviewed_by');
                }

                if (
                    Schema::hasColumn(
                        'high_school_test_submissions',
                        'image_annotations'
                    )
                ) {
                    $table->dropColumn(
                        'image_annotations'
                    );
                }

                if (
                    Schema::hasColumn(
                        'high_school_test_submissions',
                        'access_granted_at'
                    )
                ) {
                    $table->dropColumn(
                        'access_granted_at'
                    );
                }
            }
        );
    }
};
