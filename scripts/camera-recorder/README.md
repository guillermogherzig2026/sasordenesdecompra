# Grabador local de camaras

MediaMTX recibe video RTSP, lo graba en este equipo y lo presenta por HTTP a Seguridad y Vigilancia. La instalacion se mantiene en `storage/app/camera-recorder`, fuera de Git.

## Instalar y arrancar

Desde PowerShell, en la raiz del proyecto:

```powershell
.\scripts\camera-recorder\Install-CameraRecorder.ps1
.\scripts\camera-recorder\Start-CameraRecorder.ps1
```

El instalador descarga MediaMTX v1.21.1 de su publicacion oficial y verifica el archivo con SHA-256. El grabador se inicia en segundo plano. Para detenerlo:

```powershell
.\scripts\camera-recorder\Stop-CameraRecorder.ps1
```

Tras reiniciar Windows, ejecuta de nuevo `Start-CameraRecorder.ps1`. El grabador y la aplicacion Laravel son procesos independientes; ambos deben estar activos para ver video en el software.

## Direcciones locales

- Entrada RTSP para una fuente que publica: `rtsp://127.0.0.1:8554/camara1`
- Reproduccion HTTP para el navegador: `http://127.0.0.1:8888/camara1`
- Estado de las rutas: `http://127.0.0.1:9997/v3/paths/list`
- Grabaciones: `storage/app/camera-recordings/camara1/`

Estas direcciones solo funcionan en la computadora que ejecuta MediaMTX. Los puertos estan enlazados a `127.0.0.1`, no a la red local ni a Internet.

## Camara Hikvision de Sucursal Centro

La camara registrada como **Entrada principal** responde en `192.168.100.3:554` y requiere autenticacion RTSP. El campo **Server IP Address** de Hik-Connect no sirve para conectarla a MediaMTX. Con el usuario y la contrasena de la camara disponibles, ejecuta:

```powershell
.\scripts\camera-recorder\Connect-HikvisionCamera.ps1
```

El comando pide la contrasena sin mostrarla, comprueba la ruta `/Streaming/Channels/101` y solo actualiza el grabador y la URL de la camara en el software si recibe video. La contrasena queda en el archivo local `storage/app/camera-recorder/mediamtx.local.yml`, que no se agrega a Git. Si el usuario no es `admin`, usa `-Username OTRO_USUARIO`. Si la camara usa otro canal o puerto, usa `-StreamPath` o `-RtspPort`. No pongas la contrasena en el comando ni la compartas por chat.

## Registrar otras camaras desde el software

Con MediaMTX iniciado, abre **Seguridad y Vigilancia > Sucursales**, selecciona la sucursal y pulsa **Agregar camara IP**. Ingresa nombre, IP privada, puerto RTSP, ruta del canal, usuario y contrasena. Para Hikvision, el puerto habitual es `554` y la ruta principal es `Streaming/Channels/101`.

El sistema valida el acceso RTSP, crea una ruta independiente en MediaMTX y registra solo su URL HTTP local en la base de datos. Si no llega senal, restaura la configuracion y no crea la camara. La contrasena queda en `storage/app/camera-recorder/mediamtx.local.yml`, que no se agrega a Git; nunca la incluyas en la URL manual ni la compartas por chat. La opcion **URLs manuales de camaras** del formulario de sucursal sigue disponible para fuentes externas ya publicadas.

Las grabaciones se eliminan automaticamente tras siete dias, segun `recordDeleteAfter` en la configuracion local. Se recomienda una camara con video H.264 para reproduccion amplia en navegadores. Sin URL o fuente publicada no existe senal ni se generan grabaciones.
