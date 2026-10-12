@props(['lines' => 1, 'class' => ''])
<div class="space-y-2 {{ $class }}" {{ $attributes }}>
    @for ($i = 0; $i < $lines; $i++)
        <div class="skeleton" style="height: {{ $i === 0 ? '1.25rem' : '0.875rem' }}"></div>
    @endfor
</div>