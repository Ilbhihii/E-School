@extends('layouts.front')

@section('content')
<h2 class="text-3xl font-bold mb-6">Bienvenue 👋</h2>

<div class="grid grid-cols-3 gap-6">
    <div class="card text-center">
        <h3>Matiére</h3>
        <p>{{ $subjects }}</p>
    </div>

    <div class="card text-center">
        <h3>Cours</h3>
        <p>{{ $courses }}</p>
    </div>

    <div class="card text-center">
        <h3>Lives récents</h3>
        @foreach($lives as $live)
            <p>{{ $live->title }}</p>
        @endforeach
    </div>
</div>
@endsection
