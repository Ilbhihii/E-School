@extends('layouts.prof')

@section('title', 'Planning Hebdomadaire')
@section('page_title', 'Planning')
@section('breadcrumb', 'Gestion du planning')

@section('content')

<div class="adm-page-header">
    <div>
        <h1><i class="bi bi-calendar-week me-2" style="color:var(--adm-primary);"></i> Planning Hebdomadaire</h1>
        <div class="subtitle">Gérez votre emploi du temps</div>
    </div>
</div>

<div class="adm-card">
    <div class="adm-card-body">
        <div class="adm-form-group" style="margin-bottom:0;">
            <label class="adm-form-label"><i class="bi bi-funnel me-1"></i> Filtrer par classe</label>
            <select id="classFilter" class="adm-form-select" style="max-width:300px;">
                <option value="">Toutes les classes</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="adm-card">
    <div class="adm-card-body">
        <div id="calendar"></div>
    </div>
</div>

<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>

<style>
#calendar { min-height: 600px; }
.fc { color: rgba(255,255,255,0.85); font-family: 'Inter', sans-serif; }
.fc-toolbar-title { font-family: 'Poppins', sans-serif; font-weight: 700 !important; font-size: 1.2rem !important; color: rgba(255,255,255,0.9) !important; }
.fc .fc-button-primary {
    background: rgba(255,255,255,0.06) !important;
    border: 1px solid rgba(255,255,255,0.08) !important;
    color: rgba(255,255,255,0.8) !important;
    border-radius: 8px !important;
    font-weight: 500 !important;
    transition: all 0.2s ease;
}
.fc .fc-button-primary:hover { background: rgba(255,255,255,0.1) !important; }
.fc .fc-button-primary:not(:disabled).fc-button-active { background: linear-gradient(135deg,#6366F1,#8B5CF6) !important; border-color: transparent !important; color: white !important; }
.fc-daygrid-day { background: rgba(255,255,255,0.02); transition: background 0.2s; }
.fc-daygrid-day:hover { background: rgba(255,255,255,0.05); }
.fc .fc-day-other { background: rgba(255,255,255,0.01); }
.fc-col-header-cell { background: rgba(255,255,255,0.04); }
.fc-theme-standard td, .fc-theme-standard th { border-color: rgba(255,255,255,0.06); }
.fc .fc-daygrid-day-number { color: rgba(255,255,255,0.7); padding: 6px 8px; font-size: 0.85rem; }
.fc .fc-col-header-cell-cushion { color: rgba(255,255,255,0.6); font-weight: 600; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 10px 4px; }
.fc .fc-scrollgrid { border: 1px solid rgba(255,255,255,0.06); border-radius: 10px; overflow: hidden; }
.fc .fc-today-button { font-weight: 600 !important; }
.fc .fc-timegrid-slot { height: 40px; }
.fc .fc-timegrid-now-indicator-line { border-color: #EF4444; }
.fc .fc-timegrid-now-indicator-arrow { border-color: #EF4444; }
@media (max-width: 768px) { .fc-toolbar { flex-direction: column; gap: 0.75rem; } }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let calendarEl = document.getElementById('calendar');
    let classFilter = document.getElementById('classFilter');

    let calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        editable: true,
        selectable: true,
        locale: 'fr',
        firstDay: 1,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: { today: "Aujourd'hui", month: 'Mois', week: 'Semaine', day: 'Jour' },
        events: function(fetchInfo, successCallback, failureCallback) {
            let params = new URLSearchParams();
            if (classFilter.value) params.append('class_id', classFilter.value);
            fetch('/prof/schedule/data?' + params.toString())
                .then(response => response.json())
                .then(data => successCallback(data))
                .catch(error => failureCallback(error));
        },
        eventDrop: function(info) {
            fetch('/prof/schedule/update', {
                method: "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                body: JSON.stringify({ id: info.event.id, start: info.event.start, end: info.event.end })
            });
        }
    });

    classFilter.addEventListener('change', function() { calendar.refetchEvents(); });
    calendar.render();
});
</script>

@endsection
