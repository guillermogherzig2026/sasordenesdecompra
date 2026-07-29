@extends('layouts.app')

@section('body')
    <main class="login-shell">
        <section class="login-hero">
            <div class="brand-mark">OC</div>
            <p class="eyebrow">Sistema web interno</p>
            <h1>Sistema de Ordenes de Compra y Pagos</h1>
            <p>
                Administra la creacion, autorizacion, pago y recepcion de ordenes
                de compra con paneles separados para Finanzas, Compras e Inventarios.
            </p>
        </section>

        <section class="login-card" aria-label="Inicio de sesion">
            <div>
                <p class="eyebrow">ordenes.empresa.com</p>
                <h2>Iniciar sesion</h2>
            </div>

            <form method="POST" action="{{ route('login.store') }}" class="stack">
                @csrf
                <label>
                    Correo
                    <input name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
                </label>
                <label>
                    Contrasena
                    <input name="password" type="password" autocomplete="current-password" required>
                </label>
                @error('email')
                    <p class="form-error">{{ $message }}</p>
                @enderror
                <button class="button primary" type="submit">Entrar</button>
            </form>
            <p class="hint">Acceso restringido a usuarios autorizados.</p>
        </section>
    </main>
@endsection
