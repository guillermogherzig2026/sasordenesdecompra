<?php

namespace App\Support;

class RtspCameraProbe
{
    public function assertAvailable(string $ip, int $port, string $path, string $username, string $password): void
    {
        $url = "rtsp://{$ip}:{$port}/{$path}";
        $connection = curl_init($url);

        if ($connection === false) {
            throw new CameraConnectionException('No se pudo iniciar la comprobación RTSP.');
        }

        curl_setopt_array($connection, [
            CURLOPT_RTSP_REQUEST => CURL_RTSPREQ_DESCRIBE,
            CURLOPT_RTSP_STREAM_URI => $url,
            CURLOPT_HTTPAUTH => CURLAUTH_ANY,
            CURLOPT_USERPWD => $username.':'.$password,
            CURLOPT_HTTPHEADER => ['Accept: application/sdp'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT => 8,
        ]);

        $result = curl_exec($connection);
        $status = curl_getinfo($connection, CURLINFO_RESPONSE_CODE);
        curl_close($connection);

        if ($result !== false && $status === 200) {
            return;
        }

        throw new CameraConnectionException(match ($status) {
            401 => 'La cámara rechazó el usuario o la contraseña RTSP.',
            404 => 'La ruta RTSP no existe en esta cámara.',
            default => 'No se pudo obtener video RTSP de la cámara. Revisa la IP, el puerto y la conexión.',
        });
    }
}
