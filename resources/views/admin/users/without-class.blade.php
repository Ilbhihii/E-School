@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-indigo-50 p-6">
    <!-- Header -->
    <div class="bg-white/80 backdrop-blur-md rounded-3xl shadow-xl p-8 mb-8 border border-white/50">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-black bg-gradient-to-r from-red-600 to-rose-600 bg-clip-text text-transparent mb-3">
                    🚨 Étudiants sans Classe ({{ $count }})
                </h1>
                <p class="text-xl text-gray-600 leading-relaxed">
                    Ces étudiants n'ont pas de <code class="bg-red-100 text-red-800 px-2 py-1 rounded font-mono text-sm">class_id</code> assignée.
                    Ils ne peuvent pas accéder aux lives et certains cours.
                </p>
            </div>
            <a href="{{ route('admin.users') }}" class="bg-gradient-to-r from-gray-500 to-gray-700 hover:from-gray-600 hover:to-gray-800 text-white px-8 py-4 rounded-2xl font-bold text-lg shadow-xl hover:shadow-2xl transition-all duration-300 flex items-center gap-3">
                ← Retour aux utilisateurs
            </a>
        </div>
    </div>

    @if($count === 0)
        <div class="bg-emerald-50 border-2 border-emerald-200 rounded-3xl p-12 text-center">
            <div class="w-24 h-24 bg-emerald-100 rounded-3xl mx-auto mb-6 flex items-center justify-center">
                <svg class="w-16 h-16 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-emerald-800 mb-4">🎉 Tous les étudiants ont une classe !</h2>
            <p class="text-lg text-emerald-700">Aucun étudiant n'a de <code class="font-mono bg-emerald-200 px-2 py-1 rounded">class_id NULL</code></p>
        </div>
    @else
        <!-- Students Table -->
        <div class="bg-white/80 backdrop-blur-md rounded-3xl shadow-2xl border border-white/50 overflow-hidden">
            <div class="bg-gradient-to-r from-red-500/10 to-rose-500/10 px-8 py-6 border-b border-red-200/50">
                <h3 class="text-2xl font-black bg-gradient-to-r from-red-600 to-rose-600 bg-clip-text text-transparent">
                    Assigner des classes
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-gray-50 to-white/50">
                        <tr>
                            <th class="px-8 py-6 text-left font-black text-gray-900 text-lg">ID</th>
                            <th class="px-8 py-6 text-left font-black text-gray-900 text-lg">Nom</th>
                            <th class="px-8 py-6 text-left font-black text-gray-900 text-lg">Email</th>
                            <th class="px-8 py-6 text-left font-black text-gray-900 text-lg w-80">Classe à assigner</th>
                            <th class="px-8 py-6 text-center font-black text-gray-900 text-lg">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200/50">
                        @foreach($students as $student)
                        <tr class="hover:bg-gradient-to-r hover:from-red-50 hover:to-rose-50 transition-all duration-300 group">
                            <td class="px-8 py-6 font-mono font-bold text-xl text-gray-900">{{ $student->id }}</td>
                            <td class="px-8 py-6">
                                <div class="font-bold text-xl text-gray-900 group-hover:text-red-700">{{ $student->name }}</div>
                                <div class="text-sm text-gray-500 mt-1">Inscrit: {{ $student->created_at->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-8 py-6 text-gray-800 max-w-md truncate">{{ $student->email }}</td>
                            <td class="px-8 py-6">
                                <form method="POST" action="{{ route('admin.users.update', $student->id) }}" class="inline">
                                    @csrf
                                    @method('PUT')

                                    <select name="class_id" class="w-72 px-4 py-3 bg-white border-2 border-gray-200 rounded-2xl">
                                        <option value="">❌ Aucune classe</option>
                                        @foreach($classRooms as $classRoom)
                                            <option value="{{ $classRoom->id }}">{{ $classRoom->name }}</option>
                                        @endforeach
                                    </select>

                                    <button type="submit" class="ml-3 bg-green-500 text-white px-6 py-2 rounded">
                                        ✅ Assigner
                                    </button>
                                </form>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <div class="bg-gradient-to-r from-yellow-100 to-orange-100 px-6 py-3 rounded-2xl font-bold text-yellow-800 border-2 border-yellow-300/50 inline-flex items-center gap-2">
                                    <span>🚨 URGENT</span>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bulk Action -->
        @if($count > 1)
        <div class="mt-8 bg-gradient-to-r from-blue-500/10 to-indigo-500/10 backdrop-blur-md p-8 rounded-3xl border border-blue-200/50">
            <h4 class="text-xl font-bold text-blue-900 mb-4 flex items-center gap-3">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                </svg>
                Actions en masse (à venir)
            </h4>
            <p class="text-blue-800 text-lg">Sélection multiple et assignation groupée disponible prochainement.</p>
        </div>
        @endif
    @endif
</div>
@endsection

