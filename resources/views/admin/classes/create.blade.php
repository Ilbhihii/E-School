@extends('layouts.admin')

@section('content')

<div class="add-class-page">

    <!-- CARD -->
    <div class="form-card">

        <h2 class="title">➕ Ajouter une classe</h2>
        <p class="subtitle">Créer une nouvelle classe pour organiser les cours</p>

        <form method="POST" action="{{ route('admin.classes.store') }}">

            @csrf

            <!-- NOM -->
            <div class="form-group">
                <label>Nom de la classe</label>
                <input type="text"
                       name="name"
                       placeholder="Ex: Terminale A"
                       required>
            </div>


            <!-- BTN -->
            <button type="submit" class="btn-submit">
                ➕ Ajouter la classe
            </button>

        </form>

    </div>

</div>

<!-- STYLE -->
<style>

/* PAGE */
.add-class-page{
    display:flex;
    justify-content:center;
    align-items:center;
    min-height:80vh;
    padding:20px;
    background:#F5F7FA;
}

/* CARD */
.form-card{
    width:500px;
    background:white;
    border-radius:16px;
    padding:25px;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
}

/* TITLE */
.title{
    font-size:22px;
    font-weight:bold;
    color:#111827;
    margin-bottom:5px;
}

.subtitle{
    font-size:14px;
    color:#6b7280;
    margin-bottom:20px;
}

/* FORM */
.form-group{
    margin-bottom:15px;
}

label{
    display:block;
    font-weight:600;
    margin-bottom:6px;
    color:#374151;
}

/* INPUTS */
input, select{
    width:100%;
    padding:10px 12px;
    border:1px solid #ddd;
    border-radius:10px;
    outline:none;
    transition:0.3s;
    font-size:14px;
}

input:focus, select:focus{
    border-color:#003A8F;
    box-shadow:0 0 10px rgba(0,58,143,0.2);
}

/* BUTTON */
.btn-submit{
    width:100%;
    margin-top:10px;
    background:#003A8F;
    color:white;
    padding:12px;
    border:none;
    border-radius:10px;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
}

.btn-submit:hover{
    background:#002b6b;
    transform:translateY(-2px);
}

</style>

@endsection
