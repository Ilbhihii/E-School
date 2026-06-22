@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-indigo-50 p-6">
    <div class="bg-white/80 backdrop-blur-md rounded-3xl shadow-2xl p-8 mb-8 border border-white/50">
        <div class="flex items-center gap-4 mb-8">
            <div class="w-16 h-16 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-2xl flex items-center justify-center shadow-xl">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-black bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent mb-2">Résultats de Tests - {{ $user->name }}</h1>
                <p class="text-xl text-gray-600">Performance académique complète</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="px-8 py-4 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white rounded-2xl font-bold shadow-lg hover:shadow-xl transition-all duration-300 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Retour aux utilisateurs
            </a>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="bg-gradient-to-br from-emerald-50 to-teal-50 p-8 rounded-2xl border border-emerald-200 shadow-xl">
                <h3 class="text-lg font-bold text-gray-900 mb-2">Tests Passés</h3>
                <p class="text-4xl font-black text-emerald-600">{{ $testsCount }}</p>
            </div>
<div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-8 rounded-2xl border border-blue-200 shadow-xl">
                <h3 class="text-lg font-bold text-gray-900 mb-2">Moyenne Générale</h3>
                <p class="text-4xl font-black text-blue-600">{{ $testsCount > 0 ? number_format($avgPercentage ?? 0, 1) . '%' : '0%' }}</p>
            </div>
<div class="bg-gradient-to-br from-purple-50 to-violet-50 p-8 rounded-2xl border border-purple-200 shadow-xl">
                <h3 class="text-lg font-bold text-gray-900 mb-2">Note Moyenne</h3>
                <p class="text-4xl font-black text-purple-600">{{ $testsCount > 0 ? number_format((($avgPercentage ?? 0) / 100) * 20, 1) . ' / 20' : 'Non noté' }}</p>
            </div>
        </div>
    </div>

    @if($user->results->count() > 0)
        <!-- Tests Table -->
        <div class="bg-white/80 backdrop-blur-md rounded-3xl shadow-2xl border border-white/50 overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-200 bg-gradient-to-r from-emerald-50 to-teal-50">
                <h3 class="text-2xl font-black text-gray-900">Historique des Tests</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">Test</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">Matière</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">Score</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">Note /20</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">Pourcentage</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-900 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-900 uppercase tracking-wider min-w-[100px]">Détails</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($user->results as $result)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-semibold text-gray-900">{{ $result->test->title ?? 'Test supprimé' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $result->test->subject->name ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-4 py-2 bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 font-bold rounded-xl">
                                    {{ $result->score }} / {{ $result->total_questions }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                            <span class="px-4 py-2 bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 font-bold rounded-xl">
                                    {{ $result->total_questions > 0 ? number_format(($result->score / $result->total_questions) * 20, 2) . ' / 20' : '0 / 20' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-bold text-lg text-blue-600">{{ $result->percentage }}%</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $result->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.users.test-result', [$user->id, $result->test_id]) }}" class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Détails
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="text-center py-20 bg-white/60 backdrop-blur-md rounded-3xl shadow-xl border border-gray-200">
            <svg class="w-24 h-24 text-gray-400 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
            </svg>
            <h3 class="text-2xl font-bold text-gray-700 mb-4">Aucun résultat de test</h3>
            <p class="text-lg text-gray-500 mb-8">Ce étudiant n'a pas encore passé de tests</p>
            <a href="{{ route('admin.users.index') }}" class="px-8 py-4 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white rounded-2xl font-bold shadow-lg hover:shadow-xl transition-all duration-300">
                Voir tous les étudiants
            </a>
        </div>
    @endif
</div>

@endsection
