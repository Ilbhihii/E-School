#!/bin/bash
# ═══════════════════════════════════════════════════════
#  Génération du Keystore — Smart School Academy
# ═══════════════════════════════════════════════════════
#  Prérequis : Java JDK (keytool)
# ═══════════════════════════════════════════════════════

set -e

echo ""
echo "════════════════════════════════════════════════"
echo "  Smart School Academy — Génération Keystore"
echo "════════════════════════════════════════════════"
echo ""
echo "Ce script va créer un fichier upload-keystore.jks"
echo "dans le dossier android/."
echo ""

read -r -p "Mot de passe du keystore [ssa_release_2026]: " STORE_PASS
STORE_PASS=${STORE_PASS:-ssa_release_2026}
read -r -p "Mot de passe de la clé [même que keystore]: " KEY_PASS
KEY_PASS=${KEY_PASS:-$STORE_PASS}
read -r -p "Alias de la clé [ssa-upload]: " KEY_ALIAS
KEY_ALIAS=${KEY_ALIAS:-ssa-upload}

echo ""
echo "Génération du keystore..."

keytool -genkey -v -keystore upload-keystore.jks \
    -alias "$KEY_ALIAS" \
    -keyalg RSA \
    -keysize 2048 \
    -validity 10000 \
    -storepass "$STORE_PASS" \
    -keypass "$KEY_PASS" \
    -dname "CN=Smart School Academy, OU=Mobile, O=SmartSchool, L=Dakar, ST=Dakar, C=SN"

echo ""
echo "✅ Keystore créé : upload-keystore.jks"
echo ""

# Créer key.properties
cat > ../key.properties << EOF
storeFile=upload-keystore.jks
storePassword=${STORE_PASS}
keyPassword=${KEY_PASS}
keyAlias=${KEY_ALIAS}
EOF

echo "✅ key.properties créé dans android/"
echo ""
echo "════════════════════════════════════════════════"
echo "  Pour compiler l'APK de release :"
echo ""
echo "  cd flutter_app"
echo "  flutter build apk --release"
echo ""
echo "  L'APK sera dans : build/app/outputs/flutter-apk/"
echo "════════════════════════════════════════════════"
