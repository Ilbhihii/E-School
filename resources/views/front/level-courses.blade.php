@extends('layouts.front')

@section('content')

<div class="container py-5">

    <h3 class="mb-4">{{ $level->name }}</h3>

    <div class="row g-4">

        @foreach($level->courses as $course)
        <div class="col-md-4">

            <a href="{{ route('front.course.show', $course->id) }}">

                <div class="card p-3 shadow text-center">

                    <i class="bi bi-play-circle fs-1 text-danger mb-2"></i>

                    <h5>{{ $course->title }}</h5>

                </div>

            </a>

        </div>
        @endforeach

    </div>

</div>

@endsection

{{-- Design global V12 : présentation uniquement, aucun contenu modifié. --}}
@push('scripts')
<link
    rel="stylesheet"
    href="{{ asset('css/front-design-v12.css?v=12.0') }}"
>
@endpush

