# Emare Finance — Sektörel Versiyonlar (Market / Kafe / Butik / Toptan / Hizmet)

> Amaç: Emare Finance’in modüler yapısını “sektöre hazır paketler” haline getirip satış ve kurulum hızını artırmak.  
> Tarih: Mart 2026

---

## 1) Yaklaşım

Sektörel versiyon = **Ön tanımlı modül seti + varsayılan ayarlar + önerilen donanım**.

- Modüller aynı kalır.
- Sektör şablonu, kurulum sırasında “seçim” olarak sunulur.
- Seçilen şablon, `branch_modules` ve `branch.settings` içine uygulanır.

---

## 2) Sektör Şablonları

### 2.1 Market / Bakkal
**Öncelik:** Hızlı satış + terazi + stok

Aktif modüller:
- core_pos
- hardware
- label_print (opsiyonel)
- scale_integration
- basic_reports

Önerilen donanım:
- Fiş yazıcı (ESC/POS)
- Barkod okuyucu (keyboard wedge)
- Terazi (serial)
- Kasa çekmecesi (yazıcı kick)

Varsayılan ayarlar:
- KDV ve birim ayarları
- Terazi barkod şeması (ürün terazi barkodu)

---

### 2.2 Kafe / Restoran (Basit POS)
**Öncelik:** Hızlı satış + personel

Aktif modüller:
- core_pos
- staff
- income_expense (opsiyonel)
- basic_reports
- hardware

Önerilen donanım:
- Fiş yazıcı
- Müşteri ekranı (opsiyonel)

Varsayılan ayarlar:
- Hızlı ürün butonları / kategoriler
- Personel vardiya takibi (basit)

---

### 2.3 Butik / Perakende
**Öncelik:** Etiket + varyant

Aktif modüller:
- core_pos
- label_print
- basic_reports
- hardware

Önerilen donanım:
- Etiket yazıcı (TSPL/ZPL)
- Barkod okuyucu

Varsayılan ayarlar:
- Varyant tipi aktif (beden/renk)
- Etiket tasarım şablonları

---

### 2.4 Toptan / Depolu İşletme
**Öncelik:** Çoklu depo/şube + rapor

Aktif modüller:
- core_pos
- multi_branch
- advanced_reports
- income_expense
- staff
- hardware
- einvoice (opsiyonel)

Önerilen donanım:
- A4 yazıcı (fatura/irsaliye)
- Barkod okuyucu

Varsayılan ayarlar:
- Şube bazlı stok/fiyat
- A4 çıktılar: fatura, sevk irsaliyesi

---

### 2.5 Hizmet İşletmesi (Küçük)
**Öncelik:** Cari + gelir/gider

Aktif modüller:
- core_pos
- income_expense
- basic_reports

Önerilen donanım:
- opsiyonel fiş yazıcı

Varsayılan ayarlar:
- Hizmet ürün tipi (stok takipsiz)
- Cari bakiye akışı görünür

---

## 3) Uygulama Akışı (Kurulum Sihirbazı)

1. Sektör seçimi (Market / Kafe / Butik / Toptan / Hizmet)
2. Şube bilgileri
3. Donanım seçimi ve test
4. Modüllerin otomatik aktif edilmesi
5. Varsayılan rapor/etiket şablonlarının yüklenmesi

---

## 4) Teknik Uygulama (Özet)

- `industry_templates` (opsiyonel tablo) veya config tabanlı JSON şablon
- Şablon içeriği:
  - enabled_modules: [..]
  - default_settings: {..}
  - recommended_hardware: {..}
- Apply işlemi:
  - `branch_modules` upsert
  - `branches.settings` merge

---

## 5) Kabul Kriterleri

- [ ] Sektör seçince modüller otomatik aktif oluyor
- [ ] Donanım önerileri ve kurulum adımları sektörle uyumlu
- [ ] Varsayılan ayarlar ve şablonlar uygulanıyor
