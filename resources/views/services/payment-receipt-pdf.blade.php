<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 42px; }
        body { color: #20243a; font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        .header { border-bottom: 2px solid #6955e8; padding-bottom: 16px; }
        .brand { color: #5540d0; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        h1 { font-size: 21px; margin: 8px 0 4px; }
        .subtitle { color: #78829a; margin: 0; }
        .demo { background: #f0edff; border: 1px solid #6955e8; color: #5540d0; font-size: 12px; font-weight: bold; margin-top: 22px; padding: 12px; text-align: center; }
        table { border-collapse: collapse; margin-top: 25px; width: 100%; }
        th, td { border-bottom: 1px solid #e5e8f1; padding: 10px 8px; text-align: left; vertical-align: top; }
        th { color: #78829a; font-weight: normal; width: 38%; }
        td { font-weight: bold; }
        .amount { color: #5540d0; font-size: 18px; }
        .attachment { border-top: 1px solid #e5e8f1; margin-top: 28px; padding-top: 16px; }
        .attachment img { display: block; margin-top: 12px; max-height: 400px; max-width: 100%; }
        .note { color: #78829a; font-size: 10px; line-height: 1.5; margin-top: 24px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">OC System</div>
        <h1>{{ $payment['type'] === 'invoice' ? 'Factura' : 'Comprobante de pago' }}</h1>
        <p class="subtitle">Servicio {{ $payment['folio'] }}</p>
    </div>

    @if ($payment['demo'])
        <div class="demo">{{ $payment['type'] === 'invoice' ? 'DEMOSTRACIÓN - NO ES CFDI NI TIENE VALIDEZ FISCAL' : 'DEMOSTRACIÓN - SIN VALIDEZ COMO COMPROBANTE DE PAGO' }}</div>
    @endif

    <table>
        <tr><th>Titular</th><td>{{ $payment['holder'] }}</td></tr>
        <tr><th>Sucursal</th><td>{{ $payment['branch'] ?: 'Sin sucursal' }}</td></tr>
        <tr><th>Servicio</th><td>{{ $payment['service'] }}</td></tr>
        <tr><th>Proveedor</th><td>{{ $payment['provider'] ?: 'No indicado' }}</td></tr>
        <tr><th>{{ $payment['type'] === 'invoice' ? 'Vencimiento' : 'Fecha de pago' }}</th><td>{{ $payment['paidOn'] ?: 'No indicada' }}</td></tr>
        <tr><th>Referencia</th><td>{{ $payment['reference'] ?: 'No indicada' }}</td></tr>
        <tr><th>Monto</th><td class="amount">${{ number_format($payment['amount'], 2) }} MXN</td></tr>
    </table>

    @unless ($payment['demo'])
        <div class="attachment">
            <strong>Archivo adjunto:</strong> {{ $payment['originalName'] ?: 'Comprobante cargado' }}
            @if ($payment['imageData'])
                <img src="{{ $payment['imageData'] }}" alt="Imagen del comprobante adjunto">
            @else
                <p class="note">El archivo original no se puede incrustar en este PDF. Consúltalo desde Historial de Servicios. Este resumen no sustituye al archivo original.</p>
            @endif
        </div>
    @endunless

    <p class="note">
        @if ($payment['demo'])
            {{ $payment['type'] === 'invoice' ? 'Este documento es una muestra. No constituye una factura fiscal ni un CFDI.' : 'Este documento es únicamente una muestra para la demostración del software. No registra ni acredita un pago real.' }}
        @else
            Documento generado a partir de la información registrada en el historial de servicios.
        @endif
    </p>
</body>
</html>
