@extends('layouts.admin')

@section('content')

<div class="admin-dashboard">

    <!-- HEADER -->
    <div class="dashboard-header mb-5">

        <div>
            <h1 class="fw-bold text-dark">Dashboard Admin</h1>
            <p class="text-muted">Gestion complète de la plateforme éducative</p>
        </div>

        <div class="date-badge">
            📅 {{ now()->format('d M Y') }}
        </div>

    </div>

    <!-- STATS -->
    <div class="stats-grid mb-5">

        <div class="stat-card blue">
            <div class="icon">🎓</div>
            <div>
                <h4>Classes</h4>
                <h2>{{ $classesCount }}</h2>
            </div>
        </div>

        <div class="stat-card green">
            <div class="icon">📚</div>
            <div>
                <h4>Cours</h4>
                <h2>{{ $coursesCount }}</h2>
            </div>
        </div>

        <div class="stat-card red">
            <div class="icon">📺</div>
            <div>
                <h4>Lives</h4>
                <h2>{{ $livesCount }}</h2>
            </div>
        </div>

        <div class="stat-card cyan">
            <div class="icon">👥</div>
            <div>
                <h4>Étudiants</h4>
                <h2>{{ $usersCount }}</h2>
            </div>
        </div>

    </div>

    <!-- TABLE -->
    <div class="table-card">

        <div class="table-header">
            <h3>Liste des étudiants</h3>

            <input type="text" placeholder="🔍 Rechercher...">
        </div>

        <div class="table-responsive">

            <table>

                <thead>
                    <tr>
                        <th>Étudiant</th>
                        <th>Pays</th>
                        <th>Ville</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($students as $student)
                    <tr>

                        <td class="user-cell">
                            <div class="avatar">
                                {{ strtoupper(substr($student->name,0,2)) }}
                            </div>
                            <span>{{ $student->name }}</span>
                        </td>

                        <td>
                            <span class="badge">
                                {{ $student->country ?? '-' }}
                            </span>
                        </td>

                        <td>{{ $student->city ?? '-' }}</td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="empty">
                            Aucun étudiant trouvé
                        </td>
                    </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

        <div class="pagination">
            {{ $students->links() }}
        </div>

    </div>

</div>

<!-- STYLE -->
<style>

/* GENERAL */
.admin-dashboard{
    padding:30px;
    background:#F5F7FA;
    min-height:100vh;
}

/* HEADER */
.dashboard-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.date-badge{
    background:#003A8F;
    color:white;
    padding:10px 18px;
    border-radius:12px;
    font-size:14px;
}

/* STATS */
.stats-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:20px;
}

.stat-card{
    background:white;
    padding:20px;
    border-radius:16px;
    display:flex;
    align-items:center;
    gap:15px;
    box-shadow:0 10px 25px rgba(0,0,0,0.05);
    transition:0.3s;
}

.stat-card:hover{
    transform:translateY(-5px);
}

.stat-card .icon{
    font-size:28px;
}

/* COLORS */
.stat-card.blue{border-left:5px solid #003A8F;}
.stat-card.green{border-left:5px solid #4DA3FF;}
.stat-card.red{border-left:5px solid #D90429;}
.stat-card.cyan{border-left:5px solid #20c997;}

.stat-card h2{
    margin:0;
    font-size:28px;
    font-weight:bold;
}

/* TABLE */
.table-card{
    background:white;
    border-radius:16px;
    padding:20px;
    box-shadow:0 10px 25px rgba(0,0,0,0.05);
}

.table-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:15px;
}

.table-header input{
    padding:10px;
    border-radius:10px;
    border:1px solid #ddd;
    outline:none;
}

table{
    width:100%;
    border-collapse:collapse;
}

thead{
    background:#F5F7FA;
}

th,td{
    padding:12px;
    border-bottom:1px solid #eee;
}

.user-cell{
    display:flex;
    align-items:center;
    gap:10px;
}

.avatar{
    width:40px;
    height:40px;
    background:#003A8F;
    color:white;
    border-radius:50%;
    display:flex;
    justify-content:center;
    align-items:center;
    font-weight:bold;
}

.badge{
    background:#eee;
    padding:5px 10px;
    border-radius:20px;
    font-size:12px;
}

.empty{
    text-align:center;
    color:#999;
    padding:20px;
}

/* PAGINATION */
.pagination{
    margin-top:15px;
}

/* RESPONSIVE */
@media(max-width:768px){
    .dashboard-header{
        flex-direction:column;
        align-items:flex-start;
        gap:10px;
    }
}

</style>

@endsection
