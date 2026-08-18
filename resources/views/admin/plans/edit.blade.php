@extends('layouts.admin')

@section('title', 'Modifier l’offre')
@section('page_title', 'Modifier l’offre')
@section('breadcrumb', 'Offres → Modifier ' . $plan->name)

@section('content')
<div class="plan-editor-page">
    <div class="plan-editor-intro">
        <div><h2>Modifier {{ $plan->name }}</h2><p>Les changements sont utilisés immédiatement sur /plans et lors du paiement.</p></div>
        <a href="{{ route('admin.plans.index') }}"><i class="bi bi-arrow-left"></i> Retour aux offres</a>
    </div>

    <form method="POST" action="{{ route('admin.plans.update', $plan) }}">
        @csrf
        @method('PUT')
        @include('admin.plans._form')
    </form>
</div>
@endsection
