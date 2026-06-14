@extends('layouts.front')

@section('title', 'Cours')

@section('content')

<div class="container py-5">

    <!-- TITRE -->
    <div class="text-center mb-5">
        <h1 class="fw-bold">📖 Cours</h1>
        <p class="text-muted">Complétez les cours dans l'ordre pour débloquer le suivant</p>
    </div>

    <!-- COURS -->
    <div class="row g-4">
        @forelse($courses as $course)
        @php
        $progress = \App\Models\UserProgress::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->first();
        @endphp

        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100 course-card
                @if($course->order != 1 && !($progress && $progress->completed)) locked @endif">

                <div class="card-body p-4">

                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="badge bg-primary rounded-pill">
                            Cours {{ $course->order }}
                        </span>
                        @if($progress && $progress->completed)
                            <span class="badge bg-success rounded-pill">✅ Complété</span>
                        @elseif($progress)
                            <span class="badge bg-warning rounded-pill">📝 {{ $progress->score }}%</span>
                        @endif
                    </div>

                    <h5 class="fw-bold">{{ $course->title }}</h5>
                    <p class="text-muted small">{{ Str::limit($course->description, 100) }}</p>

                    @if($course->order == 1 || ($progress && $progress->completed))
                        <a href="{{ route('front.course.show', $course->id) }}"
                           class="btn btn-primary rounded-pill px-4 w-100">
                            Voir cours
                        </a>
                    @else
                        <button disabled class="btn btn-outline-secondary rounded-pill px-4 w-100">
                            🔒 Bloqué
                        </button>
                    @endif

                </div>

            </div>
        </div>
        @empty
        <div class="col-12 text-center">
            <p class="text-muted">Aucun cours disponible pour ce niveau.</p>
        </div>
        @endforelse
    </div>

</div>

<!-- STYLE -->
<style>
.course-card {
    border-radius: 15px;
    transition: all 0.3s ease;
}

.course-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.1);
}

.course-card.locked {
    opacity: 0.7;
}

.course-card.locked:hover {
    transform: none;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}
</style>

@endsection

