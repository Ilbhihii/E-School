@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-indigo-50 p-2 sm:p-6">
    <!-- Header with search and actions -->
    <div class="bg-white/80 backdrop-blur-md rounded-2xl shadow-xl p-6 mb-8 border border-white/50">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-2xl font-black bg-gradient-to-r from-purple-600 to-indigo-600 bg-clip-text text-transparent mb-2">👥 Gestion des Utilisateurs</h1>
                <p class="text-gray-600 text-lg">Tableau de bord complet des utilisateurs</p>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6 mb-8">

        <!-- Original Stats -->
        <div class="group bg-gradient-to-br from-purple-500/10 to-indigo-500/10 backdrop-blur-sm p-8 rounded-3xl border border-purple-200/50 shadow-2xl hover:shadow-3xl hover:-translate-y-2 transition-all duration-500 cursor-pointer relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-purple-500/20 to-indigo-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative z-10">
                <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-indigo-500 rounded-2xl flex items-center justify-center mb-4 shadow-2xl group-hover:rotate-12 transition-transform duration-500">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <h2 class="text-xl font-black text-gray-900 mb-2">Total Utilisateurs</h2>
                <p class="text-3xl lg:text-4xl font-black bg-gradient-to-r from-purple-600 to-indigo-600 bg-clip-text text-transparent">{{ $totalUsers }}</p>
            </div>
        </div>
        
        <div class="group bg-gradient-to-br from-indigo-500/10 to-purple-500/10 backdrop-blur-sm p-8 rounded-3xl border border-indigo-200/50 shadow-2xl hover:shadow-3xl hover:-translate-y-2 transition-all duration-500 cursor-pointer relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-indigo-500/20 to-purple-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative z-10">
                <div class="w-16 h-16 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-2xl flex items-center justify-center mb-4 shadow-2xl group-hover:rotate-12 transition-transform duration-500">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h2 class="text-xl font-black text-gray-900 mb-2">Utilisateurs Récents</h2>
                <p class="text-3xl lg:text-4xl font-black bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">{{ $recentUsers->count() }}</p>
            </div>
        </div>
        
        <div class="group bg-gradient-to-br from-emerald-500/10 to-teal-500/10 backdrop-blur-sm p-8 rounded-3xl border border-emerald-200/50 shadow-2xl hover:shadow-3xl hover:-translate-y-2 transition-all duration-500 cursor-pointer relative overflow-hidden xl:col-span-2">
            <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/20 to-teal-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative z-10">
                <div class="w-16 h-16 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-2xl flex items-center justify-center mb-4 shadow-2xl group-hover:rotate-12 transition-transform duration-500">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <h2 class="text-xl font-black text-gray-900 mb-2">Actions Disponibles</h2>
                <p class="text-lg text-gray-600 font-semibold mb-3">Gestion complète via formulaires intuitifs</p>
                <div class="flex gap-2 flex-wrap">
                    <span class="bg-white/80 px-4 py-2 rounded-xl text-sm font-semibold text-gray-800 border shadow-sm">✨ Activer/Désactiver</span>
                    <span class="bg-white/80 px-4 py-2 rounded-xl text-sm font-semibold text-gray-800 border shadow-sm">💳 Abonnements</span>
                    <span class="bg-white/80 px-4 py-2 rounded-xl text-sm font-semibold text-gray-800 border shadow-sm">📊 Statistiques</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Users Grid -->
    <div class="bg-white/80 backdrop-blur-md p-8 rounded-3xl shadow-2xl mb-8 border border-white/50 overflow-hidden">
        <div class="flex items-center gap-4 mb-8">
            <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-yellow-500 rounded-2xl flex items-center justify-center shadow-xl">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-black text-gray-900">Utilisateurs Récents</h3>
                <p class="text-gray-600">Les 10 derniers inscrits</p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
            @forelse($recentUsers as $user)
                <div class="group bg-gradient-to-b from-white/70 to-white/30 backdrop-blur-sm p-6 rounded-2xl border border-purple-200/50 shadow-lg hover:shadow-2xl hover:-translate-y-2 hover:bg-white/90 transition-all duration-500 cursor-pointer relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-purple-500/10 to-indigo-500/10 opacity-0 group-hover:opacity-100 transition-all duration-500"></div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-4 mb-3">
                            <div>
                                <h4 class="font-bold text-base text-gray-900 group-hover:text-purple-600 transition-colors">{{ $user->name }}</h4>
                                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs bg-gradient-to-r from-emerald-100 to-teal-100 text-emerald-800 px-3 py-1 rounded-full font-semibold">Nouveau</span>
                            <span class="text-sm text-gray-500 font-medium">{{ $user->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-20">
                    <div class="w-24 h-24 mx-auto bg-gray-200 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                    </div>
                <h3 class="text-xl font-bold text-gray-500 mb-2">Aucun utilisateur récent</h3>
                    <p class="text-gray-400">Les nouveaux utilisateurs apparaîtront ici</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white/80 backdrop-blur-md rounded-3xl shadow-2xl border border-white/50 overflow-hidden">
        <div class="bg-gradient-to-r from-purple-600/10 to-indigo-600/10 backdrop-blur-sm px-8 py-6 border-b border-purple-200/50">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-black bg-gradient-to-r from-purple-600 to-indigo-600 bg-clip-text text-transparent">Tous les Utilisateurs</h3>
                    <p class="text-gray-600 mt-1">Vue complète avec filtres avancés</p>
                </div>
                <div class="flex gap-3">
                    <select class="px-4 py-3 bg-white/50 backdrop-blur-sm border-2 border-purple-200 rounded-xl focus:border-purple-400 outline-none text-sm font-semibold">
                        <option>Tous</option>
                        <option>Actifs</option>
                        <option>Inactifs</option>
                        <option>Abonnés</option>
                    </select>
                    <button class="px-6 py-3 bg-gradient-to-r from-gray-800 to-gray-900 hover:from-gray-900 hover:to-black text-white rounded-xl font-bold text-sm shadow-lg hover:shadow-xl transition-all duration-300">Exporter 📊</button>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="sticky top-0 bg-gradient-to-r from-white/90 to-white/50 backdrop-blur-xl">
    <tr>
        <th class="px-6 py-5 text-left font-black text-gray-900 text-sm uppercase tracking-wider">Nom</th>
        <th class="px-6 py-5 text-left font-black text-gray-900 text-sm uppercase tracking-wider">Email</th>
        <th class="px-6 py-5 text-left font-black text-gray-900 text-sm uppercase tracking-wider">Statut Compte</th>
        <th class="px-6 py-5 text-left font-black text-gray-900 text-sm uppercase tracking-wider">Test Réussi</th>
        <th class="px-6 py-5 text-left font-black text-gray-900 text-sm uppercase tracking-wider">Activation</th>
        <th class="px-6 py-5 text-left font-black text-gray-900 text-sm uppercase tracking-wider">Inscrit</th>
        <th class="px-6 py-5 text-left font-black text-gray-900 text-sm uppercase tracking-wider">Actions</th>
    </tr>
</thead>
<tbody class="divide-y divide-gray-200/50">
@foreach($users as $user)
<tr class="hover:bg-gradient-to-r hover:from-purple-50 hover:to-indigo-50 transition-all duration-300 group">
    <td class="px-6 py-6 font-semibold text-lg text-gray-900">
        {{ $user->name }}
    </td>
    <td class="px-6 py-6 text-base text-gray-800">{{ $user->email }}</td>
    <td class="px-6 py-6">
        @if($user->is_active)
            <span class="inline-flex items-center px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-100 to-teal-100 text-emerald-800 font-bold">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Actif
            </span>
        @else
            <span class="inline-flex items-center px-4 py-2 rounded-xl bg-gradient-to-r from-red-100 to-rose-100 text-red-800 font-bold">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
                Inactif
            </span>
        @endif
    </td>
    <td class="px-6 py-6">
@php
            $bestResult = $user->results()->max('percentage') ?? 0;
        @endphp
        @if($bestResult > 0)
            <span class="px-4 py-2 rounded-xl font-bold text-sm shadow-sm
                @if($bestResult >= 60) bg-gradient-to-r from-emerald-100 to-teal-100 text-emerald-800 ring-1 ring-emerald-200 hover:from-emerald-200
                @elseif($bestResult >= 30) bg-gradient-to-r from-yellow-100 to-orange-100 text-yellow-800 ring-1 ring-yellow-200 hover:from-yellow-200
                @else bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 ring-1 ring-gray-200 hover:from-gray-200 @endif">
                {{ round($bestResult) }}%
            </span>
        @else
            <span class="px-4 py-2 bg-gray-100 text-gray-500 font-bold rounded-xl text-sm ring-1 ring-gray-200">⏳ Aucun test</span>
        @endif
    </td>
    <td class="px-6 py-6">
        @if(!$user->is_active)
            <form action="{{ route('admin.users.activate', $user->id) }}" method="POST" class="inline">
                @csrf
                @method('PUT')
                <button type="submit" class="bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white px-6 py-3 rounded-2xl font-bold shadow-lg hover:shadow-xl transition-all duration-300">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Activer
                </button>
            </form>
        @else
            <form action="{{ route('admin.users.deactivate', $user->id) }}" method="POST" class="inline">
                @csrf
                @method('PUT')
                <button type="submit" class="bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white px-6 py-3 rounded-2xl font-bold shadow-lg hover:shadow-xl transition-all duration-300" 
                        onclick="return confirm('Désactiver ce compte ? L\\'utilisateur ne pourra plus accéder à son espace.')">
                    Désactiver
                </button>
            </form>
        @endif
    </td>
    <td class="px-6 py-6">
        <span class="inline-flex items-center px-4 py-2 rounded-xl bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 font-bold">
            {{ $user->created_at->format('d/m/Y') }}
        </span>
    </td>
    <td class="px-6 py-6">
        <div class="flex gap-2">
            <a href="{{ route('admin.users.test-results', $user->id) }}" class="bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-lg transition-all duration-300">
                📊 Résultats
            </a>
        </div>

    </td>
</tr>
@endforeach
</tbody>
            </table>
        </div>
    </div>
</div>
@endsection
