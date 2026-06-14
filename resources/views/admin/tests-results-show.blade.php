@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-indigo-50 p-6">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white/80 backdrop-blur-md rounded-3xl shadow-2xl p-8 mb-8 border border-white/50">
            <div class="flex items-start gap-6 mb-8">
                <div class="w-20 h-20 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-2xl flex items-center justify-center shadow-2xl flex-shrink-0">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-black  from-gray-900 to-gray-700 mb-2">Détails du Test</h1>
                    <div class="flex items-center gap-4 text-lg mb-2">
                        <span class="font-semibold text-gray-900">{{ $user->name }}</span>
                        <span class="px-4 py-2 bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 font-bold rounded-xl">{{ $test->title }}</span>
                    </div>
                    <div class="flex items-center gap-6 text-sm text-gray-600">
                        <span>Matière: {{ $test->subject->name ?? 'N/A' }}</span>
                        <span>Date: {{ $result->created_at->format('d/m/Y H:i') }}</span>
                        <span>Durée: {{ $test->duration }} min</span>
                    </div>
                    <a href="{{ route('admin.users.test-results', $user->id) }}" class="mt-4 inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Retour aux résultats
                    </a>

                </div>
            </div>

            <!-- Score Summary -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                <div class="bg-gradient-to-br from-emerald-50 to-teal-50 p-8 rounded-2xl border border-emerald-200 shadow-xl text-center">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Score Final</h3>
                    <p class="text-5xl font-black text-emerald-600 mb-2">{{ $result->score }} / {{ $result->total_questions }}</p>
                    <div class="w-full bg-gray-200 rounded-full h-4 mb-2">
                        <div class="bg-gradient-to-r from-emerald-500 to-teal-500 h-4 rounded-full" style="width: {{ $result->percentage }}%"></div>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ $result->percentage }}%</p>
                </div>
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-8 rounded-2xl border border-blue-200 shadow-xl">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Note /20</h3>
                    <p class="text-5xl font-black text-blue-600">{{ number_format(($result->percentage / 5), 1) }}/20</p>
                </div>
                <div class="bg-gradient-to-br from-purple-50 to-violet-50 p-8 rounded-2xl border border-purple-200 shadow-xl">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Questions Répondues</h3>
                    <p class="text-5xl font-black text-purple-600">{{ $result->total_questions }}</p>
                </div>
            </div>
        </div>

        <!-- Questions Details -->
        <div class="bg-white/80 backdrop-blur-md rounded-3xl shadow-2xl border border-white/50 overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-200 bg-gradient-to-r from-emerald-50 to-teal-50">
                <h3 class="text-2xl font-black text-gray-900">Analyse Détaillée par Question</h3>
            </div>
            <div class="divide-y divide-gray-200">
                @foreach($test->questions as $question)
                <div class="p-8 hover:bg-gray-50 transition-colors">
                    <div class="flex items-start gap-6 mb-6">
                        <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-r from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center font-bold text-white text-lg -mt-2">
                            Q{{ $loop->iteration }}
                        </div>
                        <div class="flex-1">
                            <h4 class="text-xl font-bold text-gray-900 mb-3">{{ $question->question }}</h4>
                            <div class="space-y-2">
                                @foreach($question->answers as $answer)
                                <div class="flex items-center gap-3 p-3 rounded-xl {{ $loop->parent->first ? 'bg-white border border-gray-200 shadow-sm' : '' }} {{ $answer->is_correct ? 'border-emerald-300 bg-emerald-50' : 'border-gray-200' }}">
                                    <div class="w-5 h-5 rounded-full flex items-center justify-center font-bold {{ $answer->is_correct ? 'bg-emerald-500 text-white' : 'bg-gray-300 text-gray-500' }}">
                                        {{ $answer->is_correct ? '✓' : '' }}
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $answer->answer }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Student Answer -->
                    @if(isset($result->student_responses[$question->id]) && count($result->student_responses[$question->id]) > 0)
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-2xl border border-blue-200 shadow-sm mb-6">
                        <h5 class="font-bold text-lg text-blue-900 mb-3">Réponse de l'étudiant:</h5>
                        <div class="space-y-2">
                            @foreach($result->student_responses[$question->id] as $studAns)
                            <div class="flex items-center gap-3 p-3 rounded-xl bg-white border {{ $studAns['is_correct'] ? 'border-emerald-300 bg-emerald-50' : 'border-red-200 bg-red-50' }}">
                                <div class="w-5 h-5 rounded-full flex items-center justify-center font-bold {{ $studAns['is_correct'] ? 'bg-emerald-500 text-white' : 'bg-red-500 text-white' }}">
                                    {{ $studAns['is_correct'] ? '✓' : '✗' }}
                                </div>
                                <span class="font-medium {{ $studAns['is_correct'] ? 'text-emerald-900' : 'text-red-900' }}">{{ $studAns['text'] }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @else
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-6 rounded-2xl border border-gray-200 mb-6">
                        <p class="text-gray-600 font-medium">Aucune réponse fournie</p>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection
