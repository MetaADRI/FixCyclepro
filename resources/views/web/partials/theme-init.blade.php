{{--
    Applies the saved (or system) colour scheme before first paint so the
    page never flashes white when dark mode is active.
--}}
<script>
    (function () {
        try {
            var stored = localStorage.getItem('fixcycle_theme_mode');
            var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            var mode = stored || (prefersDark ? 'dark' : 'light');
            document.documentElement.classList.toggle('dark', mode === 'dark');
            document.documentElement.style.colorScheme = mode;
        } catch (e) {}
    })();
</script>
