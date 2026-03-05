# Talimatlar Sistemi – Kullanım

Bu projede **TALIMATLAR.md** dosyasına yazdığınız maddeler, yerel Node script ile uygulanır. **Cursor CLI gerekmez**; VS Code / Copilot içinde açıkken aynı şekilde çalışır.

## Nasıl kullanılır?

1. **TALIMATLAR.md** — İşaretsiz maddeler `- [ ]` ile yazılır.
2. **Tek seferlik:** `APPLY=true npm run talimatlar-ai`
3. **Kaydettiğinizde otomatik:** `npm run talimatlar-watch` (terminalde açık bırakın)
4. Tamamlanan maddeler `[x]` ile işaretlenir; sonraki çalıştırmada atlanır.

## npm script’leri

| Komut | Ne yapar? |
|-------|-----------|
| `npm run talimatlar-ai` | TALIMATLAR.md’deki işaretsiz maddeleri uygular (APPLY=true ile dosyayı günceller). |
| `npm run talimatlar-watch` | TALIMATLAR.md’yi izler; kaydedince APPLY=true ile talimatlar-ai tetikler. |
| `npm run talimatlar` | TALIMATLAR.md’deki bash bloklarını listeler; `EXEC=true` ile çalıştırır. |

## Özet

1. TALIMATLAR.md’ye maddeleri yaz (`- [ ]`).
2. `APPLY=true npm run talimatlar-ai` veya `npm run talimatlar-watch` ile çalıştır.
3. Yeni talimat ekledikçe işaretsiz satır ekleyin; sistem aynı şekilde çalışır.
