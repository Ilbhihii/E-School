@extends('layouts.admin')

@section('content')

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-indigo-50 py-10">

    <div class="max-w-7xl mx-auto space-y-10">

        <!-- TITLE -->
        <div>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                📅 Gestion du Planning
            </h1>
            <p class="text-gray-500 mt-2">Ajouter et gérer les séances du planning</p>
        </div>

        <!-- FORM -->
        <div class="bg-white/80 backdrop-blur-sm p-8 rounded-2xl shadow-xl border border-white/50">

            <h2 class="text-2xl font-bold mb-6 text-gray-800">➕ Ajouter une séance</h2>

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST"
                  action="{{ route('admin.schedule.store') }}"
                  class="grid grid-cols-1 md:grid-cols-2 gap-6"
                  onsubmit="return confirm('Confirmer l\\'ajout de cette séance ?')">

                @csrf

                <!-- CLASS -->
                <div>
                    <label class="text-sm font-semibold text-gray-700">Classe</label>
                    <select name="class_id"
                        class="w-full mt-2 p-3 border rounded-xl focus:ring-2 focus:ring-indigo-500">
                        <option value="">Choisir classe</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- PROF -->
                <div>
                    <label class="text-sm font-semibold text-gray-700">Professeur</label>
                    <select name="prof_id"
                        class="w-full mt-2 p-3 border rounded-xl focus:ring-2 focus:ring-indigo-500">
                        <option value="">Choisir professeur</option>
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- SUBJECT -->
                <div>
                    <label class="text-sm font-semibold text-gray-700">Matière</label>
                    <select name="subject"
                        class="w-full mt-2 p-3 border rounded-xl focus:ring-2 focus:ring-indigo-500">
                        <option value="">Choisir matière</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->name }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- START -->
                <div>
                    <label class="text-sm font-semibold text-gray-700">Début</label>
                    <input type="datetime-local" name="start_time"
                        class="w-full mt-2 p-3 border rounded-xl focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- END -->
                <div>
                    <label class="text-sm font-semibold text-gray-700">Fin</label>
                    <input type="datetime-local" name="end_time"
                        class="w-full mt-2 p-3 border rounded-xl focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- BUTTON -->
                <div class="md:col-span-2">
                    <button type="submit"
                        class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 rounded-xl font-bold shadow-lg hover:scale-105 transition">
                        ➕ Ajouter au planning
                    </button>
                </div>

            </form>
        </div>

        <!-- TABLE -->
        <div class="bg-white/80 backdrop-blur-sm p-8 rounded-2xl shadow-xl border border-white/50">

            <h2 class="text-2xl font-bold mb-6 text-gray-800">📋 Planning complet</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">

                    <thead>
                        <tr class="bg-gray-100 text-gray-700">
                            <th class="p-4 text-left">Classe</th>
                            <th class="p-4 text-left">Prof</th>
                            <th class="p-4 text-left">Matière</th>
                            <th class="p-4 text-left">Date</th>
                            <th class="p-4 text-left">Heure</th>
                            <th class="p-4 text-left">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($schedules as $s)
                        <tr class="border-b hover:bg-gray-50 transition">

                            <td class="p-4 font-medium">
                                {{ $s->classRoom->name ?? 'N/A' }}
                            </td>

                            <td class="p-4">
                                {{ $s->prof->name ?? 'N/A' }}
                            </td>

                            <td class="p-4">
                                <span class="px-3 py-1 rounded-full text-xs text-white bg-indigo-500">
                                    {{ $s->subject }}
                                </span>
                            </td>

                            <td class="p-4">
                                {{ $s->date?->format('d/m/Y') ?? '-' }}
                            </td>

                            <td class="p-4">
                                {{ $s->start_time?->format('H:i') ?? '-' }} → {{ $s->end_time?->format('H:i') ?? '-' }}
                            </td>

                            <td class="p-4">
                                <form method="POST" action="{{ route('admin.schedule.destroy', $s->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="bg-red-500 text-white px-4 py-1 rounded-lg hover:bg-red-600">
                                        🗑️
                                    </button>
                                </form>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center p-8 text-gray-500">
                                Aucun planning
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>

    </div>
</div>

@endsection
