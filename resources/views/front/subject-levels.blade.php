@extends('layouts.front')

@section('content')

<div class="container py-5">

    <div class="bg-primary text-white text-center p-5 rounded mb-5">
        <h2>{{ $subject->name }}</h2>
        <p>Choisissez un niveau</p>
    </div>

    <div class="row g-4">

        @foreach($levels as $level)
        <div class="col-md-4">

            <a href="{{ route('front.subject.level.classes', [$subject->id, $level->id]) }}" class="text-decoration-none">

                <div class="card level-card text-center p-4 shadow">

                    <i class="bi bi-bar-chart-line fs-1 text-primary mb-3"></i>

                    <h5 class="fw-bold">{{ $level->name }}</h5>

                </div>

            </a>

        </div>
        @endforeach

    </div>

</div>

<style>
.level-card {
    border-radius: 20px;
    transition: 0.3s;
}
.level-card:hover {
    transform: translateY(-10px);
}
</style>

@endsection

