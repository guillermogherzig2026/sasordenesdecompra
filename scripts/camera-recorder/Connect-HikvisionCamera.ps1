param(
    [string]$IpAddress = '192.168.100.3',
    [int]$RtspPort = 554,
    [string]$Username = 'admin',
    [string]$StreamPath = 'Streaming/Channels/101',
    [int]$BranchId = 33,
    [int]$CameraId = 17
)

$ErrorActionPreference = 'Stop'

& (Join-Path $PSScriptRoot 'Start-CameraRecorder.ps1') | Out-Host

$securePassword = Read-Host 'Contrasena RTSP de la camara Hikvision (no se mostrara)' -AsSecureString
if ($securePassword.Length -eq 0) {
    throw 'La contrasena no puede estar vacia.'
}

$passwordPointer = [Runtime.InteropServices.Marshal]::SecureStringToBSTR($securePassword)
try {
    $password = [Runtime.InteropServices.Marshal]::PtrToStringBSTR($passwordPointer)
    $payload = @{
        ip = $IpAddress
        port = $RtspPort
        username = $Username
        password = $password
        path = $StreamPath
        branch_id = $BranchId
        camera_id = $CameraId
    } | ConvertTo-Json -Compress

    $payload | & php (Join-Path $PSScriptRoot 'Connect-HikvisionCamera.php')
    $result = $LASTEXITCODE
} finally {
    $payload = $null
    $password = $null
    [Runtime.InteropServices.Marshal]::ZeroFreeBSTR($passwordPointer)
}

if ($result -ne 0) {
    exit $result
}
