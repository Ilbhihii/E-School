@extends('layouts.admin')

@section('content')
<div class="admin-page">
    <div class="admin-container">

        <!-- HEADER -->
        <div class="admin-header">
            <div class="adm-flex adm-gap-3" style="align-items:center;">
                <div style="width:56px;height:56px;background:linear-gradient(135deg,#10b981,#059669);border-radius:16px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi bi-award-fill" style="font-size:1.5rem;color:white;"></i>
                </div>
                <div>
                    <h1 class="admin-header-title"><span class="gradient">Détails du Test</span></h1>
                    <p class="admin-header-subtitle">
                        <strong>{{ $user->name }}</strong> — <span class="adm-badge adm-badge-purple">{{ $test->title }}</span>
                    </p>
                    <div class="adm-flex adm-gap-2 adm-mt-1" style="flex-wrap:wrap;">
                        <span style="font-size:0.85rem;color:var(--adm-text-secondary);">Matière: {{ $test->subject->name ?? 'N/A' }}</span>
                        <span style="font-size:0.85rem;color:var(--adm-text-secondary);">Date: {{ $result->created_at->format('d/m/Y H:i') }}</span>
                        <span style="font-size:0.85rem;color:var(--adm-text-secondary);">Durée: {{ $test->duration }} min</span>
                    </div>
                </div>
            </div>
            <a href="{{ route('admin.users.test-results', $user->id) }}" class="adm-btn adm-btn-ghost">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>

        <!-- SCORE SUMMARY -->
        <div class="stats-grid adm-mb-3">
            <div class="stat-card green adm-fade-up" style="text-align:center;display:block;">
                <h3 style="font-size:0.95rem;font-weight:700;color:var(--adm-text);margin:0 0 1rem;">Score Final</h3>
                <div style="font-size:2.5rem;font-weight:800;color:#16a34a;">{{ $result->score }} / {{ $result->total_questions }}</div>
                <div class="adm-progress" style="height:10px;margin:0.75rem 0;">
                    <div class="adm-progress-bar success" style="width:{{ $result->percentage }}%"></div>
                </div>
                <div style="font-size:1.5rem;font-weight:700;color:var(--adm-text);">{{ $result->percentage }}%</div>
            </div>
            <div class="stat-card blue adm-fade-up" style="text-align:center;display:block;">
                <h3 style="font-size:0.95rem;font-weight:700;color:var(--adm-text);margin:0 0 1rem;">Note /20</h3>
                <div style="font-size:2.5rem;font-weight:800;color:var(--adm-primary);">{{ number_format(($result->percentage / 5), 1) }}/20</div>
            </div>
            <div class="stat-card purple adm-fade-up" style="text-align:center;display:block;">
                <h3 style="font-size:0.95rem;font-weight:700;color:var(--adm-text);margin:0 0 1rem;">Questions Répondues</h3>
                <div style="font-size:2.5rem;font-weight:800;color:var(--adm-purple);">{{ $result->total_questions }}</div>
            </div>
        </div>

        <!-- QUESTIONS DETAIL -->
        <div class="adm-card">
            <div class="adm-card-header" style="background:linear-gradient(135deg,#f0fdf4,#ecfdf5);">
                <h3><i class="bi bi-list-check"></i> Analyse Détaillée par Question</h3>
            </div>
            <div class="adm-card-body" style="padding:0;">
                @foreach($test->questions as $question)
                <div style="padding:1.5rem 1.75rem;border-bottom:1px solid var(--adm-border);">
                    <div class="adm-flex adm-gap-3 adm-mb-3">
                        <div style="width:40px;height:40px;background:linear-gradient(135deg,#f59e0b,#d97706);border-radius:12px;display:flex;align-items:center;justify-content:center;font-weight:700;color:white;flex-shrink:0;">
                            Q{{ $loop->iteration }}
                        </div>
                        <div>
                            <h4 style="font-size:1.05rem;font-weight:700;color:var(--adm-text);margin:0 0 0.75rem;">{{ $question->question }}</h4>
                            <div class="adm-flex" style="flex-direction:column;gap:0.4rem;">
                                @foreach($question->answers as $answer)
                                <div class="adm-flex adm-gap-2" style="align-items:center;padding:0.5rem 0.75rem;border-radius:8px;{{ $answer->is_correct ? 'background:#f0fdf4;border:1px solid #bbf7d0;' : 'background:white;border:1px solid var(--adm-border);' }}">
                                    <div style="width:22px;height:22px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.75rem;{{ $answer->is_correct ? 'background:#16a34a;color:white;' : 'background:#d1d5db;color:white;' }}">
                                        @if($answer->is_correct) ✓ @endif
                                    </div>
                                    <span style="font-weight:500;">{{ $answer->answer }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @if(isset($result->student_responses[$question->id]) && count($result->student_responses[$question->id]) > 0)
                    <div style="background:linear-gradient(135deg,#eff6ff,#eef2ff);border-radius:14px;padding:1.25rem;border:1px solid #bfdbfe;">
                        <h5 style="font-weight:700;font-size:0.95rem;color:#1e40af;margin:0 0 0.75rem;">Réponse de l'étudiant:</h5>
                        <div class="adm-flex" style="flex-direction:column;gap:0.4rem;">
                            @foreach($result->student_responses[$question->id] as $studAns)
                            <div class="adm-flex adm-gap-2" style="align-items:center;padding:0.5rem 0.75rem;border-radius:8px;{{ $studAns['is_correct'] ? 'background:#f0fdf4;border:1px solid #bbf7d0;' : 'background:#fef2f2;border:1px solid #fecaca;' }}">
                                <div style="width:22px;height:22px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.75rem;{{ $studAns['is_correct'] ? 'background:#16a34a;color:white;' : 'background:#dc2626;color:white;' }}">
                                    {{ $studAns['is_correct'] ? '✓' : '✗' }}
                                </div>
                                <span style="font-weight:500;{{ $studAns['is_correct'] ? 'color:#166534;' : 'color:#991b1b;' }}">{{ $studAns['text'] }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @else
                    <div style="background:#f9fafb;border-radius:14px;padding:1.25rem;border:1px solid var(--adm-border);">
                        <p style="color:var(--adm-text-secondary);font-weight:500;margin:0;">Aucune réponse fournie</p>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

    </div>
</div>
@endsection
