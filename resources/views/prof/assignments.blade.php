@extends('layouts.prof')

@section('content')
<div class="container-fluid py-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card shadow-lg border-0" style="border-radius: 20px; backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.95);">
                <div class="card-body p-5">
                <div class="d-flex align-items-center mb-5">
                    <div class="bg-primary bg-gradient rounded-circle p-3 me-3 shadow-sm" style="width: 60px; height: 60px;">
                        <i class="bi bi-journal-text fs-4 text-white"></i>
                    </div>
                    <div>
                        <h2 class="mb-1 text-dark fw-bold">Devoirs des Étudiants</h2>
                        <p class="mb-0 text-muted">Consultez et corrigez les devoirs soumis par vos étudiants</p>
                    </div>
                </div>

@if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert" style="border-radius: 15px; background: linear-gradient(135deg, #d4edda, #c3e6cb);">
                        <i class="bi bi-check-circle-fill me-2 text-success"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
@endif

                    <div class="table-responsive shadow-lg rounded-3 overflow-hidden mb-4" style="background: rgba(255,255,255,0.7);">
                        <table class="table table-hover mb-0" style="background: transparent;">

<tr>
<th>Étudiant</th>
<th>Devoir</th>
<th>Fichier</th>
<th>Note</th>
<th>Correction</th>
</tr>

@foreach($assignments as $a)

<tr>

<td>
<strong>{{ $a->user->name }}</strong>
</td>

<td>
{{ $a->title }}
</td>

<td>
<a href="{{ asset('storage/'.$a->file) }}" target="_blank" class="btn btn-sm btn-outline-primary shadow-sm px-3" style="border-radius: 25px; transition: all 0.3s ease;">
    <i class="bi bi-eye me-1"></i>Voir fichier
</a>
</td>

<td>
@if($a->grade == 20)
    <span class="badge bg-success px-3 py-2 shadow-sm" style="border-radius: 25px; font-size: 0.9rem; font-weight: 600;">
        <i class="bi bi-check-circle-fill me-1"></i>Bien fait
    </span>
@elseif($a->grade == 0)
    <span class="badge bg-danger px-3 py-2 shadow-sm" style="border-radius: 25px; font-size: 0.9rem; font-weight: 600;">
        <i class="bi bi-x-circle-fill me-1"></i>N'a pas fait
    </span>
@else
    <span class="badge bg-warning px-3 py-2 shadow-sm text-dark" style="border-radius: 25px; font-size: 0.9rem; font-weight: 600;">
        <i class="bi bi-clock me-1"></i>Non corrigé
    </span>
@endif
</td>

<td>
<form method="POST" action="{{ route('prof.grade') }}">
@csrf
<input type="hidden" name="id" value="{{ $a->id }}">
<div class="mb-3">
    <div class="form-check mb-2">
        <input class="form-check-input" type="radio" name="status" value="bien" id="bien{{ $a->id }}" required>
        <label class="form-check-label fw-semibold text-success fs-6" for="bien{{ $a->id }}">
            ✅ Bien fait le devoir
        </label>
    </div>
    <div class="form-check">
        <input class="form-check-input" type="radio" name="status" value="pas" id="pas{{ $a->id }}">
        <label class="form-check-label fw-semibold text-danger fs-6" for="pas{{ $a->id }}">
            ❌ N'a pas fait le devoir
        </label>
    </div>
</div>
<textarea
    name="comment"
    class="form-control form-control-sm shadow-sm mb-3"
    placeholder="Commentaire de correction..."
    rows="2"
    style="border-radius: 10px; resize: vertical;"
></textarea>
<button type="submit" class="btn btn-success btn-sm px-4 shadow-lg border-0" style="border-radius: 25px;">
    <i class="bi bi-check-lg me-1"></i>Corriger
</button>
</form>
</td>

</tr>

@endforeach

</table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
.table thead th {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
    border: none !important;
    font-weight: 700 !important;
    color: #2c3e50 !important;
    padding: 18px 16px !important;
    border-radius: 12px 12px 0 0 !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    position: relative;
    overflow: hidden;
}
.table thead th::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    transition: left 0.5s;
}
.table thead th:hover::before {
    left: 100%;
}

.table tbody tr {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-bottom: 1px solid rgba(0,0,0,0.06);
}
.table tbody tr:hover {
    background: linear-gradient(135deg, rgba(13,110,253,0.06), rgba(52,152,219,0.04)) !important;
    transform: translateY(-1px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}

td {
    vertical-align: middle;
    padding: 16px !important;
}

.form-control:focus {
    border-color: #0d6efd !important;
    box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.15) !important;
    transform: translateY(-1px);
}

.btn {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    border-radius: 12px !important;
}
.btn:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.badge {
    font-size: 0.85rem !important;
    padding: 8px 16px !important;
    border-radius: 25px !important;
    font-weight: 600 !important;
}

@media (max-width: 768px) {
    .table-responsive {
        border-radius: 15px;
        margin: 0 -15px;
        border: 1px solid rgba(0,0,0,0.05);
    }
}
</style>

@endsection
