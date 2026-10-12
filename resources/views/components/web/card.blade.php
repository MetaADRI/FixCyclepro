@props(['class' => '', 'as' => 'div'])
<{{ $as }} {{ $attributes->merge(['class' => 'card ' . $class]) }}>
    {{ $slot }}
</{{ $as }}>