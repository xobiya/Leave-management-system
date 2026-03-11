@props(['value'])

<label {{ $attributes->merge(['class' => 'erp-label']) }}>
    {{ $value ?? $slot }}
</label>
