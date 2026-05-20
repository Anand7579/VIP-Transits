# Push latest VIP Transits changes to GitHub backup.
# Usage: .\scripts\push-to-github.ps1
#        .\scripts\push-to-github.ps1 -Message "Describe your changes"

param(
	[string]$Message = ""
)

$ErrorActionPreference = "Stop"
$root = Split-Path -Parent $PSScriptRoot
Set-Location $root

if (-not (Test-Path ".git")) {
	Write-Error "Not a git repository. Run initial setup from README.md first."
}

if ([string]::IsNullOrWhiteSpace($Message)) {
	$Message = "Update: $(Get-Date -Format 'yyyy-MM-dd HH:mm')"
}

git add -A
$status = git status --porcelain
if ([string]::IsNullOrWhiteSpace($status)) {
	Write-Host "Nothing to commit — working tree clean."
	exit 0
}

git status --short
git commit -m $Message
git push origin HEAD

Write-Host "Backup pushed to origin ($(git rev-parse --abbrev-ref HEAD))."
