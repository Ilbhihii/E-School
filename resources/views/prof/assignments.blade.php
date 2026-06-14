@extends('layouts.prof')

@section('title', 'Devoirs des Étudiants')
@section('page_title', 'Devoirs Étudiants')
@section('breadcrumb', 'Correction des devoirs')

@section('content')

<div class="adm-page-header">
    <div>
        <h1><i class="bi bi-journal-text me-2" style="color:var(--adm-primary);"></i> Devoirs des Étudiants</h1>
        <div class="subtitle">Consultez et corrigez les devoirs soumis par vos étudiants</div>
    </div>
</div>

@if(session('success'))
<div class="adm-alert adm-alert-success mb-4">
    <span class="adm-alert-icon"><i class="bi bi-check-circle-fill"></i></span>
    <span>{{ session('success') }}</span>
</div>
@endif

<div class="adm-card">
    <div class="adm-card-header">
        <h4><i class="bi bi-list-check" style="color:rgba(255,255,255,0.35);"></i> Soumissions des étudiants</h4>
        <div class="card-actions">
            <span style="color:var(--adm-text-muted);font-size:0.8rem;">{{ $assignments->count() }} soumission(s)</span>
        </div>
    </div>
    <div class="adm-card-body p-0">
        <div class="adm-table-wrap">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>Étudiant</th>
                        <th>Devoir</th>
                        <th>Fichier</th>
                        <th>Note</th>
                        <th style="text-align:right;">Correction</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $a)
                    <tr>
                        <td><span style="font-weight:500;">{{ $a->user->name ?? 'Inconnu' }}</span></td>
                        <td>{{ Str::limit($a->title, 40) }}</td>
                        <td>
                            <a href="{{ asset('storage/'.$a->file) }}" target="_blank" class="adm-btn adm-btn-primary adm-btn-sm">
                                <i class="bi bi-eye me-1"></i> Voir fichier
                            </a>
                        </td>
                        <td>
                            @if($a->grade == 20)
                                <span class="adm-badge adm-badge-success"><i class="bi bi-check-circle-fill me-1"></i> Bien fait</span>
                            @elseif($a->grade == 0)
                                <span class="adm-badge adm-badge-danger"><i class="bi bi-x-circle-fill me-1"></i> N'a pas fait</span>
                            @else
                                <span class="adm-badge adm-badge-warning"><i class="bi bi-clock me-1"></i> Non corrigé</span>
                            @endif
                        </td>
                        <td style="text-align:right;">
                            <form method="POST" action="{{ route('prof.grade') }}" style="max-width:300px;margin-left:auto;">
                                @csrf
                                <input type="hidden" name="id" value="{{ $a->id }}">
                                <div style="display:flex;gap:12px;margin-bottom:8px;justify-content:flex-end;">
                                    <label style="display:flex;align-items:center;gap:4px;cursor:pointer;font-size:0.85rem;color:var(--adm-success);font-weight:500;">
                                        <input type="radio" name="status" value="bien" required class="adm-form-radio"> ✅ Bien fait
                                    </label>
                                    <label style="display:flex;align-items:center;gap:4px;cursor:pointer;font-size:0.85rem;color:var(--adm-danger);font-weight:500;">
                                        <input type="radio" name="status" value="pas" class="adm-form-radio"> ❌ N'a pas fait
                                    </label>
                                </div>
                                <textarea name="comment" class="adm-form-control" placeholder="Commentaire de correction..." rows="2" style="resize:vertical;margin-bottom:8px;font-size:0.85rem;"></textarea>
                                <button type="submit" class="adm-btn adm-btn-success adm-btn-sm">
                                    <i class="bi bi-check-lg me-1"></i> Corriger
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="adm-empty">
                                <div class="adm-empty-icon"><i class="bi bi-inbox"></i></div>
                                <h5>Aucune soumission</h5>
                                <p>Aucun étudiant n'a encore soumis de devoir.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.adm-form-radio {
    appearance: none;
    width: 16px; height: 16px;
    border: 2px solid rgba(255,255,255,0.2);
    border-radius: 50%;
    outline: none;
    transition: all 0.2s;
    cursor: pointer;
}
.adm-form-radio:checked {
    border-color: var(--adm-primary);
    background: var(--adm-primary);
    box-shadow: 0 0 0 2px rgba(37,99,235,0.2);
}
</style>

@endsection
