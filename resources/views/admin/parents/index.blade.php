@extends('layouts.admin')
@section('title', 'Parents')
@section('page_title', 'Parents & Responsables')

@section('content')
<style>
.parent-admin-grid{display:grid;grid-template-columns:minmax(320px,.8fr) minmax(0,1.4fr);gap:18px}.parent-admin-card{padding:20px;border:1px solid rgba(148,163,184,.15);border-radius:18px;background:rgba(15,23,42,.72)}.parent-admin-form{display:grid;gap:11px}.parent-admin-control{width:100%;min-height:42px;padding:9px 11px;border:1px solid rgba(148,163,184,.17);border-radius:10px;color:#f8fafc;background:rgba(15,23,42,.72);font:inherit}select.parent-admin-control[multiple]{min-height:160px}.parent-item{padding:14px;margin-top:10px;border:1px solid rgba(148,163,184,.12);border-radius:14px}.parent-item small{color:#94a3b8}.parent-pills{display:flex;flex-wrap:wrap;gap:5px;margin-top:8px}.parent-pill{padding:4px 7px;border-radius:999px;background:rgba(148,163,184,.08);font-size:.6rem}@media(max-width:900px){.parent-admin-grid{grid-template-columns:1fr}}
</style>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

<div class="parent-admin-grid">
<section class="parent-admin-card">
<h3>Nouveau compte Parent</h3>
<form method="POST" action="{{ route('admin.parents.store') }}" class="parent-admin-form">@csrf
<input class="parent-admin-control" name="name" value="{{ old('name') }}" placeholder="Nom complet" required>
<input class="parent-admin-control" type="email" name="email" value="{{ old('email') }}" placeholder="E-mail" required>
<select class="parent-admin-control" name="relationship" required><option value="Père">Père</option><option value="Mère">Mère</option><option value="Tuteur">Tuteur</option><option value="Responsable">Responsable</option></select>
<select class="parent-admin-control" name="student_ids[]" multiple required>@foreach($students as $student)<option value="{{ $student->id }}">{{ $student->name }} — {{ $student->email }}</option>@endforeach</select>
<small style="color:#94a3b8">Ctrl + clic pour sélectionner plusieurs enfants.</small>
<input class="parent-admin-control" type="password" name="password" placeholder="Mot de passe" required>
<input class="parent-admin-control" type="password" name="password_confirmation" placeholder="Confirmer le mot de passe" required>
<button type="submit" class="adm-btn adm-btn-primary"><i class="bi bi-person-plus-fill"></i> Créer le parent</button>
</form>
</section>

<section class="parent-admin-card">
<h3>Comptes parents ({{ $parents->count() }})</h3>
@forelse($parents as $parent)
<article class="parent-item">
<strong>{{ $parent->name }}</strong><br><small>{{ $parent->email }}</small>
<div class="parent-pills">@forelse($parent->children_list as $child)<span class="parent-pill">{{ $child->name }} · {{ $child->parent_relationship }}</span>@empty<span class="parent-pill">Aucun enfant</span>@endforelse</div>

<form method="POST" action="{{ route('admin.parents.children.store', $parent) }}" class="parent-admin-form" style="margin-top:12px">@csrf
<select class="parent-admin-control" name="student_id" required><option value="">Associer un étudiant</option>@foreach($students as $student)<option value="{{ $student->id }}">{{ $student->name }}</option>@endforeach</select>
<select class="parent-admin-control" name="relationship" required><option value="Père">Père</option><option value="Mère">Mère</option><option value="Tuteur">Tuteur</option><option value="Responsable">Responsable</option></select>
<div style="display:flex;flex-wrap:wrap;gap:10px;color:#94a3b8;font-size:.65rem"><label><input type="checkbox" name="can_view_schedule" value="1" checked> Planning</label><label><input type="checkbox" name="can_view_absences" value="1" checked> Absences</label><label><input type="checkbox" name="can_view_assignments" value="1" checked> Devoirs</label><label><input type="checkbox" name="can_view_results" value="1" checked> Résultats</label></div>
<button type="submit" class="adm-btn adm-btn-ghost">Associer</button>
</form>

@foreach($parent->children_list as $child)
<form method="POST" action="{{ route('admin.parents.children.destroy', [$parent, $child]) }}" style="display:inline-block;margin-top:8px" onsubmit="return confirm('Retirer {{ addslashes($child->name) }} de ce parent ?');">@csrf @method('DELETE')<button class="adm-btn adm-btn-ghost" type="submit">Retirer {{ $child->name }}</button></form>
@endforeach
<form method="POST" action="{{ route('admin.parents.destroy', $parent) }}" style="margin-top:8px" onsubmit="return confirm('Supprimer ce compte Parent ?');">@csrf @method('DELETE')<button type="submit" class="adm-btn adm-btn-danger"><i class="bi bi-trash3"></i> Supprimer le parent</button></form>
</article>
@empty<p style="color:#94a3b8">Aucun parent enregistré.</p>@endforelse
</section>
</div>
@endsection
