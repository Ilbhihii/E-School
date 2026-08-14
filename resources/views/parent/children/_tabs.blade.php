<div class="parent-tabs">
    <a class="{{ request()->routeIs('parent.children.show') ? 'active' : '' }}" href="{{ route('parent.children.show', $student) }}">Vue générale</a>
    <a class="{{ request()->routeIs('parent.children.schedule') ? 'active' : '' }}" href="{{ route('parent.children.schedule', $student) }}">Emploi du temps</a>
    <a class="{{ request()->routeIs('parent.children.absences') ? 'active' : '' }}" href="{{ route('parent.children.absences', $student) }}">Présences</a>
    <a class="{{ request()->routeIs('parent.children.assignments') ? 'active' : '' }}" href="{{ route('parent.children.assignments', $student) }}">Devoirs</a>
    <a class="{{ request()->routeIs('parent.children.results') ? 'active' : '' }}" href="{{ route('parent.children.results', $student) }}">Résultats</a>
</div>
