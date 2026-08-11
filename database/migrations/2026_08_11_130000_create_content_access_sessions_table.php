<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentAccessSessionsTable extends Migration
{
    public function up()
    {
        if (
            Schema::hasTable(
                'content_access_sessions'
            )
        ) {
            return;
        }

        Schema::create(
            'content_access_sessions',
            function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger(
                    'user_id'
                );

                $table->string(
                    'device_id',
                    80
                );

                $table->string(
                    'device_label',
                    120
                )->nullable();

                $table->string(
                    'content_type',
                    20
                );

                $table->unsignedBigInteger(
                    'content_id'
                );

                $table->string(
                    'content_title'
                )->nullable();

                $table->timestamp(
                    'last_seen_at'
                );

                $table->timestamp(
                    'expires_at'
                );

                $table->string(
                    'ip_address',
                    45
                )->nullable();

                $table->text(
                    'user_agent'
                )->nullable();

                $table->timestamps();

                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');

                $table->unique(
                    [
                        'user_id',
                        'device_id',
                        'content_type',
                        'content_id',
                    ],
                    'content_access_session_unique'
                );

                $table->index(
                    [
                        'user_id',
                        'expires_at',
                    ],
                    'content_access_user_expiry_idx'
                );
            }
        );
    }

    public function down()
    {
        Schema::dropIfExists(
            'content_access_sessions'
        );
    }
}
