# WhatsApp Cloud API en Marketing

## Etapa 0: arquitectura y requisitos

Esta aplicacion es Laravel/MySQL. El ejemplo externo en Next.js, Prisma y Supabase no se copia: se reutilizan `marketing_companies`, `marketing_users`, `marketing_leads`, `marketing_conversations` y `marketing_messages`. Una empresa puede atender un numero oficial desde varias cuentas del software; no se crea un numero por vendedor. Cada empresa tiene su propia configuracion de numero/WABA y los eventos entrantes se enrutan por ambos IDs. Esta primera version asume **una Meta App compartida** para las empresas conectadas, por lo que el secreto de la app y el verify token son globales.

Antes de usar Meta en produccion se necesitan una Meta App, un WhatsApp Business Account, un numero registrado para Cloud API, un token de usuario de sistema con permisos adecuados, `APP_KEY` estable para el cifrado y una URL publica HTTPS. `*.test` y `localhost` no son accesibles por los webhooks de Meta. No se deben usar los numeros y conversaciones demo como prueba de conexion real.

No se fija Graph API v20.0 del texto externo: la version se configura al habilitar los envios y se valida contra la documentacion vigente de Meta. Tampoco se almacenan tokens en codigo, URLs, vistas ni registros. El Access Token por numero se cifra con el cast `encrypted` de Laravel; cambiar `APP_KEY` requiere un procedimiento de rotacion de datos cifrados.

## Etapa 1: base de conexion y recepcion

1. Configurar `WHATSAPP_WEBHOOK_VERIFY_TOKEN` y `META_APP_SECRET` en el entorno del servidor; nunca en Git. Limpiar la cache de configuracion despues de cambiarlos.
2. En Marketing > Conversaciones, seleccionar una empresa y guardar numero, Phone Number ID, WABA ID y Access Token con una cuenta de superadministrador. Un token en blanco al editar conserva el anterior. Este paso solo configura; no hace una llamada de prueba a Meta.
3. Publicar `https://<dominio>/api/webhooks/whatsapp` y registrarlo en la Meta App con el mismo verify token. Suscribir el campo `messages` del WhatsApp Business Account.
4. Meta verifica con GET `hub.mode`, `hub.verify_token` y `hub.challenge`. Cada POST se acepta solo con `X-Hub-Signature-256` valido, calculado sobre el cuerpo exacto y `META_APP_SECRET`.
5. Los mensajes entrantes validos se registran una sola vez por Meta Message ID, asociados al numero, empresa y contacto. Aparecen en Conversaciones como no leidos. Los eventos de estado se aceptan pero aun no actualizan el historial; las referencias de anuncios todavia no atribuyen campañas.

El estado **Esperando webhook** no significa que el numero este verificado por Meta. **Webhook recibido** aparece tras la primera recepcion valida. En esta etapa no hay envio por Graph API: el compositor de conversaciones Cloud API se bloquea tambien del lado del servidor para no guardar un falso mensaje enviado. Las conversaciones demo siguen con su flujo local previo.

## Etapas siguientes

- **Etapa 2:** cliente Graph API, envio real de texto y plantillas aprobadas, ventana de atencion, errores, estados de entrega y pruebas con numero de Meta.
- **Etapa 3:** permisos por agente, asignacion manual/automatica, auditoria, notas y bandeja multiusuario con actualizacion en tiempo real.
- **Etapa 4:** vinculacion de activos y campañas de Meta por empresa, atribucion de anuncios, sincronizacion y reportes.
- **Etapa 5:** despliegue, rotacion de secretos, observabilidad, limites, consentimiento, retencion de datos y prueba piloto.

Referencias oficiales: [coleccion Cloud API de Meta](https://www.postman.com/meta/whatsapp-business-platform/collection/wlk6lh4/whatsapp-cloud-api), [webhooks de Meta](https://www.postman.com/meta/whatsapp-business-platform/folder/lboq68h/webhooks).
