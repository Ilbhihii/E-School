@extends('layouts.prof')

@section('content')
<div class="admin-page">
    <div class="admin-container" style="max-width:800px">

        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">Créer un nouveau cours</span></h1>
                <p class="admin-header-subtitle">Remplissez les informations pour ajouter votre cours</p>
            </div>
        </div>

        <!-- FORM CARD -->
        <div class="adm-card">
            <div class="adm-card-body">
                <form action="{{ route('prof.courses.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="adm-form-group">
                        <label class="adm-form-label"><i class="bi bi-book"></i> Titre du cours</label>
                        <input type="text" name="title" value="{{ old('title') }}" class="adm-form-input @error('title') error @enderror" placeholder="Titre du cours" required>
                        @error('title') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="adm-form-group">
                        <label class="adm-form-label"><i class="bi bi-card-text"></i> Description</label>
                        <textarea name="description" rows="5" class="adm-form-textarea @error('description') error @enderror" placeholder="Description du cours" required>{{ old('description') }}</textarea>
                        @error('description') <div class="adm-form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="adm-grid-2">
                        <div class="adm-form-group">
                            <label class="adm-form-label"><i class="bi bi-mortarboard"></i> Niveau</label>
                            <select name="level_id" class="adm-form-select @error('level_id') error @enderror" required>
                                <option value="">Choisir un niveau...</option>
                                @foreach($levels as $level)
                                    <option value="{{ $level->id }}" {{ old('level_id') == $level->id ? 'selected' : '' }}>{{ $level->name }} ({{ $level->subject->name ?? '' }})</option>
                                @endforeach
                            </select>
                            @error('level_id') <div class="adm-form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="adm-form-group">
                            <label class="adm-form-label"><i class="bi bi-journal-bookmark"></i> Matière</label>
                            <select name="subject_id" class="adm-form-select @error('subject_id') error @enderror" required>
                                <option value="">Choisir une matière...</option>
                                @foreach($subjects->unique('name') as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                                @endforeach
                            </select>
                            @error('subject_id') <div class="adm-form-error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="adm-form-file adm-mb-2">
                        <label class="adm-form-label"><i class="bi bi-play-circle"></i> Vidéo introductrice</label>
                        <input type="file" name="video" accept="video/*" class="w-100" onchange="previewVideo(event)">
                        @error('video') <div class="adm-form-error">{{ $message }}</div> @enderror
                        <div id="videoPreview" class="adm-mt-1" style="display:none;">
                            <video class="w-100 rounded" controls style="max-height:200px;"></video>
                        </div>
                    </div>

                    <div class="adm-form-file adm-mb-2">
                        <label class="adm-form-label"><i class="bi bi-file-earmark-pdf"></i> Document PDF (optionnel)</label>
                        <input type="file" name="pdf" accept=".pdf" class="w-100" onchange="previewPDF(event)">
                        @error('pdf') <div class="adm-form-error">{{ $message }}</div> @enderror
                        <div id="pdfPreview" class="adm-mt-1" style="display:none;">
                            <iframe class="w-100 rounded" style="height:300px;border:none;"></iframe>
                        </div>
                    </div>

                    <!-- DEVOIR OPTIONNEL -->
                    <div class="adm-card" style="border:2px dashed var(--adm-border);backdrop-filter:none;background:#fafbfc;">
                        <div class="adm-card-header">
                            <h3><i class="bi bi-file-earmark-text"></i> Ajouter un devoir (optionnel)</h3>
                        </div>
                        <div class="adm-card-body">
                            <div class="adm-grid-2">
                                <div class="adm-form-group">
                                    <label class="adm-form-label">📘 Titre du devoir</label>
                                    <input type="text" name="assignment_title" class="adm-form-input" placeholder="Titre du devoir">
                                </div>
                                <div class="adm-form-group">
                                    <label class="adm-form-label"><i class="bi bi-calendar-check"></i> Date limite</label>
                                    <input type="date" name="assignment_due_date" class="adm-form-input">
                                </div>
                            </div>
                            <div class="adm-form-group">
                                <label class="adm-form-label">📝 Description</label>
                                <textarea name="assignment_description" class="adm-form-textarea" style="height:100px;" placeholder="Description du devoir"></textarea>
                            </div>
                            <div class="adm-form-file">
                                <label class="adm-form-label"><i class="bi bi-file-earmark-pdf"></i> Fichier PDF (optionnel)</label>
                                <input type="file" name="assignment_file" class="w-100" accept=".pdf">
                            </div>
                        </div>
                    </div>

                    <div class="adm-flex adm-flex-between adm-mt-3">
                        <a href="{{ route('prof.courses.index') }}" class="adm-btn adm-btn-ghost">
                            <i class="bi bi-arrow-left"></i> Retour
                        </a>
                        <button type="submit" class="adm-btn adm-btn-primary adm-btn-lg">
                            <i class="bi bi-rocket-takeoff"></i> Créer le cours
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
function previewVideo(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('videoPreview');
    if (file) {
        preview.querySelector('video').src = URL.createObjectURL(file);
        preview.style.display = 'block';
    }
}
function previewPDF(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('pdfPreview');
    if (file) {
        preview.querySelector('iframe').src = URL.createObjectURL(file);
        preview.style.display = 'block';
    }
}
</script>
@endsection
