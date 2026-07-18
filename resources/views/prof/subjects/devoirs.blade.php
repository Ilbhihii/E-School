@extends('layouts.prof')

@section('title', 'Devoirs - ' . $subject->name . ' - ' . $class->name)
@section('page_title', 'Devoirs — ' . $subject->name)
@section('breadcrumb', 'Matières → Niveaux → Classes → Devoirs')

@section('content')
<div class="admin-page">
    <div class="admin-container">

        <div class="admin-header">
            <div>
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;font-size:13px;color:var(--muted);">
                    <a href="{{ route('prof.subjects.list') }}" style="color:var(--muted);text-decoration:none;"><i class="bi bi-book me-1"></i>Matières</a>
                    <span>/</span>
                    <a href="{{ route('prof.subjects.levels', $subject) }}" style="color:var(--muted);text-decoration:none;">{{ $subject->name }}</a>
                    <span>/</span>
                    <a href="{{ route('prof.subjects.classes', [$subject, $level]) }}" style="color:var(--muted);text-decoration:none;">{{ $level->name }}</a>
                    <span>/</span>
                    <span style="color:var(--text);font-weight:600;">{{ $class->name }}</span>
                </div>
                <h1 class="admin-header-title"><span class="gradient">📚 Devoirs — {{ $subject->name }}</span></h1>
                <p class="admin-header-subtitle">{{ $class->name }} · {{ $level->name }} — Gérez les devoirs de cette matière</p>
            </div>
            <a href="{{ route('prof.devoir.create') }}" class="adm-btn adm-btn-success">
                <i class="bi bi-plus-lg"></i> Nouveau Devoir
            </a>
        </div>

        @php $hasDevoirs = $courses->filter(fn($c) => $c->devoirs->count() > 0)->isNotEmpty(); @endphp

        @if(!$hasDevoirs)
            <div class="adm-card">
                <div class="adm-empty">
                    <i class="bi bi-inbox"></i>
                    <h3>Aucun devoir</h3>
                    <p>Aucun devoir n'a été créé pour cette matière dans cette classe.</p>
                    <a href="{{ route('prof.devoir.create') }}" class="adm-btn adm-btn-success">
                        <i class="bi bi-plus-circle me-1"></i> Créer un devoir
                    </a>
                </div>
            </div>
        @else
            @foreach($courses as $course)
                @if($course->devoirs->count() > 0)
                    <div class="adm-card adm-mb-3">
                        <div class="adm-card-header">
                            <h3><i class="bi bi-book-fill"></i> {{ $course->title }}</h3>
                            <span class="adm-badge adm-badge-primary" style="margin-left:auto;">{{ $course->devoirs->count() }} devoir(s)</span>
                        </div>
                        <div class="adm-table-wrap">
                            <table class="adm-table">
                                <thead>
                                    <tr>
                                        <th>Titre</th>
                                        <th>Date limite</th>
                                        <th>Fichier</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($course->devoirs as $devoir)
                                    <tr>
                                        <td><span style="font-weight:600;">{{ Str::limit($devoir->title, 40) }}</span></td>
                                        <td>
                                            @php $isPast = $devoir->due_date <= now()->format('Y-m-d'); @endphp
                                            <span class="adm-badge {{ $isPast ? 'adm-badge-danger' : 'adm-badge-success' }}">
                                                {{ \Carbon\Carbon::parse($devoir->due_date)->format('d/m/Y') }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($devoir->file)
                                            <a href="{{ asset('storage/'.$devoir->file) }}" target="_blank" class="adm-btn adm-btn-sm adm-btn-ghost">
                                                <i class="bi bi-download"></i>
                                            </a>
                                            @else
                                            <span class="adm-badge adm-badge-gray">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="adm-actions">
                                                <a href="{{ route('prof.devoir.edit', $devoir) }}" class="adm-action-link adm-action-edit"><i class="bi bi-pencil-fill"></i></a>
                                                <form method="POST" action="{{ route('prof.devoir.destroy', $devoir) }}" onsubmit="return confirm('Confirmer ?')">
                                                    @csrf @method('DELETE')
                                                    <button class="adm-action-link adm-action-delete" style="border:none;"><i class="bi bi-trash-fill"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            @endforeach
        @endif

        <div class="mt-4">
            <a href="{{ route('prof.subjects.classes', [$subject, $level]) }}" class="adm-btn adm-btn-ghost">
                <i class="bi bi-arrow-left me-1"></i> Retour aux classes
            </a>
        </div>

    </div>
</div>
@endsection
