@extends('layouts.admin')

@section('title', 'Créer un live')
@section('page_title', 'Nouveau live')
@section('breadcrumb', 'Créer un live')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">

        <!-- OUTLOOK FIRST -->
        <div class="adm-card" style="border-left:4px solid #38BDF8;margin-bottom:1.5rem;">
            <div class="adm-card-header" style="background:linear-gradient(135deg, rgba(2,132,199,0.08), rgba(14,165,233,0.03));">
                <h4><i class="bi bi-calendar-plus" style="color:#38BDF8;"></i> Créer via Outlook</h4>
                <span style="color:#64748B;font-size:0.75rem;">Recommandé</span>
            </div>
            <div class="adm-card-body">
                <div style="display:flex;align-items:flex-start;gap:1.5rem;flex-wrap:wrap;">
                    <div style="flex:1;min-width:240px;">
                        <p style="color:#94A3B8;font-size:0.85rem;margin-bottom:1rem;">
                            Remplissez les informations ci-dessous, puis cliquez sur 
                            <strong style="color:#F1F5F9;">Créer dans Outlook</strong> pour ajouter l'événement 
                            à votre calendrier Outlook. Ensuite, validez dans Laravel.
                        </p>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="adm-form-group" style="margin-bottom:0;">
                                    <label class="adm-form-label" style="font-size:0.75rem;">Titre</label>
                                    <input type="text" id="outlook_title" class="adm-form-control" placeholder="Titre du live" style="font-size:0.85rem;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="adm-form-group" style="margin-bottom:0;">
                                    <label class="adm-form-label" style="font-size:0.75rem;">Classe</label>
                                    <select id="outlook_class_id" class="adm-form-select" style="font-size:0.85rem;">
                                        <option value="">Sélectionner...</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-4">
                                <div class="adm-form-group" style="margin-bottom:0;">
                                    <label class="adm-form-label" style="font-size:0.75rem;">Date</label>
                                    <input type="date" id="outlook_date" class="adm-form-control" style="font-size:0.85rem;">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="adm-form-group" style="margin-bottom:0;">
                                    <label class="adm-form-label" style="font-size:0.75rem;">Début</label>
                                    <input type="time" id="outlook_start" class="adm-form-control" style="font-size:0.85rem;">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="adm-form-group" style="margin-bottom:0;">
                                    <label class="adm-form-label" style="font-size:0.75rem;">Fin</label>
                                    <input type="time" id="outlook_end" class="adm-form-control" style="font-size:0.85rem;">
                                </div>
                            </div>
                        </div>

                        <div class="adm-form-group mt-2" style="margin-bottom:0;">
                            <label class="adm-form-label" style="font-size:0.75rem;">
                                Lien du live 
                                <span id="linkStatus" style="color:#64748B;font-size:0.7rem;font-weight:400;">(généré automatiquement)</span>
                            </label>
                            <div style="display:flex;gap:6px;">
                                <input type="url" id="outlook_url" class="adm-form-control" placeholder="https://meet.google.com/xxx-xxxx-xxx" style="font-size:0.85rem;flex:1;">
                                <button type="button" id="regenerateBtn" style="padding:8px 14px;border-radius:8px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.06);color:#94A3B8;cursor:pointer;font-size:0.75rem;display:none;" onclick="generateRoomId()" onmouseover="this.style.background='rgba(255,255,255,0.08)'" onmouseout="this.style.background='rgba(255,255,255,0.04)'">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div style="flex-shrink:0;display:flex;flex-direction:column;align-items:center;gap:0.75rem;padding:2rem 1.5rem;border-left:1px solid rgba(255,255,255,0.06);min-width:200px;">
                        <div style="width:72px;height:72px;border-radius:18px;background:linear-gradient(135deg,#0284C7,#0EA5E9);display:flex;align-items:center;justify-content:center;font-size:2rem;color:white;box-shadow:0 8px 24px rgba(2,132,199,0.3);">
                            <i class="bi bi-calendar-plus-fill"></i>
                        </div>
                        <a href="#" id="outlookMainBtn" target="_blank"
                           style="display:inline-flex;align-items:center;gap:8px;padding:12px 28px;border-radius:12px;background:linear-gradient(135deg,#0284C7,#0EA5E9);color:white;font-weight:700;font-size:0.9rem;text-decoration:none;transition:all 0.2s;pointer-events:none;opacity:0.4;white-space:nowrap;box-shadow:0 4px 16px rgba(2,132,199,0.2);"
                           onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 24px rgba(2,132,199,0.35)'"
                           onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 16px rgba(2,132,199,0.2)'">
                            <i class="bi bi-plus-circle" style="font-size:1.1rem;"></i>
                            Créer dans Outlook
                        </a>
                        <span id="outlookStatus" style="font-size:0.7rem;color:#64748B;text-align:center;">Remplissez les champs pour activer</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- MANUAL FORM (SECONDARY) -->
        <details style="margin-bottom:1.5rem;">
            <summary style="cursor:pointer;padding:0.75rem 1rem;border-radius:8px;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);color:#64748B;font-size:0.82rem;font-weight:500;transition:all 0.2s;"
                     onmouseover="this.style.background='rgba(255,255,255,0.04)'" onmouseout="this.style.background='rgba(255,255,255,0.02)'">
                <i class="bi bi-chevron-down me-2"></i> Création manuelle (optionnel)
            </summary>
            <div class="adm-card mt-3">
                <div class="adm-card-header">
                    <h4><i class="bi bi-pencil-square" style="color:rgba(255,255,255,0.35);"></i> Formulaire manuel</h4>
                </div>
                <div class="adm-card-body">
                    @if(session('success'))
                    <div class="adm-alert adm-alert-success mb-4">
                        <span class="adm-alert-icon"><i class="bi bi-check-circle-fill"></i></span>
                        <span>{{ session('success') }}</span>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('admin.lives.store') }}" id="manualForm">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="adm-form-group">
                                    <label class="adm-form-label">Titre du live</label>
                                    <input type="text" name="title" value="{{ old('title') }}" class="adm-form-control @error('title') error @enderror" placeholder="Ex: Révision Math" required>
                                    @error('title') <div class="adm-form-error">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="adm-form-group">
                                    <label class="adm-form-label">Classe</label>
                                    <select name="class_id" class="adm-form-select @error('class_id') error @enderror" required>
                                        <option value="">Choisir une classe</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('class_id') <div class="adm-form-error">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="adm-form-group">
                            <label class="adm-form-label">Lien (YouTube / Zoom) <span style="color:#64748B;font-size:0.7rem;font-weight:400;">(optionnel — vous pourrez le modifier plus tard)</span></label>
                            <input type="url" name="stream_url" value="{{ old('stream_url') }}" class="adm-form-control @error('stream_url') error @enderror" placeholder="https://...">
                            @error('stream_url') <div class="adm-form-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="adm-form-group">
                            <label class="adm-form-label">Date & heure</label>
                            <div class="row g-3 mt-1">
                                <div class="col-md-4">
                                    <input type="date" name="live_date" class="adm-form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <input type="time" name="start_time" class="adm-form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <input type="time" name="end_time" class="adm-form-control" required>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="adm-btn adm-btn-ghost w-100 mt-3" style="border:1px solid rgba(255,255,255,0.08);">
                            <i class="bi bi-save me-2"></i> Enregistrer dans Laravel
                        </button>
                    </form>
                </div>
            </div>
        </details>

    </div>
</div>

<script>
(function() {
    // Générer un ID de salle unique
    function generateRoomId() {
        let chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let room = '';
        for (let i = 0; i < 8; i++) {
            room += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        let link = 'https://meet.smartschoolacademy.com/room/' + room;
        document.getElementById('outlook_url').value = link;
        document.getElementById('regenerateBtn').style.display = 'block';
        document.getElementById('linkStatus').textContent = '(lien généré) ';
        syncFields();
    }

    window.generateRoomId = generateRoomId;

    // Sync Outlook fields to manual form
    function syncFields() {
        let title = document.getElementById('outlook_title')?.value || '';
        let classId = document.getElementById('outlook_class_id')?.value || '';
        let date = document.getElementById('outlook_date')?.value || '';
        let start = document.getElementById('outlook_start')?.value || '';
        let end = document.getElementById('outlook_end')?.value || '';
        let url = document.getElementById('outlook_url')?.value || '';

        let mf = document.getElementById('manualForm');
        if (mf) {
            mf.querySelector('[name="title"]').value = title;
            mf.querySelector('[name="class_id"]').value = classId;
            mf.querySelector('[name="live_date"]').value = date;
            mf.querySelector('[name="start_time"]').value = start;
            mf.querySelector('[name="end_time"]').value = end;
            mf.querySelector('[name="stream_url"]').value = url;
        }

        generateOutlookUrl();
    }

    function generateOutlookUrl() {
        let title = document.getElementById('outlook_title')?.value || 'Live';
        let liveDate = document.getElementById('outlook_date')?.value || '';
        let startTime = document.getElementById('outlook_start')?.value || '00:00';
        let endTime = document.getElementById('outlook_end')?.value || '';
        let streamUrl = document.getElementById('outlook_url')?.value || '';
        let classSelect = document.getElementById('outlook_class_id');
        let className = classSelect?.options[classSelect.selectedIndex]?.text || '';

        let hasMin = title && liveDate && startTime && classSelect?.value;
        let btn = document.getElementById('outlookMainBtn');
        let status = document.getElementById('outlookStatus');
        if (!btn) return;

        if (!hasMin) {
            btn.style.pointerEvents = 'none';
            btn.style.opacity = '0.4';
            if (status) status.textContent = 'Remplissez titre, classe, date et heure';
            return;
        }

        // Auto-générer le lien si pas encore fait
        let urlField = document.getElementById('outlook_url');
        if (!urlField.value) {
            generateRoomId();
            streamUrl = urlField.value;
        }

        if (!endTime) {
            let [h, m] = startTime.split(':').map(Number);
            h = (h + 1) % 24;
            endTime = String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0');
        }

        let startDt = liveDate.replace(/-/g, '') + 'T' + startTime.replace(/:/g, '');
        let endDt = liveDate.replace(/-/g, '') + 'T' + endTime.replace(/:/g, '');
        let body = 'Live : ' + title + '\\nClasse : ' + className + '\\n\\nLien : ' + streamUrl;

        let url = 'https://outlook.live.com/calendar/0/deeplink/compose?path=/calendar/action/compose&rru=addevent';
        url += '&subject=' + encodeURIComponent(title);
        url += '&startdt=' + startDt;
        url += '&enddt=' + endDt;
        url += '&body=' + encodeURIComponent(body);
        if (streamUrl) url += '&location=' + encodeURIComponent(streamUrl);

        btn.href = url;
        btn.style.pointerEvents = 'auto';
        btn.style.opacity = '1';
        if (status) status.innerHTML = '<span style="color:#34D399;"><i class="bi bi-check-circle me-1"></i> Prêt ! Cliquez pour ouvrir Outlook</span>';
    }

    document.addEventListener('DOMContentLoaded', function() {
        let fields = ['outlook_title', 'outlook_class_id', 'outlook_date', 'outlook_start', 'outlook_end'];
        fields.forEach(id => {
            let el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', syncFields);
                if (el.tagName === 'SELECT') el.addEventListener('change', syncFields);
            }
        });
        generateOutlookUrl();
    });
})();
</script>

@endsection