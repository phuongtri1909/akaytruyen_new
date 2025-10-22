<!doctype html>
<html lang="en">

<head>
    <script src="https://cmp.gatekeeperconsent.com/min.js" data-cfasync="false"></script>
    <script src="https://the.gatekeeperconsent.com/cmp.min.js" data-cfasync="false"></script>

    <script async src="//www.ezojs.com/ezoic/sa.min.js"></script>
    <script>
        window.ezstandalone = window.ezstandalone || {};
        ezstandalone.cmd = ezstandalone.cmd || [];
    </script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="index, follow">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.ga4.measurement_id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', '{{ config('services.ga4.measurement_id') }}');
    </script>

    <link
        href="https://fonts.googleapis.com/css2?family=Mooli&family=Patrick+Hand&family=Roboto+Condensed:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&family=Noto+Sans:wght@400;700&family=Noto+Serif:wght@400;700&family=Lora:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    @vite(['resources/assets/frontend/css/styles.css'])
    @vite(['resources/assets/frontend/css/app.css'])

    @stack('styles')

    @stack('custom_schema')

    @routes
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-4405345005005059"
     crossorigin="anonymous"></script>
</head>

<body @if ($bgColorCookie == 'dark') class="dark-theme" @endif>

    @include('Frontend.layouts.header')

    <main>
        @yield('content')
    </main>

    @include('Frontend.components.floating_tools')

    @include('Frontend.layouts.footer')

    @include('Frontend.layouts.script_default')

    @stack('scripts')

    @include('Frontend.snippets.loading_full')
    @include('Frontend.components.top_button')
</body>

</html>
