@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-md text-black']) }}>
    {{ $value ?? $slot }}
</label>
