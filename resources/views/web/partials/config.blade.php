{{--
    Server-injected runtime config for the web PWA.
    Read by resources/js/api.js to authenticate merchant API calls and to
    resolve the display currency. Never put merchant secrets here.
--}}
<script>
    window.FixConfig = {!! json_encode([
        'merchantId' => config('web.merchant_id'),
        'accessPin' => config('web.access_pin'),
        'currency' => config('web.currency'),
        'currencySymbol' => config('web.currency_symbol'),
    ]) !!};
</script>
