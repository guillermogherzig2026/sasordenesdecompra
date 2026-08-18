@extends('layouts.app')

@section('body')
    <x-app-shell title="Tabulador de precios unitarios">
        <section class="panel unit-price-panel" data-no-section-export>
            <div class="panel-header">
                <div class="panel-header-title">
                    <h2>Tabulador general de precios unitarios</h2>
                    <p class="fine-print">Gobierno de la Ciudad de Mexico · Actualizacion enero 2026</p>
                </div>
                <a class="button ghost" href="{{ route('construction.dashboard') }}">Panel de obra</a>
            </div>

            <div class="unit-price-summary" aria-label="Informacion del catalogo">
                <span><strong>{{ number_format($unitPrices->total()) }}</strong> conceptos encontrados</span>
                <span><strong>27.47%</strong> indirecto integrado en el P.U. oficial</span>
            </div>

            <form class="unit-price-filters" method="GET" action="{{ route('construction.placeholder', 'tabulador-precios-unitarios') }}">
                <label>
                    Buscar concepto
                    <input name="q" value="{{ $search }}" placeholder="Clave, concepto o unidad">
                </label>
                <label>
                    Capitulo
                    <select name="chapter">
                        <option value="">Todos los capitulos</option>
                        @foreach ($chapters as $chapter)
                            <option value="{{ $chapter->chapter_code }}" @selected($selectedChapter === $chapter->chapter_code)>
                                {{ $chapter->chapter_code }} · {{ $chapter->chapter_name }}
                            </option>
                        @endforeach
                    </select>
                </label>
                <div class="unit-price-filter-actions">
                    <button class="button primary" type="submit">Buscar</button>
                    <a class="button ghost" href="{{ route('construction.placeholder', 'tabulador-precios-unitarios') }}">Limpiar</a>
                </div>
            </form>

            <p class="unit-price-source-note">
                El documento fuente publica unicamente el P.U. integrado. Las columnas separadas de mano de obra y materiales se conservan como datos independientes sin asignar valores estimados.
            </p>

            <div class="table-scroll unit-price-scroll">
                <table class="unit-price-table" data-filter-search>
                    <thead>
                        <tr>
                            <th>Clave</th>
                            <th>Concepto de obra</th>
                            <th>Unidad</th>
                            <th>P.U. mano de obra</th>
                            <th>P.U. materiales</th>
                            <th>P.U. total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($unitPrices as $unitPrice)
                            <tr>
                                <td data-filter-value="{{ $unitPrice->code }}">
                                    <strong>{{ $unitPrice->code }}</strong>
                                    @if ($unitPrice->source_page)
                                        <small>Pagina {{ $unitPrice->source_page }}</small>
                                    @endif
                                </td>
                                <td>{{ $unitPrice->description }}</td>
                                <td>{{ $unitPrice->unit }}</td>
                                <td class="unit-price-amount">
                                    @if ($unitPrice->labor_unit_price !== null)
                                        ${{ number_format((float) $unitPrice->labor_unit_price, 2) }}
                                    @else
                                        <span class="unit-price-unavailable">No publicado</span>
                                    @endif
                                </td>
                                <td class="unit-price-amount">
                                    @if ($unitPrice->material_unit_price !== null)
                                        ${{ number_format((float) $unitPrice->material_unit_price, 2) }}
                                    @else
                                        <span class="unit-price-unavailable">No publicado</span>
                                    @endif
                                </td>
                                <td class="unit-price-amount unit-price-total">${{ number_format((float) $unitPrice->total_unit_price, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="empty-state" colspan="6">No hay conceptos que coincidan con los filtros.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-bar unit-price-pagination">
                <span>
                    @if ($unitPrices->total())
                        Mostrando {{ number_format($unitPrices->firstItem()) }} a {{ number_format($unitPrices->lastItem()) }} de {{ number_format($unitPrices->total()) }}
                    @else
                        Sin resultados
                    @endif
                </span>
                <div class="filter-actions">
                    @if ($unitPrices->onFirstPage())
                        <span class="button ghost small disabled">Anterior</span>
                    @else
                        <a class="button ghost small" href="{{ $unitPrices->previousPageUrl() }}">Anterior</a>
                    @endif

                    <span>Pagina {{ $unitPrices->currentPage() }} de {{ $unitPrices->lastPage() }}</span>

                    @if ($unitPrices->hasMorePages())
                        <a class="button ghost small" href="{{ $unitPrices->nextPageUrl() }}">Siguiente</a>
                    @else
                        <span class="button ghost small disabled">Siguiente</span>
                    @endif
                </div>
            </div>
        </section>
    </x-app-shell>
@endsection
