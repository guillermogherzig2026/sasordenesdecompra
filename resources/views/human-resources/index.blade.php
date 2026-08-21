@extends('layouts.app')

@section('body')
    <x-app-shell :title="$title">
        <div class="human-resources-frame-shell" data-human-resources-frame-shell>
            <iframe
                class="human-resources-frame"
                src="{{ route('human-resources.embed', ['section' => $section]) }}"
                title="{{ $title }}"
                data-human-resources-frame
                loading="eager"
                scrolling="no"
            ></iframe>
        </div>

        <style>
            .human-resources-frame-shell {
                min-width: 0;
                overflow: visible;
                background: transparent;
            }

            .human-resources-frame {
                display: block;
                width: 100%;
                height: 680px;
                min-height: 680px;
                border: 0;
                background: transparent;
            }

            @media (max-width: 760px) {
                .human-resources-frame {
                    height: 620px;
                    min-height: 620px;
                }
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const frame = document.querySelector('[data-human-resources-frame]');
                if (!frame) return;

                let resizeObserver;
                let mutationObserver;
                let animationFrame;

                const minimumHeight = () => window.matchMedia('(max-width: 760px)').matches ? 620 : 680;
                const scheduleHeightSync = () => {
                    window.cancelAnimationFrame(animationFrame);
                    animationFrame = window.requestAnimationFrame(() => {
                        const document = frame.contentDocument;
                        const app = document?.getElementById('app');
                        if (!document || !app) return;

                        const contentHeight = Math.max(
                            app.scrollHeight,
                            Math.ceil(app.getBoundingClientRect().height)
                        );
                        const nextHeight = Math.max(contentHeight, minimumHeight());

                        if (Math.abs(frame.getBoundingClientRect().height - nextHeight) > 1) {
                            frame.style.height = `${nextHeight}px`;
                        }
                    });
                };

                const observeEmbeddedView = () => {
                    resizeObserver?.disconnect();
                    mutationObserver?.disconnect();

                    const document = frame.contentDocument;
                    const app = document?.getElementById('app');
                    if (!document || !app) return;

                    resizeObserver = new ResizeObserver(scheduleHeightSync);
                    resizeObserver.observe(app);

                    mutationObserver = new MutationObserver(scheduleHeightSync);
                    mutationObserver.observe(app, {
                        childList: true,
                        subtree: true,
                        attributes: true,
                    });

                    scheduleHeightSync();
                };

                frame.addEventListener('load', observeEmbeddedView);
                window.addEventListener('resize', scheduleHeightSync, { passive: true });

                if (frame.contentDocument?.readyState === 'complete') {
                    observeEmbeddedView();
                }
            });
        </script>
    </x-app-shell>
@endsection
