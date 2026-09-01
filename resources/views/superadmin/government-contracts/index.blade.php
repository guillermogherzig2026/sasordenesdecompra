@extends('layouts.app')

@section('body')
    @php
        $panelUrl = $selectedSection === \App\Support\GovernmentContractNavigation::defaultSection()
            ? route('superadmin.government-contracts.panel')
            : route('superadmin.government-contracts.panel', ['section' => $selectedSection]);
    @endphp

    <x-app-shell title="Contratos Gobierno">
        <section class="government-contracts-host" aria-label="Panel de Contratos Gobierno">
            <iframe
                class="government-contracts-frame"
                src="{{ $panelUrl }}"
                title="Contratos Gobierno"
                sandbox="allow-scripts allow-same-origin allow-downloads allow-modals"
            ></iframe>
        </section>

        <style>
            .government-contracts-host {
                min-width: 0;
                min-height: calc(100vh - 130px);
            }

            .government-contracts-frame {
                display: block;
                width: 100%;
                height: calc(100vh - 130px);
                min-height: 760px;
                border: 1px solid var(--line);
                border-radius: 8px;
                background: #fff;
            }

            @media (max-width: 760px) {
                .government-contracts-host,
                .government-contracts-frame {
                    min-height: calc(100vh - 112px);
                }

                .government-contracts-frame {
                    height: calc(100vh - 112px);
                    border-right: 0;
                    border-left: 0;
                    border-radius: 0;
                }
            }
        </style>
    </x-app-shell>
@endsection
