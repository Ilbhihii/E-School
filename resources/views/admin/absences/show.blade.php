@extends('layouts.admin')

@section('content')

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-indigo-50 py-10">

    <div class="max-w-4xl mx-auto">

        <!-- HEADER -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                📌 Détails de l’absence
            </h1>
            <p class="text-gray-500 mt-2">Informations complètes sur l’absence</p>
        </div>

        <!-- CARD -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl border border-white/50 p-8">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                <!-- LEFT -->
                <div class="space-y-6">

                    <div>
                        <p class="text-sm text-gray-500">Étudiant</p>
                        <p class="text-lg font-semibold text-gray-800">
                            {{ $absence->user->name }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Classe</p>
                        <p class="text-lg font-semibold text-gray-800">
                            {{ $absence->user->classRoom->name ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Date</p>
                        <p class="text-lg font-semibold text-gray-800">
                            {{ \Carbon\Carbon::parse($absence->date)->format('d/m/Y') }}
                        </p>
                    </div>

                </div>

                <!-- RIGHT -->
                <div class="flex items-start">

                    <div>
                        <p class="text-sm text-gray-500 mb-2">Statut</p>

                        @if($absence->present)
                            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-green-100 text-green-700 font-semibold">
                                ✅ Présent
                            </span>
                        @else
                            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-red-100 text-red-700 font-semibold">
                                ❌ Absent
                            </span>
                        @endif
                    </div>

                </div>

            </div>

            <!-- BUTTONS -->
            <div class="flex gap-4 mt-10">

                <a href="{{ route('admin.absences') }}"
                   class="px-6 py-3 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold transition">
                    ← Retour
                </a>

                <a href="{{ route('admin.absences.edit', $absence->id) }}"
                   class="px-6 py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold shadow-lg hover:scale-105 transition">
                    ✏️ Modifier
                </a>

            </div>

        </div>

    </div>
</div>

@endsection
