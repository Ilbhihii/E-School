@extends('layouts.prof')

@section('title', 'Mes Tests')

@section('content')
<div class="container">
    <h1>Mes Tests</h1>
    <a href="{{ route('prof.tests.create') }}" class="btn btn-primary mb-3">Créer un nouveau test</a>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>Titre</th>
                <th>Matière</th>
                <th>Durée</th>
                <th>Nombre de questions</th>
                <th>Note moyenne</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tests as $test)
                <tr>
                    <td>{{ $test->title }}</td>
                    <td>{{ $test->subject->name }}</td>
                    <td>{{ $test->duration }} min</td>
                    <td>{{ $test->questions_count }}</td>
                    <td>{{ $test->average_score ? number_format($test->average_score, 2) . ' / 20' : 'Non noté' }}</td>
<td>
                        <a href="{{ route('prof.tests.show', $test) }}" class="btn btn-sm btn-success">Réponses</a>
                        <a href="{{ route('prof.tests.edit', $test) }}" class="btn btn-sm btn-warning">Éditer</a>
                        <form action="{{ route('prof.tests.destroy', $test) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ce test?')">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Aucun test créé</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

