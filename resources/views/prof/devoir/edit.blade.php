@extends('layouts.prof')

@section('content')

<div class="d-flex align-items-center gap-3 mb-5 pb-4 border-bottom">
  <div class="bg-gradient rounded-circle p-3 shadow-lg text-white d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
    <i class="fas fa-edit fs-2"></i>
  </div>
  <div>
    <h2 class="h3 mb-1 fw-bold text-dark lh-1">Modifier le devoir</h2>
    <span class="badge bg-primary bg-opacity-75 px-3 py-1 fw-semibold rounded-pill">Formulaire d'édition</span>
  </div>
</div>

<form method="POST" action="{{ route('prof.devoir.update', $devoir) }}" enctype="multipart/form-data">
@csrf
@method('PUT')

<div class="card shadow-xl border-0 p-5 rounded-4 bg-white backdrop-blur-sm" style="border: 1px solid rgba(59,130,246,.15); box-shadow: 0 20px 25px -5px rgba(0,0,0,.1), 0 10px 10px -5px rgba(0,0,0,.04);">

<div class="mb-4">
        <label class="form-label fw-semibold text-dark mb-3 fs-6">📝 Titre du devoir</label>
        <input type="text" name="title" value="{{ $devoir->title }}" class="form-control form-control-lg shadow-sm border-0 rounded-3 px-4 py-3" style="background: rgba(248,250,252,1); transition: all .3s;" placeholder="Entrez un titre descriptif...">
    </div>

<div class="mb-4">
        <label class="form-label fw-semibold text-dark mb-3 fs-6">📄 Description détaillée</label>
        <textarea name="description" rows="5" class="form-control form-control-lg shadow-sm border-0 rounded-3 px-4 py-3" style="background: rgba(248,250,252,1); transition: all .3s; font-size: 1rem; line-height: 1.6;" placeholder="Décrivez les instructions du devoir...">{{ $devoir->description }}</textarea>
    </div>

    <div class="mb-4">
        <label class="form-label fw-semibold text-dark mb-3 fs-6">🏫 Classe</label>
        <div class="input-group">
          <span class="input-group-text bg-white border-end-0 shadow-sm rounded-start-3" style="border-right: 1px solid rgba(0,0,0,.1)!important;">
            <i class="fas fa-users text-muted"></i>
          </span>
          <select name="class_room_id" class="form-select form-select-lg shadow-sm border-start-0 rounded-end-3 px-4 py-3 flex-grow-1" style="background: rgba(248,250,252,1); transition: all .3s; border-left: 1px solid rgba(0,0,0,.1)!important;">
            @foreach($classes as $class)
                <option value="{{ $class->id }}" {{ $class->id == $devoir->class_room_id ? 'selected' : '' }}>
                    {{ $class->name }}
                </option>
            @endforeach
          </select>
        </div>
    </div>

    <div class="mb-4">
        <label class="form-label fw-semibold text-dark mb-3 fs-6">⏰ Date limite</label>
        <div class="input-group">
          <span class="input-group-text bg-white border-end-0 shadow-sm rounded-start-3 pe-3" style="border-right: 1px solid rgba(0,0,0,.1)!important;">
            <i class="fas fa-calendar-alt text-primary"></i>
          </span>
          <input type="date" name="due_date" value="{{ $devoir->due_date }}" class="form-control form-control-lg shadow-sm border-start-0 rounded-end-3 px-4 py-3" style="background: rgba(248,250,252,1); transition: all .3s;" placeholder="Sélectionnez une date">
        </div>
    </div>

    <div class="mb-5">
        <label class="form-label fw-semibold text-dark mb-3 fs-6">📎 Nouveau fichier (optionnel)</label>
        <div class="input-group input-group-lg">
          <span class="input-group-text bg-white border-end-0 shadow-sm rounded-start-3" style="border-right: 1px solid rgba(0,0,0,.1)!important;">
            <i class="fas fa-cloud-upload-alt text-success"></i>
          </span>
          <input type="file" name="file" class="form-control form-control-lg shadow-sm border-start-0 rounded-end-3 px-4 py-3" style="background: rgba(248,250,252,1); transition: all .3s;" accept=".pdf,.doc,.docx,.zip">
          <span class="input-group-text bg-light border-start-0 rounded-end-3 shadow-sm">
            📄 PDF, DOC, ZIP
          </span>
        </div>
        <div class="form-text small mt-2">Le fichier sera remplacé par le nouveau. Taille max: 10Mo.</div>
    </div>

    <button type="submit" class="btn btn-lg w-100 py-4 fw-bold shadow-lg border-0 rounded-3 text-white mb-0 hover-scale" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); transition: all .3s ease; box-shadow: 0 10px 20px rgba(102,126,234,.4);">
      <i class="fas fa-save me-2"></i>💾 Modifier le devoir
    </button>

</div>

</form>

@endsection
