<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-soft transition hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-300']) }}>
    {{ $slot }}
</button>
