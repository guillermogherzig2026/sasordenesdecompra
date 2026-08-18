# Control de Obras

Sistema web en Laravel para administracion y control de obras de construccion.

## Fase 1 implementada

- Proyecto Laravel preparado para PHP 8.2.
- Autenticacion por usuario y contrasena.
- Roles base y permisos por modulo.
- Control de acceso por obra: consulta y edicion por usuario.
- Migraciones para la base minima solicitada.
- Seeders con cuatro obras demo:
  - Residencial Los Pinos, precio alzado.
  - Oficinas Corporativas, administracion.
  - Bodega Industrial, precio alzado.
  - Plaza Comercial Sur, administracion.
- Layout responsive en espanol.
- Menu lateral agrupado en 6 botones principales.
- Panel general de obras con filtros, tarjetas e indicadores.
- Alta, edicion, consulta y borrado logico de obras.
- Expediente de obra con indicadores superiores y pestanas.
- Bitacora de cambios importantes.
- Tablas estilo Excel con busqueda, filtros por columna, ordenamiento, paginacion, columnas configurables, impresion, Excel y PDF usando DataTables.
- Graficas comparativas usando Chart.js.

## Requisitos

- PHP 8.2 o superior.
- Composer.
- MySQL 8 o compatible.
- Extension PHP `pdo_mysql`.

## Instalacion local

1. Instalar dependencias:

```bash
composer install
```

2. Crear el archivo de entorno:

```bash
cp .env.example .env
```

En Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

3. Generar la llave de Laravel:

```bash
php artisan key:generate
```

4. Crear una base de datos MySQL:

```sql
CREATE DATABASE software_obras CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

5. Revisar las credenciales de MySQL en `.env`:

```env
DB_DATABASE=software_obras
DB_USERNAME=root
DB_PASSWORD=
```

6. Ejecutar migraciones y datos de prueba:

```bash
php artisan migrate --seed
```

7. Levantar el servidor local:

```bash
php artisan serve
```

Abrir `http://127.0.0.1:8000`.

## Credenciales de prueba

Todas las cuentas usan la contrasena `password`.

| Rol | Correo |
| --- | --- |
| Superadministrador | `super@obras.local` |
| Administrador de obra | `admin@obras.local` |
| Residente de obra | `residente@obras.local` |
| Supervisor | `supervisor@obras.local` |
| Almacen | `almacen@obras.local` |
| Compras | `compras@obras.local` |
| Nomina | `nomina@obras.local` |
| Contabilidad | `contabilidad@obras.local` |
| Consulta | `consulta@obras.local` |

## Estructura principal

```text
app/
  Http/
    Controllers/
    Middleware/
    Requests/
  Models/
database/
  migrations/
  seeders/
public/
  css/
  js/
  images/projects/
resources/
  views/
routes/
  web.php
```

## Migraciones incluidas

- Seguridad, empresas, usuarios, roles, permisos, clientes, obras, usuarios por obra y bitacora.
- Contratos, presupuestos, categorias y partidas/conceptos.
- Avances, estimaciones, conceptos de estimacion, pagos de estimacion y retenciones.
- Alcances semanales, cuadrillas, empleados, nomina y detalle de nomina.
- Materiales, almacenes, Kardex, requerimientos y ordenes de suministro.
- Proveedores, evaluaciones, ordenes de compra, facturas y pagos.
- Calendario, bitacoras diarias, incidencias, ordenes de cambio, documentos y fotografias.

## Pendientes de Fase 2

- CRUD completo de presupuestos.
- CRUD jerarquico de categorias, partidas, subpartidas y conceptos.
- Importacion de catalogos desde Excel.
- Exportacion formal de presupuesto a Excel y PDF desde backend.
- Validaciones especificas para importacion y actualizacion masiva.
