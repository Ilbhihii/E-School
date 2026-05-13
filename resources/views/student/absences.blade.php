@extends('layouts.student')

@section('content')


<div class="container py-5">
  <div class="row g-4">
    <div class="col-12">



<div class="glass-card neumorphism-hover overflow-hidden">


<div class="card-header d-flex justify-content-between align-items-center">

<h5>
<i class="bi bi-calendar-x me-2"></i>
Mes Absences
</h5>


<div class="stat-number display-4 fw-bold count-up mb-0" data-target="{{ $totalAbsences }}" style="color: #ef4444;">{{ $totalAbsences }}</div>
<small class="text-muted">absence{{ $totalAbsences > 1 ? 's' : '' }}</small>


</div>


<div class="card-body">

@if($absences->count() > 0)

<div class="table-responsive">


<div class="table-responsive rounded-3 overflow-hidden shadow-lg">
<table class="table table-hover modern-table mb-0">


<thead>

<tr>
<th>Date</th>
<th>Statut</th>
<th>Justifiée</th>
</tr>

</thead>

<tbody>

@foreach($absences as $absence)

<tr>

<td>
{{ \Carbon\Carbon::parse($absence->date)->format('d M Y') }}
</td>

<td>
<span class="badge bg-danger">
Absent
</span>
</td>

<td>

@if($absence->justified)

<span class="badge bg-success">
Oui
</span>

@else

<span class="badge bg-danger">
Non
</span>

@endif

</td>

</tr>

@endforeach

</tbody>

</table>

</div>

@else

<div class="text-center py-4">

<i class="bi bi-check-circle text-success fs-1 mb-3"></i>

<h5>Aucune absence enregistrée</h5>

<p class="text-muted">
Vous êtes toujours présent aux cours !
</p>

</div>

@endif


</div>

<div class="p-4 pt-0">
<div class="alert-modern glassmorphism alert-{{ $color }} p-4 rounded-3">


<strong>Situation de l'étudiant :</strong>

<br>

Total absences : <strong>{{ $totalAbsences }}</strong>

<br>

{{ $situation }}


</div>
</div>

</div>
</div>
</div>

<style>
/* Modern Absence Page Styles - Matching Dashboard */
:root {
--primary: #6366f1;
--success: #10b981;
--warning: #f59e0b;
--danger: #ef4444;
--glass-bg: rgba(255,255,255,0.65);
--glass-border: rgba(255,255,255,0.35);
--glass-blur: blur(18px);
--shadow-soft: 0 10px 35px rgba(0,0,0,0.08);
--shadow-hover: 0 20px 50px rgba(0,0,0,0.18);
}

.glass-card {
background: var(--glass-bg);
backdrop-filter: var(--glass-blur);
border: 1px solid var(--glass-border);
border-radius: 22px;
box-shadow: var(--shadow-soft);
}

.neumorphism-hover {
background: linear-gradient(145deg, #ffffff, #f4f6fb);
box-shadow: 8px 8px 18px rgba(0,0,0,0.06), -6px -6px 14px rgba(255,255,255,0.9);
transition: all .4s ease;
}

.neumorphism-hover:hover {
transform: translateY(-6px);
box-shadow: 10px 10px 30px rgba(0,0,0,0.12), -8px -8px 20px rgba(255,255,255,0.9);
}

.modern-table thead th {
background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
font-weight: 700;
border: none;
padding: 18px 16px;
border-radius: 12px 12px 0 0;
box-shadow: 0 4px 12px rgba(0,0,0,0.08);
color: #2c3e50;
}

.modern-table tbody tr:hover {
background: linear-gradient(135deg, rgba(99,102,241,0.06), rgba(79,70,229,0.04));
transform: translateY(-1px);
box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}

.alert-modern {
background: rgba(16,185,129,0.15);
border: 1px solid rgba(16,185,129,0.3);
--bs-success: #10b981;
}

.alert-modern.alert-warning {
background: rgba(245,158,11,0.15);
border-color: rgba(245,158,11,0.3);
}

.alert-modern.alert-danger {
background: rgba(239,68,68,0.15);
border-color: rgba(239,68,68,0.3);
}

.count-up {
font-weight: 800;
color: var(--danger);
}

.animate-float {
animation: float 6s ease-in-out infinite;
}

@keyframes float {
0%, 100% { transform: translateY(0); }
50% { transform: translateY(-10px); }
}

@media (max-width: 768px) {
.stat-number { font-size: 2rem !important; }
}
</style>
@endsection

