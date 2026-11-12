$ErrorActionPreference = 'Stop'
$ProjectPath = Split-Path -Parent $PSScriptRoot
$SettingsPath = Join-Path $ProjectPath 'storage\local\settings.json'

function Read-LocalSettings {
    if (-not (Test-Path -LiteralPath $SettingsPath)) {
        throw 'Run scripts\setup-local.ps1 first.'
    }
    $settings = Get-Content -LiteralPath $SettingsPath -Raw | ConvertFrom-Json
    foreach ($path in @($settings.php, $settings.composer, (Join-Path $settings.xampp 'apache\bin\httpd.exe'))) {
        if (-not (Test-Path -LiteralPath $path)) {
            throw 'A saved tool path no longer exists. Rerun scripts\setup-local.ps1 with the current paths.'
        }
    }
    $settings
}

function Invoke-LocalPhp {
    param([string]$Php, [string[]]$Arguments)
    $env:PHPRC = Split-Path -Parent $Php
    & $Php -c $env:PHPRC @Arguments
    if ($LASTEXITCODE -ne 0) {
        throw "PHP command failed with exit code $LASTEXITCODE."
    }
}

function Read-DotEnv {
    $values = @{}
    foreach ($line in Get-Content -LiteralPath (Join-Path $ProjectPath '.env')) {
        if ($line -match '^([A-Z_]+)=(.*)$') {
            $values[$Matches[1]] = $Matches[2].Trim('"', "'")
        }
    }
    $values
}
