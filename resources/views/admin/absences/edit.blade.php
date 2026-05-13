@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-indigo-50 to-purple-50 py-10 px-4">

    <div class="max-w-3xl mx-auto">

        <!-- HEADER -->
        <div class="mb-8 text-center">
            <h1 class="text-4xl font-extrabold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                ✏️ Modifier l'absence
            </h1>
            <p class="text-gray-500 mt-2">Mettre à jour les informations de présence</p>
        </div>

        <!-- CARD -->
        <div class="bg-white/80 backdrop-blur-xl shadow-2xl rounded-3xl border border-white/40 p-8">

            <form method="POST" action="{{ route('admin.absences.update', $absence->id) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- DATE -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        📅 Date
                    </label>
                    <input type="date"
                           name="date"
                           value="{{ $absence->date }}"
                           class="w-full px-5 py-3 rounded-2xl border border-gray-200 focus:ring-4 focus:ring-indigo-200 focus:border-indigo-500 outline-none transition"
                           required>

                    @error('date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- STATUS -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        👤 Statut de présence
                    </label>

                    <select name="present"
                            class="w-full px-5 py-3 rounded-2xl border border-gray-200 focus:ring-4 focus:ring-indigo-200 focus:border-indigo-500 outline-none transition">

                        <option value="1" {{ $absence->present ? 'selected' : '' }}>
                            ✅ Présent
                        </option>

                        <option value="0" {{ !$absence->present ? 'selected' : '' }}>
                            ❌ Absent
                        </option>

                    </select>

                    @error('present')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- INFO BOX -->
                <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-5">
                    <p class="text-sm text-indigo-700">
                        ℹ️ Vérifie bien la date et le statut avant d’enregistrer la modification.
                    </p>
                </div>

                <!-- BUTTONS -->
                <div class="flex gap-4 pt-4">

                    <a href="{{ route('admin.absences.show', $absence->id) }}"
                       class="flex-1 text-center px-6 py-3 rounded-2xl bg-gray-200 hover:bg-gray-300 font-semibold transition">
                        ❌ Annuler
                    </a>

                    <button type="submit"
                            class="flex-1 px-6 py-3 rounded-2xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold shadow-lg hover:shadow-xl hover:-translate-y-1 transition">
                        💾 Enregistrer
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>
@endsection
