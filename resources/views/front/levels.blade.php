@extends('layouts.front')

@section('content')

<div class="container py-5">

    <h2 class="text-center fw-bold mb-5">Choisir votre niveau</h2>

    <div class="row g-4">

        @foreach($levels as $level)
        <div class="col-md-4">

            <a href="{{ route('levels.courses', $level->id) }}" class="text-decoration-none">

                <div class="card level-card text-center p-4 shadow-sm">

                    <div class="icon mb-3">
                        <i class="bi bi-mortarboard-fill"></i>
                    </div>

                    <h4 class="fw-bold">{{ $level->name }}</h4>

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
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}
.icon {
    font-size: 40px;
    color: #4f46e5;
}
</style>

@endsection

