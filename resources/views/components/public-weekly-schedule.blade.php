<section class="home-public-planning" aria-labelledby="home-public-planning-title">
    <div class="home-public-planning-shell">
        <div class="home-public-planning-head">
            <div>
                <span>Planning des cours</span>
                <h2 id="home-public-planning-title">Les horaires de la semaine</h2>
                <p>Consultez l’heure de début, l’heure de fin et la durée réelle de chaque séance.</p>
            </div>

            @if(Route::has('public.schedule.index'))
                <a href="{{ route('public.schedule.index') }}">
                    Voir tous les horaires
                    <i class="bi bi-arrow-right"></i>
                </a>
            @endif
        </div>

        <div class="home-public-planning-grid">
            @forelse($schedules as $schedule)
                <article>
                    <div class="home-public-planning-top">
                        <span class="home-public-planning-day">{{ $schedule['day_label'] }}</span>
                        <span class="home-public-planning-time">{{ $schedule['start_label'] }}</span>
                    </div>
                    <h3>{{ $schedule['subject'] }}</h3>
                    <p>{{ $schedule['level'] }} → {{ $schedule['class_name'] }}</p>
                    <footer><i class="bi bi-clock-history"></i> {{ $schedule['duration_label'] }}</footer>
                </article>
            @empty
                <div class="home-public-planning-empty">
                    <i class="bi bi-calendar2-week"></i>
                    <strong>Le planning sera publié prochainement.</strong>
                </div>
            @endforelse
        </div>
    </div>
</section>

@once
<style>
.home-public-planning{padding:72px 20px;background:linear-gradient(180deg,rgba(8,12,20,.15),rgba(15,23,42,.38))}.home-public-planning-shell{max-width:1180px;margin:0 auto}.home-public-planning-head{display:flex;align-items:flex-end;justify-content:space-between;gap:22px;margin-bottom:26px}.home-public-planning-head>div>span{color:#FFD166;font-size:.72rem;font-weight:900;letter-spacing:.12em;text-transform:uppercase}.home-public-planning-head h2{margin:8px 0 6px;color:#fff;font-size:clamp(1.7rem,4vw,2.5rem);font-weight:850}.home-public-planning-head p{margin:0;color:rgba(255,255,255,.48)}.home-public-planning-head>a{display:inline-flex;align-items:center;gap:8px;padding:12px 16px;border-radius:999px;background:linear-gradient(135deg,#2563eb,#7c3aed);color:#fff;text-decoration:none;font-weight:800;white-space:nowrap}.home-public-planning-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:15px}.home-public-planning-grid article{padding:19px;border-radius:20px;background:rgba(15,23,42,.72);border:1px solid rgba(148,163,184,.12);box-shadow:0 16px 40px rgba(0,0,0,.17)}.home-public-planning-top{display:flex;justify-content:space-between;align-items:center;gap:10px}.home-public-planning-day{color:#bfdbfe;font-size:.76rem;font-weight:850}.home-public-planning-time{padding:7px 10px;border-radius:999px;background:rgba(37,99,235,.13);color:#93c5fd;font-size:.78rem;font-weight:850}.home-public-planning-grid h3{margin:16px 0 5px;color:#fff;font-size:1.08rem}.home-public-planning-grid p{margin:0;color:rgba(255,255,255,.48);font-size:.84rem}.home-public-planning-grid footer{margin-top:14px;padding-top:12px;border-top:1px solid rgba(148,163,184,.1);color:#fcd34d;font-size:.76rem;font-weight:750}.home-public-planning-empty{grid-column:1/-1;display:flex;align-items:center;justify-content:center;gap:10px;padding:34px;border:1px dashed rgba(148,163,184,.2);border-radius:20px;color:rgba(255,255,255,.5)}@media(max-width:900px){.home-public-planning-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:640px){.home-public-planning{padding:50px 16px}.home-public-planning-head{align-items:flex-start;flex-direction:column}.home-public-planning-head>a{width:100%;justify-content:center}.home-public-planning-grid{grid-template-columns:1fr}}
html.light-mode .home-public-planning{background:#f8fafc}.light-mode .home-public-planning-head h2{color:#1e293b}.light-mode .home-public-planning-head p{color:#64748b}.light-mode .home-public-planning-grid article{background:#fff;border-color:#e2e8f0;box-shadow:0 14px 35px rgba(15,23,42,.06)}.light-mode .home-public-planning-grid h3{color:#1e293b}.light-mode .home-public-planning-grid p{color:#64748b}.light-mode .home-public-planning-empty{color:#64748b;border-color:#cbd5e1}
</style>
@endonce
