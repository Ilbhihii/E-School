@extends('layouts.prof')

@section('content')
<div class="admin-page">
    <div class="admin-container">

        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">Mes Tests</span></h1>
                <p class="admin-header-subtitle">Gérez vos questionnaires et évaluations</p>
            </div>
            <a href="{{ route('prof.tests.create') }}" class="adm-btn adm-btn-primary">
                <i class="bi bi-plus-lg"></i> Créer un test
            </a>
        </div>

        @if(session('success'))
            <div class="adm-alert adm-alert-success">{{ session('success') }}</div>
        @endif

        <div class="adm-card">
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Matière</th>
                            <th>Durée</th>
                            <th>Questions</th>
                            <th>Note moyenne</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tests as $test)
                        <tr>
                            <td><span style="font-weight:600;">{{ $test->title }}</span></td>
                            <td><span class="adm-badge adm-badge-purple">{{ $test->subject->name }}</span></td>
                            <td><span class="adm-badge adm-badge-gray">{{ $test->duration }} min</span></td>
                            <td>{{ $test->questions_count }}</td>
                            <td>{{ $test->average_score ? number_format($test->average_score, 2) . ' / 20' : 'Non noté' }}</td>
                            <td>
                                <div class="adm-actions">
                                    <a href="{{ route('prof.tests.show', $test) }}" class="adm-action-link adm-action-view"><i class="bi bi-eye-fill"></i></a>
                                    <a href="{{ route('prof.tests.edit', $test) }}" class="adm-action-link adm-action-edit"><i class="bi bi-pencil-fill"></i></a>
                                    <form action="{{ route('prof.tests.destroy', $test) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce test?')">
                                        @csrf @method('DELETE')
                                        <button class="adm-action-link adm-action-delete" style="border:none;cursor:pointer;"><i class="bi bi-trash-fill"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="adm-empty">Aucun test créé</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
