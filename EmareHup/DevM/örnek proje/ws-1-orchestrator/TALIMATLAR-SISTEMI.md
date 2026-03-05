# Talimatlar Sistemi – Kullanım ve Teknik Açıklama

Bu projede **TALIMATLAR.md** dosyasına yazdığınız maddeleri, terminalden veya dosyayı kaydettiğinizde **Cursor CLI (AI)** otomatik uygular. Farklı Cursor oturumlarında veya başka biri projeyi açtığında bu dosyayı okuyarak sistemi anlayabilir.

---

## Ne yaptık?

1. **TALIMATLAR.md** – Doğal dille talimatlar yazıyorsunuz (örn. "README güncelle", "yeni dosya oluştur").
2. **Cursor CLI** – Bu talimatları okuyup projede değişiklik yapan AI (`agent` komutu).
3. **Script’ler** – Talimatları AI’a okutan ve isteğe bağlı dosya izleyici (watch) çalıştıran Node script’leri.
4. **Tekrar uygulama engelleme** – Tamamlanan maddeler `[x]` ile işaretlenir; bir sonraki çalıştırmada sadece işaretsiz maddeler uygulanır.

---

## Gereksinimler

- **Cursor CLI** yüklü ve giriş yapılmış olmalı.

```bash
# Kurulum (bir kez)
curl https://cursor.com/install -fsS | bash

# Giriş (bir kez)
agent login
```

Terminali kapatıp açtıktan sonra `agent` komutu kullanılabilir.

---

## Nasıl kullanılır?

### 1. Talimatları yazmak

Proje kökündeki **TALIMATLAR.md** dosyasını açın. Her maddeyi satır satır yazın. Henüz yapılmamış maddeler **işaretsiz** veya **`[ ]`** ile yazılır:

```markdown
# Talimatlar

- [ ] README.md'ye kurulum adımlarını ekle
- [ ] package.json description alanını güncelle
- [ ] docs/ klasörüne API dokümantasyonu ekle
```

### 2. Talimatları bir kez çalıştırmak

Terminalde (proje kökünde):

```bash
npm run talimatlar-ai
```

Cursor CLI, TALIMATLAR.md’yi okur ve **işaretsiz / `[ ]`** maddeleri sırayla uygular. Uyguladığı her maddeyi dosyada **`[x]`** ile işaretler; bir sonraki çalıştırmada bu maddeler atlanır.

### 3. Kaydettiğinizde otomatik çalıştırmak

Terminalde (proje kökünde) izleyiciyi başlatın ve **açık bırakın**:

```bash
npm run talimatlar-watch
```

Bundan sonra TALIMATLAR.md’yi her **kaydettiğinizde** (yaklaşık 2,5 sn debounce sonrası) AI talimatları tekrar çalıştırılır. Sadece **işaretsiz** maddeler uygulanır; `[x]` olanlar atlanır.

Durdurmak için: **Ctrl+C**.

---

## Tekrar uygulama nasıl engelleniyor?

- AI, talimatı uyguladıktan sonra **TALIMATLAR.md** içinde o satırı günceller: başına `[x]` veya `✅` ekler.
- Prompt’ta AI’a şu söylenir: **"Satırda [x], ✅ veya 'Tamamlandı' varsa bu maddeyi atla; sadece işaretsiz maddeleri uygula."**
- Böylece aynı talimat tekrar çalıştırıldığında tekrar uygulanmaz.

Yeni talimat eklediğinizde `[ ]` veya işaretsiz yazın; AI sadece onları yapacaktır.

---

## Projedeki dosyalar

| Dosya / klasör | Açıklama |
|----------------|----------|
| **TALIMATLAR.md** (proje kökü) | Talimatların yazıldığı dosya. AI bunu okur ve uygular. |
| **scripts/run-talimatlar-ai.js** | Cursor CLI’ı `agent -p --force "..."` ile çalıştırır; prompt’ta TALIMATLAR.md’yi okuyup sadece işaretsiz maddeleri uygulaması ve uyguladıklarını `[x]` ile işaretlemesi söylenir. |
| **scripts/watch-talimatlar.js** | TALIMATLAR.md’yi izler; dosya değişince (debounce sonrası) `run-talimatlar-ai.js` çalıştırır. |
| **scripts/run-talimatlar.js** | *(İsteğe bağlı)* TALIMATLAR.md içindeki **terminal komutlarını** ($ veya \`\`\`bash blokları) çalıştırır; AI kullanmaz. |
| **.cursor/rules/talimat-dosyasi.mdc** | Cursor kuralı: Kullanıcı "talimatları uygula" dediğinde TALIMATLAR.md’yi okuyup uygula (IDE içi sohbetten). |

---

## npm script’leri (package.json)

| Komut | Ne yapar? |
|-------|-----------|
| `npm run talimatlar-ai` | TALIMATLAR.md’yi AI’a okutur; sadece işaretsiz maddeleri uygular, uyguladıklarını `[x]` ile işaretler. Bir kez çalışır ve biter. |
| `npm run talimatlar-watch` | TALIMATLAR.md’yi izler; her kaydetmede (debounce sonrası) `talimatlar-ai` mantığını tetikler. Terminal açık kalmalı. |
| `npm run talimatlar` | TALIMATLAR.md’deki **terminal komutlarını** ($ veya \`\`\`bash) çalıştırır; AI kullanmaz. |

---

## Farklı Cursor oturumlarında

- Yeni bir Cursor oturumu açtığınızda veya projeyi başka biri açtığında:
  1. Bu dosyayı (**docs/TALIMATLAR-SISTEMI.md**) okuyun.
  2. Cursor CLI kurulu ve `agent login` yapılmış mı kontrol edin.
  3. Talimat yazmak için **TALIMATLAR.md** kullanın; çalıştırmak için `npm run talimatlar-ai` veya `npm run talimatlar-watch` kullanın.
- IDE içinden talimat uygulatmak için sohbette **"talimatları uygula"** veya **"TALIMATLAR.md'yi oku ve yap"** yazın (`.cursor/rules/talimat-dosyasi.mdc` bu davranışı tanımlar).

---

## Özet akış

1. **TALIMATLAR.md**’ye maddeleri yaz (işaretsiz veya `[ ]`).
2. **`npm run talimatlar-ai`** ile bir kez çalıştır **veya** **`npm run talimatlar-watch`** ile kaydettiğinde otomatik çalışsın.
3. AI sadece işaretsiz maddeleri uygular, uyguladıklarını `[x]` ile işaretler.
4. Yeni talimat ekledikçe aynı dosyaya işaretsiz satırlar ekleyin; sistem aynı şekilde çalışmaya devam eder.

Bu doküman, talimatlar sisteminin ne yaptığını ve nasıl kullanıldığını tek bir yerden anlamak için yazıldı.
