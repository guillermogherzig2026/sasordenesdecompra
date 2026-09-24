$ErrorActionPreference = 'Stop'

$projectRoot = (Resolve-Path -LiteralPath (Join-Path $PSScriptRoot '../..')).Path
$runtime = Join-Path $projectRoot 'storage/app/camera-recorder'
$executable = Join-Path $runtime 'mediamtx.exe'
$config = Join-Path $runtime 'mediamtx.local.yml'
$template = Join-Path $PSScriptRoot 'mediamtx.template.yml'

if (-not (Test-Path -LiteralPath $executable)) {
    throw "MediaMTX is missing. Run scripts/camera-recorder/Install-CameraRecorder.ps1 first."
}

if (-not (Test-Path -LiteralPath $config)) {
    Copy-Item -LiteralPath $template -Destination $config
}

function Test-CameraRecorder {
    try {
        $response = Invoke-WebRequest -Uri 'http://127.0.0.1:9997/v3/paths/list' -UseBasicParsing -TimeoutSec 2
        return $response.StatusCode -eq 200
    } catch {
        return $false
    }
}

if (Test-CameraRecorder) {
    Write-Output 'Camera recorder is already listening at http://127.0.0.1:9997.'
    exit 0
}

$process = Start-Process -FilePath $executable -ArgumentList @($config) -WorkingDirectory $runtime -WindowStyle Hidden -PassThru
Set-Content -LiteralPath (Join-Path $runtime 'mediamtx.pid') -Value $process.Id

for ($attempt = 0; $attempt -lt 20; $attempt++) {
    Start-Sleep -Milliseconds 250
    if ($process.HasExited) {
        throw "MediaMTX exited with code $($process.ExitCode). Check mediamtx.local.yml."
    }
    if (Test-CameraRecorder) {
        Write-Output "Camera recorder started (PID $($process.Id))."
        Write-Output 'RTSP: rtsp://127.0.0.1:8554/camara1'
        Write-Output 'HTTP: http://127.0.0.1:8888/camara1'
        exit 0
    }
}

throw 'MediaMTX did not open its local API port within five seconds.'
