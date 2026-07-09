<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold">
            Tableau de bord Admin
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="bg-white p-6 rounded shadow">
            <p class="text-gray-500">Total élèves</p>
            <p class="text-3xl font-bold text-indigo-600">120</p>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <p class="text-gray-500">Cours</p>
            <p class="text-3xl font-bold text-green-600">45</p>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <p class="text-gray-500">Lives</p>
            <p class="text-3xl font-bold text-red-600">3</p>
        </div>

    </div>
</x-app-layout>
