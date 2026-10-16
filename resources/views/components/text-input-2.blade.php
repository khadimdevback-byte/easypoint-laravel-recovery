@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'block w-full bg-secondary border-t-0 border-r-0 border-l-0 border-b-2 text-black border-primary-color py-3 mt-3 focus:ring-0 focus:border-primary-color rounded-md pl-4']) }}>
