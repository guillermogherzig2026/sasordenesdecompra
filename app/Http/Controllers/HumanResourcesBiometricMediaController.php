<?php

namespace App\Http\Controllers;

use App\Models\EmployeeBiometricMedia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;

class HumanResourcesBiometricMediaController extends Controller
{
    private const MAX_FILES_PER_EMPLOYEE = 10;

    public function index(): JsonResponse
    {
        $this->ensureSuperAdmin();

        return response()->json([
            'items' => EmployeeBiometricMedia::query()
                ->where('user_id', Auth::id())
                ->latest()
                ->get()
                ->map(fn (EmployeeBiometricMedia $media): array => $this->serialize($media)),
        ])->header('Cache-Control', 'private, no-store');
    }

    public function store(Request $request): JsonResponse
    {
        $this->ensureSuperAdmin();
        $data = $request->validate([
            'employee_number' => ['required', 'regex:/^EMP-\d{3,8}$/'],
            'files' => ['required', 'array', 'min:1', 'max:5'],
            'files.*' => ['required', 'file', 'max:25600', 'mimetypes:image/jpeg,image/png,image/webp,video/mp4,video/webm,video/quicktime'],
        ]);

        $employeeNumber = $data['employee_number'];
        $query = EmployeeBiometricMedia::query()
            ->where('user_id', Auth::id())
            ->where('employee_number', $employeeNumber);
        if ((clone $query)->count() + count($data['files']) > self::MAX_FILES_PER_EMPLOYEE) {
            return response()->json(['message' => 'Se permiten hasta 10 archivos por empleado.'], 422);
        }

        $storedPaths = [];
        try {
            foreach ($data['files'] as $file) {
                /** @var UploadedFile $file */
                $path = 'human-resources/biometrics/'.Auth::id().'/'.Str::uuid().'.enc';
                $contents = file_get_contents($file->getRealPath());
                if ($contents === false || !Storage::disk('local')->put($path, Crypt::encryptString($contents))) {
                    throw new \RuntimeException('No se pudo guardar el archivo.');
                }
                $storedPaths[] = $path;

                EmployeeBiometricMedia::query()->create([
                    'user_id' => Auth::id(),
                    'employee_number' => $employeeNumber,
                    'original_name' => $this->safeFilename($file),
                    'path' => $path,
                    'mime_type' => $file->getMimeType(),
                    'media_type' => str_starts_with($file->getMimeType(), 'image/') ? 'photo' : 'video',
                    'byte_size' => $file->getSize(),
                ]);
            }
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($storedPaths);
            EmployeeBiometricMedia::query()->whereIn('path', $storedPaths)->delete();
            throw $exception;
        }

        return response()->json([
            'items' => (clone $query)->latest()->get()
                ->map(fn (EmployeeBiometricMedia $media): array => $this->serialize($media)),
        ], 201)->header('Cache-Control', 'private, no-store');
    }

    public function show(EmployeeBiometricMedia $employeeBiometricMedia): Response
    {
        $this->ensureOwned($employeeBiometricMedia);
        $encrypted = Storage::disk('local')->get($employeeBiometricMedia->path);
        abort_if($encrypted === null, 404);

        return response(Crypt::decryptString($encrypted), 200, [
            'Content-Type' => $employeeBiometricMedia->mime_type,
            'Content-Disposition' => HeaderUtils::makeDisposition('inline', $employeeBiometricMedia->original_name, 'archivo'),
            'Cache-Control' => 'private, no-store, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function destroy(EmployeeBiometricMedia $employeeBiometricMedia): JsonResponse
    {
        $this->ensureOwned($employeeBiometricMedia);
        if (Storage::disk('local')->exists($employeeBiometricMedia->path)
            && !Storage::disk('local')->delete($employeeBiometricMedia->path)) {
            return response()->json(['message' => 'No se pudo eliminar el archivo.'], 500);
        }
        $employeeBiometricMedia->delete();

        return response()->json(['message' => 'Archivo eliminado.'])
            ->header('Cache-Control', 'private, no-store');
    }

    private function ensureSuperAdmin(): void
    {
        abort_unless(Auth::user()?->canAccessRole('superadmin'), 403);
    }

    private function ensureOwned(EmployeeBiometricMedia $media): void
    {
        $this->ensureSuperAdmin();
        abort_unless($media->user_id === Auth::id(), 404);
    }

    private function serialize(EmployeeBiometricMedia $media): array
    {
        return [
            'id' => $media->id,
            'employeeNumber' => $media->employee_number,
            'name' => $media->original_name,
            'type' => $media->media_type,
            'size' => $media->byte_size,
            'createdAt' => $media->created_at?->toIso8601String(),
            'url' => route('human-resources.biometric-media.show', $media),
        ];
    }

    private function safeFilename(UploadedFile $file): string
    {
        $name = basename(str_replace('\\', '/', $file->getClientOriginalName()));

        return mb_substr(preg_replace('/[\x00-\x1F\x7F]/u', '', $name) ?: 'archivo', 0, 200);
    }
}
