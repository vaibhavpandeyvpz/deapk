<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} &hyphen; Decompile APK Online</title>
    <meta name="description" content="DeAPK is an open-source, online APK decompiler which lets you upload an APK and then decompile it to Smali or Java sources. It is built using Laravel, Vue.js, Bootstrap, FontAwesome, Pusher, Redis, MySQL, apktool, jadx and hosted atop DigitalOcean cloud platform.">
    <meta name="keywords" content="android decompiler, apk decompiler, java decompiler, online apk decompiler, decompile apk online">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=IBM+Plex+Sans:400,700" rel="stylesheet">
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    @if ($gap = config('services.google_analytics.property'))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gap }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ $gap }}');
        </script>
    @endif
</head>
<body>
<header class="sticky-top mb-3">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary py-1">
        <div class="container">
            <a class="navbar-brand" href="/">{{ config('app.name', 'Laravel') }} &hyphen; Decompile APK Online</a>
        </div>
    </nav>
</header>
<main class="container mb-3" id="app">
    <h1><i class="fas fa-circle-notch fa-spin"></i> Loading&hellip;</h1>
    <p class="text-muted">Please wait the app is being loaded.</p>
</main>
<div class="bg-success py-1">
    <div class="container text-right">
        <small>
            <strong>apktool:</strong>
            {{ config('versions.apktool') }}
            &hyphen;
            <strong>jadx:</strong>
            {{ config('versions.jadx') }}
        </small>
    </div>
</div>
<footer class="bg-dark text-white">
    <div class="container py-3">
        <ul class="list-inline">
            <li class="list-inline-item">
                <a class="text-white"
                   href="https://www.facebook.com/sharer.php?u=https://deapk.vaibhavpandey.com/"
                   target="_blank">
                    <i class="fab fa-facebook fa-2x"></i>
                </a>
            </li>
            <li class="list-inline-item">
                <a class="text-white"
                   href="https://twitter.com/share?url=https://deapk.vaibhavpandey.com/&text=DeAPK+-+Online+APK+Decompiler"
                   target="_blank">
                    <i class="fab fa-twitter fa-2x"></i>
                </a>
            </li>
            <li class="list-inline-item">
                <a class="text-white"
                   href="https://pinterest.com/pin/create/button/?url=https://deapk.vaibhavpandey.com/&description=DeAPK+-+Online+APK+Decompiler"
                   target="_blank">
                    <i class="fab fa-pinterest fa-2x"></i>
                </a>
            </li>
            <li class="list-inline-item">
                <a class="text-white" href="https://github.com/vaibhavpandeyvpz/deapk" target="_blank">
                    <i class="fab fa-github fa-2x"></i>
                </a>
            </li>
        </ul>
        <h5 class="text-uppercase mb-2">Disclaimer</h5>
        <p class="text-justify text-muted text-packed mb-1">
            <small>
                This website neither intends to perform nor promote unauthorised or illegal reproduction of source code.
                It is to be used by original developers for testing their app against reverse-engineering before
                distributing to marketplaces.
                <span class="text-uppercase">Please use it for legitimate purposes only.</span>
            </small>
        </p>
        <p class="text-right mb-0">
            <small>Vaibhav Pandey -aka- VPZ &copy; {{ date('Y') }}.</small>
        </p>
    </div>
</footer>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
<script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
