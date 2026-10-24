# Manifiesto

## Modulo funcional

- 11 controladores de Marketing.
- 11 modelos de Marketing.
- 1 servicio para recibir mensajes de WhatsApp.
- 3 clases de soporte para canales, navegacion y contexto de empresa/campana.
- 3 migraciones del modulo.
- 1 seeder con datos de demostracion.
- 20 vistas Blade, incluido el componente de iconos.
- 6 recursos CSS/JavaScript especificos del modulo.
- 5 archivos de pruebas Feature.

## Integracion portable

- Layout HTML base.
- Shell SAS reducido con navegacion de Marketing.
- Hoja de estilos responsive del shell.
- Middleware de permisos exclusivo para Marketing.
- Trait opcional para el modelo autenticable.
- Migracion opcional de `menu_permissions`.
- Rutas web y API listas para fusionar.
- Configuracion y variables de entorno de WhatsApp.

## Referencia SAS original

- Layout general `layouts.app`.
- Componente completo `app-shell`.
- Tema global `sas-nexo-theme.css`.
- Catalogo y middleware globales de permisos.
- Modelo `User` del proyecto fuente.

## Puntos que deben fusionarse

No reemplaces directamente estos archivos del proyecto destino:

- `routes/web.php`
- `routes/api.php`
- `config/services.php`
- `.env`
- `bootstrap/app.php`
- `app/Models/User.php`
- `database/seeders/DatabaseSeeder.php`

Usa los archivos de `snippets/` y las instrucciones de `README.md`.
