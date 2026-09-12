<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            !Schema::hasColumn(
                'messages',
                'private_professor_id'
            )
        ) {
            Schema::table(
                'messages',
                function (Blueprint $table) {
                    $table
                        ->foreignId(
                            'private_professor_id'
                        )
                        ->nullable()
                        ->after(
                            'conversation_user_id'
                        )
                        ->constrained('users')
                        ->cascadeOnDelete();

                    $table->index(
                        [
                            'subject_id',
                            'conversation_user_id',
                            'private_professor_id',
                        ],
                        'messages_private_conversation_idx'
                    );
                }
            );
        }
    }

    public function down(): void
    {
        if (
            Schema::hasColumn(
                'messages',
                'private_professor_id'
            )
        ) {
            Schema::table(
                'messages',
                function (Blueprint $table) {
                    $table->dropIndex(
                        'messages_private_conversation_idx'
                    );

                    $table->dropForeign([
                        'private_professor_id',
                    ]);

                    $table->dropColumn(
                        'private_professor_id'
                    );
                }
            );
        }
    }
};
