@extends('layouts.student')

@section('content')

<h2 class="fw-bold text-center mb-4">
    {{ $subject->name }}
</h2>

<div class="row g-4">

@foreach($subject->classes as $class)
<div class="col-md-4">

    <a href="{{ route('student.courses', [$subject->id, $class->id]) }}"
       class="text-decoration-none">

        <div class="card text-center p-4 shadow rounded-4">

            <i class="bi bi-mortarboard fs-1 text-success mb-3"></i>

            <h5 class="fw-bold text-dark">{{ $class->name }}</h5>

        </div>

    </a>

</div>
@endforeach

</div>

@endsection
