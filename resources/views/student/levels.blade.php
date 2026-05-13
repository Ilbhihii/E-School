@extends('layouts.student')

@section('content')
<div class="container py-5">
    <h2 class="text-center mb-5">Choisir un niveau</h2>

    <div class="row">
        @foreach($levels as $level)
        <div class="col-md-4">
            <a href="{{ route('student.subjects', $level->id) }}">
                <div class="card p-4 text-center shadow">
                    <h4>{{ $level->name }}</h4>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endsection

