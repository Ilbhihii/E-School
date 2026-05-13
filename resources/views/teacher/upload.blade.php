<form method="POST" action="/admin/uploads" enctype="multipart/form-data"
      class="max-w-xl mx-auto bg-white p-6 rounded shadow">
    @csrf

    <input type="text" name="title" placeholder="Titre du cours"
           class="w-full border p-2 mb-3" required>

    <input type="file" name="video" class="mb-3">
    <input type="file" name="pdf" class="mb-3">

    <button class="bg-indigo-600 text-white px-4 py-2 rounded">
        Publier
    </button>
</form>
