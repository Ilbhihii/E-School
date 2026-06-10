@extends('layouts.admin')

@section('content')
<div class="admin-page">
    <div class="admin-container">

        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">🚨 Étudiants sans Classe ({{ $count }})</span></h1>
                <p class="admin-header-subtitle">Ces étudiants n'ont pas de classe assignée et ne peuvent pas accéder aux lives et cours.</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="adm-btn adm-btn-ghost">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>

        @if($count === 0)
            <div class="adm-card" style="text-align:center;padding:3rem;">
                <div class="adm-empty-icon" style="background:#dcfce7;color:#16a34a;margin:0 auto 1.25rem;">
                    <i class="bi bi-check-lg"></i>
                </div>
                <h3 style="font-size:1.5rem;font-weight:700;color:#166534;">🎉 Tous les étudiants ont une classe !</h3>
                <p style="color:#15803d;">Aucun étudiant n'a de class_id NULL</p>
            </div>
        @else
            <div class="adm-card">
                <div class="adm-card-header" style="background:linear-gradient(135deg,#fef2f2,#fff1f2);border-bottom:1px solid #fecaca;">
                    <h3 style="color:#991b1b;"><i class="bi bi-exclamation-triangle-fill"></i> Assigner des classes</h3>
                </div>
                <div class="adm-table-wrap">
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Classe à assigner</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                            <tr>
                                <td><span class="adm-badge adm-badge-gray">#{{ $student->id }}</span></td>
                                <td>
                                    <div class="adm-user-cell">
                                        <div class="adm-avatar adm-avatar-sm" style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
                                            {{ strtoupper(substr($student->name,0,2)) }}
                                        </div>
                                        <div class="adm-user-cell-info">
                                            <div class="adm-user-cell-name">{{ $student->name }}</div>
                                            <div class="adm-user-cell-email">Inscrit: {{ $student->created_at->format('d/m/Y') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="adm-user-cell-email">{{ $student->email }}</span></td>
                                <td>
                                    <form method="POST" action="{{ route('admin.users.update', $student->id) }}" class="adm-flex adm-gap-2">
                                        @csrf @method('PUT')
                                        <select name="class_id" class="adm-form-select" style="width:auto;min-width:160px;">
                                            <option value="">❌ Aucune classe</option>
                                            @foreach($classRooms as $classRoom)
                                                <option value="{{ $classRoom->id }}">{{ $classRoom->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="adm-btn adm-btn-success adm-btn-sm">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                </td>
                                <td><span class="adm-badge adm-badge-danger"><i class="bi bi-exclamation-circle-fill"></i> URGENT</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if($count > 1)
            <div class="adm-card adm-mt-3">
                <div class="adm-card-header">
                    <h3><i class="bi bi-grid-3x3-gap-fill"></i> Actions en masse (à venir)</h3>
                </div>
                <div class="adm-card-body">
                    <p style="color:var(--adm-text-secondary);">Sélection multiple et assignation groupée disponible prochainement.</p>
                </div>
            </div>
            @endif
        @endif

    </div>
</div>
@endsection
