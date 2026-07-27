@echo off
REM ═══════════════════════════════════════════════════════
REM  Génération du Keystore — Smart School Academy
REM ═══════════════════════════════════════════════════════
REM
REM  Ce script génère un keystore de release pour signer
REM  l'APK de production.
REM
REM  Prérequis : Java JDK (keytool)
REM ═══════════════════════════════════════════════════════

echo.
echo ════════════════════════════════════════════════
echo   Smart School Academy — Génération Keystore
echo ════════════════════════════════════════════════
echo.
echo Ce script va créer un fichier upload-keystore.jks
echo dans le dossier android/.
echo.
echo IMPORTANT: Gardez ces informations en lieu sûr !
echo.

set /p STORE_PASS="Mot de passe du keystore (au moins 6 caractères) : "
set /p KEY_PASS="Mot de passe de la clé (identique recommandé) : "
set /p KEY_ALIAS="Alias de la clé (ex: ssa-upload) : "

if "%STORE_PASS%"=="" set STORE_PASS=ssa_release_2026
if "%KEY_PASS%"=="" set KEY_PASS=%STORE_PASS%
if "%KEY_ALIAS%"=="" set KEY_ALIAS=ssa-upload

echo.
echo Génération du keystore...
echo.

keytool -genkey -v -keystore upload-keystore.jks ^
    -alias %KEY_ALIAS% ^
    -keyalg RSA ^
    -keysize 2048 ^
    -validity 10000 ^
    -storepass %STORE_PASS% ^
    -keypass %KEY_PASS% ^
    -dname "CN=Smart School Academy, OU=Mobile, O=SmartSchool, L=Dakar, ST=Dakar, C=SN"

if %ERRORLEVEL% NEQ 0 (
    echo.
    echo ERREUR : La génération a échoué.
    echo Assurez-vous que Java JDK est installé (keytool).
    pause
    exit /b 1
)

echo.
echo ✅ Keystore créé avec succès !
echo.
echo Fichier : upload-keystore.jks
echo Alias   : %KEY_ALIAS%
echo.
echo Création du fichier key.properties...
echo.

REM Créer key.properties
echo storeFile=upload-keystore.jks > ..\key.properties
echo storePassword=%STORE_PASS% >> ..\key.properties
echo keyPassword=%KEY_PASS% >> ..\key.properties
echo keyAlias=%KEY_ALIAS% >> ..\key.properties

echo ✅ key.properties créé !
echo.
echo ════════════════════════════════════════════════
echo  Pour compiler l'APK de release :
echo.
echo  cd flutter_app
echo  flutter build apk --release
echo.
echo  L'APK sera dans : build/app/outputs/flutter-apk/
echo ════════════════════════════════════════════════
echo.

pause
