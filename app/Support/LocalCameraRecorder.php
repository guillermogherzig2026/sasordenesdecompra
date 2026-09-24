<?php

namespace App\Support;

use App\Models\SecurityCamera;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class LocalCameraRecorder
{
    /** @param Collection<int, SecurityCamera> $cameras */
    public function readyPlaybackUrls(Collection $cameras): array
    {
        $baseUrl = rtrim((string) config('services.camera_recorder.hls_url'), '/');
        $configuredPaths = [];

        foreach ($cameras as $camera) {
            $path = $this->localPath($camera->stream_url, $baseUrl);

            if ($path !== null) {
                $configuredPaths[$camera->getKey()] = $path;
            }
        }

        if ($configuredPaths === []) {
            return [];
        }

        try {
            $response = Http::connectTimeout(1)
                ->timeout(2)
                ->get(rtrim((string) config('services.camera_recorder.api_url'), '/').'/v3/paths/list');
        } catch (ConnectionException) {
            return [];
        }

        if (! $response->successful()) {
            return [];
        }

        $readyPaths = collect($response->json('items', []))
            ->filter(fn ($item) => is_array($item) && ($item['ready'] ?? false) === true)
            ->pluck('name')
            ->all();

        $playbackUrls = [];

        foreach ($configuredPaths as $cameraId => $path) {
            if (in_array($path, $readyPaths, true)) {
                $playbackUrls[$cameraId] = $baseUrl.'/'.$path;
            }
        }

        return $playbackUrls;
    }

    private function localPath(string $url, string $baseUrl): ?string
    {
        $base = parse_url($baseUrl);
        $candidate = parse_url(trim($url));

        if (! is_array($base) || ! is_array($candidate)) {
            return null;
        }

        $scheme = strtolower((string) ($base['scheme'] ?? ''));

        if (! in_array($scheme, ['http', 'https'], true)
            || empty($base['host'])
            || strtolower((string) ($candidate['scheme'] ?? '')) !== $scheme
            || strtolower((string) ($candidate['host'] ?? '')) !== strtolower((string) ($base['host'] ?? ''))
            || ($candidate['port'] ?? null) !== ($base['port'] ?? null)
            || ! in_array($base['path'] ?? '', ['', '/'], true)
            || isset($candidate['user'])
            || isset($candidate['pass'])
            || isset($candidate['query'])
            || isset($candidate['fragment'])) {
            return null;
        }

        $path = ltrim((string) ($candidate['path'] ?? ''), '/');

        return preg_match('/\A[A-Za-z0-9_-]+\z/D', $path) ? $path : null;
    }
}
