@extends('layouts.admin')

@section('title', 'Nouvelle offre')
@section('page_title', 'Nouvelle offre')
@section('breadcrumb', 'Offres → Nouvelle offre')

@section('content')
<div class="plan-editor-page">
    <div class="plan-editor-intro">
        <div><h2>Créer une nouvelle offre</h2><p>Une offre active apparaîtra immédiatement sur la page publique /plans.</p></div>
        <a href="{{ route('admin.plans.index') }}"><i class="bi bi-arrow-left"></i> Retour aux offres</a>
    </div>

    <form method="POST" action="{{ route('admin.plans.store') }}">
        @csrf
        @include('admin.plans._form')
    </form>
</div>
@endsection
