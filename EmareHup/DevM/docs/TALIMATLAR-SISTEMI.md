# Talimatlar Sistemi

Bu proje, TALIMATLAR.md dosyasindan otomatik calisan bir is akisi kullanir.

## Komutlar

- `npm run talimatlar-ai`: TALIMATLAR.md dosyasini AI'a okutur ve isaretlenmemis maddeleri uygular.
- `npm run talimatlar-watch`: TALIMATLAR.md dosyasini izler; her kaydetmede AI tetikler.
- `npm run talimatlar`: TALIMATLAR.md icindeki terminal komutlarini calistirir (token harcamaz).

## Isleyis

1. Talimatlari TALIMATLAR.md dosyasina yazin.
2. Yapilan maddeler [x] ile isaretlenir.
3. Sonraki calistirmada [x] olanlar atlanir.

## Gereksinim

Cursor CLI kurulu ve `agent login` yapilmis olmalidir.
