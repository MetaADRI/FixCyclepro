{{--
    Phase 4: mobile off-canvas drawer backdrop for the admin menubar.
    The theme's Site.js owns the open/hide state (body.site-menubar-open);
    this only mirrors that state into a tappable backdrop + Escape handling
    on phones. Vanilla JS, no dependencies, no behaviour change on desktop.
--}}
<script>
(function () {
    'use strict';
    var MOBILE = '(max-width: 767.98px)';
    var body = document.body;
    var backdrop = document.createElement('div');
    backdrop.className = 'site-menubar-backdrop';
    backdrop.setAttribute('aria-hidden', 'true');

    function isMobile() {
        return window.matchMedia ? window.matchMedia(MOBILE).matches : false;
    }

    function isOpen() {
        return body.classList.contains('site-menubar-open');
    }

    function attachBackdrop() {
        if (!body.contains(backdrop)) {
            body.appendChild(backdrop);
        }
    }

    function detachBackdrop() {
        if (backdrop.parentNode) {
            backdrop.parentNode.removeChild(backdrop);
        }
    }

    function closeMenubar() {
        var toggles = document.querySelectorAll('[data-toggle="menubar"]');
        for (var i = 0; i < toggles.length; i++) {
            var el = toggles[i];
            if (el.offsetParent !== null || el.getClientRects().length) {
                el.click();
                return;
            }
        }
        body.classList.remove('site-menubar-open');
    }

    function sync() {
        if (isMobile() && isOpen()) {
            attachBackdrop();
        } else {
            detachBackdrop();
        }
    }

    backdrop.addEventListener('click', closeMenubar);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && isOpen()) {
            closeMenubar();
        }
    });

    if (window.MutationObserver) {
        new MutationObserver(sync).observe(body, { attributes: true, attributeFilter: ['class'] });
    }

    var mql = window.matchMedia ? window.matchMedia(MOBILE) : null;
    if (mql) {
        if (mql.addEventListener) {
            mql.addEventListener('change', sync);
        } else if (mql.addListener) {
            mql.addListener(sync);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', sync);
    } else {
        sync();
    }
})();
</script>
