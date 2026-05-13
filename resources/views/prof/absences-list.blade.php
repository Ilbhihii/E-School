@extends('layouts.prof')

@section('content')

<div class="container-fluid py-4" style="background: linear-gradient(135deg,#eef2ff,#f8fafc); min-height:100vh;">
<div class="container">

@if(session('success'))
<div class="alert alert-success shadow-sm rounded-3 mb-4">
    {{ session('success') }}
</div>
@endif

<!-- Enhanced Filter Form -->
<form method="GET" class="mb-4">
    <div class="row g-2">
        <div class="col-md-4">
            <select name="class_id" class="form-select">
                <option value="">-- Toutes classes --</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                        {{ $class->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100" type="submit">
                <i class="fas fa-search me-1"></i> Filtrer
            </button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('prof.absences.list') }}" class="btn btn-outline-secondary w-100">
                <i class="fas fa-times me-1"></i> Reset
            </a>
        </div>
    </div>
</form>

<div class="card border-0 shadow-xl" style="border-radius:20px; overflow:hidden;">

<!-- Header -->
<div class="card-header bg-gradient-primary text-white p-4 position-relative" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;">
    <div class="d-flex justify-content-between align-items-center w-100">
        <div>
            <h3 class="fw-bold mb-1">📋 Historique des absences</h3>
            <p class="opacity-75 mb-0">Suivi et modification des présences étudiants</p>
        </div>
    </div>
</div>

<div class="card-body p-0">

<style>
:root {
  --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
  --danger-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
  --info-gradient: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
  --warning-gradient: linear-gradient(135deg, #f7971e 0%, #ffd200 100%);
  --shadow-lg: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
  --shadow-xl: 0 25px 50px -12px rgba(0,0,0,0.25);
  --table-header: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
}
.bg-gradient-primary { background: var(--primary-gradient) !important; }
.bg-gradient-success { background: var(--success-gradient) !important; }
.bg-gradient-danger { background: var(--danger-gradient) !important; }
.shadow-xl { box-shadow: var(--shadow-xl) !important; }
.table-dark th { background: var(--table-header) !important; border: none !important; }
tbody tr:hover { 
  transform: translateY(-2px) scale(1.02); 
  background: linear-gradient(90deg, rgba(99,102,241,0.08), rgba(118,75,162,0.08)) !important;
  box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}
.btn { transition: all 0.2s ease; }
.btn:hover { transform: translateY(-1px); }
.badge { font-weight: 600 !important; }
</style>

@if($absences->count() > 0)
<div class="table-responsive">
<table class="table table-hover align-middle mb-0 bg-white">
  <thead class="table-dark">
    <tr>
      <th style="width: 30%;">
        <div class="d-flex align-items-center gap-3 py-3 px-4">
          <a href="?sort=user.name&dir={{ request('sort') == 'user.name' && request('dir') == 'asc' ? 'desc' : 'asc' }}&{{ http_build_query(request()->except(['sort', 'dir'])) }}" class="text-white text-center text-decoration-none fw-bold">
            Étudiant 
            <i class="fas ms-2 {{ request('sort') == 'user.name' ? (request('dir') == 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} opacity-75"></i>
          </a>
        </div>
      </th>
      <th style="width: 15%;">
        <div class="d-flex align-items-center gap-3 py-3 px-4">
          <a href="?sort=created_at&dir={{ request('sort') == 'created_at' && request('dir') == 'asc' ? 'desc' : 'asc' }}&{{ http_build_query(request()->except(['sort', 'dir'])) }}" class="text-white text-decoration-none fw-bold">
            Date 
            <i class="fas ms-2 {{ request('sort') == 'created_at' ? (request('dir') == 'asc' ? 'fa-sort-up' : 'fa-sort-down') : 'fa-sort' }} opacity-75"></i>
          </a>
        </div>
      </th>
      <th style="width: 20%;">Classe</th>
      <th style="width: 15%;">
        <div class="d-flex align-items-center gap-2 py-3 px-4">
          <i class="fas fa-info-circle opacity-75 fs-5"></i>
          <span class="fw-bold text-white ">Statut</span>
        </div>
      </th>
      <th style="width: 20%;" class="text-center">Action</th>
    </tr>
  </thead>
  <tbody class="table-group-divider">
    @foreach($absences as $absence)
    <tr>
      <!-- Étudiant -->
      <td class="px-4">
        <div class="d-flex align-items-center gap-3">
          <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center;color:white;font-weight:bold;font-size:0.9rem;">
            {{ strtoupper(substr($absence->user?->name ?? 'E',0,1)) }}
          </div>
          <span class="fw-semibold">{{ $absence->user?->name ?? 'Étudiant inconnu' }}</span>
        </div>
      </td>
      <!-- Date -->
      <td class="text-muted fw-semibold">{{ $absence->created_at->format('d/m/Y H:i') }}</td>
      <!-- Classe -->
      <td>
        <span class="badge bg-info px-3 py-2">
          <i class="fas fa-graduation-cap me-1"></i>
          {{ $absence->user->classRoom?->name ?? 'Non assigné' }}
        </span>
      </td>
      <!-- Status (read-only display) -->
      <td class="text-center">
        @if($absence->present)
          <span class="badge bg-gradient-success px-3 py-2 text-white">
            <i class="fas fa-check-circle me-1"></i> Présent
          </span>
        @else
          <span class="badge bg-gradient-danger px-3 py-2 text-white">
            <i class="fas fa-times-circle me-1"></i> Absent
          </span>
        @endif
      </td>
      <!-- Action -->
      <td class="text-center">
        <form method="POST" action="{{ route('prof.absences.update', $absence->id) }}" class="d-inline">
          @csrf
          @method('PUT')
          <div class="btn-group btn-group-sm" role="group">
            <select name="present" onchange="this.form.submit()" class="form-select form-select-sm shadow-sm" style="border-radius:8px 0 0 8px; border-right: none; width: 85px;">
              <option value="1" {{ $absence->present ? 'selected' : '' }}>Présent</option>
              <option value="0" {{ !$absence->present ? 'selected' : '' }}>Absent</option>
            </select>
            <button type="submit" class="btn btn-primary btn-sm px-2 shadow-sm" style="border-radius:0 8px 8px 0; border-left: none;" title="Mettre à jour">
              <i class="fas fa-save"></i>
            </button>
          </div>
        </form>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>
</div>

<!-- Pagination -->
@if(isset($absences))
<div class="card-footer bg-light">
  {{ $absences->appends(request()->query())->links() }}
</div>
@endif

@else
<div class="text-center py-8">
  <div class="mb-4">
    <i class="fas fa-calendar-check fa-4x text-success opacity-50 mb-3"></i>
  </div>
  <h5 class="text-muted mb-3 fw-normal">🎉 Parfait !</h5>
  <p class="text-muted mb-4 lead">Aucune absence enregistrée pour le moment.</p>
  <div class="bg-light rounded-4 p-4 shadow-sm mx-auto" style="max-width:400px;">
    <i class="fas fa-lightbulb text-warning mb-2 d-block"></i>
    <small class="text-muted">Utilisez le filtre pour vérifier par classe.</small>
  </div>
</div>
@endif

</div>
</div>

</div>
</div>

@endsection

