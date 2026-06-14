@extends('layouts.student')
@section('title', 'Mes Devoirs')
@section('content')

<div class="page-header">
    <div>
        <h1><i class="bi bi-upload" style="color:#059669;"></i> Mes Devoirs</h1>
        <div class="subtitle">Soumettez vos devoirs et suivez vos notes</div>
    </div>
</div>

@if(session('success'))
<div class="pr-alert pr-alert-success mb-3"><i class="bi bi-check-circle-fill" style="flex-shrink:0;margin-top:1px;"></i> <span>{{ session('success') }}</span></div>
@endif
@if(session('error'))
<div class="pr-alert pr-alert-danger mb-3"><i class="bi bi-exclamation-circle-fill" style="flex-shrink:0;margin-top:1px;"></i> <span>{{ session('error') }}</span></div>
@endif

<!-- Level & Class Info -->
<div class="pr-card mb-3">
    <div class="pr-card-body">
        <div class="row g-2 align-items-center">
            <div class="col-md-3 col-6">
                <div style="display:flex;align-items:center;gap:0.5rem;padding:0.5rem 0.75rem;background:rgba(5,150,105,0.06);border-radius:8px;">
                    <i class="bi bi-layers-fill" style="color:#059669;"></i>
                    <div><small style="color:#64748B;font-size:11px;display:block;">Niveau</small><strong style="color:#059669;font-size:0.82rem;">{{ $level->name }}</strong></div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div style="display:flex;align-items:center;gap:0.5rem;padding:0.5rem 0.75rem;background:rgba(79,70,229,0.06);border-radius:8px;">
                    <i class="bi bi-building" style="color:#4F46E5;"></i>
                    <div><small style="color:#64748B;font-size:11px;display:block;">Classe</small><strong style="color:#4F46E5;font-size:0.82rem;">{{ $class->name }}</strong></div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($subjects->isEmpty())
<div class="pr-card"><div class="pr-empty"><div class="pr-empty-icon"><i class="bi bi-inbox"></i></div><h5>Aucun devoir disponible</h5><p>Votre professeur n'a pas encore créé de devoirs pour votre classe.</p></div></div>
@else

@if($coursesWithDevoirs->count() > 0)
<div class="pr-card mb-3">
    <div class="pr-card-header">
        <h4><i class="bi bi-clipboard-fill" style="color:#4F46E5;"></i> Devoirs à faire</h4>
        <span class="pr-badge pr-badge-primary">{{ $coursesWithDevoirs->sum(fn($c) => $c->devoirs->count()) }} devoir(s)</span>
    </div>
    <div class="pr-card-body">
        @php $currentSubject = null; @endphp
        @foreach($coursesWithDevoirs as $course)
            @if($course->subject && $course->subject->name !== $currentSubject)
                @php $currentSubject = $course->subject->name; @endphp
                <div style="font-weight:600;font-size:0.85rem;color:#4F46E5;margin-top:1rem;margin-bottom:0.5rem;{{ $loop->first ? 'margin-top:0;' : '' }}border-bottom:1px solid rgba(255,255,255,0.04);padding-bottom:0.5rem;">
                    <i class="bi bi-book-fill me-2"></i>{{ $course->subject->name }}
                </div>
            @endif
            @foreach($course->devoirs as $devoir)
            <div style="border:1px solid rgba(255,255,255,0.04);border-radius:8px;padding:0.85rem;margin-bottom:0.5rem;transition:border-color 0.2s;" onmouseover="this.style.borderColor='rgba(255,255,255,0.08)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.04)'">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div style="flex:1;">
                        <h6 style="font-weight:600;margin-bottom:0.15rem;color:#F1F5F9;font-size:0.88rem;"><i class="bi bi-file-text me-1" style="color:#4F46E5;"></i> {{ $devoir->title }}</h6>
                        @if($devoir->description)<p style="font-size:0.78rem;color:#64748B;margin-bottom:0.35rem;">{{ $devoir->description }}</p>@endif
                        @if($devoir->due_date)<small style="color:#D97706;font-size:0.72rem;"><i class="bi bi-calendar me-1"></i>À rendre avant le {{ \Carbon\Carbon::parse($devoir->due_date)->format('d/m/Y') }}</small>@endif
                    </div>
                    <div class="d-flex gap-1 flex-shrink-0">
                        @if($devoir->file)
                        <a href="{{ asset('storage/'.$devoir->file) }}" target="_blank" class="pr-btn pr-btn-ghost pr-btn-sm" style="font-size:0.7rem;"><i class="bi bi-eye"></i></a>
                        <a href="{{ asset('storage/'.$devoir->file) }}" download class="pr-btn pr-btn-primary pr-btn-sm" style="font-size:0.7rem;"><i class="bi bi-download"></i></a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        @endforeach
    </div>
</div>
@endif

<!-- Formulaire -->
<div class="pr-card mb-3">
    <div class="pr-card-header">
        <h4><i class="bi bi-cloud-upload-fill" style="color:#059669;"></i> Envoyer mon devoir</h4>
    </div>
    <div class="pr-card-body">
        <form method="POST" action="{{ route('student.devoirs.send') }}" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <div style="margin-bottom:1rem;">
                        <label class="pr-label"><i class="bi bi-book me-1" style="color:#4F46E5;"></i>Matière <span style="color:#DC2626;">*</span></label>
                        <select name="subject_id" class="pr-select" required>
                            <option value="">Choisir une matière...</option>
                            @foreach($subjects as $subject)<option value="{{ $subject->id }}">{{ $subject->name }}</option>@endforeach
                        </select>
                        @error('subject_id') <span style="font-size:0.75rem;color:#F87171;">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div style="margin-bottom:1rem;">
                        <label class="pr-label"><i class="bi bi-file-earmark me-1" style="color:#DC2626;"></i>Fichier <span style="color:#DC2626;">*</span></label>
                        <div style="border:2px dashed rgba(255,255,255,0.08);border-radius:10px;padding:1.25rem;text-align:center;cursor:pointer;transition:all 0.2s;" id="uploadZone">
                            <i class="bi bi-cloud-arrow-up-fill" style="font-size:1.5rem;color:#4F46E5;display:block;margin-bottom:0.35rem;"></i>
                            <p style="font-weight:500;color:#94A3B8;margin-bottom:0.25rem;font-size:0.82rem;">Glisser-déposer ou cliquer</p>
                            <small style="color:#64748B;font-size:0.7rem;">PDF, DOC, DOCX, JPG, PNG — Max 10 MB</small>
                            <input type="file" name="file" id="fileInput" class="pr-input" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx" required style="display:block;margin:0.5rem auto 0;max-width:280px;">
                        </div>
                        @error('file') <span style="font-size:0.75rem;color:#F87171;">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <button type="submit" class="pr-btn pr-btn-success" style="width:100%;justify-content:center;padding:10px;">
                <i class="bi bi-upload me-1"></i> Envoyer le devoir
            </button>
        </form>
    </div>
</div>
@endif

<!-- Tableau des soumissions -->
<div class="pr-card">
    <div class="pr-card-header">
        <h4><i class="bi bi-list-check" style="color:#059669;"></i> Mes devoirs soumis</h4>
        <span class="pr-badge pr-badge-primary">{{ $submittedAssignments->count() }} soumis</span>
    </div>
    <div class="pr-card-body p-0">
        @if($submittedAssignments->count() === 0)
        <div class="pr-empty"><div class="pr-empty-icon"><i class="bi bi-inbox"></i></div><h5>Aucun devoir soumis</h5><p>Utilisez le formulaire ci-dessus pour envoyer votre premier devoir.</p></div>
        @else
        <div class="pr-table-wrap">
            <table class="pr-table">
                <thead>
                    <tr><th>Date</th><th>Matière</th><th>Devoir</th><th>Fichier</th><th style="text-align:center;">Note</th><th>Commentaire</th></tr>
                </thead>
                <tbody>
                    @foreach($submittedAssignments as $a)
                    <tr>
                        <td><small style="color:#64748B;">{{ $a->created_at->format('d/m/Y') }}<br>{{ $a->created_at->format('H:i') }}</small></td>
                        <td>@if($a->course && $a->course->subject)<span class="pr-badge pr-badge-purple" style="font-size:0.65rem;">{{ $a->course->subject->name }}</span>@else<small style="color:#64748B;">-</small>@endif</td>
                        <td><div style="font-weight:500;font-size:0.82rem;color:#F1F5F9;">{{ Str::limit($a->title, 30) }}</div></td>
                        <td><a href="{{ asset('storage/'.$a->file) }}" target="_blank" class="pr-btn pr-btn-ghost pr-btn-sm" style="font-size:0.7rem;"><i class="bi bi-eye"></i> Voir</a></td>
                        <td style="text-align:center;">
                            @if($a->grade !== null)
                                <span class="pr-badge {{ $a->grade >= 10 ? 'pr-badge-success' : 'pr-badge-danger' }}" style="font-weight:700;">{{ $a->grade }}/20</span>
                            @else
                                <span class="pr-badge pr-badge-warning"><i class="bi bi-hourglass me-1"></i>En attente</span>
                            @endif
                        </td>
                        <td>@if($a->comment)<small style="color:#94A3B8;">{{ Str::limit($a->comment, 40) }}</small>@else<small style="color:#475569;">-</small>@endif</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

<script>
document.getElementById('fileInput')?.addEventListener('change', function() {
    if(this.files?.[0]) document.getElementById('fileName') && (document.getElementById('fileName').textContent = this.files[0].name);
});
</script>

@endsection
