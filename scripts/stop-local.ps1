. "$PSScriptRoot\local-common.ps1"
$file = Join-Path $ProjectPath 'storage\local\apache-process.txt'
if (Test-Path -LiteralPath $file) {
    $id = [int](Get-Content -LiteralPath $file)
    $process = Get-CimInstance Win32_Process -Filter "ProcessId=$id"
    if ($process -and $process.CommandLine -like "*$ProjectPath*storage*local*apache.conf*") {
        $children = @(Get-CimInstance Win32_Process | Where-Object { $_.ParentProcessId -eq $id } | Select-Object -ExpandProperty ProcessId)
        if ($children) {
            Stop-Process -Id $children -ErrorAction SilentlyContinue
        }
        Stop-Process -Id $id -ErrorAction SilentlyContinue
    }
}
Write-Output 'The project server is stopped. MySQL is left running for other projects.'
