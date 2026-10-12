@props(['icon' => 'map', 'title' => 'Nothing here yet', 'description' => ''])
<div class="flex flex-col items-center justify-center px-8 py-16 text-center" {{ $attributes }}>
    <div class="brand-bg brand-contrast mb-4 flex h-14 w-14 items-center justify-center rounded-3xl">
        <x-web.icon :name="$icon" class="h-7 w-7" />
    </div>
    <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $title }}</h3>
    @if ($description)
        <p class="mt-1 max-w-sm text-sm text-slate-500 dark:text-slate-400">{{ $description }}</p>
    @endif
    {{ $slot }}
</div>