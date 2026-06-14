@extends('layouts.admin')

@section('content')
<style>
.chat-container::-webkit-scrollbar { width: 6px; }
.chat-container::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
.chat-container::-webkit-scrollbar-thumb { background: linear-gradient(to bottom, #6366f1, #8b5cf6); border-radius: 10px; }
.chat-container::-webkit-scrollbar-thumb:hover { background: #4f46e5; }
.subject-card { 
  background: rgba(255,255,255,0.9); 
  backdrop-filter: blur(20px); 
  border: 1px solid rgba(99,102,241,0.2);
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.subject-card:hover { 
  transform: translateY(-12px) scale(1.02); 
  box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25), 0 0 0 1px rgba(99,102,241,0.5); 
  border-color: rgba(99,102,241,0.5);
}
.title-gradient {
  background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

@supports (overflow: clip) {
  .line-clamp-2, .line-clamp-3 {
    overflow: clip;
  }
}
@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-10px); }
}
.floating { animation: float 4s ease-in-out infinite; }
</style>
<div class="flex flex-col h-[80vh]">
    <div class="flex-none">
        <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 text-white p-6 pb-4 shadow-2xl mb-2 rounded-b-3xl">
            <div class="flex items-center gap-3">
                <i class="fas fa-comments text-3xl"></i>
                <div>
                    <h3 class="text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight title-gradient mb-1 leading-tight">Chat Administration</h3>
                    <p class="text-indigo-100 font-medium opacity-90">Toutes les matières - Admin & Discussions</p>
                </div>
            </div>
        </div>
    </div>

    <div class="flex-1 p-4 overflow-auto chat-container">
        @if(isset($subjects) && $subjects->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                @foreach($subjects->unique('name') as $subject)
                    <div class="group">
                        <a href="{{ route('admin.chat', $subject->id) }}" class="block w-full h-full no-underline">
                            <div class="subject-card h-full shadow-xl rounded-3xl p-6 sm:p-8 flex flex-col justify-between transition-all duration-500 overflow-hidden hover:shadow-2xl">
                                <div class="flex items-start gap-3 sm:gap-4 mb-4">
                                    <div class="w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg flex-shrink-0">
                                        @php
                                            $icons = ['book', 'brain', 'calculator', 'palette', 'graduation-cap', 'flask', 'microscope'];
                                            $randomIcon = $icons[array_rand($icons)];
                                        @endphp
                                        <i class="fas fa-{{ $randomIcon }} text-xl sm:text-2xl text-white"></i>
                                    </div>
                                    <div class="flex-1 min-h-0">
                                        <h4 class="text-lg sm:text-xl font-bold text-gray-900 mb-2 group-hover:title-gradient transition-all duration-300 line-clamp-2 leading-tight">{{ $subject->name }}</h4>
                                        @if($subject->messages_count > 0)
                                            <p class="text-gray-600 leading-relaxed text-sm sm:text-base line-clamp-3 flex-grow">
                                                {{ $subject->messages_count }} discussion{{ $subject->messages_count > 1 ? 's' : '' }}
                                                @if($subject->messages->first())
                                                    • Dernier: {{ Str::limit($subject->messages->first()->message, 50) }}
                                                @endif
                                            </p>
                                        @else
                                            <p class="text-gray-600 leading-relaxed text-sm sm:text-base line-clamp-3 flex-grow">{{ $subject->description ?? 'Pas de description disponible.' }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center justify-between pt-4 border-t border-indigo-100">
                                    @if(str_contains(strtolower($subject->name), 'admin') || $subject->name === 'Administration')
                                        <span class="bg-gradient-to-r from-emerald-500 to-teal-600 text-white px-3 py-1.5 sm:px-4 sm:py-2 rounded-full text-xs sm:text-sm font-semibold shadow-md hover:scale-105 transition-transform">1:1 Admin</span>
                                    @elseif($subject->messages_count > 0)
                                        <span class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-3 py-1.5 sm:px-4 sm:py-2 rounded-full text-xs sm:text-sm font-semibold shadow-md hover:scale-105 transition-transform">
                                            {{ $subject->messages_count }} message{{ $subject->messages_count > 1 ? 's' : '' }}
                                        </span>
                                    @else
                                        <span class="bg-gradient-to-r from-orange-500 to-yellow-600 text-white px-3 py-1.5 sm:px-4 sm:py-2 rounded-full text-xs sm:text-sm font-semibold shadow-md hover:scale-105 transition-transform">En attente</span>
                                    @endif
                                    <div class="flex items-center gap-1.5 sm:gap-2 text-indigo-600 font-semibold text-sm group-hover:text-purple-600 transition-colors">
                                        <i class="fas fa-arrow-right text-sm transition-transform group-hover:translate-x-1"></i>
                                        <span class="whitespace-nowrap">{{ $subject->messages_count > 0 ? 'Voir discussions' : 'Ouvrir chat' }}</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center h-full p-8 sm:p-12 text-center">
                <div class="w-24 h-24 sm:w-32 sm:h-32 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-3xl flex items-center justify-center shadow-2xl mb-6 sm:mb-8 floating">
                    <i class="fas fa-comments text-3xl sm:text-4xl text-white"></i>
                </div>
                <h3 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-700 mb-3 sm:mb-4 title-gradient leading-tight">Aucune matière disponible</h3>
                <p class="text-lg sm:text-xl text-gray-500 mb-6 sm:mb-8 max-w-md mx-auto leading-relaxed px-4">Les matières de chat seront bientôt disponibles. Restez connecté !</p>
                <button class="w-full sm:w-auto sm:max-w-sm h-12 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-2xl flex items-center justify-center font-semibold shadow-xl hover:shadow-2xl transform hover:scale-105 active:scale-95 transition-all duration-300 cursor-pointer px-6">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Actualiser
                </button>
            </div>
        @endif
    </div>
</div>
@endsection
