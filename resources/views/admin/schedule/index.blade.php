@extends('layouts.admin')

@section('content')
<div class="admin-page">
    <div class="admin-container">

        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">📅 Gestion du Planning</span></h1>
                <p class="admin-header-subtitle">Ajouter et gérer les séances du planning</p>
            </div>
        </div>

        <!-- FORM -->
        <div class="adm-card adm-mb-3">
            <div class="adm-card-header">
                <h3><i class="bi bi-plus-circle-fill"></i> Ajouter une séance</h3>
            </div>
            <div class="adm-card-body">
                @if ($errors->any())
                    <div class="adm-alert adm-alert-danger">
                        <ul class="mb-0" style="list-style:disc;list-style-position:inside;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.schedule.store') }}" class="adm-grid-2" onsubmit="return confirm('Confirmer l\'ajout de cette séance ?')">
                    @csrf

                    <div class="adm-form-group">
                        <label class="adm-form-label">Classe</label>
                        <select name="class_id" class="adm-form-select">
                            <option value="">Choisir classe</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="adm-form-group">
                        <label class="adm-form-label">Professeur</label>
                        <select name="prof_id" class="adm-form-select">
                            <option value="">Choisir professeur</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="adm-form-group">
                        <label class="adm-form-label">Matière</label>
                        <select name="subject" class="adm-form-select">
                            <option value="">Choisir matière</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->name }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="adm-form-group">
                        <label class="adm-form-label">Début</label>
                        <input type="datetime-local" name="start_time" class="adm-form-input">
                    </div>
                    <div class="adm-form-group">
                        <label class="adm-form-label">Fin</label>
                        <input type="datetime-local" name="end_time" class="adm-form-input">
                    </div>
                    <div class="adm-flex adm-flex-end" style="align-items:flex-end;">
                        <button type="submit" class="adm-btn adm-btn-primary adm-btn-lg" style="width:100%;">
                            <i class="bi bi-plus-lg"></i> Ajouter au planning
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- TABLE -->
        <div class="adm-card">
            <div class="adm-card-header">
                <h3><i class="bi bi-list-check"></i> Planning complet</h3>
            </div>
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>Classe</th>
                            <th>Prof</th>
                            <th>Matière</th>
                            <th>Date</th>
                            <th>Heure</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schedules as $s)
                        <tr>
                            <td><span style="font-weight:600;">{{ $s->classRoom->name ?? 'N/A' }}</span></td>
                            <td>{{ $s->prof->name ?? 'N/A' }}</td>
                            <td><span class="adm-badge adm-badge-purple">{{ $s->subject }}</span></td>
                            <td><span class="adm-badge adm-badge-gray">{{ $s->date?->format('d/m/Y') ?? '-' }}</span></td>
                            <td>{{ $s->start_time?->format('H:i') ?? '-' }} → {{ $s->end_time?->format('H:i') ?? '-' }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.schedule.destroy', $s->id) }}" onsubmit="return confirm('Supprimer ?')">
                                    @csrf @method('DELETE')
                                    <button class="adm-action-link adm-action-delete" style="border:none;cursor:pointer;">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="adm-empty">Aucun planning</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
