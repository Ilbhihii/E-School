@extends('layouts.admin')

@section('content')

<div class="classes-page">

    <!-- HEADER -->
    <div class="page-header">

        <div>
            <h2 class="title">📚 Gestion des Niveaux</h2>
            <p class="subtitle">Créer et gérer les niveaux de la plateforme</p>
        </div>

        <a href="{{ route('admin.classes.create') }}" class="btn-add">
            ➕ Ajouter une classe
        </a>

    </div>

    <!-- TABLE CARD -->
    <div class="table-card">

        <table>

            <thead>
                <tr>
                    <th>Nom de la classe</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                @forelse($classes as $class)

                <tr>

                    <td class="class-name">
                        🎓 {{ $class->name }}
                    </td>

                    <td class="actions">

                        <a href="{{ route('admin.classes.edit', $class->id) }}"
                           class="btn-edit">
                            ✏️ Modifier
                        </a>

                        <form method="POST"
                              action="{{ route('admin.classes.destroy', $class->id) }}"
                              onsubmit="return confirm('Supprimer cette classe ?')">
                            @csrf
                            @method('DELETE')

                            <button class="btn-delete">
                                🗑 Supprimer
                            </button>

                        </form>

                    </td>

                </tr>

                @empty

                <tr>
                    <td colspan="2" class="empty">
                        Aucune classe disponible
                    </td>
                </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

<!-- STYLE -->
<style>

/* PAGE */
.classes-page{
    padding:20px;
}

/* HEADER */
.page-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.title{
    font-size:22px;
    font-weight:bold;
    color:#1f2937;
}

.subtitle{
    font-size:14px;
    color:#6b7280;
}

.btn-add{
    background:#003A8F;
    color:white;
    padding:10px 16px;
    border-radius:10px;
    text-decoration:none;
    font-weight:600;
    transition:0.3s;
}

.btn-add:hover{
    background:#002b6b;
    transform:translateY(-2px);
}

/* TABLE */
.table-card{
    background:white;
    border-radius:16px;
    box-shadow:0 10px 25px rgba(0,0,0,0.05);
    overflow:hidden;
}

table{
    width:100%;
    border-collapse:collapse;
}

thead{
    background:#F5F7FA;
}

th{
    text-align:left;
    padding:15px;
    font-size:12px;
    text-transform:uppercase;
    color:#6b7280;
}

td{
    padding:15px;
    border-top:1px solid #eee;
}

/* ROW HOVER */
tr:hover{
    background:#f9fafb;
}

/* CLASS NAME */
.class-name{
    font-weight:600;
    color:#111827;
}

/* ACTIONS */
.actions{
    display:flex;
    gap:10px;
}

/* BUTTONS */
.btn-edit{
    background:#facc15;
    color:black;
    padding:6px 10px;
    border-radius:8px;
    text-decoration:none;
    font-size:13px;
    transition:0.3s;
}

.btn-edit:hover{
    background:#eab308;
}

.btn-delete{
    background:#ef4444;
    color:white;
    border:none;
    padding:6px 10px;
    border-radius:8px;
    font-size:13px;
    cursor:pointer;
    transition:0.3s;
}

.btn-delete:hover{
    background:#dc2626;
}

/* EMPTY */
.empty{
    text-align:center;
    color:#9ca3af;
    padding:20px;
}

</style>

@endsection
