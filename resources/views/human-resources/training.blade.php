<!doctype html>
<html lang="es-MX">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title }}</title>
        <link rel="stylesheet" href="{{ asset('hr-suite/assets/css/styles.css') }}">
        <link rel="stylesheet" href="{{ asset('modules/human-resources/training.css') }}">
    </head>
    <body class="hr-suite-embedded training-embedded">
        <div id="app"></div>
        <div id="toast-region" class="toast-region" aria-live="polite"></div>
        <script type="module" src="{{ asset('modules/human-resources/training.js') }}"></script>
    </body>
</html>
