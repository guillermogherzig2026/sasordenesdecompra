<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ingresar | Control de Obras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="login-page">
    <main class="login-card">
        <div class="brand mb-3 p-0" style="color: var(--navy);">
            <span class="brand-mark"><i data-lucide="hard-hat"></i></span>
            <span>Control de Obras</span>
        </div>
        <h1 class="page-title mb-1">Acceso al sistema</h1>
        <p class="page-subtitle mb-4">Administracion y seguimiento de obras</p>

        <form method="POST" action="{{ route('login.attempt') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label" for="email">Correo</label>
                <input id="email" class="form-control @error('email') is-invalid @enderror" name="email" type="email" value="{{ old('email', 'super@obras.local') }}" required autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label" for="password">Contrasena</label>
                <input id="password" class="form-control @error('password') is-invalid @enderror" name="password" type="password" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <label class="d-flex align-items-center gap-2 text-muted">
                    <input class="form-check-input mt-0" type="checkbox" name="remember" value="1">
                    Recordarme
                </label>
                <span class="text-muted small">Demo: password</span>
            </div>

            <button class="btn btn-aqua w-100" type="submit">
                <i data-lucide="log-in"></i>
                Ingresar
            </button>
        </form>
    </main>

    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
