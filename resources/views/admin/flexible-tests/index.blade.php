@extends('layouts.admin')

@section('title', 'Tests')
@section('page_title', 'Tests')
@section('breadcrumb', 'Évaluations → Tests')

@section('content')
<div class="adm-card">
    <div class="adm-card-header d-flex justify-content-between align-items-center gap-3">
        <div>
            <h4 class="mb-1">
                <i class="bi bi-ui-checks-grid me-2"></i>
                Tests flexibles
            </h4>
            <p class="mb-0 text-secondary">
                Vocal, écrit ou QCM avec texte, images, PDF ou DOCX.
            </p>
        </div>

        <a href="{{ route('admin.flexible-tests.create') }}" class="adm-btn adm-btn-primary">
            <i class="bi bi-plus-lg"></i>
            Nouveau test
        </a>
    </div>

    <div class="adm-card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-dark align-middle">
                <thead>
                    <tr>
                        <th>Test</th>
                        <th>Parcours</th>
                        <th>Support</th>
                        <th>Réponse</th>
                        <th>État</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tests as $test)
                        <tr>
                            <td>
                                <strong>{{ $test->title }}</strong>
                                <div class="small text-secondary">
                                    Créé le {{ $test->created_at?->format('d/m/Y H:i') }}
                                </div>
                            </td>

                            <td>
                                {{ optional($test->subject)->name ?: '—' }}
                                →
                                {{ optional($test->level)->name ?: '—' }}
                                →
                                {{ optional($test->classRoom)->name ?: '—' }}
                            </td>

                            <td>
                                @switch($test->source_type)
                                    @case('text') Texte @break
                                    @case('files') Fichiers @break
                                    @default Texte + fichiers
                                @endswitch

                                @if(is_array($test->source_files) && count($test->source_files))
                                    <div class="mt-1 d-flex flex-wrap gap-1">
                                        @foreach($test->source_files as $index => $file)
                                            <a
                                                href="{{ route('admin.flexible-tests.file', [$test, $index]) }}"
                                                class="badge text-bg-secondary text-decoration-none"
                                            >
                                                {{ $file['name'] ?? ('Fichier ' . ($index + 1)) }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </td>

                            <td>
                                @if($test->response_type === 'vocal')
                                    <span class="badge text-bg-primary">Vocal</span>
                                @elseif($test->response_type === 'written')
                                    <span class="badge text-bg-warning">Écrit</span>
                                    <div class="small text-secondary">
                                        {{ $test->written_response_mode }}
                                    </div>
                                @else
                                    <span class="badge text-bg-success">QCM</span>
                                    <div class="small text-secondary">
                                        {{ count($test->qcm_questions ?: []) }} question(s)
                                    </div>
                                @endif
                            </td>

                            <td>
                                <span class="badge {{ $test->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $test->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-secondary py-5">
                                Aucun test flexible créé pour le moment.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $tests->links() }}
    </div>
</div>
@endsection