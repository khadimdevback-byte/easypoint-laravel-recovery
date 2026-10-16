@props([
    'name',
    'id' => null,
    'options' => [], // Tableau des options (id => valeur)
    'selected' => null, // Valeur sélectionnée
    'disabled' => false,
])

<div class="mb-4">
    <select name="{{ $name }}" id="{{ $name }}" {{ $disabled ? 'disabled' : '' }}
        class="block w-full bg-secondary focus:bg-secondary border-t-0 border-r-0 border-l-0 border-b-2  border-primary-color py-3 mt-3 focus:ring-0 focus:border-primary-color rounded-md pl-4"
        {{ $attributes }}>
        <option value="">Sélectionnez...</option>
        @foreach ($options as $key => $value)
            <option value="{{ $key }}" {{ $selected == $key ? 'selected' : '' }}>
                {{ $value }}
            </option>
        @endforeach
    </select>
</div>

{{-- <div class="mb-4">
    <select id="{{ $name }}" name="{{ $name }}" {{ $disabled ? 'disabled' : '' }}
        class="tom-select block w-full bg-secondary focus:bg-secondary border-t-0 border-r-0 border-l-0 border-b-2 border-primary-color py-3 mt-3 focus:ring-0 focus:border-primary-color rounded-md pl-4"
        {{ $attributes }}>
        <option value="">Sélectionnez...</option>
        @foreach ($options as $key => $value)
            <option value="{{ $key }}" {{ $selected == $key ? 'selected' : '' }}>{{ $value }}</option>
        @endforeach
    </select>
</div> --}}






