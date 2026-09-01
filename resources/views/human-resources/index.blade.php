@extends('layouts.app')

@section('body')
    <x-app-shell title="Recursos Humanos">
        <section class="panel stack">
            <div>
                <p class="eyebrow">RECURSOS HUMANOS</p>
                <h2>Panel general</h2>
                <p class="fine-print">Consulta central de personal, incidencias y movimientos administrativos.</p>
            </div>

            <div class="metrics-grid">
                <article class="metric-card">
                    <span>Personal activo</span>
                    <strong>0</strong>
                </article>
                <article class="metric-card">
                    <span>Incidencias abiertas</span>
                    <strong>0</strong>
                </article>
                <article class="metric-card">
                    <span>Movimientos del mes</span>
                    <strong>0</strong>
                </article>
            </div>

            <div class="empty-state">
                <strong>Sin movimientos registrados</strong>
                <p class="fine-print">Los movimientos de Recursos Humanos apareceran en esta vista.</p>
            </div>
        </section>
    </x-app-shell>
@endsection
