<nav class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Role-based menu with named routes -->
        @auth
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}">Dashboard Admin</a>
            <a href="{{ route('admin.users.index') }}">Utilisateurs</a>
            <a href="{{ route('admin.courses.index') }}">Cours</a>
            <a href="{{ route('admin.levels.index') }}">Niveaux</a>
        @endif

        @if(auth()->user()->role === 'prof')
            <a href="{{ route('prof.dashboard') }}">Dashboard Prof</a>
            <a href="{{ route('prof.courses.index') }}">Mes Cours</a>
            <a href="{{ route('prof.lives.index') }}">Lives</a>
            <a href="{{ route('prof.devoir.index') }}">Devoirs</a>
            <a href="{{ route('prof.tests.index') }}">Tests</a>
        @endif

        @if(auth()->user()->role === 'student')
            <a href="{{ route('student.dashboard') }}">Dashboard</a>
            <a href="{{ route('student.subjects') }}">Matières</a>
            <a href="{{ route('student.lives') }}">Lives</a>
            <a href="{{ route('student.courses.index') }}">Cours</a>
        @endif

        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
            @csrf
            <button type="submit">Logout</button>
        </form>
        @endauth
    </div>
</nav>

