# Talimatlar Sistemi — ws-cursor

## API'siz mi, API ile mi?

- **API'siz:** `APPLY=true npm run talimatlar-ai` ve `npm run talimatlar-watch` çalışır. Sadece script içindeki kalıplar ve heuristic'ler kullanılır; Cursor API veya agent gerekmez. Bazı maddeler "Atlandı" kalabilir.
- **API/Agent ile:** `npm run talimatlar-agent` için Cursor CLI kurulu ve `agent login` yapılmış olmalı. Doğal dil talimatları AI tarafından uygulanır.

## Komutlar

| Komut | Açıklama |
|-------|----------|
| `APPLY=true npm run talimatlar-ai` | Yerel script; kalıp/heuristic ile maddeleri uygular, [x] işaretler. |
| `npm run talimatlar-watch` | TALIMATLAR.md'yi izler; kaydedince yukarıdaki gibi tetikler. |
| `npm run talimatlar-agent` | Cursor CLI ile TALIMATLAR.md'yi AI'a gönderir (agent gerekli). |
| `npm run talimatlar` | Bash bloklarını listeler; `EXEC=true` ile çalıştırır. |

## Cursor IDE sohbeti

Sohbette **"TALIMATLAR.md'yi uygula"** veya **"işaretsiz maddeleri yap"** yazarsan, .cursor/rules sayesinde AI bu dosyayı okuyup maddeleri uygulayabilir ve [x] işaretleyebilir.
