@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-indigo-50 p-6">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="bg-white/80 backdrop-blur-md rounded-3xl shadow-xl p-8 mb-8 border border-white/50 text-center">
            <h1 class="text-3xl font-black bg-gradient-to-r from-emerald-600 to-green-600 bg-clip-text text-transparent mb-4">
                ✏️ Modifier la classe de {{ $user->name }}
            </h1>
            <p class="text-xl text-gray-600 leading-relaxed">
                Assigner une classe à cet étudiant pour lui donner accès aux lives et cours.
            </p>
        </div>

        <!-- Form Card -->
        <div class="bg-white/80 backdrop-blur-md rounded-3xl shadow-2xl border border-white/50 overflow-hidden">
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="p-8">
                @csrf
                @method('PUT')
                
                <div class="space-y-8">
                    <!-- User Info -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-2xl border border-blue-200/50">
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Infos Étudiant</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-lg">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Nom</label>
                                <span class="bg-white px-4 py-3 rounded-xl border shadow-sm font-bold text-gray-900">{{ $user->name }}</span>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                                <span class="bg-white px-4 py-3 rounded-xl border shadow-sm font-mono text-gray-900">{{ $user->email }}</span>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">ID</label>
                                <span class="bg-gradient-to-r from-gray-100 to-gray-200 px-4 py-3 rounded-xl font-mono font-bold text-lg">{{ $user->id }}</span>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Classe actuelle</label>
                                <span class="bg-gradient-to-r from-yellow-100 to-orange-100 px-4 py-3 rounded-xl font-bold text-yellow-800 {{ $user->class_id ? '' : 'border-2 border-yellow-300/50' }}">
                                    {{ $user->classRoom?->name ?? '❌ Aucune' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Class Select -->
                    <div>
                        <label class="block text-lg font-bold text-gray-900 mb-4">Classe à assigner</label>
                        <select name="class_id" class="w-full px-6 py-5 bg-white border-2 border-gray-200 rounded-3xl focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100 font-semibold text-xl shadow-lg transition-all duration-300" required>
                            <option value="">❌ Aucune classe</option>
                            @foreach($classRooms as $classRoom)
                                <option value="{{ $classRoom->id }}" {{ old('class_id', $user->class_id) == $classRoom->id ? 'selected' : '' }}>
                                    {{ $classRoom->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_id')
                            <div class="mt-3 p-4 bg-red-50 border border-red-200 border-l-4 border-red-400 rounded-xl text-red-700 font-medium">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex gap-4 mt-12 pt-8 border-t border-gray-200">
                    <a href="{{ route('admin.users.without-class') }}" class="flex-1 bg-gradient-to-r from-gray-500 to-gray-700 hover:from-gray-600 hover:to-gray-800 text-white py-5 px-8 rounded-2xl font-bold text-xl shadow-xl hover:shadow-2xl transition-all duration-300 flex items-center justify-center gap-3">
                        ← Annuler
                    </a>
                    <button type="submit" class="flex-1 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white py-5 px-8 rounded-2xl font-bold text-xl shadow-xl hover:shadow-2xl transition-all duration-300 flex items-center justify-center gap-3">
                        ✅ {{ $user->class_id ? 'Modifier' : 'Assigner Classe' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

