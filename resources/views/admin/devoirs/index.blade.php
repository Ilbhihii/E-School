@extends('layouts.admin')

@section('content')
<div class="admin-page">
    <div class="admin-container">

        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">📚 Gestion des Devoirs</span></h1>
                <p class="admin-header-subtitle">Administration des devoirs et exercices</p>
            </div>
            <a href="{{ route('prof.devoir.create', ['course_id' => $course_id ?? '']) }}" class="adm-btn adm-btn-success">
                <i class="bi bi-plus-lg"></i> Nouveau Devoir
            </a>
        </div>

        @if($course)
        <div class="adm-alert adm-alert-info">
            <i class="bi bi-info-circle-fill"></i> Devoirs du cours: <strong>{{ $course->title }}</strong>
            <a href="{{ route('admin.devoirs.index') }}" class="adm-btn adm-btn-ghost adm-btn-sm" style="margin-left:auto;">← Tous les devoirs</a>
        </div>
        @endif

        <!-- TABLE -->
        <div class="adm-card">
            <div class="adm-card-header">
                <div>
                    <h3><i class="bi bi-file-text-fill"></i> Liste des Devoirs</h3>
                    <p>Tous les devoirs enregistrés</p>
                </div>
            </div>
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Classe</th>
                            <th>Date Limite</th>
                            <th>Fichier</th>
                            <th>Prof</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($devoirs as $devoir)
                        <tr>
                            <td><span style="font-weight:600;">{{ Str::limit($devoir->title, 50) }}</span></td>
                            <td><span class="adm-badge adm-badge-primary">{{ $devoir->classRoom->name ?? '---' }}</span></td>
                            <td>
                                @php $isPast = $devoir->due_date < now()->format('Y-m-d'); @endphp
                                <span class="adm-badge {{ $isPast ? 'adm-badge-danger' : 'adm-badge-success' }}">
                                    {{ \Carbon\Carbon::parse($devoir->due_date)->format('d/m/Y') }}
                                </span>
                            </td>
                            <td>
                                @if($devoir->file)
                                    <a href="{{ Storage::url($devoir->file) }}" target="_blank" class="adm-btn adm-btn-sm adm-btn-ghost">
                                        <i class="bi bi-paperclip"></i> Fichier
                                    </a>
                                @else
                                    <span class="adm-badge adm-badge-gray">Aucun</span>
                                @endif
                            </td>
                            <td><span style="color:var(--adm-text-secondary);">{{ $devoir->user->name ?? 'Admin' }}</span></td>
                            <td>
                                <div class="adm-actions">
                                    <a href="{{ route('admin.devoirs.edit', $devoir) }}" class="adm-action-link adm-action-edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.devoirs.destroy', $devoir) }}" class="d-inline" onsubmit="return confirm('Confirmer ?')">
                                        @csrf @method('DELETE')
                                        <button class="adm-action-link adm-action-delete" style="border:none;cursor:pointer;">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="adm-empty">
                                <div class="adm-empty-icon"><i class="bi bi-inbox"></i></div>
                                <h3>Aucun devoir</h3>
                                <p>Aucun devoir pour ce cours.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(method_exists($devoirs, 'links'))
            <div class="adm-card-footer">
                <div class="adm-pagination">{{ $devoirs->appends(request()->query())->links() }}</div>
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
