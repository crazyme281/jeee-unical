. "$PSScriptRoot\local-common.ps1"
$settings = Read-LocalSettings
Push-Location $ProjectPath
try {
    Invoke-LocalPhp $settings.php @((Join-Path $PSScriptRoot 'runtime-check.php'))
    $env:Path = (Split-Path $settings.php) + ';' + $env:Path
    $env:PHPRC = Split-Path $settings.php
    $values = Read-DotEnv
    if ($values['APP_ENV'] -ne 'local' -or $values['DB_HOST'] -notin @('127.0.0.1', 'localhost')) {
        throw 'This start script only runs a local copy of the site.'
    }
    $mysql = Join-Path $settings.xampp 'mysql\bin\mysqld.exe'
    if (-not (Get-NetTCPConnection -State Listen -LocalPort ([int]$values['DB_PORT']) -ErrorAction SilentlyContinue)) {
        Start-Process -FilePath $mysql -ArgumentList ('--defaults-file="' + (Join-Path $settings.xampp 'mysql\bin\my.ini') + '" --bind-address=127.0.0.1 --console') -WorkingDirectory (Split-Path $mysql) -RedirectStandardOutput 'storage\local\mysql.out' -RedirectStandardError 'storage\local\mysql.err' | Out-Null
        $deadline = (Get-Date).AddSeconds(30)
        do {
            Start-Sleep -Milliseconds 250
            $databaseReady = Get-NetTCPConnection -State Listen -LocalPort ([int]$values['DB_PORT']) -ErrorAction SilentlyContinue
        } until ($databaseReady -or (Get-Date) -gt $deadline)
        if (-not $databaseReady) {
            throw 'MySQL did not start. See storage\local\mysql.err.'
        }
    }
    $apache = Join-Path $settings.xampp 'apache\bin\httpd.exe'
    $root = (Join-Path $settings.xampp 'apache').Replace('\', '/')
    $php = (Split-Path $settings.php).Replace('\', '/')
    $public = (Join-Path $ProjectPath 'public').Replace('\', '/')
    $local = (Join-Path $ProjectPath 'storage\local').Replace('\', '/')
    $configuration = @"
ServerRoot "$root"
Listen 127.0.0.1:$($settings.port)
ServerName localhost
LoadModule authz_core_module modules/mod_authz_core.so
LoadModule authz_host_module modules/mod_authz_host.so
LoadModule authz_user_module modules/mod_authz_user.so
LoadModule dir_module modules/mod_dir.so
LoadModule mime_module modules/mod_mime.so
LoadModule rewrite_module modules/mod_rewrite.so
LoadModule log_config_module modules/mod_log_config.so
LoadModule alias_module modules/mod_alias.so
LoadFile "$php/libcrypto-3-x64.dll"
LoadFile "$php/libssl-3-x64.dll"
LoadFile "$php/libssh2.dll"
LoadFile "$php/php8ts.dll"
LoadFile "$php/libpq.dll"
LoadFile "$php/libsqlite3.dll"
LoadModule php_module "$php/php8apache2_4.dll"
PHPIniDir "$php"
php_admin_flag opcache.enable Off
DocumentRoot "$public"
DirectoryIndex index.php index.html
TypesConfig conf/mime.types
PidFile "$local/apache.pid"
ErrorLog "$local/apache-error.log"
CustomLog "$local/apache-access.log" common
<FilesMatch "\.php$">
    SetHandler application/x-httpd-php
</FilesMatch>
<Directory />
    AllowOverride None
    Require all denied
</Directory>
<Directory "$public">
    Options -Indexes +FollowSymLinks
    AllowOverride All
    Require local
    <FilesMatch "^(?!index\.php$).*\.(php|phtml|phar)$">
        Require all denied
    </FilesMatch>
</Directory>
"@
    $file = Join-Path $ProjectPath 'storage\local\apache.conf'
    [IO.File]::WriteAllText($file, $configuration)
    & $apache -t -f $file
    if ($LASTEXITCODE -ne 0) {
        throw 'Apache settings failed. No server was started.'
    }
    if (-not (Get-NetTCPConnection -State Listen -LocalPort ([int]$settings.port) -ErrorAction SilentlyContinue)) {
        $process = Start-Process -FilePath $apache -ArgumentList ('-f "' + $file + '"') -WorkingDirectory (Split-Path $settings.php) -PassThru
        $process.Id | Set-Content 'storage\local\apache-process.txt'
        $deadline = (Get-Date).AddSeconds(15)
        do {
            Start-Sleep -Milliseconds 250
            $ready = Get-NetTCPConnection -State Listen -LocalPort ([int]$settings.port) -ErrorAction SilentlyContinue
        } until ($ready -or (Get-Date) -gt $deadline)
        if (-not $ready) {
            throw 'Apache did not start. See storage\local\apache-error.log.'
        }
    }
    $uri = "http://127.0.0.1:$($settings.port)/_local/runtime"
    try {
        $runtime = Invoke-RestMethod -Uri $uri -TimeoutSec 15
        if ($runtime.php -notlike '8.5.*' -or $runtime.upload_progress -ne '0') {
            throw 'The website is using the wrong PHP version or settings.'
        }
        if ($runtime.project -ne $ProjectPath -or $runtime.ini -ne (Join-Path (Split-Path $settings.php) 'php.ini')) {
            throw 'This port is serving a different project or PHP folder.'
        }
    } catch {
        throw "The site check failed at $uri. The port may belong to another site. See storage\local\apache-error.log. $($_.Exception.Message)"
    }
    Write-Output "Open http://127.0.0.1:$($settings.port)"
} finally {
    Pop-Location
}
