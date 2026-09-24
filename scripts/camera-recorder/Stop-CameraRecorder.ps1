$ErrorActionPreference = 'Stop'

$projectRoot = (Resolve-Path -LiteralPath (Join-Path $PSScriptRoot '../..')).Path
$pidFile = Join-Path $projectRoot 'storage/app/camera-recorder/mediamtx.pid'

if (-not (Test-Path -LiteralPath $pidFile)) {
    Write-Output 'No camera recorder PID was found.'
    exit 0
}

$recorderPid = [int](Get-Content -LiteralPath $pidFile -Raw)
$process = Get-Process -Id $recorderPid -ErrorAction SilentlyContinue

if ($process -and $process.ProcessName -ne 'mediamtx') {
    throw "PID $recorderPid now belongs to another program; it was not stopped."
}

if ($process) {
    Stop-Process -Id $recorderPid
}

Remove-Item -LiteralPath $pidFile
Write-Output 'Camera recorder stopped.'
