<nav class="navbar-3d px-4 py-3 d-flex align-items-center justify-content-between flex-wrap gap-3">
    <a href="{{ route('dashboard') }}" class="navbar-brand mb-0">
        <i class="bi bi-mortarboard-fill me-2"></i>
        E-School
    </a>

    <div class="d-flex align-items-center gap-3 flex-wrap">
        @auth
            <span class="text-white-50 small d-none d-md-inline">
                <i class="bi bi-person-circle me-1"></i>
                {{ Auth::user()->name }}
            </span>

            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button class="btn nav-btn-3d nav-btn-3d-danger btn-sm py-1 px-3" style="font-size: 0.85rem;">
                    <i class="bi bi-box-arrow-right me-1"></i> Déconnexion
                </button>
            </form>
        @endauth
    </div>
</nav>
