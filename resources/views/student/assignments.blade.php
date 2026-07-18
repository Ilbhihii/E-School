@extends('layouts.student')
@section('title', 'Mes Devoirs')
@section('breadcrumb', 'Devoirs')
@section('content')

<div class="st-page-header">
    <div>
        <h1><i class="bi bi-clipboard-check" style="color:#818CF8;"></i> Mes Devoirs</h1>
        <div class="subtitle">Envoyez et suivez l'avancement de vos devoirs</div>
    </div>
    @if($assignments->count() > 0)
    <div class="page-actions">
        <span class="st-badge st-badge-primary" style="font-size:0.75rem;">
            {{ $assignments->count() }} devoir{{ $assignments->count() > 1 ? 's' : '' }} envoyé{{ $assignments->count() > 1 ? 's' : '' }}
        </span>
    </div>
    @endif
</div>

<!-- Info niveau + classe -->
@if(isset($classRoom))
<div style="background:linear-gradient(135deg, rgba(79,70,229,0.1), rgba(124,58,237,0.05));border:1px solid rgba(124,58,237,0.1);border-radius:12px;padding:1.25rem 1.5rem;margin-bottom:1.5rem;display:flex;align-items:center;flex-wrap:wrap;gap:1.25rem;">
    <div style="display:flex;align-items:center;gap:12px;">
        <div style="width:44px;height:44px;border-radius:10px;background:linear-gradient(135deg,#4F46E5,#7C3AED);display:flex;align-items:center;justify-content:center;color:white;font-size:1.1rem;flex-shrink:0;">
            <i class="bi bi-mortarboard-fill"></i>
        </div>
        <div>
            <div style="font-size:0.65rem;text-transform:uppercase;letter-spacing:0.06em;color:#64748B;font-weight:600;">Niveau</div>
            <div style="font-weight:700;color:#F1F5F9;font-size:1rem;">{{ $classRoom->level->name ?? 'Non défini' }}</div>
        </div>
    </div>
    <div style="width:1px;height:32px;background:rgba(255,255,255,0.08);"></div>
    <div style="display:flex;align-items:center;gap:12px;">
        <div style="width:44px;height:44px;border-radius:10px;background:linear-gradient(135deg,#059669,#10B981);display:flex;align-items:center;justify-content:center;color:white;font-size:1.1rem;flex-shrink:0;">
            <i class="bi bi-building"></i>
        </div>
        <div>
            <div style="font-size:0.65rem;text-transform:uppercase;letter-spacing:0.06em;color:#64748B;font-weight:600;">Classe</div>
            <div style="font-weight:700;color:#F1F5F9;font-size:1rem;">{{ $classRoom->name }}</div>
        </div>
    </div>
</div>
@endif

<!-- ENVOYER UN DEVOIR -->
<div id="envoyerDevoir" class="st-card" style="background:linear-gradient(135deg, rgba(255,255,255,0.03), rgba(255,255,255,0.01));">
    <div class="st-card-header">
        <h4><i class="bi bi-cloud-upload" style="color:#818CF8;"></i> Envoyer un devoir</h4>
        <span class="st-badge st-badge-primary">Nouveau</span>
    </div>
    <div class="st-card-body">
        <form method="POST" action="{{ route('student.assignments.send') }}" enctype="multipart/form-data">
            @csrf
            <!-- Infos Matière / Niveau / Classe (lecture seule) -->
            @if($classRoom)
            <div style="background:rgba(79,70,229,0.06);border:1px solid rgba(79,70,229,0.12);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.25rem;display:flex;flex-wrap:wrap;gap:1.25rem;">
                <div>
                    <div style="font-size:0.6rem;text-transform:uppercase;letter-spacing:0.06em;color:#64748B;font-weight:600;margin-bottom:4px;">Niveau</div>
                    <div style="font-weight:600;color:#F1F5F9;font-size:0.85rem;">
                        <i class="bi bi-mortarboard-fill" style="color:#818CF8;margin-right:6px;"></i>
                        {{ $classRoom->level->name ?? 'Non défini' }}
                    </div>
                </div>
                <div style="width:1px;height:28px;background:rgba(255,255,255,0.06);align-self:center;"></div>
                <div>
                    <div style="font-size:0.6rem;text-transform:uppercase;letter-spacing:0.06em;color:#64748B;font-weight:600;margin-bottom:4px;">Classe</div>
                    <div style="font-weight:600;color:#F1F5F9;font-size:0.85rem;">
                        <i class="bi bi-building" style="color:#34D399;margin-right:6px;"></i>
                        {{ $classRoom->name ?? 'Non définie' }}
                    </div>
                </div>
                <div style="width:1px;height:28px;background:rgba(255,255,255,0.06);align-self:center;"></div>
                <div>
                    <div style="font-size:0.6rem;text-transform:uppercase;letter-spacing:0.06em;color:#64748B;font-weight:600;margin-bottom:4px;">Matière</div>
                    @if($hasSingleSubject)
                        <div style="font-weight:600;color:#F1F5F9;font-size:0.85rem;">
                            <i class="bi bi-book-fill" style="color:#FBBF24;margin-right:6px;"></i>
                            {{ $subjects->first()->name }}
                        </div>
                        <input type="hidden" name="subject_id" value="{{ $subjects->first()->id }}">
                    @else
                        <select name="subject_id" class="st-form-select" required style="font-size:0.8rem;padding:4px 28px 4px 10px;min-width:160px;">
                            <option value="">Choisir...</option>
                            @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                            @endforeach
                        </select>
                    @endif
                </div>
            </div>
            @endif

            <div class="row g-3">
                <div class="col-md-12">
                    <div class="st-form-group">
                        <label class="st-form-label"><i class="bi bi-journal-text me-1" style="color:#059669;"></i> Titre <span style="color:#DC2626;">*</span></label>
                        <input type="text" name="title" class="st-form-control" placeholder="Ex: Exercice algèbre" value="{{ old('title') }}" required>
                    </div>
                </div>
            </div>
            <div class="st-form-group">
                <label class="st-form-label"><i class="bi bi-file-earmark me-1" style="color:#DC2626;"></i> Fichier <span style="color:#DC2626;">*</span></label>
                <div style="border:2px dashed rgba(255,255,255,0.08);border-radius:10px;padding:1.5rem;text-align:center;transition:border-color 0.2s;" id="dropZone" onmouseover="this.style.borderColor='rgba(79,70,229,0.25)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.08)'">
                    <i class="bi bi-cloud-arrow-up-fill" style="font-size:2rem;color:rgba(255,255,255,0.2);display:block;margin-bottom:0.5rem;"></i>
                    <p style="color:rgba(255,255,255,0.5);margin-bottom:0.25rem;font-weight:500;">Glissez votre fichier ici ou cliquez pour parcourir</p>
                    <p style="color:rgba(255,255,255,0.25);font-size:0.75rem;margin-bottom:0.75rem;">PDF, DOC, DOCX — Max 10 Mo</p>
                    <input type="file" name="file" class="st-form-control" accept=".pdf,.doc,.docx" required style="max-width:300px;margin:0 auto;">
                </div>
            </div>
            <button type="submit" class="st-btn st-btn-primary" style="width:100%;justify-content:center;padding:12px;font-size:0.9rem;">
                <i class="bi bi-send-fill me-2"></i> Envoyer le devoir
            </button>
        </form>
    </div>
</div>

<!-- TABLEAU DES DEVOIRS DU PROFESSEUR -->
@if($profAssignments->count() > 0)
<div class="st-card mt-4" style="border-left:3px solid #818CF8;">
    <div class="st-card-header">
        <h4><i class="bi bi-journal-bookmark-fill" style="color:#818CF8;"></i> Devoirs du professeur</h4>
        <span class="st-badge st-badge-primary">{{ $profAssignments->count() }}</span>
    </div>
    <div class="st-card-body p-0">
        <div class="st-table-wrap">
            <table class="st-table">
                <thead>
                    <tr>
                        <th style="width:25%;">Titre</th>
                        <th style="width:13%;">Date</th>
                        <th style="width:15%;">Date limite</th>
                        <th style="width:15%;">Professeur</th>
                        <th style="width:10%;text-align:center;">Fichier</th>
                        <th style="width:12%;text-align:center;">Statut</th>
                        <th style="width:10%;text-align:center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($profAssignments as $pa)
                    @php
                        $now = \Carbon\Carbon::now();
                        $dueDate = $pa->due_date ? \Carbon\Carbon::parse($pa->due_date) : null;
                        $isOverdue = $dueDate && $now->gt($dueDate) && !$pa->student_submitted;
                        
                        if ($pa->student_grade_status === 'acqui') {
                            $statusLabel = 'Acquis';
                            $statusIcon = 'bi-check-circle-fill';
                            $statusColor = '#34D399';
                            $statusBg = 'rgba(52,211,153,0.12)';
                            $statusBorder = 'rgba(52,211,153,0.2)';
                        } elseif ($pa->student_grade_status === 'en_cours') {
                            $statusLabel = 'En cours d\'acquisition';
                            $statusIcon = 'bi-arrow-repeat';
                            $statusColor = '#FBBF24';
                            $statusBg = 'rgba(251,191,36,0.12)';
                            $statusBorder = 'rgba(251,191,36,0.2)';
                        } else {
                            $statusLabel = 'Non acquis';
                            $statusIcon = 'bi-x-circle-fill';
                            $statusColor = '#F87171';
                            $statusBg = 'rgba(248,113,113,0.12)';
                            $statusBorder = 'rgba(248,113,113,0.2)';
                        }
                    @endphp
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div style="width:28px;height:28px;border-radius:6px;background:rgba(129,140,248,0.08);display:flex;align-items:center;justify-content:center;font-size:0.7rem;color:#818CF8;flex-shrink:0;">
                                    <i class="bi bi-journal-text"></i>
                                </div>
                                <div>
                                    <div style="font-weight:500;color:#F1F5F9;font-size:0.82rem;">{{ Str::limit($pa->title, 35) }}</div>
                                    @if($pa->description)
                                    <div style="font-size:0.65rem;color:#64748B;">{{ Str::limit($pa->description, 45) }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span style="font-size:0.75rem;color:#64748B;">{{ $pa->created_at->format('d/m/Y') }}</span>
                        </td>
                        <td>
                            @if($pa->due_date)
                            <span style="font-size:0.75rem;color:{{ $isOverdue ? '#F87171' : '#94A3B8' }};font-weight:{{ $isOverdue ? '600' : '400' }};">
                                {{ \Carbon\Carbon::parse($pa->due_date)->format('d/m/Y') }}
                            </span>
                            @if($isOverdue)
                            <div style="font-size:0.6rem;color:#F87171;font-weight:600;text-transform:uppercase;letter-spacing:0.04em;">
                                <i class="bi bi-exclamation-triangle"></i> En retard
                            </div>
                            @endif
                            @else
                            <span style="color:#475569;font-size:0.75rem;">—</span>
                            @endif
                        </td>
                        <td>
                            <span style="font-size:0.78rem;color:#94A3B8;">
                                <i class="bi bi-person me-1" style="color:#818CF8;"></i>
                                {{ $pa->user?->name ?? 'Professeur' }}
                            </span>
                        </td>
                        <td style="text-align:center;">
                            @if($pa->file)
                            <a href="{{ asset('storage/'.$pa->file) }}" target="_blank" style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:6px;background:rgba(255,255,255,0.04);color:#94A3B8;font-size:0.72rem;text-decoration:none;transition:all 0.15s;" onmouseover="this.style.background='rgba(255,255,255,0.08)';this.style.color='#F1F5F9'" onmouseout="this.style.background='rgba(255,255,255,0.04)';this.style.color='#94A3B8'">
                                <i class="bi bi-download"></i> Télécharger
                            </a>
                            @else
                            <span style="font-size:0.65rem;color:#475569;">—</span>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:20px;font-size:0.72rem;font-weight:600;background:{{ $statusBg }};color:{{ $statusColor }};border:1px solid {{ $statusBorder }};">
                                <i class="bi {{ $statusIcon }}"></i>
                                {{ $statusLabel }}
                            </span>
                            @if($pa->student_grade !== null)
                            <div style="font-size:0.7rem;color:#64748B;margin-top:3px;">Note: {{ $pa->student_grade }}/20</div>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            @if($pa->student_submitted)
                            <span class="st-badge st-badge-success" style="font-size:0.65rem;"><i class="bi bi-check me-1"></i>Soumis</span>
                            @elseif($pa->is_locked)
                            <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border-radius:6px;background:rgba(248,113,113,0.1);color:#F87171;font-size:0.65rem;font-weight:600;white-space:nowrap;">
                                <i class="bi bi-lock-fill"></i> 
                                @if(!$pa->has_file) Non disponible @else Verrouillé @endif
                            </span>
                            @else
                            <a href="#envoyerDevoir" style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:6px;background:rgba(79,70,229,0.1);color:#818CF8;font-size:0.65rem;font-weight:600;text-decoration:none;transition:all 0.15s;white-space:nowrap;" onmouseover="this.style.background='rgba(79,70,229,0.2)'" onmouseout="this.style.background='rgba(79,70,229,0.1)'">
                                <i class="bi bi-send-fill"></i> Soumettre
                            </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- TABLEAU DES DEVOIRS ENVOYÉS -->
<div class="st-card mt-4">
    <div class="st-card-header">
        <h4><i class="bi bi-list-check" style="color:#818CF8;"></i> Mes devoirs envoyés</h4>
        @if($assignments->count() > 0)
        <span class="st-badge st-badge-primary">{{ $assignments->count() }}</span>
        @endif
    </div>
    <div class="st-card-body p-0">
        @if($assignments->count() === 0)
        <div class="st-empty">
            <div class="st-empty-icon"><i class="bi bi-inbox"></i></div>
            <h5>Aucun devoir envoyé</h5>
            <p>Commencez par envoyer votre premier devoir ci-dessus !</p>
        </div>
        @else
        <div class="st-table-wrap">
            <table class="st-table">
                <thead>
                    <tr>
                        <th style="width:35%;">Titre</th>
                        <th style="width:15%;">Matière</th>
                        <th style="width:15%;">Date</th>
                        <th style="width:12%;text-align:center;">Fichier</th>
                        <th style="width:13%;text-align:center;">Statut</th>
                        <th style="width:10%;">Note</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assignments as $a)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div style="width:28px;height:28px;border-radius:6px;background:rgba(79,70,229,0.08);display:flex;align-items:center;justify-content:center;font-size:0.7rem;color:#818CF8;flex-shrink:0;">
                                    <i class="bi bi-file-text"></i>
                                </div>
                                <div>
                                    <div style="font-weight:500;color:#F1F5F9;font-size:0.82rem;">{{ Str::limit($a->title, 35) }}</div>
                                    @if($a->course)<div style="font-size:0.65rem;color:#64748B;">{{ Str::limit($a->course->title, 30) }}</div>@endif
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($a->subject)
                            <span style="font-size:0.78rem;color:#94A3B8;">{{ $a->subject->name }}</span>
                            @elseif($a->course && $a->course->subject)
                            <span style="font-size:0.78rem;color:#94A3B8;">{{ $a->course->subject->name }}</span>
                            @else
                            <span style="color:#475569;font-size:0.75rem;">—</span>
                            @endif
                        </td>
                        <td>
                            <span style="font-size:0.75rem;color:#64748B;">{{ $a->created_at->format('d/m/Y') }}</span>
                            <div style="font-size:0.65rem;color:#475569;">{{ $a->created_at->format('H:i') }}</div>
                        </td>
                        <td style="text-align:center;">
                            <a href="{{ asset('storage/'.$a->file) }}" target="_blank" style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:6px;background:rgba(255,255,255,0.04);color:#94A3B8;font-size:0.72rem;text-decoration:none;transition:all 0.15s;" onmouseover="this.style.background='rgba(255,255,255,0.08)';this.style.color='#F1F5F9'" onmouseout="this.style.background='rgba(255,255,255,0.04)';this.style.color='#94A3B8'">
                                <i class="bi bi-download"></i> Voir
                            </a>
                        </td>
                        <td style="text-align:center;">
                            @if($a->grade !== null)
                            <span class="st-badge st-badge-success"><i class="bi bi-check-circle-fill me-1"></i> Noté</span>
                            @else
                            <span class="st-badge st-badge-warning"><i class="bi bi-clock me-1"></i> En attente</span>
                            @endif
                        </td>
                        <td>
                            @if($a->grade !== null)
                            <div style="text-align:center;">
                                <span style="font-weight:700;color:#34D399;font-size:1rem;">{{ $a->grade }}/20</span>
                                @if($a->comment)
                                <div style="font-size:0.65rem;color:#64748B;margin-top:1px;cursor:pointer;" title="{{ $a->comment }}">
                                    <i class="bi bi-chat-dots me-1"></i>{{ Str::limit($a->comment, 15) }}
                                </div>
                                @endif
                            </div>
                            @else
                            <span style="color:#475569;font-size:0.75rem;text-align:center;display:block;">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>



<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropZone = document.getElementById('dropZone');
    if (dropZone) {
        ['dragenter', 'dragover'].forEach(e => {
            dropZone.addEventListener(e, function(ev) {
                ev.preventDefault();
                this.style.borderColor = 'rgba(79,70,229,0.35)';
                this.style.background = 'rgba(79,70,229,0.04)';
            });
        });
        ['dragleave', 'drop'].forEach(e => {
            dropZone.addEventListener(e, function(ev) {
                ev.preventDefault();
                this.style.borderColor = 'rgba(255,255,255,0.08)';
                this.style.background = 'transparent';
            });
        });
    }
});
</script>

@endsection
