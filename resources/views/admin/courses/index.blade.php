@extends('layouts.admin')

@section('content')

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 p-6">

    <div class="max-w-7xl mx-auto">

        <!-- HEADER -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">📘 Gestion des Cours</h1>
            <p class="text-gray-500">Administration des contenus pédagogiques</p>
        </div>

        <!-- STATS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

            <div class="bg-white rounded-2xl shadow p-6 hover:shadow-lg transition">
                <p class="text-gray-500">Total cours</p>
                <h2 class="text-3xl font-bold text-green-600">{{ $courses->count() }}</h2>
            </div>

            <div class="bg-white rounded-2xl shadow p-6 hover:shadow-lg transition">
                <p class="text-gray-500">Cours récents</p>
                <h2 class="text-3xl font-bold text-blue-600">
                    {{ $courses->where('created_at','>',now()->subDays(7))->count() }}
                </h2>
            </div>

            <div class="bg-white rounded-2xl shadow p-6 flex items-center justify-between">
                <div>
                    <p class="text-gray-500">Actions</p>
                    <a href="{{ route('admin.courses.create') }}"
                       class="mt-2 inline-block bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                        ➕ Ajouter cours
                    </a>
                </div>
            </div>

        </div>

        <!-- TABLE CARD -->
        <div class="bg-white rounded-2xl shadow overflow-hidden">

            <div class="p-5 border-b">
                <h3 class="text-lg font-semibold text-gray-700">Liste des cours</h3>
            </div>

            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="p-4 text-left">Titre</th>
                            <th class="p-4 text-left">Classe</th>
                            <th class="p-4 text-left">Matière</th>
                            <th class="p-4 text-left">Date</th>
                            <th class="p-4 text-left">Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($courses as $course)
                        <tr class="border-t hover:bg-gray-50">

                            <td class="p-4 font-medium text-gray-800">
                                {{ $course->title }}
                            </td>

                            <td class="p-4 text-gray-600">
                                {{ $course->classRoom->name ?? '---' }}
                            </td>

                            <td class="p-4 text-gray-600">
                                {{ $course->subject->name ?? '---' }}
                            </td>

                            <td class="p-4 text-gray-500">
                                {{ $course->created_at->format('d/m/Y') }}
                            </td>

                            <td class="p-4 flex gap-2 flex-wrap">

                                <a href="{{ route('admin.courses.edit', $course->id) }}"
                                   class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200">
                                    ✏️ Edit
                                </a>

                                <a href="{{ route('prof.devoir.create', $course->id) }}"
                                   class="px-3 py-1 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200">
                                    ➕ Devoirs
                                </a>

                                <a href="{{ route('admin.lives.create', $course->class_id) }}"
                                   class="px-3 py-1 bg-red-100 text-red-700 rounded-lg hover:bg-red-200">
                                    🔴 Live
                                </a>

                                <form method="POST"
                                      action="{{ route('admin.courses.destroy',$course->id) }}"
                                      onsubmit="return confirm('Supprimer ce cours ?')">
                                    @csrf
                                    @method('DELETE')

                                    <button class="px-3 py-1 bg-red-100 text-red-700 rounded-lg hover:bg-red-200">
                                        🗑
                                    </button>
                                </form>

                            </td>

                        </tr>
                        @empty

                        <tr>
                            <td colspan="5" class="text-center p-10 text-gray-400">
                                Aucun cours disponible
                            </td>
                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="p-4">
                {{ $courses->links() }}
            </div>

        </div>

    </div>

</div>

@endsection
