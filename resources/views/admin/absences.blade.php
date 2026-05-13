@extends('layouts.admin')

@section('content')

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-indigo-50 py-10">

    <div class="max-w-7xl mx-auto space-y-8">

        <!-- HEADER -->
        <div>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                📋 Gestion des absences
            </h1>
            <p class="text-gray-500 mt-2">Suivi des présences et absences des étudiants</p>
        </div>

        <!-- FILTER -->
        <div class="bg-white/80 backdrop-blur-sm p-6 rounded-2xl shadow-xl border border-white/50">

            <form method="GET" class="flex flex-col md:flex-row gap-4">

                <select name="class_id"
                    class="w-full md:w-1/3 p-3 border rounded-xl focus:ring-2 focus:ring-indigo-500">

                    <option value="">Toutes les classes</option>

                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach

                </select>

                <button class="px-6 py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition">
                    🔎 Filtrer
                </button>

            </form>

        </div>

        <!-- TABLE -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/50 overflow-hidden">

            <!-- HEADER TABLE -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6 text-white">
                <h2 class="text-2xl font-bold">📊 Liste des absences</h2>
            </div>

            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                        <tr>
                            <th class="p-4 text-left">Étudiant</th>
                            <th class="p-4 text-left">Date</th>
                            <th class="p-4 text-left">Classe</th>
                            <th class="p-4 text-center">Statut</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($absences as $absence)
                        <tr class="border-b hover:bg-gray-50 transition">

                            <!-- STUDENT -->
                            <td class="p-4 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold">
                                    {{ strtoupper(substr($absence->user->name ?? 'E', 0, 1)) }}
                                </div>
                                <span class="font-medium text-gray-800">
                                    {{ $absence->user->name ?? 'Inconnu' }}
                                </span>
                            </td>

                            <!-- DATE -->
                            <td class="p-4 text-gray-600">
                                {{ \Carbon\Carbon::parse($absence->date)->format('d/m/Y') }}
                            </td>

                            <!-- CLASS -->
                            <td class="p-4">
                                <span class="px-3 py-1 rounded-full bg-indigo-100 text-indigo-700 font-semibold text-xs">
                                    {{ $absence->user->classRoom->name ?? '-' }}
                                </span>
                            </td>

                            <!-- STATUS -->
                            <td class="p-4 text-center">
                                @if($absence->present)
                                    <span class="px-4 py-2 rounded-full bg-green-100 text-green-700 font-bold">
                                        ✅ Présent
                                    </span>
                                @else
                                    <span class="px-4 py-2 rounded-full bg-red-100 text-red-700 font-bold">
                                        ❌ Absent
                                    </span>
                                @endif
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center p-10 text-gray-500">
                                Aucune absence trouvée
                            </td>
                        </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        <!-- PAGINATION -->
        <div class="flex justify-center">
            {{ $absences->appends(request()->query())->links() }}
        </div>

    </div>
</div>

@endsection
