<!doctype html>
<html lang="es" data-selected-section="{{ $selectedSection }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Administracion de Plazas</title>
        <link rel="stylesheet" href="{{ asset('modules/rentas360-plazas/styles.css') }}?v={{ filemtime(public_path('modules/rentas360-plazas/styles.css')) }}">
        <script>
            if (window.parent !== window) {
                document.documentElement.classList.add('plazas-panel-embedded');
            }
        </script>
        <style>
            :root {
                --bg: #f4f7fb;
                --line: #d9e2ef;
                --teal: #176b87;
                --teal-dark: #0e536a;
            }

            html,
            body {
                min-height: 100%;
            }

            body {
                overflow-x: hidden;
            }

            html.plazas-panel-embedded,
            html.plazas-panel-embedded body {
                min-height: 0;
                overflow-y: clip;
            }

            .app-shell {
                display: block;
                min-height: 100vh;
            }

            html.plazas-panel-embedded .app-shell {
                min-height: 0;
            }

            .workspace {
                min-width: 0;
                padding: 0 0 24px;
            }

            .module-chrome {
                position: sticky;
                top: 0;
                z-index: 25;
                border-bottom: 1px solid var(--line);
                background: rgba(255, 255, 255, .97);
                box-shadow: 0 5px 14px rgba(35, 48, 73, .06);
            }

            .topbar {
                align-items: center;
                margin: 0;
                padding: 16px 20px 12px;
            }

            .topbar h2 {
                font-size: 21px;
            }

            .topbar .muted {
                max-width: 900px;
                margin-top: 4px;
            }

            .month-chip {
                min-height: 34px;
            }

            .nav-tabs {
                display: none !important;
                gap: 5px;
                padding: 0 20px 10px;
                overflow-x: auto;
                scrollbar-width: thin;
            }

            .nav-tab {
                display: inline-flex;
                flex: 0 0 auto;
                grid-template-columns: none;
                width: auto;
                min-height: 34px;
                padding: 7px 10px;
                border: 1px solid transparent;
                white-space: nowrap;
                font-size: 11px;
            }

            .nav-tab:hover,
            .nav-tab.is-active {
                border-color: #c8dce5;
            }

            .nav-tab svg {
                width: 16px;
                height: 16px;
            }

            .content-area {
                padding: 20px;
            }

            html[data-selected-section="dashboard"] .module-chrome,
            html[data-selected-section="administration"] .module-chrome,
            html[data-selected-section="contracts"] .module-chrome,
            html[data-selected-section="marketplace"] .module-chrome,
            html[data-selected-section="tenants"] .module-chrome,
            html[data-selected-section="properties"] .module-chrome {
                display: none;
            }

            html[data-selected-section="dashboard"] .content-area,
            html[data-selected-section="administration"] .content-area,
            html[data-selected-section="contracts"] .content-area,
            html[data-selected-section="marketplace"] .content-area,
            html[data-selected-section="tenants"] .content-area,
            html[data-selected-section="properties"] .content-area {
                padding: 28px 30px 32px;
            }

            html[data-selected-section="administration"] .workspace,
            html[data-selected-section="administration"] .content-area {
                padding-bottom: 0;
            }

            html[data-selected-section="administration"] .property-administration-content {
                height: clamp(376px, calc(45vh + 56px), 676px);
            }

            html[data-selected-section="contracts"] .workspace,
            html[data-selected-section="contracts"] .content-area {
                padding-bottom: 0;
            }

            html[data-selected-section="contracts"] .plaza-contracts-page {
                min-height: calc(100vh - 28px);
                grid-template-rows: auto minmax(0, 1fr);
            }

            html[data-selected-section="contracts"] .plaza-catalog-content,
            html[data-selected-section="contracts"] .property-legal-section {
                min-height: 0;
                height: 100%;
            }

            @media (max-width: 760px) {
                .workspace {
                    padding: 0 0 18px;
                }

                .topbar {
                    grid-template-columns: minmax(0, 1fr);
                    gap: 9px;
                    padding: 14px 14px 10px;
                }

                .topbar .muted {
                    display: none;
                }

                .month-chip {
                    align-self: start;
                    justify-self: start;
                    min-height: 30px;
                }

                .nav-tabs {
                    grid-template-columns: none;
                    flex-wrap: wrap;
                    overflow-x: visible;
                    padding: 0 14px 9px;
                }

                .nav-tab {
                    flex: 1 1 calc(50% - 5px);
                    justify-content: center;
                    min-width: 0;
                    white-space: normal;
                    text-align: center;
                }

                .content-area {
                    padding: 14px;
                }

                html[data-selected-section="dashboard"] .content-area,
                html[data-selected-section="administration"] .content-area,
                html[data-selected-section="contracts"] .content-area,
                html[data-selected-section="marketplace"] .content-area {
                    padding: 14px;
                }

                html[data-selected-section="administration"] .workspace,
                html[data-selected-section="administration"] .content-area {
                    padding-bottom: 0;
                }

                html[data-selected-section="administration"] .property-administration-content {
                    height: clamp(312px, calc(55vh + 32px), 552px);
                }

                html[data-selected-section="contracts"] .workspace,
                html[data-selected-section="contracts"] .content-area {
                    padding-bottom: 0;
                }

                html[data-selected-section="contracts"] .plaza-contracts-page {
                    min-height: calc(100vh - 14px);
                }

                html[data-selected-section="contracts"] .property-legal-section {
                    min-height: 320px;
                }

                .property-card header {
                    flex-wrap: wrap;
                }

                .property-card .role-badge {
                    max-width: 100%;
                    white-space: normal;
                }
            }
        </style>
    </head>
    <body>
        <section id="loginView" hidden aria-hidden="true">
            <form id="loginForm">
                <select id="loginUserSelect" aria-label="Usuario"></select>
                <input id="loginUsername" name="username">
                <input id="loginPassword" name="password" type="password">
                <p id="loginError" hidden></p>
                <table><tbody id="credentialsTable"></tbody></table>
            </form>
        </section>

        <div hidden aria-hidden="true">
            <button id="logoutButton" type="button"></button>
            <div id="sessionSummary"></div>
            <select id="roleSelect" aria-label="Rol"></select>
            <select id="userSelect" aria-label="Usuario activo"></select>
        </div>

        <div id="appShell" class="app-shell" hidden>
            <main class="workspace">
                <div class="module-chrome">
                    <header class="topbar">
                        <div>
                            <p id="roleEyebrow" class="eyebrow"></p>
                            <h2 id="viewTitle">Catalogo de unidades</h2>
                            <p id="viewSubtitle" class="muted"></p>
                        </div>

                        <div class="month-chip" aria-label="Periodo de cobro">
                            <span id="currentMonth"></span>
                        </div>
                    </header>

                    <nav id="navTabs" class="nav-tabs" aria-label="Secciones de Administracion de Plazas" aria-hidden="true"></nav>
                </div>

                <section id="contentArea" class="content-area"></section>
            </main>
        </div>

        <div id="modalBackdrop" class="modal-backdrop" hidden>
            <section class="modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
                <div class="modal-header">
                    <div>
                        <p id="modalEyebrow" class="eyebrow"></p>
                        <h3 id="modalTitle"></h3>
                    </div>
                    <button id="modalClose" class="icon-button" type="button" aria-label="Cerrar">
                        <span data-icon="x" aria-hidden="true"></span>
                    </button>
                </div>
                <div id="modalBody" class="modal-body"></div>
            </section>
        </div>

        <script>
            window.RENTAS360_PANEL = {
                slug: 'plazas-superadmin',
                userId: 'u-super-admin',
                roleId: 'superadmin',
                defaultTab: @json($defaultTab),
                selectedSection: @json($selectedSection),
                displayName: @json(e(auth()->user()->name))
            };

            try {
                localStorage.setItem('rentas360-plazas-superadmin-session-v1', 'u-super-admin');
            } catch (error) {
                // The module can still render its seeded data if storage is unavailable.
            }
        </script>
        <script src="{{ asset('modules/rentas360-plazas/app.js') }}?v={{ filemtime(public_path('modules/rentas360-plazas/app.js')) }}"></script>
    </body>
</html>
