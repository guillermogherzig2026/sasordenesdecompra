$ErrorActionPreference = 'SilentlyContinue'

$projectRoot = $PSScriptRoot
$pidFile = Join-Path $projectRoot '.runtime\server.pid'

if (Test-Path -LiteralPath $pidFile) {
    $serverPid = (Get-Content -LiteralPath $pidFile -Raw).Trim()
    if ($serverPid -match '^\d+$') {
        $process = Get-Process -Id ([int] $serverPid) -ErrorAction SilentlyContinue
        if ($process -and $process.ProcessName -like 'php*') {
            Stop-Process -Id $process.Id -Force
            Remove-Item -LiteralPath $pidFile -Force
            Write-Host 'Software cerrado.'
            exit 0
        }
    }
}

$connection = Get-NetTCPConnection -LocalPort 8000 -ErrorAction SilentlyContinue | Select-Object -First 1
if ($connection) {
    $process = Get-Process -Id $connection.OwningProcess -ErrorAction SilentlyContinue
    if ($process -and $process.ProcessName -like 'php*') {
        Stop-Process -Id $process.Id -Force
        Write-Host 'Software cerrado.'
        exit 0
    }
}

Write-Host 'No encontre un servidor del software abierto.'
