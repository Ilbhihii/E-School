@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 py-10">

    <div class="max-w-7xl mx-auto px-4">

        <!-- HEADER -->
        <div class="mb-10">
            <h1 class="text-4xl font-extrabold bg-gradient-to-r from-red-600 to-pink-600 bg-clip-text text-transparent flex items-center gap-3">
                📡 Gestion des Lives
            </h1>
            <p class="text-gray-500 mt-2">Tableau de gestion des lives, planning et statistiques</p>
        </div>

        <!-- STATS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">

            <div class="bg-white rounded-2xl shadow-md p-6 hover:shadow-xl transition">
                <p class="text-gray-500">Total Lives</p>
                <h2 class="text-3xl font-bold text-red-600 mt-2">{{ $totalLives }}</h2>
            </div>

            <div class="bg-white rounded-2xl shadow-md p-6 hover:shadow-xl transition">
                <p class="text-gray-500">Lives récents</p>
                <h2 class="text-3xl font-bold text-orange-500 mt-2">{{ $recentLives->count() }}</h2>
            </div>

            <div class="bg-white rounded-2xl shadow-md p-6 flex items-center justify-between hover:shadow-xl transition">
                <div>
                    <p class="text-gray-500">Actions</p>
                    <h2 class="text-lg font-bold mt-1">Créer un live</h2>
                </div>

                <a href="{{ route('admin.lives.create') }}"
                   class="bg-red-600 text-white px-5 py-3 rounded-xl font-semibold hover:bg-red-700 transition">
                    + Nouveau
                </a>
            </div>

        </div>

        <!-- RECENTS -->
        <div class="bg-white rounded-2xl shadow-md p-6 mb-10">

            <h3 class="text-xl font-bold mb-5">Lives récents</h3>

            <div class="space-y-3">
                @forelse($recentLives as $live)
                    <div class="flex justify-between items-center bg-gray-50 p-4 rounded-xl hover:bg-gray-100">
                        <span class="font-medium text-gray-800">{{ $live->title }}</span>
                        <span class="text-sm text-gray-500">{{ $live->created_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <p class="text-gray-500">Aucun live récent</p>
                @endforelse
            </div>

        </div>

        <!-- TABLE -->
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">

            <div class="p-6 border-b">
                <h3 class="text-xl font-bold">Tous les Lives</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">

                    <thead class="bg-gray-100 text-left text-sm">
                        <tr>
                            <th class="p-4">Titre</th>
                            <th class="p-4">Classe</th>
                            <th class="p-4">Lien</th>
                            <th class="p-4">Date</th>
                            <th class="p-4">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($lives as $live)
                        <tr class="border-t hover:bg-gray-50">

                            <td class="p-4 font-medium">
                                {{ $live->title }}
                            </td>

                            <td class="p-4">
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm">
                                    {{ $live->classRoom->name ?? '-' }}
                                </span>
                            </td>

                            <td class="p-4">
                                <a href="{{ $live->stream_url }}" target="_blank"
                                   class="text-blue-600 hover:underline">
                                    Ouvrir
                                </a>
                            </td>

                            <td class="p-4 text-gray-600">
                                {{ $live->created_at->format('d/m/Y') }}
                            </td>

                            <td class="p-4 flex gap-3">

                                <a href="{{ route('admin.lives.edit', $live) }}"
                                   class="text-yellow-600 hover:underline">
                                    Modifier
                                </a>

                                <form method="POST"
                                      action="{{ route('admin.lives.destroy',$live) }}"
                                      onsubmit="return confirm('Supprimer ?')">
                                    @csrf
                                    @method('DELETE')

                                    <button class="text-red-600 hover:underline">
                                        Supprimer
                                    </button>
                                </form>

                            </td>

                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

        </div>

        <!-- CALENDAR -->
        <div class="bg-white rounded-2xl shadow-md p-6 mt-10">
            <h3 class="text-xl font-bold mb-5">Calendrier des lives</h3>
            <div id="calendar"></div>
        </div>

    </div>
</div>

<!-- FULLCALENDAR -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek'
        },

        events: [
            @foreach($lives as $live)
            {
                title: "{{ $live->title }}",
                start: "{{ $live->live_date }}T{{ $live->start_time }}",
                end: "{{ $live->live_date }}T{{ $live->end_time }}"
            },
            @endforeach
        ]
    });

    calendar.render();
});
</script>

@endsection
