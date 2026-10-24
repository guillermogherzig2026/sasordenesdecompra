# Panel de Marketing SAS

Paquete portable del panel de Marketing de SAS Ordenes de Compra, preparado el 23 de octubre de 2026 para integrarlo en otro proyecto Laravel.

## Contenido

- `module/laravel/`: modulo funcional de Marketing. Incluye controladores, modelos, servicios, soporte, migraciones, seeder, vistas, CSS, JavaScript y pruebas.
- `starter/laravel/`: estructura general SAS minima y desacoplada. Es la opcion recomendada cuando el proyecto destino no tiene el shell original.
- `sas-shell-reference/`: copia exacta del layout, componente de navegacion, tema y permisos del proyecto SAS actual. Sirve como referencia para fusionar estilos y estructura; no debe reemplazar archivos del proyecto destino sin revisarlos.
- `snippets/`: rutas, configuracion de servicios, variables de entorno e instrucciones cortas para integrar el modulo.
- `MANIFEST.md`: inventario del paquete y puntos de integracion.

## Requisitos

- PHP 8.2 o superior.
- Laravel 12.
- Un sistema de autenticacion con la ruta nombrada `logout`.
- Base de datos compatible con las migraciones de Laravel.

El codigo no agrega paquetes de Composer ni de npm.

## Instalacion recomendada

1. Copia el contenido de `module/laravel/` sobre la raiz del proyecto Laravel destino, conservando las carpetas.
2. Copia el contenido de `starter/laravel/` sobre la raiz del proyecto destino. Este paso agrega el shell minimo y reemplaza solamente el layout interno de Marketing por su variante portable.
3. Agrega al modelo autenticable `App\Models\User` el trait incluido:

```php
use App\Models\Concerns\HasMarketingPermissions;

class User extends Authenticatable
{
    use HasMarketingPermissions;
}
```

Si el modelo ya tiene un metodo `canNavigateTo()`, no uses el trait. Integra en ese metodo los permisos `marketing.*` listados en este documento.

4. Fusiona `snippets/routes-marketing-web.php` en `routes/web.php`.
5. Fusiona `snippets/routes-marketing-api.php` en `routes/api.php`. Si el proyecto no carga `routes/api.php`, registralo en `bootstrap/app.php`.
6. Fusiona el bloque de `snippets/services-whatsapp.php` dentro del arreglo de `config/services.php`.
7. Copia las variables de `snippets/env.marketing.example` al archivo `.env` del proyecto destino.
8. Agrega `meta_access_token` al arreglo `dontFlash` de excepciones en `bootstrap/app.php` para impedir que el token reaparezca tras una validacion fallida.
9. Ejecuta:

```bash
php artisan migrate
php artisan db:seed --class=MarketingLeadFlowSeeder
php artisan storage:link
php artisan optimize:clear
```

El seeder es opcional. Agrega datos de demostracion para recorrer el panel.

## Estructura visual

La opcion recomendada usa:

- `resources/views/layouts/sas-marketing.blade.php`
- `resources/views/components/sas-marketing-shell.blade.php`
- `public/css/sas-marketing-shell.css`

El shell incluye la barra lateral, encabezado, usuario autenticado, cierre de sesion, estado activo del menu y adaptacion movil. El modulo conserva sus estilos propios en `public/css/marketing-leadflow.css` y `public/modules/marketing/`.

Para replicar toda la aplicacion SAS, consulta `sas-shell-reference/`. Esa version depende de rutas, modelos y categorias ajenas a Marketing, por lo que debe fusionarse con cuidado.

## Permisos

El modulo reconoce estos permisos:

- `marketing.dashboard`
- `marketing.companies`
- `marketing.campaigns`
- `marketing.leads`
- `marketing.conversations`
- `marketing.reports`
- `marketing.users`
- `marketing.settings`

El trait portable permite todos los permisos de Marketing cuando `menu_permissions` es `null`. Si existe un arreglo de permisos, aplica solamente los seleccionados. Un usuario con rol `superadmin` siempre tiene acceso. El middleware rechaza usuarios inactivos cuando el modelo dispone de la columna `active`.

La migracion del starter agrega unicamente `menu_permissions` a `users`. Las columnas `role` y `active` son opcionales para el panel general, aunque el alta de una conexion de WhatsApp queda reservada al rol `superadmin`.

## WhatsApp Cloud API

El paquete incluye configuracion de numeros, verificacion de webhook y recepcion de mensajes. Define:

```dotenv
WHATSAPP_WEBHOOK_VERIFY_TOKEN=
META_APP_SECRET=
META_GRAPH_VERSION=v22.0
```

Los tokens de acceso se guardan cifrados por Laravel. Configura correctamente `APP_KEY` antes de guardar credenciales.

La recepcion por webhook esta implementada. El envio saliente real por Graph API no forma parte de esta entrega; la conversacion registra mensajes salientes localmente. No expongas el webhook sin HTTPS, firma y secretos validos.

## Aislamiento de datos

Los scopes `visibleTo()` de los modelos de Marketing conservan el comportamiento del proyecto fuente y actualmente no limitan registros por empresa o usuario. Si el proyecto destino es multiempresa, agrega la restriccion de tenant en esos scopes antes de usar el modulo con datos de produccion.

## Verificacion

Ejecuta las pruebas incluidas despues de integrar el paquete:

```bash
php artisan test tests/Feature/MarketingChannelConfigurationTest.php
php artisan test tests/Feature/MarketingCompanyFavoritesTest.php
php artisan test tests/Feature/MarketingDashboardTest.php
php artisan test tests/Feature/MarketingModuleTest.php
php artisan test tests/Feature/MarketingWhatsAppCloudTest.php
```

Despues abre `/marketing` con un usuario autenticado.
