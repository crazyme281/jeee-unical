. "$PSScriptRoot\local-common.ps1"
$settings = Read-LocalSettings
$arguments = $args
Push-Location $ProjectPath
try {
    Invoke-LocalPhp $settings.php $arguments
} finally {
    Pop-Location
}
