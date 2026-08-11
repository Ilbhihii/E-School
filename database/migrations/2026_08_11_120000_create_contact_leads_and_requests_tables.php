<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactLeadsAndRequestsTables extends Migration
{
    public function up()
    {
        Schema::create('contact_leads', function (Blueprint $table) {
            $table->id();

            $table->string('first_name', 100);
            $table->string('last_name', 100);

            $table->string('email', 190);
            $table->string('email_normalized', 190)->unique();

            $table->string('phone', 30);
            $table->string('phone_normalized', 40)->unique();

            $table->text('latest_reason')->nullable();

            $table
                ->unsignedInteger('submissions_count')
                ->default(1);

            $table
                ->boolean('marketing_consent')
                ->default(false);

            $table->timestamp('first_contact_at')->nullable();
            $table->timestamp('last_contact_at')->nullable();
            $table->timestamp('sheet_synced_at')->nullable();

            $table->timestamps();

            $table->index('submissions_count');
            $table->index('marketing_consent');
            $table->index('last_contact_at');
        });

        Schema::create('contact_requests', function (Blueprint $table) {
            $table->id();

            $table
                ->foreignId('contact_lead_id')
                ->constrained('contact_leads')
                ->cascadeOnDelete();

            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', 190);
            $table->string('phone', 30);
            $table->text('reason');

            $table
                ->boolean('marketing_consent')
                ->default(false);

            $table
                ->string('source', 50)
                ->default('homepage');

            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();

            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('contact_requests');
        Schema::dropIfExists('contact_leads');
    }
}
