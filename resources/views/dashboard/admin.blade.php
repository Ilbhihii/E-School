<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold">Dashboard Admin</h2>
    </x-slot>

    <div class="admin-page">
        <div class="admin-container">
            <div class="stats-grid">
                <div class="stat-card blue adm-fade-up">
                    <div class="stat-card-icon blue"><i class="bi bi-people-fill"></i></div>
                    <div>
                        <div class="stat-card-value">120</div>
                        <div class="stat-card-label">Total élèves</div>
                    </div>
                </div>
                <div class="stat-card green adm-fade-up">
                    <div class="stat-card-icon green"><i class="bi bi-book-fill"></i></div>
                    <div>
                        <div class="stat-card-value">45</div>
                        <div class="stat-card-label">Cours</div>
                    </div>
                </div>
                <div class="stat-card red adm-fade-up">
                    <div class="stat-card-icon red"><i class="bi bi-camera-video-fill"></i></div>
                    <div>
                        <div class="stat-card-value">3</div>
                        <div class="stat-card-label">Lives</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
