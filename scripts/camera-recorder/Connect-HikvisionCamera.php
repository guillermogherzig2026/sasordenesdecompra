<?php

declare(strict_types=1);

use App\Models\SecurityCamera;
use App\Support\LocalCameraProvisioner;
use Illuminate\Contracts\Console\Kernel;

$projectRoot = dirname(__DIR__, 2);
require $projectRoot.'/vendor/autoload.php';

try {
    $input = json_decode(stream_get_contents(STDIN), true, 512, JSON_THROW_ON_ERROR);

    if (! is_array($input)) {
        throw new RuntimeException('No se recibieron los datos de la cámara.');
    }

    $ip = (string) ($input['ip'] ?? '');
    $port = filter_var($input['port'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 65535]]);
    $username = (string) ($input['username'] ?? '');
    $password = (string) ($input['password'] ?? '');
    $path = trim((string) ($input['path'] ?? ''), '/');
    $branchId = filter_var($input['branch_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $cameraId = filter_var($input['camera_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

    if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)
        || ! is_int($port)
        || $username === ''
        || str_contains($username, ':')
        || $password === ''
        || ! preg_match('~\A[A-Za-z0-9_-]+(?:/[A-Za-z0-9_-]+)*\z~D', $path)
        || ! is_int($branchId)
        || ! is_int($cameraId)) {
        throw new RuntimeException('La IP, el puerto, el usuario o la ruta RTSP no son válidos.');
    }

    $app = require $projectRoot.'/bootstrap/app.php';
    $app->make(Kernel::class)->bootstrap();

    $camera = SecurityCamera::query()->find($cameraId);

    if (! $camera || $camera->security_branch_id !== $branchId) {
        throw new RuntimeException('La cámara registrada no pertenece a la sucursal indicada.');
    }

    if (! in_array(parse_url($camera->stream_url, PHP_URL_HOST), [$ip, '127.0.0.1'], true)) {
        throw new RuntimeException('La ficha de la cámara apunta a otra dirección; no se modificó.');
    }

    $app->make(LocalCameraProvisioner::class)->reconnect($camera, 'camara1', [
        'ip' => $ip,
        'port' => $port,
        'path' => $path,
        'username' => $username,
        'password' => $password,
    ]);

    echo "Cámara conectada y registrada en Seguridad y Vigilancia.\n";
    echo "Reproducción: http://127.0.0.1:8888/camara1\n";
    echo "Grabaciones: storage/app/camera-recordings/camara1\n";
} catch (Throwable $exception) {
    fwrite(STDERR, $exception instanceof RuntimeException
        ? $exception->getMessage().PHP_EOL
        : 'No se pudo completar la conexión; la configuración anterior se restauró.'.PHP_EOL);
    exit(1);
}
