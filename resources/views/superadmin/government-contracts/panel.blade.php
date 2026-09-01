<!doctype html>
<html class="government-contracts-parent-navigation" lang="es" data-selected-module="{{ $selectedModule }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Contratos Gobierno</title>
        <link rel="icon" href="{{ asset('modules/contratos-gobierno/favicon.svg') }}">
        <link rel="stylesheet" href="{{ asset('modules/contratos-gobierno/app.css') }}?v={{ filemtime(public_path('modules/contratos-gobierno/app.css')) }}">
        <link rel="stylesheet" href="{{ asset('modules/contratos-gobierno/contract-carousel.css') }}?v={{ filemtime(public_path('modules/contratos-gobierno/contract-carousel.css')) }}">
        <style>
            .government-contracts-parent-navigation .app-shell {
                grid-template-columns: minmax(0, 1fr) !important;
            }

            .government-contracts-parent-navigation .sidebar,
            .government-contracts-parent-navigation .mobile-tabbar {
                display: none !important;
            }

            .government-contracts-parent-navigation .workspace {
                min-width: 0;
            }

            @media (max-width: 960px) {
                .government-contracts-parent-navigation .app-shell {
                    display: grid !important;
                    padding-bottom: 0 !important;
                }
            }
        </style>
    </head>
    <body>
        <div id="root"></div>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const targetModule = document.documentElement.dataset.selectedModule;
                let attempts = 0;

                const activateModule = () => {
                    const targetButton = Array.from(document.querySelectorAll('.sidebar nav button'))
                        .find((button) => {
                            const label = button.querySelector('strong')?.firstChild?.textContent?.trim();

                            return label === targetModule;
                        });

                    if (targetButton) {
                        if (!targetButton.classList.contains('active')) targetButton.click();
                        return;
                    }

                    attempts += 1;
                    if (attempts < 80) window.setTimeout(activateModule, 25);
                };

                activateModule();
            });
        </script>
        <script type="module" src="{{ asset('modules/contratos-gobierno/app.js') }}?v={{ filemtime(public_path('modules/contratos-gobierno/app.js')) }}"></script>
        <script src="{{ asset('modules/contratos-gobierno/contract-carousel.js') }}?v={{ filemtime(public_path('modules/contratos-gobierno/contract-carousel.js')) }}" defer></script>
    </body>
</html>
