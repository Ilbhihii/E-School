{{-- À placer dans la liste du menu visiteur, idéalement entre « Matières » et « Rendez-vous ». --}}
<li class="nav-item">
    <a class="nav-link {{ request()->routeIs('public.schedule.*') ? 'active' : '' }}"
       href="{{ route('public.schedule.index') }}">
        <i class="bi bi-calendar-week me-1"></i>
        Planning
    </a>
</li>
