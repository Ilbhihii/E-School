@extends('layouts.admin')

@section('content')
<div class="p-8 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-8 drop-shadow-lg">📚 Gestion des Devoirs</h1>
        
        <!-- Filter if course -->
        @if($course)
        <div class="bg-white/80 backdrop-blur-sm p-6 rounded-2xl shadow-xl mb-8 border border-white/50">
            <h3 class="text-2xl font-bold text-gray-800 mb-4">Devoirs du cours: {{ $course->title }}</h3>
            <a href="{{ route('admin.devoirs.index') }}" class="text-indigo-600 hover:text-indigo-800 font-bold">← Tous les devoirs</a>
        </div>
        @endif

        <!-- Add button -->
        <div class="mb-8">
            <a href="{{ route('prof.devoir.create', ['course_id' => $course_id ?? '']) }}" class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-8 py-4 rounded-xl font-bold shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 inline-flex items-center">
                ➕ Nouveau Devoir (Prof)
            </a>
        </div>

        <!-- Table -->
        <div class="bg-white/80 backdrop-blur-sm p-8 rounded-2xl shadow-xl border border-white/50 hover:shadow-2xl transition-all duration-300">
            <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                <svg class="w-8 h-8 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Liste des Devoirs
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full table-auto divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100 sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Titre</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Classe</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Date Limite</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Fichier</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Prof</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($devoirs as $devoir)
                        <tr class="hover:bg-gray-50 transition-colors group">
                            <td class="px-6 py-5 font-medium text-gray-900">{{ Str::limit($devoir->title, 50) }}</td>
                            <td class="px-6 py-5 text-sm text-gray-700">{{ $devoir->classRoom->name ?? '---' }}</td>
                            <td class="px-6 py-5 text-sm text-gray-700 font-medium">
                                <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $devoir->due_date < now()->format('Y-m-d') ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                    {{ \Carbon\Carbon::parse($devoir->due_date)->format('d/m/Y') }}
                                </span>
                            </td>
                            <td class="px-6 py-5 text-sm">
                                @if($devoir->file)
                                    <a href="{{ Storage::url($devoir->file) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 font-medium">
                                        📎 Fichier
                                    </a>
                                @else
                                    <span class="text-gray-400">Aucun</span>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-sm text-gray-700">{{ $devoir->user->name ?? 'Admin' }}</td>
                            <td class="px-6 py-5 text-sm font-medium space-x-2">
                                <a href="{{ route('admin.devoirs.edit', $devoir) }}" class="inline-flex items-center px-4 py-2 bg-yellow-100 text-yellow-800 rounded-lg hover:bg-yellow-200 transition-all duration-200 group-hover:scale-105">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Modifier
                                </a>
                                <form method="POST" action="{{ route('admin.devoirs.destroy', $devoir) }}" class="inline" onsubmit="return confirm('Confirmer ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-100 text-red-800 rounded-lg hover:bg-red-200 transition-all duration-200 group-hover:scale-105">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m7-10V4a1 1 0 00-1-1h-4M4 4h4"></path>
                                        </svg>
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Aucun devoir pour ce cours.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-6">
                {{ $devoirs->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

