@extends('layouts.student')

@section('title', 'Tests disponibles')

@section('content')
<div class="container">
    <h1>Tests disponibles</h1>

    <table class="table">
        <thead>
            <tr>
                <th>Titre</th>
                <th>Matière</th>
                <th>Durée</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tests as $test)
                <tr>
                    <td>{{ $test->title }}</td>
                    <td>{{ $test->subject->name }}</td>
                    <td>{{ $test->duration }} min</td>
                    <td>
                        <a href="{{ route('student.tests.show', $test) }}" class="btn btn-primary btn-sm">Passer le test</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Aucun test disponible</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

