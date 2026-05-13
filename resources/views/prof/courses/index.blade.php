@extends('layouts.prof')

@section('content')

<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card bg-primary text-white shadow-lg rounded-4 mb-5 border-0">
        <div class="card-body text-center py-5">
            <i class="bi bi-book-half display-4 opacity-75 mb-3 d-block"></i>
            <h1 class="fw-bold mb-2">📘 Gestion des Cours</h1>
            <p class="mb-0 opacity-75">Gérez efficacement vos cours et ressources pédagogiques</p>
        </div>
    </div>

    {{-- STATS --}}
    <div class="row g-4 mb-5">

        <div class="col-xl-4 col-md-6">
            <div class="card shadow-sm border-0 rounded-4 h-100 text-center p-4">
                <i class="bi bi-book text-success display-5 mb-3"></i>
                <h5 class="fw-bold">Total Cours</h5>
                <h2 class="fw-bold text-success">{{ $courses->count() }}</h2>
                <small class="text-muted">Cours créés</small>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="card shadow-sm border-0 rounded-4 h-100 text-center p-4">
                <i class="bi bi-clock-history text-info display-5 mb-3"></i>
                <h5 class="fw-bold">Cours récents</h5>
                <h2 class="fw-bold text-info">
                    {{ $courses->where('created_at','>', now()->subDays(7))->count() }}
                </h2>
                <small class="text-muted">7 derniers jours</small>
            </div>
        </div>

        <div class="col-xl-4 col-md-12">
            <div class="card shadow-sm border-0 rounded-4 h-100 text-center p-4 bg-light">
                <i class="bi bi-plus-circle text-primary display-5 mb-3"></i>
                <h5 class="fw-bold mb-3">Nouveau cours</h5>
                <a href="{{ route('prof.courses.create') }}" class="btn btn-primary px-4 rounded-pill">
                    Créer un cours
                </a>
            </div>
        </div>

    </div>

    {{-- RECENTS --}}
    <div class="card shadow-sm border-0 rounded-4 mb-5">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="fw-bold mb-0">
                <i class="bi bi-clock me-2 text-primary"></i> Cours récents
            </h5>
        </div>

        <div class="card-body p-0">
            <ul class="list-group list-group-flush">

                @forelse($courses->take(5) as $course)
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                        <span class="fw-semibold">
                            {{ Str::limit($course->title, 60) }}
                        </span>
                        <small class="text-muted">
                            {{ $course->created_at->diffForHumans() }}
                        </small>
                    </li>
                @empty
                    <li class="list-group-item text-center py-5 text-muted">
                        Aucun cours récent
                    </li>
                @endforelse

            </ul>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card shadow-sm border-0 rounded-4">

        <div class="card-header bg-dark text-white py-3">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-table me-2"></i> Tous les cours
            </h5>
        </div>

        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-center">

                    <thead class="table-light">
                        <tr>
                            <th>Titre</th>
                            <th>Niveau</th>
                            <th>Matière</th>
                            <th>Date</th>
                            <th>Devoirs</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($courses as $course)
                        <tr>

                            <td class="fw-semibold">
                                {{ Str::limit($course->title, 40) }}
                            </td>

                            <td>{{ $course->level->name ?? '---' }}</td>

                            <td>{{ $course->subject->name ?? '---' }}</td>

                            <td class="text-muted small">
                                {{ $course->created_at->format('d/m/Y') }}
                            </td>

                            <td>
                                @forelse($course->assignments as $devoir)
                                    <span class="badge bg-primary mb-1">
                                        {{ Str::limit($devoir->title, 18) }}
                                    </span>
                                @empty
                                    <span class="text-muted small">Aucun</span>
                                @endforelse
                            </td>

                            <td>
                                <div class="d-flex justify-content-center gap-2">

                                    <a href="{{ route('prof.devoir.create', ['course_id'=>$course->id]) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-plus"></i>
                                    </a>

                                    <a href="{{ route('prof.courses.edit', $course->id) }}"
                                       class="btn btn-sm btn-outline-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <form method="POST"
                                          action="{{ route('prof.courses.destroy', $course->id) }}"
                                          onsubmit="return confirm('Confirmer ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

        </div>

        <div class="card-footer bg-white border-0 py-3 text-center">
            {{ $courses->links() }}
        </div>

    </div>

</div>

@endsection
