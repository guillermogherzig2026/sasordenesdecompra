<!doctype html>
<html lang="es-MX">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Módulo de Recursos Humanos">
        <base href="{{ rtrim(asset('hr-suite'), '/') }}/">
        <title>{{ $title }}</title>
        <link rel="stylesheet" href="{{ asset('hr-suite/assets/css/styles.css') }}">
        <style>
            html {
                min-height: 0;
                overflow: hidden;
                background: transparent;
            }

            body.hr-suite-embedded {
                min-height: 0;
                overflow: hidden;
                background: transparent;
            }

            .hr-embedded-app,
            body.hr-suite-embedded .main {
                min-height: 0;
            }

            body.hr-suite-embedded .content {
                width: 100%;
                max-width: none;
                padding: 0;
            }
        </style>
    </head>
    <body class="hr-suite-embedded">
        <div id="app" aria-live="polite"></div>
        <div id="toast-region" class="toast-region" aria-live="assertive"></div>
        @php
            $hrSuiteConfig = [
                'embedded' => true,
                'route' => $appRoute,
                'registeredCompanies' => $registeredCompanies,
            ];
        @endphp
        <script>
            window.HR_SUITE_CONFIG = {{ Illuminate\Support\Js::from($hrSuiteConfig) }};
        </script>
        <script type="module" src="{{ asset('hr-suite/assets/js/app.js') }}"></script>
    </body>
</html>
