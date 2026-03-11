<button {{ $attributes->merge(['type' => 'submit', 'class' => 'erp-button']) }}>
    {{ $slot }}
</button>
