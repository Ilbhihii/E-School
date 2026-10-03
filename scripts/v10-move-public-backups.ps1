$ErrorActionPreference = 'Stop'

$destinationRoot = Join-Path $PSScriptRoot '..\storage\feature-backups'
New-Item -ItemType Directory -Force -Path $destinationRoot | Out-Null

$backups = Get-ChildItem -Path (Join-Path $PSScriptRoot '..\public') -Directory -Filter 'storage_backup_*' -ErrorAction SilentlyContinue

if (-not $backups) {
    Write-Host 'No public/storage_backup_* directory found.'
    exit 0
}

foreach ($backup in $backups) {
    $stamp = Get-Date -Format 'yyyyMMdd_HHmmss'
    $destination = Join-Path $destinationRoot ($backup.Name + '_' + $stamp)
    Write-Host ("Moving {0} -> {1}" -f $backup.FullName, $destination)
    Move-Item -Path $backup.FullName -Destination $destination
}
