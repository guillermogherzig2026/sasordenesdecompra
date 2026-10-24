# Cambios en bootstrap/app.php

En Laravel 12, confirma que `routes/api.php` este registrado:

```php
->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php',
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
)
```

Agrega `meta_access_token` a los valores que Laravel nunca debe conservar en la sesion tras una validacion fallida:

```php
->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->dontFlash(['password', 'password_confirmation', 'meta_access_token']);
})
```
