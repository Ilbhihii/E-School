<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubscriptionFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'trial_ends_at')) {
                $table->timestamp('trial_ends_at')->nullable()->after('password');
            }
            if (!Schema::hasColumn('users', 'is_subscribed')) {
                $table->boolean('is_subscribed')->default(false)->after('trial_ends_at');
            }
            if (!Schema::hasColumn('users', 'subscription_type')) {
                $table->string('subscription_type')->nullable();
            }
            if (!Schema::hasColumn('users', 'payment_date')) {
                $table->date('payment_date')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['trial_ends_at', 'is_subscribed','subscription_type', 'payment_date']);
        });
    }

}
