#!/usr/bin/env node
const fs = require('fs');
const path = require('path');
const { spawn } = require('child_process');

const ROOT = path.resolve(__dirname, '..');
const TALIMATLAR_PATH = path.join(ROOT, 'TALIMATLAR.md');

function hasUncheckedItems(content) {
  const lines = content.split(/\r?\n/);
  for (const line of lines) {
    const t = line.trim();
    if (t.startsWith('- [ ]')) return true;
    if (t.startsWith('- ') && !/[x✅]/.test(t) && t.length > 4) return true;
    if (/^\d+\.\s+/.test(t) && !/\[x\]|✅|Tamamlandi/.test(t)) return true;
  }
  return false;
}

const prompt = [
  'Read TALIMATLAR.md in this project root.',
  'Only execute instructions that are NOT yet marked as done (no [x], no ✅, no "Tamamlandi" on that line).',
  'Skip any line that already has [x], ✅ or "Tamamlandi".',
  'After completing an instruction, update TALIMATLAR.md and mark it as done.',
  'Apply all changes directly. Do not ask for confirmation.'
].join(' ');

if (!fs.existsSync(TALIMATLAR_PATH)) {
  console.log('TALIMATLAR.md bulunamadi.');
  process.exit(0);
}

const content = fs.readFileSync(TALIMATLAR_PATH, 'utf8');
if (!hasUncheckedItems(content)) {
  console.log('Yeni talimat yok. AI cagrilmadi, token kullanilmadi.');
  process.exit(0);
}

console.log('Cursor AI talimatlari uyguluyor...');

const child = spawn('agent', ['-p', '--force', prompt], {
  cwd: ROOT,
  stdio: 'inherit',
  shell: false,
});

child.on('error', (err) => {
  if (err.code === 'ENOENT') {
    console.error('Cursor CLI bulunamadi. Kurulum: curl https://cursor.com/install -fsS | bash');
  } else {
    console.error(err);
  }
  process.exit(1);
});

child.on('close', (code) => {
  if (code !== 0) {
    console.log('\nIlk kullanimda giris gerekli: agent login');
  }
  process.exit(code || 0);
});
