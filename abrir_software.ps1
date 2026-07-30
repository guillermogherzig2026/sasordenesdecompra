$ErrorActionPreference = 'Stop'

$projectRoot = $PSScriptRoot
$php = Join-Path $projectRoot '.runtime\php-8.4\php.exe'
$ext = Join-Path $projectRoot '.runtime\php-8.4\ext'
$url = 'http://127.0.0.1:8000/login'

if (-not (Test-Path -LiteralPath $php)) {
    Write-Host 'No se encontro PHP portable en .runtime\php-8.4.'
    Write-Host 'Abre este proyecto con Codex para prepararlo de nuevo.'
    Read-Host 'Presiona Enter para cerrar'
    exit 1
}

if (-not (Test-Path -LiteralPath (Join-Path $projectRoot '.env'))) {
    Write-Host 'No se encontro el archivo de configuracion .env.'
    Write-Host 'Abre este proyecto con Codex para configurarlo.'
    Read-Host 'Presiona Enter para cerrar'
    exit 1
}

$alreadyRunning = $false
try {
    $response = Invoke-WebRequest -Uri $url -UseBasicParsing -TimeoutSec 2
    $alreadyRunning = $response.StatusCode -ge 200 -and $response.StatusCode -lt 500
} catch {
    $alreadyRunning = $false
}

if (-not $alreadyRunning) {
    $argList = @(
        '-d', "extension_dir=$ext",
        '-d', 'extension=openssl',
        '-d', 'extension=mbstring',
        '-d', 'extension=fileinfo',
        '-d', 'extension=pdo_mysql',
        '-d', 'extension=curl',
        '-d', 'extension=zip',
        '-S', '127.0.0.1:8000',
        '-t', 'public',
        'public\index.php'
    )

    function Quote-Arg([string]$value) {
        if ($value -match '[\s"]') {
            return '"' + ($value -replace '"', '\"') + '"'
        }

        return $value
    }

    $psi = New-Object System.Diagnostics.ProcessStartInfo
    $psi.FileName = $php
    $psi.WorkingDirectory = $projectRoot
    $psi.Arguments = (($argList | ForEach-Object { Quote-Arg $_ }) -join ' ')
    $psi.UseShellExecute = $true
    $psi.WindowStyle = [System.Diagnostics.ProcessWindowStyle]::Hidden

    $process = [System.Diagnostics.Process]::Start($psi)
    Set-Content -LiteralPath (Join-Path $projectRoot '.runtime\server.pid') -Value $process.Id

    $ready = $false
    for ($i = 0; $i -lt 20; $i++) {
        Start-Sleep -Milliseconds 500
        try {
            $response = Invoke-WebRequest -Uri $url -UseBasicParsing -TimeoutSec 2
            if ($response.StatusCode -ge 200 -and $response.StatusCode -lt 500) {
                $ready = $true
                break
            }
        } catch {
            $ready = $false
        }
    }

    if (-not $ready) {
        Write-Host 'La app no termino de arrancar en http://127.0.0.1:8000.'
        Read-Host 'Presiona Enter para cerrar'
        exit 1
    }
}

Start-Process $url
