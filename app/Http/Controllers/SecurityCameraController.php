<?php

namespace App\Http\Controllers;

use App\Models\SecurityBranch;
use App\Models\SecurityCamera;
use App\Support\CameraConnectionException;
use App\Support\LocalCameraProvisioner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SecurityCameraController extends Controller
{
    public function store(Request $request, SecurityBranch $securityBranch, LocalCameraProvisioner $provisioner): RedirectResponse
    {
        $validated = $request->validateWithBag('camera', [
            'name' => ['required', 'string', 'max:120'],
            'ip' => ['required', 'ipv4', $this->privateIpRule()],
            'rtsp_port' => ['required', 'integer', 'between:1,65535'],
            'rtsp_path' => ['required', 'string', 'max:255', 'regex:~\A[A-Za-z0-9_-]+(?:/[A-Za-z0-9_-]+)*\z~D'],
            'rtsp_username' => ['required', 'string', 'max:120', 'not_regex:~:~'],
            'rtsp_password' => ['required', 'string', 'max:255'],
        ], [], [
            'name' => 'nombre de la cámara',
            'ip' => 'dirección IP',
            'rtsp_port' => 'puerto RTSP',
            'rtsp_path' => 'ruta RTSP',
            'rtsp_username' => 'usuario RTSP',
            'rtsp_password' => 'contraseña RTSP',
        ]);

        $redirect = $this->branchUrl($securityBranch);

        try {
            $provisioner->register($securityBranch, [
                'name' => trim($validated['name']),
                'ip' => $validated['ip'],
                'port' => (int) $validated['rtsp_port'],
                'path' => trim($validated['rtsp_path'], '/'),
                'username' => $validated['rtsp_username'],
                'password' => $validated['rtsp_password'],
            ]);
        } catch (CameraConnectionException $exception) {
            return redirect($redirect)
                ->withErrors(['connection' => $exception->getMessage()], 'camera')
                ->withInput($request->except('rtsp_password'));
        }

        return redirect($redirect)->with('camera_registered', true);
    }

    public function update(Request $request, SecurityBranch $securityBranch, SecurityCamera $securityCamera, LocalCameraProvisioner $provisioner): RedirectResponse
    {
        abort_unless($securityCamera->security_branch_id === $securityBranch->id, 404);

        $redirect = $this->branchUrl($securityBranch);
        $name = $request->validateWithBag('camera_edit', [
            'name' => ['required', 'string', 'max:120'],
        ])['name'];

        if ($securityCamera->recorder_path === null) {
            $request->validateWithBag('camera_edit', [
                'connection_type' => ['sometimes', Rule::in(['url', 'ip'])],
            ]);

            if ($request->input('connection_type') === 'ip') {
                $validated = $request->validateWithBag('camera_edit', [
                    'ip' => ['required', 'ipv4', $this->privateIpRule()],
                    'rtsp_port' => ['required', 'integer', 'between:1,65535'],
                    'rtsp_path' => ['required', 'string', 'max:255', 'regex:~\A[A-Za-z0-9_-]+(?:/[A-Za-z0-9_-]+)*\z~D'],
                    'rtsp_username' => ['required', 'string', 'max:120', 'not_regex:~:~'],
                    'rtsp_password' => ['required', 'string', 'max:255'],
                ], [], [
                    'ip' => 'dirección IP',
                    'rtsp_port' => 'puerto RTSP',
                    'rtsp_path' => 'ruta RTSP',
                    'rtsp_username' => 'usuario RTSP',
                    'rtsp_password' => 'contraseña RTSP',
                ]);

                try {
                    $provisioner->connectExisting($securityCamera, [
                        'name' => trim($name),
                        'ip' => $validated['ip'],
                        'port' => (int) $validated['rtsp_port'],
                        'path' => trim($validated['rtsp_path'], '/'),
                        'username' => $validated['rtsp_username'],
                        'password' => $validated['rtsp_password'],
                    ]);
                } catch (CameraConnectionException $exception) {
                    return redirect($redirect)
                        ->withErrors(['connection' => $exception->getMessage()], 'camera_edit')
                        ->withInput($request->except('rtsp_password'));
                }

                return redirect($redirect)->with('camera_registered', true);
            }

            $validated = $request->validateWithBag('camera_edit', [
                'stream_url' => ['required', 'string', 'max:2048'],
            ], [], ['stream_url' => 'URL de la cámara']);

            $securityCamera->update([
                'name' => trim($name),
                'stream_url' => trim($validated['stream_url']),
            ]);

            return redirect($redirect)->with('camera_updated', true);
        }

        $current = $provisioner->sourceDetails($securityCamera);
        if ($request->input('editing_camera_mode') === 'details') {
            $securityCamera->update(['name' => trim($name)]);

            return redirect($redirect)->with('camera_updated', true);
        }

        $validated = $request->validateWithBag('camera_edit', [
            'ip' => ['required', 'ipv4', $this->privateIpRule()],
            'rtsp_port' => ['required', 'integer', 'between:1,65535'],
            'rtsp_path' => ['required', 'string', 'max:255', 'regex:~\A[A-Za-z0-9_-]+(?:/[A-Za-z0-9_-]+)*\z~D'],
            'rtsp_username' => ['required', 'string', 'max:120', 'not_regex:~:~'],
            'rtsp_password' => [Rule::requiredIf($current === null), 'nullable', 'string', 'max:255'],
        ], [], [
            'ip' => 'dirección IP',
            'rtsp_port' => 'puerto RTSP',
            'rtsp_path' => 'ruta RTSP',
            'rtsp_username' => 'usuario RTSP',
            'rtsp_password' => 'contraseña RTSP',
        ]);

        try {
            $provisioner->reconnect($securityCamera, $securityCamera->recorder_path, [
                'name' => trim($name),
                'ip' => $validated['ip'],
                'port' => (int) $validated['rtsp_port'],
                'path' => trim($validated['rtsp_path'], '/'),
                'username' => $validated['rtsp_username'],
                'password' => $validated['rtsp_password'] ?? $current['password'],
            ]);
        } catch (CameraConnectionException $exception) {
            return redirect($redirect)
                ->withErrors(['connection' => $exception->getMessage()], 'camera_edit')
                ->withInput($request->except('rtsp_password'));
        }

        return redirect($redirect)
            ->with('camera_updated', true)
            ->with('camera_connection_saved', true);
    }

    private function branchUrl(SecurityBranch $branch): string
    {
        return route('security.index', [
            'section' => 'branches',
            'company_id' => $branch->security_company_id,
            'branch_id' => $branch->id,
        ]);
    }

    private function privateIpRule(): \Closure
    {
        return function (string $attribute, mixed $value, $fail): void {
            $parts = array_map('intval', explode('.', (string) $value));

            if (count($parts) !== 4 || ! (
                $parts[0] === 10
                || ($parts[0] === 172 && $parts[1] >= 16 && $parts[1] <= 31)
                || ($parts[0] === 192 && $parts[1] === 168)
            )) {
                $fail('La IP de la cámara debe pertenecer a una red privada.');
            }
        };
    }
}
