param(
    [string]$XamppPath = 'C:\xampp',
    [string]$PhpPath,
    [string]$ComposerPath,
    [ValidateRange(1024, 65535)]
    [int]$Port = 8080
)

. "$PSScriptRoot\local-common.ps1"
Push-Location $ProjectPath
try {
    $XamppPath = (Resolve-Path -LiteralPath $XamppPath).Path
    if (-not $PhpPath) {
        $candidates = @(Get-ChildItem -LiteralPath $XamppPath -Directory -Filter 'php*' | Sort-Object Name -Descending)
        foreach ($candidate in $candidates) {
            $file = Join-Path $candidate.FullName 'php.exe'
            if (-not (Test-Path -LiteralPath $file)) {
                continue
            }
            $output = (& $file -n -v) -join "`n"
            if ($output -match '^PHP (8\.5\.(\d+)) ' -and [int]$Matches[2] -ge 12) {
                $version = $Matches[1]
                if ($version -eq '8.5.12') {
                    $build = [regex]::Match($output, '\(built: ([^)]+)\)').Groups[1].Value
                    if ([DateTime]::ParseExact($build, 'MMM dd yyyy HH:mm:ss', [Globalization.CultureInfo]::InvariantCulture) -lt [DateTime]'2026-10-26') {
                        continue
                    }
                }
                $PhpPath = $file
                break
            }
        }
    }
    if (-not $PhpPath) {
        throw 'Install the official PHP 8.5.12-1 x64 Thread Safe build or newer. Older PHP is not used.'
    }
    $PhpPath = (Resolve-Path -LiteralPath $PhpPath).Path
    if (-not $ComposerPath) {
        $ComposerPath = Join-Path $XamppPath 'composer\composer.phar'
    }
    $ComposerPath = (Resolve-Path -LiteralPath $ComposerPath).Path
    Invoke-LocalPhp $PhpPath @((Join-Path $PSScriptRoot 'runtime-check.php'))
    if (-not (Test-Path -LiteralPath (Join-Path (Split-Path $PhpPath) 'php8apache2_4.dll'))) {
        throw 'This PHP does not include Apache support. Choose the x64 Thread Safe Windows build.'
    }
    $env:Path = (Split-Path $PhpPath) + ';' + $env:Path
    $folders = @('storage\local', 'storage\logs', 'storage\framework\cache\data', 'storage\framework\sessions', 'storage\framework\views', 'storage\app\public', 'bootstrap\cache')
    foreach ($folder in $folders) {
        New-Item -ItemType Directory -Path $folder -Force | Out-Null
    }
    if (-not (Test-Path -LiteralPath '.env')) {
        Copy-Item -LiteralPath '.env.example' -Destination '.env'
        $lines = Get-Content -LiteralPath '.env'
        $lines = $lines -replace '^APP_URL=.*$', "APP_URL=http://127.0.0.1:$Port"
        $passwordBytes = New-Object byte[] 24
        [Security.Cryptography.RandomNumberGenerator]::Create().GetBytes($passwordBytes)
        $password = [Convert]::ToBase64String($passwordBytes)
        $lines = $lines -replace '^DB_PASSWORD=.*$', "DB_PASSWORD=$password"
        [IO.File]::WriteAllLines((Join-Path $ProjectPath '.env'), $lines)
    }
    $values = Read-DotEnv
    if ($values['APP_ENV'] -ne 'local' -or $values['DB_CONNECTION'] -ne 'mysql' -or $values['DB_HOST'] -notin @('127.0.0.1', 'localhost')) {
        throw 'Setup only runs on a local XAMPP database. It will not change an online database.'
    }
    $lines = (Get-Content -LiteralPath '.env') -replace '^APP_URL=.*$', "APP_URL=http://127.0.0.1:$Port"
    [IO.File]::WriteAllLines((Join-Path $ProjectPath '.env'), $lines)
    Invoke-LocalPhp $PhpPath @($ComposerPath, 'install', '--no-interaction', '--prefer-dist')
    if (-not $values['DB_PASSWORD']) {
        $passwordBytes = New-Object byte[] 24
        [Security.Cryptography.RandomNumberGenerator]::Create().GetBytes($passwordBytes)
        $password = [Convert]::ToBase64String($passwordBytes)
        $lines = (Get-Content -LiteralPath '.env') -replace '^DB_PASSWORD=.*$', "DB_PASSWORD=$password"
        [IO.File]::WriteAllLines((Join-Path $ProjectPath '.env'), $lines)
        $values = Read-DotEnv
    }
    $mysql = Join-Path $XamppPath 'mysql\bin\mysqld.exe'
    if (-not (Test-Path -LiteralPath $mysql)) {
        throw 'XAMPP MariaDB is missing. Install XAMPP with MySQL selected.'
    }
    $listening = Get-NetTCPConnection -State Listen -LocalPort ([int]$values['DB_PORT']) -ErrorAction SilentlyContinue
    if (-not $listening) {
        $argument = '--defaults-file="' + (Join-Path $XamppPath 'mysql\bin\my.ini') + '" --bind-address=127.0.0.1 --console'
        Start-Process -FilePath $mysql -ArgumentList $argument -WorkingDirectory (Split-Path $mysql) -RedirectStandardOutput 'storage\local\mysql.out' -RedirectStandardError 'storage\local\mysql.err' | Out-Null
        $deadline = (Get-Date).AddSeconds(30)
        do {
            Start-Sleep -Milliseconds 250
            $listening = Get-NetTCPConnection -State Listen -LocalPort ([int]$values['DB_PORT']) -ErrorAction SilentlyContinue
        } until ($listening -or (Get-Date) -gt $deadline)
        if (-not $listening) {
            throw 'XAMPP MySQL did not start. See storage\local\mysql.err.'
        }
    }
    Invoke-LocalPhp $PhpPath @((Join-Path $PSScriptRoot 'local-db.php'))
    if (-not $values['APP_KEY']) {
        Invoke-LocalPhp $PhpPath @('artisan', 'key:generate', '--no-interaction')
    }
    Invoke-LocalPhp $PhpPath @('artisan', 'config:clear')
    Invoke-LocalPhp $PhpPath @('artisan', 'migrate', '--no-interaction')
    Invoke-LocalPhp $PhpPath @('artisan', 'db:seed', '--class=SettingsSeeder', '--no-interaction')
    if (-not (Test-Path -LiteralPath 'public\storage')) {
        $existingLink = Get-Item -LiteralPath 'public\storage' -Force -ErrorAction SilentlyContinue
        if ($existingLink) {
            throw 'public\storage points to a missing folder. Remove only this broken link and run setup again.'
        }
        Invoke-LocalPhp $PhpPath @('artisan', 'storage:link')
    }
    @{ xampp = $XamppPath
        php = $PhpPath
        composer = $ComposerPath
        port = $Port
    } | ConvertTo-Json | Set-Content -LiteralPath $SettingsPath -Encoding UTF8
    Write-Output 'Setup finished. Run scripts\start-local.ps1.'
} finally {
    Pop-Location
}
