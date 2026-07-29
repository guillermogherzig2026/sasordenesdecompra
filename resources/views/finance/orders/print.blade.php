<!doctype html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <title>{{ $order->folio }}</title>
        <style>
            @page { size: letter; margin: 12mm; }
            * { box-sizing: border-box; }
            body { margin: 0; background: #f4f7fb; color: #111; font-family: Arial, Helvetica, sans-serif; font-size: 12px; }
            .print-actions { width: 216mm; margin: 14px auto; text-align: right; }
            .print-actions button { border: 1px solid #176b87; border-radius: 6px; padding: 8px 12px; color: #fff; background: #176b87; font-weight: 700; cursor: pointer; }
            .sheet { width: 216mm; min-height: 279mm; margin: 0 auto 24px; padding: 12mm; background: #fff; }
            .oc-frame { min-height: 250mm; border: 2px solid #000; display: grid; grid-template-rows: auto auto auto auto 1fr auto; }
            .top-header { min-height: 58px; display: grid; grid-template-columns: 190px 1fr; align-items: center; border-bottom: 2px solid #000; }
            .brand { padding: 8px 10px; display: flex; align-items: center; gap: 10px; }
            .brand-mark { width: 92px; height: 40px; border-radius: 999px; display: grid; place-items: center; color: #103f8f; font-size: 30px; font-weight: 900; letter-spacing: -2px; border-top: 4px solid #7bb13c; }
            .brand-logo { max-width: 92px; max-height: 46px; object-fit: contain; display: block; }
            .company-title { text-align: center; font-size: 17px; font-weight: 900; text-transform: uppercase; line-height: 1.2; }
            .title-bar { padding: 7px; border-top: 3px solid #000; border-bottom: 2px solid #000; color: #fff; background: #082764; text-align: center; font-size: 21px; font-weight: 900; }
            .info-grid { display: grid; grid-template-columns: 1fr 1fr; border-bottom: 1px solid #777; }
            .info-row { display: grid; grid-template-columns: 185px 1fr; min-height: 25px; }
            .info-row.third { grid-template-columns: 185px 1fr 170px 1fr; }
            .label { padding: 5px 6px; background: #c8c8c8; font-weight: 900; text-align: right; border-right: 1px solid #aaa; }
            .value { padding: 5px 7px; border: 1px solid #bcbcbc; border-top: 0; min-height: 24px; }
            .value.center { text-align: center; }
            .provider-grid { display: grid; grid-template-columns: 1.35fr .75fr; border-bottom: 1px solid #777; }
            .provider-left { display: grid; grid-template-columns: 120px 1fr; }
            .provider-labels { background: #c8c8c8; padding: 12px 6px; display: grid; align-content: center; gap: 9px; text-align: right; font-weight: 900; }
            .provider-values { padding: 10px 8px; border-right: 1px solid #bbb; line-height: 1.35; }
            .provider-right { display: grid; grid-template-columns: 120px 1fr; }
            .provider-right .label-stack { background: #c8c8c8; padding: 16px 6px; display: grid; align-content: center; gap: 19px; text-align: right; font-weight: 900; }
            .provider-right .value-stack { padding: 16px 8px; display: grid; align-content: center; gap: 18px; text-align: center; border-left: 1px solid #bbb; }
            .delivery-row { display: grid; grid-template-columns: 190px 120px 1fr 120px; border-bottom: 1px solid #777; }
            .billing { display: grid; grid-template-columns: 100px 1fr; border-bottom: 2px solid #000; }
            .billing .label { border-bottom: 1px solid #777; }
            .billing .value { border-left: 0; border-right: 0; border-bottom: 1px solid #777; }
            .items { width: 100%; border-collapse: collapse; }
            .items th { border: 2px solid #000; padding: 10px 6px; font-size: 12px; text-transform: uppercase; }
            .items td { border: 2px solid #000; padding: 8px 6px; vertical-align: top; }
            .items .partida { width: 88px; text-align: center; }
            .items .cantidad { width: 90px; text-align: center; font-weight: 900; }
            .items .money { width: 120px; text-align: right; }
            .totals { width: 260px; margin-left: auto; border-collapse: collapse; font-weight: 900; }
            .totals td { padding: 4px 5px; }
            .totals .amount { border: 2px solid #000; text-align: right; font-weight: 400; }
            .totals .grand { font-size: 14px; font-weight: 900; }
            .notes-wrap { align-self: start; width: 52%; margin: 70px auto 0; border: 2px solid #000; }
            .notes-title { padding: 4px; border-bottom: 1px solid #000; background: #e7f1f7; text-align: center; font-size: 16px; }
            .notes-body { min-height: 85px; padding: 5px; font-size: 10px; line-height: 1.35; white-space: pre-line; }
            .signature { padding-bottom: 80px; text-align: center; font-weight: 900; align-self: end; }
            .muted { color: #444; }
            .strong { font-weight: 900; }
            @media print {
                body { background: #fff; }
                .print-actions { display: none; }
                .sheet { width: auto; min-height: auto; margin: 0; padding: 0; }
                .oc-frame { min-height: 250mm; }
            }
        </style>
    </head>
    <body>
        @php
            $subtotal = (float) $order->total;
            $discount = 0;
            $subtotalWithDiscount = $subtotal - $discount;
            $iva = 0;
            $total = $subtotalWithDiscount + $iva;
            $creditText = $order->is_credit
                ? 'CREDITO ' . $order->credit_days . ' DIAS A PARTIR FECHA DE SOLICITUD'
                : 'PENDIENTE DE PAGO';
            $defaultNotes = "* FECHA DE ENTREGA: " . ($order->delivery_date?->format('d/m/Y') ?? 'N/A') . "\n* PLAZO DE PAGO: " . $creditText;
            $orderNotes = trim((string) $order->observations);
            $companyNotes = trim((string) $order->company->purchase_order_notes);
            $notes = $orderNotes ?: ($companyNotes ?: $defaultNotes);
            $orderReference = $order->reference ?: $order->provider->reference;
        @endphp

        <div class="print-actions">
            <button onclick="window.print()">Imprimir / guardar PDF</button>
        </div>

        <main class="sheet">
            <section class="oc-frame">
                <header class="top-header">
                    <div class="brand">
                        @if ($order->company->logo_path)
                            <img class="brand-logo" src="{{ route('companies.logo', $order->company) }}" alt="{{ $order->company->name }}">
                        @else
                            <div class="brand-mark">{{ $order->company->initials() }}</div>
                        @endif
                    </div>
                    <div class="company-title">
                        <div>{{ $order->company->name }}</div>
                    </div>
                </header>

                <div class="title-bar">Orden de compra</div>

                <section class="info-grid">
                    <div class="info-row">
                        <div class="label">No. de Orden de Compra:</div>
                        <div class="value center">{{ str_replace('OC-', 'OC', $order->folio) }}</div>
                    </div>
                    <div class="info-row">
                        <div class="label">Fecha de elaboracion:</div>
                        <div class="value center">{{ $order->created_on?->format('d-M-y') }}</div>
                    </div>
                    <div class="info-row" style="grid-column: 1 / -1;">
                        <div class="label">Depto solicita:</div>
                        <div class="value">COMPRAS</div>
                    </div>
                </section>

                <section class="provider-grid">
                    <div class="provider-left">
                        <div class="provider-labels">
                            <div>Proveedor:</div>
                            <div>Datos<br>bancarios:</div>
                            <div>Direccion:</div>
                            <div>Contacto:<br>Telefono:</div>
                        </div>
                        <div class="provider-values">
                            <div class="strong">{{ $order->provider->business_name }}</div>
                            <div>RFC: {{ $order->provider->rfc }}</div>
                            <div>{{ $order->provider->bank }} cuenta: {{ $order->provider->account_number }}</div>
                            <div>Clabe: {{ $order->provider->clabe }}</div>
                            <br>
                            <div>N/A</div>
                            <br>
                            <div>{{ $order->buyer->name }}</div>
                            <div>N/A</div>
                        </div>
                    </div>
                    <div class="provider-right">
                        <div class="label-stack">
                            <div>Referencia</div>
                            <div>Tipo:</div>
                            <div>Concepto:</div>
                            <div>Fax:</div>
                        </div>
                        <div class="value-stack">
                            <div>{{ $orderReference ?: 'N/A' }}</div>
                            <div>{{ $order->provider->business_line }}</div>
                            <div>{{ $order->payment_concept ?: 'N/A' }}</div>
                            <div>N/A</div>
                        </div>
                    </div>
                </section>

                <section>
                    <div class="delivery-row">
                        <div class="label">Fecha de entrega propuesta:</div>
                        <div class="value center">{{ $order->delivery_date?->format('d-M-Y') }}</div>
                        <div class="label">*Hora de entrega acordada de pedido "Urgente":</div>
                        <div class="value center">N/A</div>
                    </div>
                    <div class="billing">
                        <div class="label">Facturar a:</div>
                        <div class="value strong">{{ $order->company->name }}</div>
                        <div class="label">Direccion.</div>
                        <div class="value">{{ $order->company->address }}</div>
                        <div class="label">R.F.C.</div>
                        <div class="value">{{ $order->company->rfc }} <span class="strong" style="margin-left: 120px;">USO DE CFDI:</span> Adquisicion de mercancia</div>
                        <div class="label">Entregar en:</div>
                        <div class="value">{{ $order->company->address }}</div>
                    </div>
                </section>

                <section>
                    <table class="items">
                        <thead>
                            <tr>
                                <th class="partida">Partida</th>
                                <th>Descripcion</th>
                                <th class="cantidad">Cantidad</th>
                                <th class="money">Precio<br>unitario</th>
                                <th class="money">Subtotal por<br>partida</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td class="partida">{{ $loop->iteration }}</td>
                                    <td>{{ $item->article }}</td>
                                    <td class="cantidad">{{ number_format((float) $item->quantity, 0) }}</td>
                                    <td class="money">${{ number_format((float) $item->unit_price, 3) }}</td>
                                    <td class="money">${{ number_format((float) $item->line_total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <table class="totals">
                        <tr><td>SUBTOTAL ORDEN</td><td class="amount">${{ number_format($subtotal, 2) }}</td></tr>
                        <tr><td>DESCUENTO</td><td class="amount">${{ number_format($discount, 2) }}</td></tr>
                        <tr><td>SUBTOTAL CON DESC.</td><td class="amount">${{ number_format($subtotalWithDiscount, 2) }}</td></tr>
                        <tr><td>I.V.A. 16%</td><td class="amount">${{ number_format($iva, 2) }}</td></tr>
                        <tr><td class="grand">TOTAL</td><td class="amount grand">${{ number_format($total, 2) }}</td></tr>
                    </table>
                </section>

                <section class="notes-wrap">
                    <div class="notes-title">Observaciones</div>
                    <div class="notes-body">{{ $notes }}</div>
                </section>

                <footer class="signature">Elaboro</footer>
            </section>
        </main>
    </body>
</html>
