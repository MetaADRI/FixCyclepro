@props(['name' => '', 'class' => 'h-5 w-5'])
<svg class="{{ $class }}" {{ $attributes }} aria-hidden="true">
    <use href="#i-{{ $name }}"/>
</svg>