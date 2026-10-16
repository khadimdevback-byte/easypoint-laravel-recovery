<button {{ $attributes->merge(['type' => 'submit', 'class' => 'bg-primary items-center px-5 lg:px-5 xl:px-10 py-2 border-transparent rounded-md  text-white font-semibold']) }}>
    {{ $slot }}
</button>