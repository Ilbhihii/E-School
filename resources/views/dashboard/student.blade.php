<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold">
            Tableau Élève
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6">

        <div class="bg-white p-6 rounded shadow">
            <h3 class="font-semibold">Mes cours</h3>
            <a href="/student/courses" class="text-indigo-600 underline">
                Accéder aux cours
            </a>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <h3 class="font-semibold">Live en cours</h3>
            <a href="/student/live" class="bg-green-600 text-white px-4 py-2 rounded">
                Rejoindre
            </a>
        </div>

    </div>
</x-app-layout>
