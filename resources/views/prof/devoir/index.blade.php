@extends('layouts.prof')

@section('content')

<div class="container-fluid py-4">
    {{-- Hero Header --}}
    <div class="card bg-primary text-white shadow-lg rounded-4 mb-5 overflow-hidden">
        <div class="card-body text-center py-5">
            <i class="bi bi-file-earmark-check display-4 opacity-75 mb-4 d-block"></i>
            <h1 class="display-3 fw-bold mb-3">📚 Gestion des Devoirs</h1>
            <p class="lead mb-0 opacity-90">Organisez et suivez les devoirs de vos classes</p>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-lg border-0 text-center h-100 rounded-3 p-4">
                <div class="position-absolute top-0 start-0 bg-primary" style="width: 100%; height: 4px;"></div>
                <div class="card-body d-flex flex-column align-items-center">
                    <i class="bi bi-file-earmark-check display-4 text-primary mb-3"></i>
                    <h3 class="h4 fw-bold text-dark mb-2">Total Devoirs</h3>
                    <div class="display-4 fw-bold text-primary mb-2">{{ $devoirs->count() }}</div>
                    <small class="text-muted">Devoirs créés</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-lg border-0 text-center h-100 rounded-3 p-4">
                <div class="position-absolute top-0 start-0 bg-info" style="width: 100%; height: 4px;"></div>
                <div class="card-body d-flex flex-column align-items-center">
                    <i class="bi bi-clock-history display-4 text-info mb-3"></i>
                    <h3 class="h4 fw-bold text-dark mb-2">À Venir</h3>
                    <div class="display-4 fw-bold text-info mb-2">{{ $devoirs->where('due_date', '>', now()->format('Y-m-d'))->count() }}</div>
                    <small class="text-muted">Pas encore échus</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-lg border-0 text-center h-100 rounded-3 p-4">
                <div class="position-absolute top-0 start-0 bg-warning" style="width: 100%; height: 4px;"></div>
                <div class="card-body d-flex flex-column align-items-center">
                    <i class="bi bi-file-earmark-x display-4 text-warning mb-3"></i>
                    <h3 class="h4 fw-bold text-dark mb-2">Expirés</h3>
                    <div class="display-4 fw-bold text-warning mb-2">{{ $devoirs->where('due_date', '<=', now()->format('Y-m-d'))->count() }}</div>
                    <small class="text-muted">Date limite passée</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-gradient-success shadow-lg text-center h-100 rounded-3 p-4 border-0 position-relative">
                <div class="position-absolute top-0 start-0 bg-success" style="width: 100%; height: 4px;"></div>
                <div class="card-body d-flex flex-column justify-content-center">
                    <i class="bi bi-plus-circle display-4 text-white opacity-90 mb-3"></i>
                    <h3 class="h5 fw-bold text-white mb-4">Nouveau Devoir</h3>
                    <a href="{{ route('prof.devoir.create') }}" class="btn btn-success btn-lg px-4 fw-bold">
                        <i class="bi bi-plus-circle me-2"></i>Ajouter
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Devoirs Table --}}
    <div class="card shadow-lg border-0 rounded-3">
        <div class="card-header bg-gradient-primary text-black py-4 border-0">
            <h4 class="card-title mb-0 fw-bold">
                <i class="bi bi-grid-3x3-gap me-2"></i>
                Liste des Devoirs
            </h4>
        </div>
        <div class="card-body p-0">
            @forelse($devoirs as $devoir)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center"><strong>Titre</strong></th>
                            <th class="text-center"><strong>Classe</strong></th>
                            <th class="text-center"><strong>Date limite</strong></th>
                            <th class="text-center"><strong>Fichier</strong></th>
                            <th class="text-center"><strong>Actions</strong></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="py-4 fw-semibold text-center">{{ Str::limit($devoir->title, 40) }}</td>
                            <td class="py-4 text-center">
                                <span class="badge bg-info rounded-pill px-3 py-2">
                                    {{ $devoir->classRoom->name ?? '-' }}
                                </span>
                            </td>
                            <td class="py-4 fw-semibold text-center">
                                <span class="badge {{ $devoir->due_date <= now()->format('Y-m-d') ? 'bg-danger' : 'bg-success' }} rounded-pill px-3 py-2">
                                    {{ \Carbon\Carbon::parse($devoir->due_date)->format('d/m/Y') }}
                                </span>
                            </td>
                            <td class="py-4 text-center">
                                @if($devoir->file)
                                <a href="{{ asset('storage/'.$devoir->file) }}" target="_blank" class="btn btn-sm btn-success" title="Télécharger">
                                    <i class="bi bi-download"></i>
                                </a>
                                @else
                                <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="py-4 text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('prof.devoir.edit', $devoir) }}" class="btn btn-outline-warning" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('prof.devoir.destroy', $devoir) }}" class="d-inline ms-1" onsubmit="return confirm('Confirmer la suppression ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @empty
            <div class="text-center py-8 text-muted">
                <i class="bi bi-file-earmark-x fs-1 opacity-50 mb-4 d-block"></i>
                <h5>Aucun devoir trouvé</h5>
                <p class="mb-0">Commencez par <a href="{{ route('prof.devoir.create') }}" class="text-primary fw-bold">ajouter votre premier devoir</a></p>
            </div>
            @endforelse
            @if($devoirs->hasPages())
            <div class="card-footer bg-transparent py-4 border-0">
                <div class="d-flex justify-content-center">
                    {{ $devoirs->appends(request()->query())->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

