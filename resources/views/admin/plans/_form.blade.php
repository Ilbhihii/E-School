@php
    $editing = $plan->exists;
    $featureValues = old('features', $plan->features ?: ['']);
    if (!is_array($featureValues) || count($featureValues) === 0) {
        $featureValues = [''];
    }
@endphp

@if($errors->any())
    <div class="plan-form-alert">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <div><strong>Vérifiez le formulaire.</strong><span>{{ $errors->first() }}</span></div>
    </div>
@endif

<div class="plan-form-grid">
    <section class="plan-form-card">
        <div class="plan-card-head">
            <span><i class="bi bi-card-heading"></i></span>
            <div><small>Identité</small><h3>Informations de l’offre</h3></div>
        </div>

        <div class="plan-fields two">
            <label>
                <span>Nom de l’offre *</span>
                <input type="text" name="name" value="{{ old('name', $plan->name) }}" required maxlength="120" placeholder="Premium">
            </label>
            <label>
                <span>Code interne *</span>
                <input type="text" name="code" value="{{ old('code', $plan->code) }}" required maxlength="60" pattern="[a-z0-9_]+" placeholder="premium" {{ $editing ? 'readonly' : '' }}>
                <small>{{ $editing ? 'Protégé : les abonnés utilisent ce code.' : 'Minuscules, chiffres et underscore uniquement.' }}</small>
            </label>
        </div>

        <div class="plan-fields two">
            <label>
                <span>Périmètre affiché</span>
                <input type="text" name="scope" value="{{ old('scope', $plan->scope) }}" maxlength="160" placeholder="Tous les parcours">
            </label>
            <label>
                <span>Badge</span>
                <input type="text" name="badge" value="{{ old('badge', $plan->badge) }}" maxlength="80" placeholder="Recommandé">
            </label>
        </div>

        <label class="plan-field-full">
            <span>Sous-titre</span>
            <input type="text" name="subtitle" value="{{ old('subtitle', $plan->subtitle) }}" maxlength="255" placeholder="Accès complet et illimité">
        </label>

        <div class="plan-fields two">
            <label>
                <span>Icône Bootstrap *</span>
                <div class="plan-icon-input">
                    <i id="planIconPreview" class="bi {{ old('icon', $plan->icon ?: 'bi-stars') }}"></i>
                    <input id="planIconInput" type="text" name="icon" value="{{ old('icon', $plan->icon ?: 'bi-stars') }}" required placeholder="bi-stars">
                </div>
                <small>Ex. bi-stars, bi-mortarboard-fill, bi-book-fill.</small>
            </label>
            <label>
                <span>Ordre d’affichage *</span>
                <input type="number" name="sort_order" min="0" max="999999" value="{{ old('sort_order', $plan->sort_order ?? 10) }}" required>
                <small>Le plus petit nombre apparaît en premier.</small>
            </label>
        </div>
    </section>

    <section class="plan-form-card">
        <div class="plan-card-head">
            <span><i class="bi bi-cash-stack"></i></span>
            <div><small>Tarification</small><h3>Prix et période</h3></div>
        </div>

        <div class="plan-fields three">
            <label>
                <span>Prix *</span>
                <input type="number" name="price" min="0" step="0.01" value="{{ old('price', $plan->exists ? $plan->amount_major : '0.00') }}" required>
            </label>
            <label>
                <span>Devise *</span>
                <select name="currency" id="planCurrency" required>
                    @foreach(['mad' => 'MAD', 'eur' => 'EUR', 'usd' => 'USD', 'gbp' => 'GBP'] as $value => $label)
                        <option value="{{ $value }}" {{ old('currency', $plan->currency ?: 'mad') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                <span>Symbole *</span>
                <input type="text" id="planCurrencySymbol" name="currency_symbol" maxlength="10" value="{{ old('currency_symbol', $plan->currency_symbol ?: 'DH') }}" required>
            </label>
        </div>

        <label class="plan-field-full">
            <span>Période *</span>
            <select name="period" required>
                @foreach(['an' => 'Par an', 'mois' => 'Par mois', 'trimestre' => 'Par trimestre', 'semestre' => 'Par semestre', 'paiement unique' => 'Paiement unique'] as $value => $label)
                    <option value="{{ $value }}" {{ old('period', $plan->period ?: 'an') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </label>
    </section>

    <section class="plan-form-card plan-form-card-wide">
        <div class="plan-card-head plan-card-head-between">
            <div class="d-flex align-items-center gap-2">
                <span><i class="bi bi-check2-square"></i></span>
                <div><small>Contenu</small><h3>Fonctionnalités incluses</h3></div>
            </div>
            <button type="button" id="addPlanFeature" class="plan-add-feature"><i class="bi bi-plus-lg"></i> Ajouter</button>
        </div>

        <div id="planFeatures" class="plan-features-editor">
            @foreach($featureValues as $feature)
                <div class="plan-feature-row">
                    <span><i class="bi bi-check-lg"></i></span>
                    <input type="text" name="features[]" maxlength="255" value="{{ $feature }}" placeholder="Ex. Tous les cours et documents PDF">
                    <button type="button" class="remove-plan-feature" title="Retirer"><i class="bi bi-x-lg"></i></button>
                </div>
            @endforeach
        </div>
    </section>

    <section class="plan-form-card">
        <div class="plan-card-head">
            <span><i class="bi bi-credit-card-2-front-fill"></i></span>
            <div><small>Paiement</small><h3>Méthodes autorisées</h3></div>
        </div>

        <div class="plan-toggle-list">
            <label class="plan-toggle-row">
                <div><i class="bi bi-paypal"></i><span><strong>PayPal</strong><small>Afficher le bouton PayPal.</small></span></div>
                <input type="hidden" name="allow_paypal" value="0">
                <input type="checkbox" name="allow_paypal" value="1" {{ old('allow_paypal', $plan->allow_paypal) ? 'checked' : '' }}>
            </label>
            <label class="plan-toggle-row">
                <div><i class="bi bi-bank"></i><span><strong>Virement bancaire</strong><small>Afficher le bouton de virement.</small></span></div>
                <input type="hidden" name="allow_bank" value="0">
                <input type="checkbox" name="allow_bank" value="1" {{ old('allow_bank', $plan->allow_bank) ? 'checked' : '' }}>
            </label>
        </div>

        <label class="plan-field-full mt-3">
            <span>Lien PayPal</span>
            <input type="url" name="paypal_url" value="{{ old('paypal_url', $plan->paypal_url) }}" maxlength="500" placeholder="https://www.paypal.me/...">
        </label>
    </section>

    <section class="plan-form-card">
        <div class="plan-card-head">
            <span><i class="bi bi-sliders2"></i></span>
            <div><small>Publication</small><h3>Statut et accès</h3></div>
        </div>

        <div class="plan-toggle-list">
            <label class="plan-toggle-row">
                <div><i class="bi bi-eye-fill"></i><span><strong>Offre active</strong><small>Visible immédiatement sur /plans.</small></span></div>
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $plan->is_active) ? 'checked' : '' }}>
            </label>
            <label class="plan-toggle-row">
                <div><i class="bi bi-stars"></i><span><strong>Offre recommandée</strong><small>Une seule offre peut être recommandée.</small></span></div>
                <input type="hidden" name="is_recommended" value="0">
                <input type="checkbox" name="is_recommended" value="1" {{ old('is_recommended', $plan->is_recommended) ? 'checked' : '' }}>
            </label>
            <label class="plan-toggle-row warning">
                <div><i class="bi bi-mortarboard-fill"></i><span><strong>Soutien Lycée uniquement</strong><small>Bloque Arabe et Coran pour les abonnés de cette offre.</small></span></div>
                <input type="hidden" name="restricted_to_high_school" value="0">
                <input type="checkbox" name="restricted_to_high_school" value="1" {{ old('restricted_to_high_school', $plan->restricted_to_high_school) ? 'checked' : '' }}>
            </label>
        </div>
    </section>
</div>

<div class="plan-form-actions">
    <a href="{{ route('admin.plans.index') }}" class="plan-form-cancel"><i class="bi bi-arrow-left"></i> Annuler</a>
    <button type="submit" class="plan-form-submit"><i class="bi bi-check2-circle"></i> {{ $editing ? 'Enregistrer les modifications' : 'Créer l’offre' }}</button>
</div>

@once
<style>
.plan-editor-page{max-width:1180px;margin:0 auto;color:#e5edf8}.plan-editor-intro{display:flex;align-items:center;justify-content:space-between;gap:20px;margin-bottom:17px;padding:20px 22px;border:1px solid rgba(148,163,184,.12);border-radius:18px;background:linear-gradient(145deg,#101d31,#0a1423)}.plan-editor-intro h2{margin:0 0 5px;color:#fff;font-size:1.28rem;font-weight:800}.plan-editor-intro p{margin:0;color:#76879f;font-size:.7rem}.plan-editor-intro a{color:#a9b9cf;font-size:.68rem;text-decoration:none}.plan-form-alert{display:flex;gap:10px;align-items:flex-start;margin-bottom:14px;padding:12px 14px;border:1px solid rgba(239,68,68,.18);border-radius:12px;color:#f3a1aa;background:rgba(239,68,68,.07);font-size:.68rem}.plan-form-alert span{display:block;margin-top:2px;color:#c9838b}.plan-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}.plan-form-card{padding:18px;border:1px solid rgba(148,163,184,.11);border-radius:17px;background:#0c1627}.plan-form-card-wide{grid-column:1/-1}.plan-card-head{display:flex;align-items:center;gap:10px;margin-bottom:16px}.plan-card-head>span,.plan-card-head-between>div>span{width:36px;height:36px;display:grid;place-items:center;flex:0 0 36px;border-radius:10px;color:#9db0ff;background:rgba(79,114,245,.1)}.plan-card-head small{display:block;color:#62728a;font-size:.52rem;text-transform:uppercase;letter-spacing:.09em}.plan-card-head h3{margin:2px 0 0;color:#eff4fb;font-size:.82rem;font-weight:800}.plan-card-head-between{justify-content:space-between}.plan-fields{display:grid;gap:10px;margin-bottom:10px}.plan-fields.two{grid-template-columns:repeat(2,minmax(0,1fr))}.plan-fields.three{grid-template-columns:1.25fr .8fr .75fr}.plan-fields label,.plan-field-full{display:block}.plan-fields label>span,.plan-field-full>span{display:block;margin-bottom:5px;color:#aab7c9;font-size:.58rem;font-weight:700}.plan-fields input,.plan-fields select,.plan-field-full input,.plan-field-full select{width:100%;height:41px;padding:0 11px;color:#e9eff8;border:1px solid rgba(148,163,184,.13);border-radius:10px;outline:0;background:#08111f;font-size:.68rem}.plan-fields input:focus,.plan-fields select:focus,.plan-field-full input:focus,.plan-field-full select:focus{border-color:rgba(79,114,245,.5);box-shadow:0 0 0 3px rgba(79,114,245,.08)}.plan-fields input[readonly]{color:#7f8fa7;background:#0a1320;cursor:not-allowed}.plan-fields label>small{display:block;margin-top:4px;color:#54657d;font-size:.51rem;line-height:1.35}.plan-icon-input{display:flex;align-items:center;border:1px solid rgba(148,163,184,.13);border-radius:10px;background:#08111f}.plan-icon-input>i{width:38px;text-align:center;color:#aab9ff}.plan-icon-input input{border:0!important;background:transparent!important;box-shadow:none!important}.plan-add-feature{display:inline-flex;align-items:center;gap:5px;padding:7px 9px;color:#9fb2ff;border:1px solid rgba(79,114,245,.18);border-radius:8px;background:rgba(79,114,245,.06);font-size:.56rem;font-weight:800}.plan-features-editor{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px}.plan-feature-row{display:flex;align-items:center;gap:7px;padding:7px 8px;border:1px solid rgba(148,163,184,.09);border-radius:10px;background:#08111f}.plan-feature-row>span{width:23px;height:23px;display:grid;place-items:center;flex:0 0 23px;border-radius:7px;color:#57d5a6;background:rgba(36,183,134,.09);font-size:.5rem}.plan-feature-row input{min-width:0;flex:1;height:28px;color:#dfe7f2;border:0;outline:0;background:transparent;font-size:.62rem}.remove-plan-feature{width:27px;height:27px;display:grid;place-items:center;border:0;border-radius:7px;color:#8e6670;background:rgba(224,91,104,.06);font-size:.55rem}.plan-toggle-list{display:flex;flex-direction:column;gap:7px}.plan-toggle-row{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:10px;border:1px solid rgba(148,163,184,.09);border-radius:11px;background:#08111f;cursor:pointer}.plan-toggle-row>div{display:flex;align-items:center;gap:9px}.plan-toggle-row>div>i{width:28px;color:#8ea7ff;text-align:center}.plan-toggle-row strong{display:block;color:#dfe7f2;font-size:.62rem}.plan-toggle-row small{display:block;margin-top:2px;color:#596a82;font-size:.5rem}.plan-toggle-row.warning>div>i{color:#efb34d}.plan-toggle-row input{width:17px;height:17px;accent-color:#5a72ee}.plan-form-actions{display:flex;justify-content:flex-end;gap:9px;margin-top:15px}.plan-form-cancel,.plan-form-submit{min-height:42px;display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:0 14px;border-radius:10px;font-size:.64rem;font-weight:800;text-decoration:none}.plan-form-cancel{color:#91a1b7;border:1px solid rgba(148,163,184,.12);background:#0b1422}.plan-form-submit{color:#fff;border:0;background:linear-gradient(135deg,#4569ef,#7354e8);box-shadow:0 10px 22px rgba(79,114,245,.18)}@media(max-width:900px){.plan-form-grid{grid-template-columns:1fr}.plan-form-card-wide{grid-column:auto}.plan-features-editor{grid-template-columns:1fr}}@media(max-width:620px){.plan-fields.two,.plan-fields.three{grid-template-columns:1fr}.plan-editor-intro{align-items:flex-start;flex-direction:column}.plan-form-actions{align-items:stretch;flex-direction:column}.plan-form-cancel,.plan-form-submit{width:100%}}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const list = document.getElementById('planFeatures');
    const add = document.getElementById('addPlanFeature');
    const iconInput = document.getElementById('planIconInput');
    const iconPreview = document.getElementById('planIconPreview');
    const currency = document.getElementById('planCurrency');
    const symbol = document.getElementById('planCurrencySymbol');

    const rowHtml = () => `
        <div class="plan-feature-row">
            <span><i class="bi bi-check-lg"></i></span>
            <input type="text" name="features[]" maxlength="255" placeholder="Nouvelle fonctionnalité">
            <button type="button" class="remove-plan-feature" title="Retirer"><i class="bi bi-x-lg"></i></button>
        </div>`;

    add?.addEventListener('click', function () {
        if (!list || list.children.length >= 25) return;
        list.insertAdjacentHTML('beforeend', rowHtml());
        list.lastElementChild?.querySelector('input')?.focus();
    });

    list?.addEventListener('click', function (event) {
        const button = event.target.closest('.remove-plan-feature');
        if (!button) return;
        if (list.children.length === 1) {
            const input = list.querySelector('input');
            if (input) input.value = '';
            return;
        }
        button.closest('.plan-feature-row')?.remove();
    });

    iconInput?.addEventListener('input', function () {
        const value = (iconInput.value || 'bi-stars').trim();
        iconPreview.className = 'bi ' + (value.startsWith('bi-') ? value : 'bi-stars');
    });

    const symbols = { mad: 'DH', eur: '€', usd: '$', gbp: '£' };
    currency?.addEventListener('change', function () {
        if (symbol && symbols[currency.value]) symbol.value = symbols[currency.value];
    });
});
</script>
@endonce
