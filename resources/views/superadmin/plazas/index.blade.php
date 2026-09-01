@extends('layouts.app')

@section('body')
    <x-app-shell title="Administracion de Plazas">
        @php
            $panelUrl = $selectedSection === \App\Support\PlazaNavigation::defaultSection()
                ? route('superadmin.plazas.panel')
                : route('superadmin.plazas.panel', ['section' => $selectedSection]);
        @endphp
        <section class="plazas-module-host" aria-label="Panel de Administracion de Plazas">
            <iframe
                class="plazas-module-frame"
                src="{{ $panelUrl }}"
                title="Administracion de Plazas"
                sandbox="allow-scripts allow-same-origin allow-downloads allow-modals"
                scrolling="no"
            ></iframe>
        </section>

        <style>
            .plazas-module-host {
                min-width: 0;
                min-height: calc(100vh - 130px);
            }

            .plazas-module-frame {
                display: block;
                width: 100%;
                height: calc(100vh - 130px);
                min-height: 720px;
                overflow: hidden;
                border: 1px solid var(--line);
                border-radius: 8px;
                background: #fff;
            }

            @media (max-width: 760px) {
                .plazas-module-host,
                .plazas-module-frame {
                    min-height: calc(100vh - 112px);
                }

                .plazas-module-frame {
                    height: calc(100vh - 112px);
                    border-right: 0;
                    border-left: 0;
                    border-radius: 0;
                }
            }
        </style>

        <script>
            (() => {
                const frame = document.querySelector('.plazas-module-frame');
                if (!frame) return;

                let frameResizeRequest = 0;
                let contentResizeObserver = null;
                let contentMutationObserver = null;

                const minimumFrameHeight = () => Math.max(
                    720,
                    window.innerHeight - (window.innerWidth <= 760 ? 112 : 130)
                );

                const resizeFrame = () => {
                    window.cancelAnimationFrame(frameResizeRequest);
                    frameResizeRequest = window.requestAnimationFrame(() => {
                        const panelDocument = frame.contentDocument;
                        if (!panelDocument?.body) return;

                        const panelContent = panelDocument.querySelector('#appShell');
                        const contentHeight = Math.max(
                            panelDocument.body.scrollHeight,
                            panelDocument.body.offsetHeight,
                            panelContent?.scrollHeight || 0,
                            panelContent?.offsetHeight || 0
                        );
                        const frameBorderHeight = frame.offsetHeight - frame.clientHeight;

                        frame.style.height = `${Math.ceil(Math.max(contentHeight, minimumFrameHeight()) + frameBorderHeight)}px`;
                    });
                };

                const observePanelContent = () => {
                    contentResizeObserver?.disconnect();
                    contentMutationObserver?.disconnect();

                    const panelDocument = frame.contentDocument;
                    const panelContent = panelDocument?.querySelector('#appShell');
                    if (!panelDocument?.body || !panelContent) return;

                    panelDocument.documentElement.style.overflowY = 'clip';
                    panelDocument.body.style.overflowY = 'clip';
                    panelDocument.documentElement.scrollTop = 0;
                    panelDocument.body.scrollTop = 0;
                    frame.contentWindow?.scrollTo(0, 0);

                    if ('ResizeObserver' in window) {
                        contentResizeObserver = new ResizeObserver(resizeFrame);
                        contentResizeObserver.observe(panelContent);
                    }

                    contentMutationObserver = new MutationObserver(resizeFrame);
                    contentMutationObserver.observe(panelContent, {
                        childList: true,
                        subtree: true,
                    });

                    resizeFrame();
                    window.setTimeout(resizeFrame, 100);
                };

                frame.addEventListener('load', observePanelContent);
                window.addEventListener('resize', resizeFrame);

                if (frame.contentDocument?.readyState === 'complete') {
                    observePanelContent();
                }
            })();
        </script>
    </x-app-shell>
@endsection
