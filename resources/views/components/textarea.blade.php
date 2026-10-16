<textarea {{ $attributes->merge([
    'class' => 'border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-2 w-full'
]) }}>
    {{ $slot }}
</textarea>