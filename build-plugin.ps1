# Caminhos
$pluginDir = "botao-whatsapp"
$versionFile = "$pluginDir\botao-whatsapp.php"
$updateJson = "update.json"
$buildDir = "build"

# Extrair versão atual
$versionLine = Select-String -Path $versionFile -Pattern "Version:" | Select-Object -First 1
$currentVersion = $versionLine.ToString().Split(":")[1].Trim()
Write-Host "Versão atual: $currentVersion"

# Nova versão
$newVersion = Read-Host "Digite a nova versão (ex: 1.2)"

# Atualizar no PHP
(Get-Content $versionFile) | ForEach-Object {
    if ($_ -match "Version:") {
        $_ -replace "Version: .*", "Version: $newVersion"
    } else {
        $_
    }
} | Set-Content $versionFile

# Atualizar no JSON
(Get-Content $updateJson) | ForEach-Object {
    if ($_ -match '"version":') {
        $_ -replace '"version":\s*".*"', '"version": "' + $newVersion + '"'
    } else {
        $_
    }
} | Set-Content $updateJson

# Criar diretório de build
if (!(Test-Path -Path $buildDir)) {
    New-Item -ItemType Directory -Path $buildDir | Out-Null
}

# Criar o ZIP
$zipName = "$buildDir\botao-whatsapp-v$newVersion.zip"
if (Test-Path $zipName) { Remove-Item $zipName -Force }
Compress-Archive -Path $pluginDir -DestinationPath $zipName -Force

Write-Host "✅ Build finalizada: $zipName"
Write-Host "📦 Versão atualizada para $newVersion"
