@extends('layouts.prof')

@section('content')

<div class="mb-6 p-4 bg-white rounded-xl shadow">
    <label class="block text-sm font-medium text-gray-700 mb-2">👇 Filtrer par classe</label>
    <select id="classFilter" class="w-full md:w-64 p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <option value="">Toutes les classes</option>

        @foreach($classes as $class)
            <option value="{{ $class->id }}">{{ $class->name }}</option>
        @endforeach
    </select>
</div>

<div class="card shadow-lg border-0">
    <div class="card-header bg-primary text-white">
        <h4>📅 Planning Hebdomadaire</h4>
    </div>

    <div class="card-body">
        <div id="calendar"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    let calendarEl = document.getElementById('calendar');
    let classFilter = document.getElementById('classFilter');
    let calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        editable: true, // 🔥 drag & drop activé
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
            // update en base de données
            fetch('/prof/schedule/update', {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    id: info.event.id,
                    start: info.event.start,
                    end: info.event.end
                })
            });
        },

        eventDidMount: function(info) {
            let subject = info.event.extendedProps.subject;

            let colors = {
                'Math': '#6366f1',
                'Physique': '#10b981',
                'Informatique': '#f59e0b',
                'Français': '#ef4444'
            };

            if(colors[subject]) {
                info.el.style.backgroundColor = colors[subject];
            }
        }

    });



    // Filter change handler
    classFilter.addEventListener('change', function() {
        calendar.refetchEvents();
    });

    calendar.render();
});
</script>

@endsection
