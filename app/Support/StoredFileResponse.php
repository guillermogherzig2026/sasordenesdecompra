<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class StoredFileResponse
{
    public static function inline(?string $path, ?string $name = null)
    {
        return self::respond($path, $name, true);
    }

    public static function download(?string $path, ?string $name = null)
    {
        return self::respond($path, $name, false);
    }

    private static function respond(?string $path, ?string $name = null, bool $inline = false)
    {
        abort_unless(filled($path), 404);

        $normalized = ltrim(str_replace('\\', '/', $path), '/');
        $filename = $name ?: basename($normalized);

        $relative = preg_replace('#^(storage/app/public|storage/app/private|storage/app|public/storage|storage|app/public|app/private|app|public)/#', '', $normalized);
        $paths = array_values(array_unique([$normalized, $relative, 'public/'.$relative]));

        foreach (['local', 'public'] as $disk) {
            foreach ($paths as $candidatePath) {
                if (Storage::disk($disk)->exists($candidatePath)) {
                    return $inline
                        ? Storage::disk($disk)->response($candidatePath, $filename)
                        : Storage::disk($disk)->download($candidatePath, $filename);
                }
            }
        }

        foreach ($paths as $candidatePath) {
            if (Storage::exists($candidatePath)) {
                return $inline
                    ? Storage::response($candidatePath, $filename)
                    : Storage::download($candidatePath, $filename);
            }
        }

        $candidates = array_unique([
            storage_path('app/private/'.$relative),
            storage_path('app/'.$relative),
            storage_path('app/public/'.$relative),
            storage_path('app/private/'.$normalized),
            storage_path('app/'.$normalized),
            storage_path('app/public/'.$normalized),
            public_path('storage/'.$relative),
            public_path($relative),
            public_path($normalized),
        ]);

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $inline
                    ? response()->file($candidate, ['Content-Disposition' => 'inline; filename="'.str_replace('"', '', $filename).'"'])
                    : response()->download($candidate, $filename);
            }
        }

        abort(404);
    }
}
