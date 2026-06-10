@extends('layouts.prof')

@section('content')
<div class="admin-page">
    <div class="admin-container">

        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">Devoirs des Étudiants</span></h1>
                <p class="admin-header-subtitle">Consultez et corrigez les devoirs soumis par vos étudiants</p>
            </div>
        </div>

        @if(session('success'))
            <div class="adm-alert adm-alert-success">{{ session('success') }}</div>
        @endif

        <div class="adm-card">
            <div class="adm-table-wrap">
                <table class="adm-table">
                    <thead>
                        <tr>
                            <th>Étudiant</th>
                            <th>Devoir</th>
                            <th>Fichier</th>
                            <th>Note</th>
                            <th style="min-width:280px;">Correction</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assignments as $a)
                        <tr>
                            <td><span style="font-weight:600;">{{ $a->user->name }}</span></td>
                            <td>{{ $a->title }}</td>
                            <td>
                                <a href="{{ asset('storage/'.$a->file) }}" target="_blank" class="adm-btn adm-btn-sm adm-btn-ghost">
                                    <i class="bi bi-eye"></i> Voir fichier
                                </a>
                            </td>
                            <td>
                                @if($a->grade == 20)
                                    <span class="adm-badge adm-badge-success"><i class="bi bi-check-circle-fill"></i> Bien fait</span>
                                @elseif($a->grade == 0)
                                    <span class="adm-badge adm-badge-danger"><i class="bi bi-x-circle-fill"></i> N'a pas fait</span>
                                @else
                                    <span class="adm-badge adm-badge-gray"><i class="bi bi-clock"></i> Non corrigé</span>
                                @endif
                            </td>
                            <td>
                                <form method="POST" action="{{ route('prof.grade') }}" class="adm-flex adm-gap-2" style="flex-wrap:wrap;align-items:flex-end;">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $a->id }}">
                                    <div>
                                        <div class="adm-flex adm-gap-2" style="align-items:center;">
                                            <label class="adm-form-label" style="margin-bottom:0;font-size:0.78rem;">
                                                <input type="radio" name="status" value="bien" required> ✅ Bien fait
                                            </label>
                                            <label class="adm-form-label" style="margin-bottom:0;font-size:0.78rem;">
                                                <input type="radio" name="status" value="pas"> ❌ Pas fait
                                            </label>
                                        </div>
                                        <textarea name="comment" class="adm-form-input" style="padding:0.4rem 0.75rem;font-size:0.8rem;margin-top:0.25rem;" placeholder="Commentaire..." rows="1"></textarea>
                                    </div>
                                    <button type="submit" class="adm-btn adm-btn-success adm-btn-sm">
                                        <i class="bi bi-check-lg"></i> Corriger
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
