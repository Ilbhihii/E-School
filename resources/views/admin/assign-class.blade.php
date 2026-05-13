@extends('layouts.admin')

@section('content')


<div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6 rounded-xl shadow-2xl mb-8 text-white">
    <div class="flex items-center gap-3">
        <div class="p-3 bg-white/20 rounded-xl">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
        </div>
        <div>
            <h3 class="text-2xl font-bold tracking-tight">Assigner étudiant à une classe</h3>
            <p class="opacity-90 mt-1">Gérez les assignations étudiants-classes facilement</p>
        </div>
    </div>
</div>



@if(session('success'))
<div class="bg-green-100 border-l-4 border-green-500 p-6 mb-6 rounded-xl shadow-md flex items-start gap-3">
    <div class="flex-shrink-0">
        <svg class="w-6 h-6 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
    </div>
    <div class="flex-1">
        <p class="text-green-800 font-medium">{{ session('success') }}</p>
    </div>
</div>
@endif


@if(isset($assignments) && count($assignments) > 0)



<div class="bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden mb-8">
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4">
        <h4 class="text-xl font-bold text-white flex items-center gap-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-9 3h1m-1 4h1m8-7h1"></path>
            </svg>
            Étudiants assignés
        </h4>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">

<thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-indigo-200">
<tr class="divide-x divide-gray-">
    <th class="px-6 py-4 text-left text-xs text-center font-bold text-gray-900 uppercase tracking-wider w-20">ID</th>
    <th class="px-6 py-4 text-left text-sm text-center font-semibold text-gray-900">Nom étudiant</th>
    <th class="px-6 py-4 text-left text-sm text-center font-semibold text-gray-900">Classe</th>
    <th class="px-6 py-4 text-left text-sm text-center font-semibold text-gray-900 w-48">Actions</th>
</tr>
</thead>


<tbody>

@foreach($assignments as $assignment)

<tr>


<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">{{ $assignment->user_id }}</td>

<td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $assignment->student_name }}</td>



<td class="px-6 py-4">
    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gradient-to-r from-indigo-500 to-purple-600 text-white shadow-lg">
        {{ $assignment->class_name }}
    </span>
</td>


<td>


<td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex gap-2">
    <!-- UPDATE -->
    <button class="group relative inline-flex items-center px-4 py-2 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2"
        onclick="editAssignment({{ $assignment->user_id }}, {{ $assignment->class_id }}, {{ $assignment->pivot_id }})"
        title="Modifier">
        <svg class="w-4 h-4 mr-1 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.5h3m1.5-7v7a1 1 0 01-1 1H6a1 1 0 01-1-1v-7a1 1 0 011-1h7a1 1 0 011 1z"></path>
        </svg>
        Modifier
    </button>

    <!-- DELETE -->
    <form method="POST"
        action="{{ route('admin.assign.class.destroy',$assignment->pivot_id) }}"
        class="inline-block"
        onsubmit="return confirm('Supprimer cette assignation ?')">
        @csrf
        @method('DELETE')
        <button class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
            title="Supprimer">
            <svg class="w-4 h-4 mr-1 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
            Supprimer
        </button>
    </form>
</td>


</td>

</tr>

@endforeach

</tbody>

</table>

</div>

@endif

<!-- Modal Modification -->

<div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4" id="editModal" style="display:none;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto transform transition-all animate-in fade-in zoom-in duration-200">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-indigo-100 rounded-xl">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.5h3m1.5-7v7a1 1 0 01-1 1H6a1 1 0 01-1-1v-7a1 1 0 011-1h7a1 1 0 011 1z"></path>
                        </svg>
                    </div>
                    <h5 class="text-xl font-bold text-gray-900">Modifier l'assignation</h5>
                </div>
                <button onclick="closeEditModal()" class="p-2 hover:bg-gray-100 rounded-xl transition-colors">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

<form method="POST" action="{{ route('admin.assign.class.update', '__PIVOT_ID__') }}" id="editForm">
                @csrf
                @method('PATCH')
                <input type="hidden" name="pivot_id" id="edit_assignment_id">

                <div class="p-6 space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Étudiant</label>
                        <select name="user_id" id="edit_user_id" class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all shadow-sm">
                            @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Classe</label>
                        <select name="class_id" id="edit_class_id" class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all shadow-sm">
@php $classRooms = \App\Models\ClassRoom::all(); @endphp
                            @foreach($classRooms as $classRoom)
                            <option value="{{ $classRoom->id }}">{{ $classRoom->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="px-6 pb-6 pt-0 border-t border-gray-200 flex gap-3">
                    <button type="button" onclick="closeEditModal()" class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-900 font-semibold rounded-xl transition-all shadow-sm">Annuler</button>
                    <button type="submit" class="flex-1 px-4 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">Modifier</button>
                </div>
            </form>
        </div>
    </div>
</div>



<script>
function editAssignment(userId, classId, pivotId) {
    document.getElementById('edit_assignment_id').value = pivotId;
    document.getElementById('edit_user_id').value = userId;
    document.getElementById('edit_class_id').value = classId;

    // Fix form action URL
    const form = document.getElementById('editForm');
    form.action = form.action.replace('__PIVOT_ID__', pivotId);

    document.getElementById('editModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Close modal on outside click
document.addEventListener('click', function(e) {
    const modal = document.getElementById('editModal');
    if (e.target === modal) {
        closeEditModal();
    }
});
</script>




<div class="bg-white rounded-2xl shadow-2xl p-8 border border-gray-200">
    <div class="text-center mb-8">
        <div class="w-20 h-20 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-3xl flex items-center justify-center mx-auto mb-4 shadow-xl">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
        </div>
        <h3 class="text-2xl font-bold text-gray-900 mb-2">Nouvelle assignation</h3>
        <p class="text-gray-600">Assignez un étudiant à une classe</p>
    </div>

    <form method="POST" action="{{ route('admin.assign.class.store') }}" class="space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-3">Étudiant</label>
            <select name="user_id" class="w-full p-4 border border-gray-300 rounded-xl focus:ring-3 focus:ring-indigo-200 focus:border-indigo-500 transition-all shadow-lg hover:shadow-xl">
                @foreach($students as $student)
                <option value="{{ $student->id }}">{{ $student->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-900 mb-3">Classe</label>
            <select name="class_id" class="w-full p-4 border border-gray-300 rounded-xl focus:ring-3 focus:ring-indigo-200 focus:border-indigo-500 transition-all shadow-lg hover:shadow-xl">
@foreach($classRooms as $classRoom)
                            <option value="{{ $classRoom->id }}">{{ $classRoom->name }}</option>
                            @endforeach
            </select>
        </div>

        <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-4 px-8 rounded-xl shadow-2xl hover:shadow-3xl transform hover:-translate-y-1 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-indigo-300">
            <span class="flex items-center justify-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Assigner à la classe
            </span>
        </button>
    </form>
</div>
@endsection

