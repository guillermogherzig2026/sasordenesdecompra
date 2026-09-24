<?php

namespace App\Support;

use App\Models\SecurityBranch;
use App\Models\SecurityCamera;
use Closure;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Yaml\Yaml;
use Throwable;

class LocalCameraProvisioner
{
    public function __construct(private readonly RtspCameraProbe $probe) {}

    public function register(SecurityBranch $branch, array $details): SecurityCamera
    {
        $path = 'camara-'.bin2hex(random_bytes(6));

        return $this->configure($path, $details, function () use ($branch, $details, $path): SecurityCamera {
            return $branch->cameras()->create([
                'name' => $details['name'],
                'stream_url' => rtrim((string) config('services.camera_recorder.hls_url'), '/').'/'.$path,
                'recorder_path' => $path,
                'sort_order' => ((int) $branch->cameras()->max('sort_order')) + 1,
            ]);
        });
    }

    public function connectExisting(SecurityCamera $camera, array $details): SecurityCamera
    {
        $path = 'camara-'.bin2hex(random_bytes(6));

        return $this->configure($path, $details, function () use ($camera, $details, $path): SecurityCamera {
            $camera->update([
                'name' => $details['name'],
                'stream_url' => rtrim((string) config('services.camera_recorder.hls_url'), '/').'/'.$path,
                'recorder_path' => $path,
            ]);

            return $camera;
        });
    }

    public function reconnect(SecurityCamera $camera, string $path, array $details): SecurityCamera
    {
        return $this->configure($path, $details, function () use ($camera, $path, $details): SecurityCamera {
            $camera->update([
                'name' => $details['name'],
                'stream_url' => rtrim((string) config('services.camera_recorder.hls_url'), '/').'/'.$path,
                'recorder_path' => $path,
            ]);

            return $camera;
        }, allowExisting: true, requireReady: false);
    }

    public function sourceDetails(SecurityCamera $camera): ?array
    {
        $path = $camera->recorder_path;
        $configPath = (string) config('services.camera_recorder.config_path');

        if (! is_string($path) || ! preg_match('/\A[A-Za-z0-9_-]+\z/D', $path) || ! is_file($configPath)) {
            return null;
        }

        try {
            $configuration = Yaml::parseFile($configPath);
            $source = $configuration['paths'][$path]['source'] ?? null;
            $url = is_string($source) ? parse_url($source) : false;
        } catch (Throwable) {
            return null;
        }

        if (! is_array($url) || ($url['scheme'] ?? null) !== 'rtsp' || ! isset($url['host'], $url['user'], $url['pass'])) {
            return null;
        }

        return [
            'ip' => $url['host'],
            'port' => (int) ($url['port'] ?? 554),
            'path' => ltrim((string) ($url['path'] ?? ''), '/'),
            'username' => rawurldecode($url['user']),
            'password' => rawurldecode($url['pass']),
        ];
    }

    private function configure(string $path, array $details, Closure $saveCamera, bool $allowExisting = false, bool $requireReady = true): SecurityCamera
    {
        $configPath = (string) config('services.camera_recorder.config_path');

        if (! is_file($configPath)) {
            throw new CameraConnectionException('No se encontró la configuración local de MediaMTX. Inicia el grabador antes de agregar la cámara.');
        }

        $lock = @fopen($configPath.'.lock', 'c');

        if ($lock === false) {
            throw new CameraConnectionException('No se pudo bloquear la configuración local del grabador.');
        }

        if (! flock($lock, LOCK_EX)) {
            fclose($lock);

            throw new CameraConnectionException('No se pudo bloquear la configuración local del grabador.');
        }

        $previousConfig = null;
        $changed = false;

        try {
            $apiUrl = rtrim((string) config('services.camera_recorder.api_url'), '/');

            if ($requireReady && ! $this->recorderResponds($apiUrl)) {
                throw new CameraConnectionException('MediaMTX no está iniciado en esta computadora.');
            }

            $previousConfig = file_get_contents($configPath);
            $configuration = Yaml::parse($previousConfig);

            if (! is_array($configuration) || ! isset($configuration['paths']) || ! is_array($configuration['paths'])) {
                throw new CameraConnectionException('La configuración local de MediaMTX no es válida.');
            }

            if (isset($configuration['paths'][$path]) && ! $allowExisting) {
                throw new CameraConnectionException('La ruta de esta cámara ya existe.');
            }

            foreach ($configuration['paths'] as $existingPath => $settings) {
                if ($existingPath === $path || ! is_array($settings)) {
                    continue;
                }

                $source = parse_url((string) ($settings['source'] ?? ''));

                if (is_array($source)
                    && ($source['host'] ?? null) === $details['ip']
                    && ($source['port'] ?? 554) === (int) $details['port']
                    && ltrim((string) ($source['path'] ?? ''), '/') === $details['path']) {
                    throw new CameraConnectionException('Esta señal RTSP ya está conectada al grabador.');
                }
            }

            if ($requireReady) {
                $this->probe->assertAvailable(
                    $details['ip'],
                    (int) $details['port'],
                    $details['path'],
                    $details['username'],
                    $details['password'],
                );
            }

            if (($configuration['pathDefaults']['record'] ?? null) === 'yes') {
                $configuration['pathDefaults']['record'] = true;
            }

            $configuration['paths'][$path] = [
                'source' => 'rtsp://'.rawurlencode($details['username']).':'.rawurlencode($details['password'])
                    .'@'.$details['ip'].':'.$details['port'].'/'.$details['path'],
            ];

            $this->replaceConfig($configPath, Yaml::dump($configuration, 6, 2));
            $changed = true;

            if (! $requireReady) {
                return $saveCamera();
            }

            $attempts = max(1, (int) config('services.camera_recorder.ready_attempts', 20));

            for ($attempt = 0; $attempt < $attempts; $attempt++) {
                if ($this->pathIsReady($apiUrl, $path)) {
                    return $saveCamera();
                }

                if ($attempt < $attempts - 1) {
                    sleep(1);
                }
            }

            throw new CameraConnectionException('El grabador no recibió señal de la cámara. No se guardó el alta.');
        } catch (Throwable $exception) {
            if ($changed && $previousConfig !== null) {
                $this->replaceConfig($configPath, $previousConfig);
            }

            if ($exception instanceof CameraConnectionException) {
                throw $exception;
            }

            throw new CameraConnectionException('No se pudo guardar la conexión de la cámara.', previous: $exception);
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    private function recorderResponds(string $apiUrl): bool
    {
        try {
            return Http::connectTimeout(1)->timeout(2)->get($apiUrl.'/v3/paths/list')->successful();
        } catch (Throwable) {
            return false;
        }
    }

    private function pathIsReady(string $apiUrl, string $path): bool
    {
        try {
            $response = Http::connectTimeout(1)->timeout(2)->get($apiUrl.'/v3/paths/list');

            if (! $response->successful()) {
                return false;
            }

            foreach ($response->json('items', []) as $item) {
                if (($item['name'] ?? null) === $path && ($item['ready'] ?? false) === true) {
                    return true;
                }
            }
        } catch (Throwable) {
            return false;
        }

        return false;
    }

    private function replaceConfig(string $path, string $contents): void
    {
        $temporaryPath = tempnam(dirname($path), 'mediamtx-');

        if ($temporaryPath === false) {
            throw new CameraConnectionException('No se pudo guardar la configuración local del grabador.');
        }

        try {
            if (file_put_contents($temporaryPath, $contents, LOCK_EX) !== strlen($contents)
                || ! @rename($temporaryPath, $path)) {
                throw new CameraConnectionException('No se pudo guardar la configuración local del grabador.');
            }
        } finally {
            if (is_file($temporaryPath)) {
                @unlink($temporaryPath);
            }
        }
    }
}
