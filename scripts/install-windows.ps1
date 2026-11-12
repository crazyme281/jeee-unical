param([string]$XamppPath = 'C:\xampp')

$ErrorActionPreference = 'Stop'
$temp = Join-Path ([IO.Path]::GetTempPath()) 'jetsi-install'
New-Item -ItemType Directory -Path $temp -Force | Out-Null

function Get-CheckedDownload {
    param([string]$Url, [string]$File, [string]$Hash)
    if (-not (Test-Path -LiteralPath $File) -or (Get-FileHash -LiteralPath $File -Algorithm SHA256).Hash -ne $Hash) {
        curl.exe --fail --location --silent --show-error --retry 2 --output $File $Url
        if ($LASTEXITCODE -ne 0) {
            throw "Download failed: $Url. Download this file yourself and place it at $File."
        }
    }
    if ((Get-FileHash -LiteralPath $File -Algorithm SHA256).Hash -ne $Hash) {
        throw "The download check failed: $File. Do not run it."
    }
}

$installer = Join-Path $temp 'xampp-installer.exe'
if (-not (Test-Path -LiteralPath $XamppPath)) {
    Get-CheckedDownload 'https://downloads.sourceforge.net/project/xampp/XAMPP%20Windows/8.5.12/xampp-windows-x64-8.5.12-0-VS17-installer.exe' $installer '54c4c492def0734fa156843b571036bddbb7020f89e9881ba9d8c02db124263c'
    $process = Start-Process -FilePath $installer -ArgumentList ('--mode unattended --unattendedmodeui none --prefix "' + $XamppPath + '" --disable-components xampp_filezilla,xampp_mercury,xampp_tomcat,xampp_perl,xampp_webalizer,xampp_sendmail') -Wait -PassThru
    if ($process.ExitCode -ne 0) {
        throw 'XAMPP could not be installed. Run the downloaded installer yourself and select Apache, MySQL, PHP, and phpMyAdmin.'
    }
}
if (-not (Test-Path -LiteralPath (Join-Path $XamppPath 'apache\bin\httpd.exe'))) {
    throw 'This is not a XAMPP folder. Choose its full folder path.'
}

$phpZip = Join-Path $temp 'php-8.5.12-1.zip'
Get-CheckedDownload 'https://downloads.php.net/~windows/releases/archives/php-8.5.12-1-Win32-vs17-x64.zip' $phpZip '0dacd6484b092b5e9ae9fa3d7f96a937caf9d874eee2d980b68ed3129741144a'
$phpFolder = Join-Path $XamppPath 'php-8.5.12-1'
if (-not (Test-Path -LiteralPath $phpFolder)) {
    Expand-Archive -LiteralPath $phpZip -DestinationPath $phpFolder
    $folder = $phpFolder.Replace('\', '/')
    $xampp = $XamppPath.Replace('\', '/')
    $configuration = @"
extension_dir="$folder/ext"
extension=curl
extension=exif
extension=fileinfo
extension=gd
extension=intl
extension=mbstring
extension=mysqli
extension=openssl
extension=pdo_mysql
extension=pdo_pgsql
extension=pdo_sqlite
extension=zip
memory_limit=512M
max_execution_time=120
post_max_size=230M
upload_max_filesize=210M
date.timezone=Africa/Lagos
display_errors=Off
log_errors=On
error_reporting=E_ALL
session.save_path="$xampp/tmp"
session.upload_progress.enabled=0
opcache.enable=0
opcache.enable_cli=0
curl.cainfo="$xampp/apache/bin/curl-ca-bundle.crt"
openssl.cafile="$xampp/apache/bin/curl-ca-bundle.crt"
"@
    [IO.File]::WriteAllText((Join-Path $phpFolder 'php.ini'), $configuration)
}
$composerFolder = Join-Path $XamppPath 'composer'
New-Item -ItemType Directory -Path $composerFolder -Force | Out-Null
$composer = Join-Path $composerFolder 'composer.phar'
Get-CheckedDownload 'https://getcomposer.org/download/2.11.2/composer.phar' $composer 'dd158c0afd41d15fac89e1b4e47b5d42911fe316c3687cd5edac98319b25d028'
& (Join-Path $phpFolder 'php.exe') -c (Join-Path $phpFolder 'php.ini') "$PSScriptRoot\runtime-check.php"
if ($LASTEXITCODE -ne 0) {
    throw 'PHP could not start or a required setting is missing. Install Microsoft Visual C++ 2015-2022 x64 from https://aka.ms/vs/17/release/vc_redist.x64.exe if Windows reports missing runtime files.'
}
Write-Output 'Installation finished. Now run scripts\setup-local.ps1 with your XAMPP path.'
