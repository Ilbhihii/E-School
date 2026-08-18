<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('plans')) {
            return;
        }

        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('code', 60)->unique();
            $table->string('name', 120);
            $table->string('subtitle', 255)->nullable();
            $table->string('scope', 160)->nullable();
            $table->unsignedBigInteger('amount_minor')->default(0);
            $table->string('currency', 3)->default('mad');
            $table->string('currency_symbol', 10)->default('DH');
            $table->string('period', 30)->default('an');
            $table->string('badge', 80)->nullable();
            $table->string('icon', 100)->default('bi-stars');
            $table->json('features')->nullable();
            $table->boolean('restricted_to_high_school')->default(false);
            $table->boolean('allow_paypal')->default(true);
            $table->boolean('allow_bank')->default(true);
            $table->string('paypal_url', 500)->nullable();
            $table->boolean('is_recommended')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'sort_order']);
        });

        /*
         * Import automatique de TOUTES les offres actuellement présentes
         * dans config/plans.php. On ne force donc pas une offre qui n'existe
         * pas déjà dans le projet de l'utilisateur.
         */
        $offers = (array) config('plans.offers', []);
        $defaultCode = (string) config('plans.default', 'premium');
        $sortOrder = 10;
        $now = now();

        foreach ($offers as $code => $offer) {
            $offer = (array) $offer;
            $badge = (string) ($offer['badge'] ?? '');

            DB::table('plans')->insert([
                'code' => (string) ($offer['code'] ?? $code),
                'name' => (string) ($offer['name'] ?? $code),
                'subtitle' => $offer['subtitle'] ?? null,
                'scope' => $offer['scope'] ?? null,
                'amount_minor' => (int) ($offer['amount_minor'] ?? 0),
                'currency' => strtolower((string) ($offer['currency'] ?? 'mad')),
                'currency_symbol' => (string) ($offer['currency_symbol'] ?? 'DH'),
                'period' => (string) ($offer['period'] ?? 'an'),
                'badge' => $badge !== '' ? $badge : null,
                'icon' => (string) ($offer['icon'] ?? 'bi-stars'),
                'features' => json_encode(
                    array_values((array) ($offer['features'] ?? [])),
                    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                ),
                'restricted_to_high_school' => (bool) ($offer['restricted_to_high_school'] ?? false),
                'allow_paypal' => (bool) ($offer['allow_paypal'] ?? true),
                'allow_bank' => (bool) ($offer['allow_bank'] ?? true),
                'paypal_url' => $offer['paypal_url']
                    ?? 'https://www.paypal.me/abdelghanimaloulou1',
                'is_recommended' =>
                    ((string) ($offer['code'] ?? $code) === $defaultCode)
                    || mb_strtolower($badge) === 'recommandé',
                'is_active' => (bool) ($offer['is_active'] ?? true),
                'sort_order' => (int) ($offer['sort_order'] ?? $sortOrder),
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $sortOrder += 10;
        }
    }

    public function down()
    {
        Schema::dropIfExists('plans');
    }
}
