@props(['name', 'label', 'rows' => 4])

<div class="mb-6">
    <label class="block text-sm font-semibold text-gray-700 mb-2">
        {{ $label }}
    </label>

    <textarea
        name="{{ $name }}"
        rows="{{ $rows }}"
        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-400 focus:border-indigo-500"
    >{{ old($name) }}</textarea>

    @error($name)
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
