@props(['order', 'dialogId'])

<button class="button ghost small" type="button" data-supply-detail-open="{{ $dialogId }}">Ver</button>

<dialog class="supply-detail-dialog" id="{{ $dialogId }}" data-supply-detail-dialog>
    <div class="supply-detail-card">
        <button class="supply-detail-close" type="button" data-supply-detail-close aria-label="Cerrar">×</button>
        <div>
            <h3>Partidas de {{ $order->folio }}</h3>
            <p class="fine-print">Detalle de insumos solicitados en esta orden de suministro.</p>
        </div>

        <div class="supply-detail-lines" role="table" aria-label="Detalle de partidas">
            <div class="supply-detail-line supply-detail-head" role="row">
                <span>Cantidad</span>
                <span>Unidad</span>
                <span>Descripcion del insumo</span>
                <span>Precio unitario</span>
                <span>Precio total</span>
            </div>
            @foreach ($order->items as $item)
                <div class="supply-detail-line" role="row">
                    <span>{{ number_format((float) $item->quantity, 2) }}</span>
                    <span>{{ $item->catalogItem?->unit ?: 'unidad' }}</span>
                    <span>
                        <strong>{{ $item->article }}</strong>
                        @if ($item->catalogItem?->description)
                            <small class="fine-print">{{ $item->catalogItem->description }}</small>
                        @endif
                    </span>
                    <span>${{ number_format((float) $item->unit_cost, 2) }}</span>
                    <span>${{ number_format((float) $item->line_total, 2) }}</span>
                </div>
            @endforeach
        </div>
    </div>
</dialog>
