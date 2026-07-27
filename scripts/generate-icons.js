const sharp = require('sharp');
const path = require('path');
const fs = require('fs');

const SVG_PATH = path.resolve(__dirname, '..', 'public', 'images', 'icons', 'icon.svg');
const OUTPUT_DIR = path.resolve(__dirname, '..', 'public', 'images', 'icons');

const SIZES = [
  { size: 192, name: 'icon-192x192.png' },
  { size: 512, name: 'icon-512x512.png' },
];

async function generateIcons() {
  console.log('🎨 Génération des icônes PWA...\n');

  // Vérifier que le SVG source existe
  if (!fs.existsSync(SVG_PATH)) {
    console.error(`❌ SVG introuvable : ${SVG_PATH}`);
    process.exit(1);
  }

  const svgBuffer = fs.readFileSync(SVG_PATH);

  for (const { size, name } of SIZES) {
    const outputPath = path.join(OUTPUT_DIR, name);

    try {
      await sharp(svgBuffer)
        .resize(size, size)
        .png()
        .toFile(outputPath);

      const stats = fs.statSync(outputPath);
      const fileSizeKB = (stats.size / 1024).toFixed(1);
      console.log(`  ✅ ${name} — ${size}x${size} (${fileSizeKB} Ko)`);
    } catch (err) {
      console.error(`  ❌ Erreur pour ${name}: ${err.message}`);
    }
  }

  console.log('\n✨ Génération terminée !');
}

generateIcons();
