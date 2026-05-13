@extends('layouts.admin')

@section('content')

<div class="container py-4">

    <h3 class="fw-bold mb-4">🎓 Gestion des Niveaux</h3>

    <!-- ALERT -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif


    <!-- TABLE -->
    <div class="card shadow-sm rounded-4">
        <div class="card-body">

            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Niveau</th>
                        <th>Matière</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($levels as $level)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $level->name }}</td>
                        <td>{{ $level->subject->name ?? '-' }}</td>

                        <td>
                            <!-- EDIT -->
                            <button class="btn btn-warning btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#editModal{{ $level->id }}">
                                Modifier
                            </button>

                            <!-- DELETE -->
                            <form action="{{ route('admin.levels.destroy',$level->id) }}"
                                  method="POST"
                                  class="d-inline">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-danger btn-sm">
                                    Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- EDIT MODAL -->
                    <div class="modal fade" id="editModal{{ $level->id }}">
                        <div class="modal-dialog">
                            <form method="POST"
                                  action="{{ route('admin.levels.update',$level->id) }}"
                                  class="modal-content">
                                @csrf
                                @method('PUT')

                                <div class="modal-header">
                                    <h5>Modifier Niveau</h5>
                                </div>

                                <div class="modal-body">

                                    <input type="text"
                                           name="name"
                                           value="{{ $level->name }}"
                                           class="form-control mb-2"
                                           required>

                                    <select name="subject_id" class="form-control">
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}"
                                                {{ $subject->id == $level->subject_id ? 'selected' : '' }}>
                                                {{ $subject->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                </div>

                                <div class="modal-footer">
                                    <button class="btn btn-success">Sauvegarder</button>
                                </div>

                            </form>
                        </div>
                    </div>

                @endforeach
                </tbody>

            </table>

        </div>
    </div>

</div>

<!-- ADD MODAL -->
<div class="modal fade" id="addModal">
    <div class="modal-dialog">
        <form method="POST"
              action="{{ route('admin.levels.store') }}"
              class="modal-content">

            @csrf

            <div class="modal-header">
                <h5>Ajouter Niveau</h5>
            </div>

            <div class="modal-body">

                <input type="text"
                       name="name"
                       placeholder="Nom du niveau"
                       class="form-control mb-3"
                       required>

                <select name="subject_id" class="form-control">
                    <option value="">Choisir matière</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}">
                            {{ $subject->name }}
                        </option>
                    @endforeach
                </select>

            </div>

            <div class="modal-footer">
                <button class="btn btn-primary">Ajouter</button>
            </div>

        </form>
    </div>
</div>

@endsection
