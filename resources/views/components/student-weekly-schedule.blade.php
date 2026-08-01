<section class="student-planning-widget" aria-labelledby="student-planning-title">
    <div class="student-planning-head">
        <div>
            <span class="student-planning-kicker">Emploi du temps</span>
            <h3 id="student-planning-title">Mes prochaines classes</h3>
        </div>
        @if(Route::has('student.schedule.index'))
            <a href="{{ route('student.schedule.index') }}" class="student-planning-link">
                Voir tout <i class="bi bi-arrow-right"></i>
            </a>
        @endif
    </div>

    @forelse($occurrences as $occurrence)
        <article class="student-planning-item">
            <div class="student-planning-date">
                <span>{{ $occurrence['day_short'] }}</span>
                <strong>{{ $occurrence['day_number'] }}</strong>
            </div>
            <div class="student-planning-main">
                <div class="student-planning-time"><i class="bi bi-clock"></i>{{ $occurrence['time_label'] }}</div>
                <h4>{{ $occurrence['subject'] }} — {{ $occurrence['class_name'] }}</h4>
                <p>{{ $occurrence['level'] }}</p>
            </div>
            <div class="student-planning-room"><i class="bi bi-door-open"></i>{{ $occurrence['room'] }}</div>
        </article>
    @empty
        <div class="student-planning-empty">
            <i class="bi bi-calendar2-check"></i>
            <div><strong>Aucune classe planifiée prochainement.</strong><span>Le planning apparaîtra ici après sa publication par l’administration.</span></div>
        </div>
    @endforelse
</section>

@once
<style>
.student-planning-widget{background:var(--card-bg,#fff);border:1px solid rgba(148,163,184,.22);border-radius:22px;padding:22px;box-shadow:0 16px 45px rgba(15,23,42,.07);margin-bottom:24px}.student-planning-head{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:16px}.student-planning-kicker{display:block;color:#7c3aed;font-size:.74rem;font-weight:800;text-transform:uppercase;letter-spacing:.12em;margin-bottom:4px}.student-planning-head h3{margin:0;color:var(--text-primary,#111827);font-size:1.2rem}.student-planning-link{display:inline-flex;align-items:center;gap:7px;color:#7c3aed;font-weight:700;text-decoration:none}.student-planning-item{display:grid;grid-template-columns:58px minmax(0,1fr) auto;gap:14px;align-items:center;padding:14px 0;border-top:1px solid rgba(148,163,184,.18)}.student-planning-date{width:52px;height:58px;border-radius:16px;background:linear-gradient(145deg,#ede9fe,#f5f3ff);display:flex;flex-direction:column;align-items:center;justify-content:center;color:#6d28d9}.student-planning-date span{font-size:.69rem;font-weight:800;text-transform:uppercase}.student-planning-date strong{font-size:1.3rem;line-height:1}.student-planning-time{display:flex;align-items:center;gap:6px;color:#64748b;font-size:.79rem;font-weight:700}.student-planning-main h4{margin:4px 0 2px;color:var(--text-primary,#111827);font-size:1rem}.student-planning-main p{margin:0;color:#64748b;font-size:.85rem}.student-planning-room{display:inline-flex;align-items:center;gap:7px;border-radius:999px;background:#f8fafc;color:#475569;padding:8px 11px;font-size:.78rem;font-weight:700;white-space:nowrap}.student-planning-empty{display:flex;gap:13px;align-items:center;border:1px dashed rgba(124,58,237,.28);background:#faf8ff;border-radius:16px;padding:18px;color:#64748b}.student-planning-empty>i{font-size:1.5rem;color:#7c3aed}.student-planning-empty strong,.student-planning-empty span{display:block}.student-planning-empty strong{color:#334155;margin-bottom:3px}@media(max-width:680px){.student-planning-widget{padding:18px}.student-planning-item{grid-template-columns:52px minmax(0,1fr)}.student-planning-room{grid-column:2;justify-self:start}.student-planning-link{font-size:.82rem}}
</style>
@endonce
