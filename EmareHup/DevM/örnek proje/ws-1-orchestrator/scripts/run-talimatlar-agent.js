// Bu script varsa gerçek Cursor `agent` komutunu çalıştırır.
// Güvenlik nedeniyle önce `agent` komutunun varlığını kontrol eder.
// Eğer yoksa, yerel simülasyonu (`run-talimatlar-ai.js`) çağırır.

const { spawnSync } = require('child_process');
const path = require('path');

function hasAgent() {
  try {
    const res = spawnSync('agent', ['--version'], { stdio: 'ignore' });
    return res.status === 0 || res.status === null;
  } catch (e) {
    return false;
  }
}

if (hasAgent()) {
  console.log('agent bulundu — TALIMATLAR.md agent ile çalıştırılıyor...');
  // Örnek: agent -p --force "..." şeklinde çalıştırma. Gerçek prompt burada konulmalı.
  const talimatPath = path.resolve(__dirname, '..', 'TALIMATLAR.md');
  const prompt = `Lütfen ${talimatPath} içindeki işaretsiz maddeleri (- [ ]) uygula ve uyguladıklarını satıra [x] ile işaretle.`;
  const res = spawnSync('agent', ['-p', '--force', prompt], { stdio: 'inherit', shell: false });
  if (res.error) {
    console.error('agent çalıştırılırken hata:', res.error);
    process.exit(1);
  }
  process.exit(res.status);
} else {
  console.log('agent bulunamadı — yerel simülasyon çalıştırılıyor. Eğer Cursor CLI kuruluysa `agent login` yapın.');
  const env = Object.assign({}, process.env, { APPLY: 'true' });
  const res = spawnSync(process.execPath, [path.resolve(__dirname, 'run-talimatlar-ai.js')], { stdio: 'inherit', env });
  process.exit(res.status);
}
