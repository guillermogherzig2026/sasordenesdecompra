@extends('layouts.app')

@section('body')
    <main class="view" style="max-width:720px;margin:0 auto;width:100%">
        <section class="panel" style="min-height:auto">
            <h2>Informacion guardada</h2>
            <p class="fine-print">La actualizacion del almacen se guardo correctamente. Esta ventana se cerrara automaticamente.</p>
            <div class="form-actions">
                <a class="button ghost" href="{{ route('inventory.warehouses.index') }}">Volver a Almacenes</a>
                <button class="button primary" type="button" onclick="window.close()">Cerrar ventana</button>
            </div>
        </section>
    </main>
    <script>
        window.addEventListener('load', () => {
            setTimeout(() => {
                window.close();
            }, 450);
        });
    </script>
@endsection
