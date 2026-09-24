$ErrorActionPreference = 'Stop'

$version = 'v1.21.1'
$archiveName = "mediamtx_$version`_windows_amd64.zip"
$releaseUrl = "https://github.com/bluenviron/mediamtx/releases/download/$version"
$projectRoot = (Resolve-Path -LiteralPath (Join-Path $PSScriptRoot '../..')).Path
$runtime = Join-Path $projectRoot 'storage/app/camera-recorder'
$archive = Join-Path $runtime $archiveName
$checksums = Join-Path $runtime 'checksums.sha256'
$executable = Join-Path $runtime 'mediamtx.exe'

if (Test-Path -LiteralPath $executable) {
    Write-Output 'MediaMTX is already installed.'
    exit 0
}

New-Item -ItemType Directory -Path $runtime -Force | Out-Null
Invoke-WebRequest -Uri "$releaseUrl/$archiveName" -OutFile $archive
Invoke-WebRequest -Uri "$releaseUrl/checksums.sha256" -OutFile $checksums

$entry = Get-Content -LiteralPath $checksums | Where-Object { $_.EndsWith($archiveName) } | Select-Object -First 1
if (-not $entry) {
    throw "No checksum found for $archiveName."
}

$expected = ($entry -split '\s+')[0].ToLowerInvariant()
$actual = (Get-FileHash -LiteralPath $archive -Algorithm SHA256).Hash.ToLowerInvariant()
if ($expected -ne $actual) {
    throw 'MediaMTX archive failed SHA-256 verification.'
}

Expand-Archive -LiteralPath $archive -DestinationPath $runtime -Force
Write-Output "Installed MediaMTX $version. SHA-256 verified."
