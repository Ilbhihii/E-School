<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::query()
            ->ordered()
            ->get();

        $stats = [
            'total' => $plans->count(),
            'active' => $plans->where('is_active', true)->count(),
            'inactive' => $plans->where('is_active', false)->count(),
            'recommended' => $plans->where('is_recommended', true)->count(),
        ];

        return view(
            'admin.plans.index',
            compact('plans', 'stats')
        );
    }

    public function create()
    {
        $plan = new Plan();
        $plan->currency = 'mad';
        $plan->currency_symbol = 'DH';
        $plan->period = 'an';
        $plan->icon = 'bi-stars';
        $plan->allow_paypal = true;
        $plan->allow_bank = true;
        $plan->is_active = true;
        $plan->restricted_to_high_school = false;
        $plan->is_recommended = false;
        $plan->sort_order = ((int) Plan::max('sort_order')) + 10;
        $plan->paypal_url = 'https://www.paypal.me/abdelghanimaloulou1';
        $plan->features = [];

        return view(
            'admin.plans.create',
            compact('plan')
        );
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        DB::transaction(function () use ($data) {
            if ($data['is_recommended']) {
                Plan::query()->update([
                    'is_recommended' => false,
                ]);
            }

            Plan::create($data);
        });

        return redirect()
            ->route('admin.plans.index')
            ->with(
                'success',
                'L’offre a été créée et sera affichée automatiquement sur /plans si elle est active.'
            );
    }

    public function edit(Plan $plan)
    {
        return view(
            'admin.plans.edit',
            compact('plan')
        );
    }

    public function update(
        Request $request,
        Plan $plan
    ) {
        /*
         * Le code est volontairement immuable : users.subscription_type
         * référence cette valeur. Le modifier casserait les anciens abonnés.
         */
        $data = $this->validatedData(
            $request,
            $plan
        );

        unset($data['code']);

        DB::transaction(function () use ($data, $plan) {
            if ($data['is_recommended']) {
                Plan::query()
                    ->where('id', '!=', $plan->id)
                    ->update([
                        'is_recommended' => false,
                    ]);
            }

            $plan->update($data);
        });

        return redirect()
            ->route('admin.plans.index')
            ->with(
                'success',
                'L’offre « ' . $plan->name . ' » a été mise à jour. Les nouvelles informations sont maintenant utilisées sur /plans et la page de paiement.'
            );
    }

    public function toggleStatus(Plan $plan)
    {
        $newStatus = !$plan->is_active;

        $plan->update([
            'is_active' => $newStatus,
            'is_recommended' => $newStatus
                ? $plan->is_recommended
                : false,
        ]);

        return back()->with(
            'success',
            $plan->is_active
                ? 'L’offre « ' . $plan->name . ' » est maintenant visible.'
                : 'L’offre « ' . $plan->name . ' » est maintenant masquée du site public.'
        );
    }

    public function destroy(Plan $plan)
    {
        $isUsed = User::query()
            ->where(
                'subscription_type',
                $plan->code
            )
            ->exists();

        /*
         * Les plans historiques et les offres déjà attribuées à un compte
         * ne sont jamais supprimés physiquement. Cela protège les abonnés.
         */
        if ($isUsed || $plan->isSystemPlan()) {
            $plan->update([
                'is_active' => false,
                'is_recommended' => false,
            ]);

            return back()->with(
                'success',
                'L’offre « ' . $plan->name . ' » a été désactivée au lieu d’être supprimée afin de préserver les comptes qui peuvent encore la référencer.'
            );
        }

        $name = $plan->name;
        $plan->delete();

        return back()->with(
            'success',
            'L’offre « ' . $name . ' » a été supprimée.'
        );
    }

    private function validatedData(
        Request $request,
        Plan $plan = null
    ) {
        $codeRules = [
            'required',
            'string',
            'max:60',
            'regex:/^[a-z0-9_]+$/',
        ];

        if (!$plan) {
            $codeRules[] = Rule::unique(
                'plans',
                'code'
            );
        }

        $validated = $request->validate([
            'code' => $codeRules,
            'name' => [
                'required',
                'string',
                'max:120',
            ],
            'subtitle' => [
                'nullable',
                'string',
                'max:255',
            ],
            'scope' => [
                'nullable',
                'string',
                'max:160',
            ],
            'price' => [
                'required',
                'numeric',
                'min:0',
                'max:99999999.99',
            ],
            'currency' => [
                'required',
                'string',
                'size:3',
                'regex:/^[A-Za-z]{3}$/',
            ],
            'currency_symbol' => [
                'required',
                'string',
                'max:10',
            ],
            'period' => [
                'required',
                'string',
                'max:30',
            ],
            'badge' => [
                'nullable',
                'string',
                'max:80',
            ],
            'icon' => [
                'required',
                'string',
                'max:100',
                'regex:/^bi-[a-z0-9-]+$/',
            ],
            'features' => [
                'required',
                'array',
                'min:1',
                'max:25',
            ],
            'features.*' => [
                'nullable',
                'string',
                'max:255',
            ],
            'paypal_url' => [
                'nullable',
                'url',
                'max:500',
            ],
            'sort_order' => [
                'required',
                'integer',
                'min:0',
                'max:999999',
            ],
        ]);

        $features = collect(
            $request->input('features', [])
        )
            ->map(function ($feature) {
                return trim((string) $feature);
            })
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($features)) {
            throw ValidationException::withMessages([
                'features' => 'Ajoutez au moins une fonctionnalité à l’offre.',
            ]);
        }

        return [
            'code' => $plan
                ? $plan->code
                : strtolower(
                    trim($validated['code'])
                ),
            'name' => trim($validated['name']),
            'subtitle' => $this->nullableTrim(
                $validated['subtitle'] ?? null
            ),
            'scope' => $this->nullableTrim(
                $validated['scope'] ?? null
            ),
            'amount_minor' => (int) round(
                ((float) $validated['price'])
                * 100
            ),
            'currency' => strtolower(
                $validated['currency']
            ),
            'currency_symbol' => trim(
                $validated['currency_symbol']
            ),
            'period' => trim(
                $validated['period']
            ),
            'badge' => $this->nullableTrim(
                $validated['badge'] ?? null
            ),
            'icon' => trim($validated['icon']),
            'features' => $features,
            'restricted_to_high_school' =>
                $request->boolean(
                    'restricted_to_high_school'
                ),
            'allow_paypal' =>
                $request->boolean(
                    'allow_paypal'
                ),
            'allow_bank' =>
                $request->boolean(
                    'allow_bank'
                ),
            'paypal_url' => $this->nullableTrim(
                $validated['paypal_url'] ?? null
            ),
            'is_recommended' =>
                $request->boolean('is_active')
                && $request->boolean(
                    'is_recommended'
                ),
            'is_active' =>
                $request->boolean('is_active'),
            'sort_order' => (int) $validated[
                'sort_order'
            ],
        ];
    }

    private function nullableTrim($value)
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
