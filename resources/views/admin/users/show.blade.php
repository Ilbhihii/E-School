@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-indigo-50 p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white/80 backdrop-blur-md rounded-3xl shadow-xl p-8 mb-8 border border-white/50 text-center">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-6">
                <div class="flex items-center gap-6">
                    <div class="w-24 h-24 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-3xl flex items-center justify-center shadow-2xl">
                        <span class="text-3xl font-black text-white">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                    </div>
                    <div>
                        <h1 class="text-4xl font-black bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text">{{ $user->name }}</h1>
                        <p class="text-2xl text-gray-600 mt-2">{{ $user->email }}</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    @if($user->is_active)
                        <span class="px-6 py-3 bg-gradient-to-r from-emerald-500 to-green-600 text-white rounded-2xl font-bold shadow-lg">✅ Actif</span>
                    @else
                        <span class="px-6 py-3 bg-gradient-to-r from-red-500 to-rose-600 text-white rounded-2xl font-bold shadow-lg">❌ Inactif</span>
                    @endif
                    @if($user->test_passed)
                        <span class="px-6 py-3 bg-gradient-to-r from-emerald-400 to-teal-500 text-white rounded-2xl font-bold shadow-lg">🎓 Test Validé</span>
                    @else
                        <span class="px-6 py-3 bg-gradient-to-r from-yellow-400 to-orange-500 text-white rounded-2xl font-bold shadow-lg">⏳ Test en attente</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Profile Info -->
            <div class="lg:col-span-2 bg-white/80 backdrop-blur-md rounded-3xl shadow-xl p-8 border border-white/50">
                <h2 class="text-2xl font-black text-gray-900 mb-6">📋 Détails du profil</h2>
                <div class="grid md:grid-cols-2 gap-8 text-lg">
                    <div>
                        <div class="space-y-4 mb-8">
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-2">ID</label>
                                <span class="bg-gradient-to-r from-gray-100 to-gray-200 px-6 py-3 rounded-2xl font-mono font-bold text-xl">#{{ $user->id }}</span>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-2">Rôle</label>
                                <span class="bg-gradient-to-r from-blue-100 to-indigo-100 px-6 py-3 rounded-2xl font-bold text-blue-800">Étudiant</span>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-2">Niveau</label>
                                @if($user->classRoom && $user->classRoom->level)
                                    <a href="{{ route('admin.levels.edit', $user->classRoom->level) }}" class="bg-gradient-to-r from-emerald-100 to-teal-100 px-6 py-3 rounded-2xl font-bold text-emerald-800 hover:from-emerald-200 transition-all">
                                        {{ $user->classRoom->level->name }}
                                    </a>
                                @else
                                    <span class="bg-gradient-to-r from-gray-100 to-gray-200 px-6 py-3 rounded-2xl font-bold text-gray-600">❌ Non assigné</span>
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-2">Abonné</label>
                                <span class="px-6 py-3 rounded-2xl font-bold {{ $user->is_paid ? 'bg-gradient-to-r from-emerald-100 to-teal-100 text-emerald-800' : 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-600' }}">
                                    {{ $user->is_paid ? '✅ Oui' : '❌ Non' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-2">Inscrit le</label>
                                <span class="bg-gradient-to-r from-blue-100 to-indigo-100 px-6 py-3 rounded-2xl font-bold text-blue-800">
                                    {{ $user->created_at->format('d/m/Y à H:i') }}
                                </span>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-600 mb-2">Dernière connexion</label>
                                <span class="bg-gradient-to-r from-purple-100 to-indigo-100 px-6 py-3 rounded-2xl font-bold text-purple-800">
                                    {{ $user->last_login ? $user->last_login->format('d/m/Y à H:i') : 'Jamais' }}
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-3 mt-6 pt-6 border-t border-gray-200">
                                <a href="{{ route('admin.users.test-results', $user) }}" class="bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 text-white py-4 px-6 rounded-2xl font-bold shadow-xl hover:shadow-2xl transition-all text-center">📊 Tests</a>

                                <a href="{{ route('admin.users.edit', $user) }}" class="bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white py-4 px-6 rounded-2xl font-bold shadow-xl hover:shadow-2xl transition-all text-center">✏️ Modifier</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Card -->
            <div class="bg-white/80 backdrop-blur-md rounded-3xl shadow-xl p-8 border border-white/50">
                <h3 class="text-xl font-black text-gray-900 mb-6 text-center">📈 Statistiques</h3>
                <div class="space-y-6">
                    <div class="text-center">
                        <div class="text-3xl font-black text-emerald-600 mb-2">{{ $testsCount ?? 0 }}</div>
                        <div class="text-sm text-gray-600 uppercase tracking-wide font-semibold">Tests passés</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-black {{ $avgScore ?? 0 >= 60 ? 'text-emerald-600' : 'text-yellow-600' }} mb-2">{{ round($avgScore ?? 0) }}%</div>
                        <div class="text-sm text-gray-600 uppercase tracking-wide font-semibold">Moyenne générale</div>
                    </div>
                    <div class="text-center">
                        <div class="w-full bg-gray-200 rounded-2xl h-3">
                            <div class="bg-gradient-to-r from-emerald-500 to-green-600 h-3 rounded-2xl" style="width: {{ ($avgScore ?? 0) }}%"></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-2">{{ round($avgScore ?? 0) }}% de réussite</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-12 bg-white/80 backdrop-blur-md rounded-3xl shadow-xl p-8 border border-white/50">
            <h3 class="text-2xl font-black text-gray-900 mb-6">⚡ Actions rapides</h3>
            <div class="grid md:grid-cols-4 gap-4">
                <form action="{{ route('admin.users.activate', $user->id) }}" method="POST" class="text-center">
                    @csrf @method('PUT')
                    <button type="submit" class="w-full bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white py-6 px-8 rounded-2xl font-bold shadow-xl hover:shadow-2xl transition-all {{ $user->is_active ? 'opacity-50 cursor-not-allowed' : '' }}">
                        ✅ Activer compte
                    </button>
                </form>
                <form action="{{ route('admin.users.deactivate', $user->id) }}" method="POST" class="text-center">
                    @csrf @method('PUT')
                    <button type="submit" class="w-full bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white py-6 px-8 rounded-2xl font-bold shadow-xl hover:shadow-2xl transition-all {{ !$user->is_active ? 'opacity-50 cursor-not-allowed' : '' }}" onclick="return confirm('Désactiver ce compte?')">
                        ❌ Désactiver
                    </button>
                </form>
                <a href="{{ route('admin.users.without-class') }}" class="flex items-center justify-center bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white py-6 px-8 rounded-2xl font-bold shadow-xl hover:shadow-2xl transition-all">
                    👥 Gérer classes
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center justify-center bg-gradient-to-r from-gray-500 to-gray-700 hover:from-gray-600 hover:to-gray-800 text-white py-6 px-8 rounded-2xl font-bold shadow-xl hover:shadow-2xl transition-all">
                    ← Retour liste
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

