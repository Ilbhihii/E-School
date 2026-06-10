@extends('layouts.prof')

@section('content')
<div class="admin-page">
    <div class="admin-container">

        <!-- HEADER -->
        <div class="admin-header">
            <div>
                <h1 class="admin-header-title"><span class="gradient">📅 Planning Hebdomadaire</span></h1>
                <p class="admin-header-subtitle">Gérez votre emploi du temps</p>
            </div>
        </div>

        <!-- FILTER -->
        <div class="adm-card adm-mb-3">
            <div class="adm-card-body">
                <div class="adm-flex adm-gap-2" style="align-items:flex-end;">
                    <div class="adm-form-group" style="margin-bottom:0;flex:1;">
                        <label class="adm-form-label">👇 Filtrer par classe</label>
                        <select id="classFilter" class="adm-form-select">
                            <option value="">Toutes les classes</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- CALENDAR -->
        <div class="adm-card">
            <div class="adm-card-header" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);color:white;border-radius:var(--adm-radius-lg) var(--adm-radius-lg) 0 0;">
                <h3 style="color:white;">📅 Planning</h3>
            </div>
            <div class="adm-card-body">
                <div id="calendar"></div>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let calendarEl = document.getElementById('calendar');
    let classFilter = document.getElementById('classFilter');
    let calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        editable: true,
        selectable: true,
        events: function(fetchInfo, successCallback, failureCallback) {
            let params = new URLSearchParams();
            if (classFilter.value) {
                params.append('class_id', classFilter.value);
            }
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
        },
        eventDidMount: function(info) {
            let subject = info.event.extendedProps.subject;
            let colors = { 'Math': '#6366f1', 'Physique': '#10b981', 'Informatique': '#f59e0b', 'Français': '#ef4444' };
            if(colors[subject]) info.el.style.backgroundColor = colors[subject];
        }
    });

    classFilter.addEventListener('change', function() {
        calendar.refetchEvents();
    });
    calendar.render();
});
</script>
@endsection
