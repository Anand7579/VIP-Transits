# Push main to GitHub (private repo). Run on YOUR PC only — never paste tokens in chat or commit them.
# Usage:
#   cd "e:\VIP Transits\vip-transits"
#   .\scripts\push-github.ps1
#
# Optional: set token for this session only (PowerShell), then run script:
#   $env:GITHUB_TOKEN = "ghp_YOUR_NEW_TOKEN"
#   .\scripts\push-github.ps1

$ErrorActionPreference = 'Stop'
$repoRoot = Split-Path -Parent $PSScriptRoot
Set-Location $repoRoot

if (-not $env:GITHUB_TOKEN -or $env:GITHUB_TOKEN.Trim() -eq '') {
	Write-Host 'Enter a GitHub Personal Access Token (classic, scope: repo). Input is hidden.' -ForegroundColor Cyan
	$secure = Read-Host 'PAT' -AsSecureString
	$bstr   = [System.Runtime.InteropServices.Marshal]::SecureStringToBSTR($secure)
	try {
		$env:GITHUB_TOKEN = [System.Runtime.InteropServices.Marshal]::PtrToStringAuto($bstr)
	} finally {
		[System.Runtime.InteropServices.Marshal]::ZeroFreeBSTR($bstr)
	}
}

$token = $env:GITHUB_TOKEN.Trim()
if ($token -notmatch '^ghp_') {
	Write-Warning 'Token should start with ghp_. If push fails, create a new classic token with repo scope.'
}

$pushUrl = "https://x-access-token:${token}@github.com/Anand7579/VIP-Transits.git"

Write-Host "Pushing main to Anand7579/VIP-Transits ..." -ForegroundColor Cyan
git push -u $pushUrl main

if ($LASTEXITCODE -eq 0) {
	Write-Host 'Done. Verify: https://github.com/Anand7579/VIP-Transits' -ForegroundColor Green
} else {
	Write-Host 'Push failed. Common fixes:' -ForegroundColor Yellow
	Write-Host '  1. Revoke any token you pasted in chat; create a new one (repo scope).'
	Write-Host '  2. Create the repo: https://github.com/new name VIP-Transits, Private, no README.'
	Write-Host '  3. Ensure you are logged in as Anand7579 on GitHub.'
	exit $LASTEXITCODE
}

# Clear token from this session (optional hygiene)
Remove-Item Env:GITHUB_TOKEN -ErrorAction SilentlyContinue
