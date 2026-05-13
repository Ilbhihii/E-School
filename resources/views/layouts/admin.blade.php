<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body class="bg-gray-100">

<div class="flex min-h-screen">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-gradient-to-b from-[#003A8F] to-[#0F3460] text-white flex flex-col justify-between shadow-xl">

        <div class="p-6">

            @php
                $route = request()->route()->getName();
            @endphp

            <h2 class="text-2xl font-bold mb-10 tracking-wide">
                <img src="{{ asset('images/Edu-School.png') }}" width="40" height="40" alt="E-School Logo">Edu Admin
            </h2>

            <nav class="space-y-2 text-sm">

                <a href="{{ route('admin.dashboard') }}"
                   class="menu-item flex items-center gap-2 px-3 py-2 rounded-lg {{ str_contains($route,'dashboard') ? 'bg-white/20' : '' }}">
                    📊 Dashboard
                </a>

                <a href="{{ route('admin.levels.index') }}"
                   class="menu-item flex items-center gap-2 px-3 py-2 rounded-lg {{ str_contains($route,'levels') ? 'bg-white/20' : '' }}">
                    🎓 Niveaux
                </a>

                @if(auth()->user()->role == 'admin' || auth()->user()->role == 'prof')
                <a href="{{ route('admin.courses.index') }}"
                   class="menu-item flex items-center gap-2 px-3 py-2 rounded-lg {{ str_contains($route,'courses') ? 'bg-white/20' : '' }}">
                    📚 Cours
                </a>
                @endif

                <a href="{{ route('admin.lives.index') }}"
                   class="menu-item flex items-center gap-2 px-3 py-2 rounded-lg {{ str_contains($route,'lives') ? 'bg-white/20' : '' }}">
                    🔴 Lives
                </a>

                <a href="{{ route('admin.users.index') }}"
                   class="menu-item flex items-center gap-2 px-3 py-2 rounded-lg {{ str_contains($route,'users') ? 'bg-white/20' : '' }}">
                    👥 Utilisateurs
                </a>

                <a href="{{ route('admin.chat.list') }}"
                   class="menu-item flex items-center gap-2 px-3 py-2 rounded-lg {{ str_contains($route,'chat') ? 'bg-white/20' : '' }}">
                    💬 Chat
                </a>

                <a href="{{ route('admin.absences') }}"
                   class="menu-item flex items-center gap-2 px-3 py-2 rounded-lg {{ str_contains($route,'absences') ? 'bg-white/20' : '' }}">
                    📋 Absences
                </a>

                <a href="{{ route('admin.schedule.index') }}"
                   class="menu-item flex items-center gap-2 px-3 py-2 rounded-lg {{ str_contains($route,'schedule') ? 'bg-white/20' : '' }}">
                    📅 Planning
                </a>

                <a href="{{ route('admin.assign.class') }}"
                   class="menu-item flex items-center gap-2 px-3 py-2 rounded-lg {{ str_contains($route,'assign.class') ? 'bg-white/20' : '' }}">
                    👨‍🎓 Assignation
                </a>

                <a href="{{ route('admin.profile') }}"
                   class="menu-item flex items-center gap-2 px-3 py-2 rounded-lg {{ str_contains($route,'profile') ? 'bg-white/20' : '' }}">
                    👤 Profil
                </a>

                <a href="{{ route('admin.settings') }}"
                   class="menu-item flex items-center gap-2 px-3 py-2 rounded-lg {{ str_contains($route,'settings') ? 'bg-white/20' : '' }}">
                    ⚙️ Paramètres
                </a>

            </nav>

        </div>

        <!-- LOGOUT -->
        <div class="p-6">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full bg-red-500 hover:bg-red-600 py-2 rounded-lg font-semibold transition">
                    Logout
                </button>
            </form>
        </div>

    </aside>

    <!-- MAIN -->
    <div class="flex-1 flex flex-col">

        <!-- TOPBAR -->
        <header class="glass shadow-md p-4 flex justify-between items-center sticky top-0 z-10">

            <h1 class="text-lg font-bold text-gray-800">
                Dashboard Administrateur
            </h1>

            <div class="relative">

                <button onclick="toggleMenu()" class="flex items-center gap-3 focus:outline-none">

                    <span class="text-gray-700 font-medium">
                        {{ auth()->user()->name }}
                    </span>

                    <div class="w-10 h-10 bg-[#003A8F] text-white flex items-center justify-center rounded-full font-bold shadow">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>

                </button>

                <!-- Dropdown -->
                <div id="userMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg py-2 z-50">

                    <a href="{{ route('admin.profile') }}" class="block px-4 py-2 hover:bg-gray-100">
                        👤 Mon Profil
                    </a>

                    <a href="{{ route('admin.settings') }}" class="block px-4 py-2 hover:bg-gray-100">
                        ⚙️ Paramètres
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-100">
                            🚪 Déconnexion
                        </button>
                    </form>

                </div>
            </div>

        </header>

        <!-- CONTENT -->
        <main class="p-6 bg-gray-50 flex-1 overflow-auto">
            @yield('content')
        </main>

    </div>

</div>

<script>
function toggleMenu() {
    document.getElementById('userMenu').classList.toggle('hidden');
}
</script>

</body>
</html>
