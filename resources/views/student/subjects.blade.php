@extends('layouts.student')

@section('content')
<div class="container py-5">
    <div class="mb-4">
        <a href="{{ route('student.levels') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-2"></i>Retour aux niveaux
        </a>
    </div>
    <h2 class="text-center mb-5">Choisir une matière ({{ $level->name }})</h2>

    <div class="row">
        @foreach($subjects as $subject)
        <div class="col-md-4">
            <a href="{{ route('student.classes', [$subject->id, $level->id]) }}">
                <div class="card p-4 text-center shadow">
                    <i class="bi bi-book fs-1 text-primary mb-3"></i>
                    <h4>{{ $subject->name }}</h4>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection
