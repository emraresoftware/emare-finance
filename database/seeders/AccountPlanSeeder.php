<?php

namespace Database\Seeders;

use App\Models\AccountPlan;
use Illuminate\Database\Seeder;

class AccountPlanSeeder extends Seeder
{
    public function run(): void
    {
        // Varsa temizle
        AccountPlan::query()->delete();

        $accounts = $this->getAccounts();

        $keys = ['code', 'name', 'type', 'normal_balance', 'level', 'parent_code', 'is_system'];

        foreach ($accounts as $acc) {
            AccountPlan::create(array_combine($keys, $acc));
        }

        $this->command->info('Tekdüzen Hesap Planı yüklendi: ' . count($accounts) . ' hesap.');
    }

    private function getAccounts(): array
    {
        // [code, name, type, normal_balance, level, parent_code, is_system]
        // type: asset | liability | equity | revenue | cost | expense
        // normal_balance: debit | credit  (Aktif/Gider = debit, Pasif/Gelir = credit)

        return [
            // ════════════════════════════════════════════════════
            // 1. DÖNEN VARLIKLAR
            // ════════════════════════════════════════════════════
            ['1',   'DÖNEN VARLIKLAR',                          'asset', 'debit', 1, null,  true],
            ['10',  'HAZIR DEĞERLER',                           'asset', 'debit', 2, '1',   true],
            ['100', 'KASA',                                     'asset', 'debit', 3, '10',  true],
            ['101', 'ALINAN ÇEKLER',                            'asset', 'debit', 3, '10',  true],
            ['102', 'BANKALAR',                                 'asset', 'debit', 3, '10',  true],
            ['103', 'VERİLEN ÇEKLER VE ÖDEME EMİRLERİ (-)',     'asset', 'credit',3, '10',  false],
            ['108', 'DİĞER HAZIR DEĞERLER',                    'asset', 'debit', 3, '10',  false],

            ['11',  'MENKUL KIYMETLER',                        'asset', 'debit', 2, '1',   false],
            ['110', 'HİSSE SENETLERİ',                         'asset', 'debit', 3, '11',  false],
            ['111', 'ÖZEL KESİM TAHVİL SENET VE BONOLARI',    'asset', 'debit', 3, '11',  false],
            ['112', 'KAMU KESİMİ TAHVİL SENET VE BONOLARI',   'asset', 'debit', 3, '11',  false],
            ['118', 'DİĞER MENKUL KIYMETLER',                  'asset', 'debit', 3, '11',  false],
            ['119', 'MENKUL KIYMETLER DEĞER DÜŞÜKLÜĞÜ KARŞILIĞI (-)', 'asset', 'credit', 3, '11', false],

            ['12',  'TİCARİ ALACAKLAR',                        'asset', 'debit', 2, '1',   true],
            ['120', 'ALICILAR',                                 'asset', 'debit', 3, '12',  true],
            ['121', 'ALACAK SENETLERİ',                        'asset', 'debit', 3, '12',  false],
            ['122', 'ALACAK SENETLERİ REESKONTU (-)',          'asset', 'credit',3, '12',  false],
            ['126', 'VERİLEN DEPOZİTO VE TEMİNATLAR',         'asset', 'debit', 3, '12',  false],
            ['128', 'ŞÜPHELİ TİCARİ ALACAKLAR',               'asset', 'debit', 3, '12',  false],
            ['129', 'ŞÜPHELİ TİCARİ ALACAKLAR KARŞILIĞI (-)', 'asset', 'credit',3, '12',  false],

            ['13',  'DİĞER ALACAKLAR',                         'asset', 'debit', 2, '1',   false],
            ['131', 'ORTAKLARDAN ALACAKLAR',                   'asset', 'debit', 3, '13',  false],
            ['132', 'İŞTİRAKLERDEN ALACAKLAR',                 'asset', 'debit', 3, '13',  false],
            ['135', 'PERSONELDEN ALACAKLAR',                   'asset', 'debit', 3, '13',  false],
            ['136', 'DİĞER ÇEŞİTLİ ALACAKLAR',               'asset', 'debit', 3, '13',  false],
            ['137', 'DİĞER ALACAK SENETLERİ REESKONTU (-)',   'asset', 'credit',3, '13',  false],
            ['138', 'ŞÜPHELİ DİĞER ALACAKLAR',               'asset', 'debit', 3, '13',  false],
            ['139', 'ŞÜPHELİ DİĞER ALACAKLAR KARŞILIĞI (-)', 'asset', 'credit',3, '13',  false],

            ['15',  'STOKLAR',                                 'asset', 'debit', 2, '1',   true],
            ['150', 'İLK MADDE VE MALZEME',                   'asset', 'debit', 3, '15',  false],
            ['151', 'YARI MAMULLER - ÜRETİM',                 'asset', 'debit', 3, '15',  false],
            ['152', 'MAMULLER',                                'asset', 'debit', 3, '15',  false],
            ['153', 'TİCARİ MALLAR',                           'asset', 'debit', 3, '15',  true],
            ['157', 'DİĞER STOKLAR',                           'asset', 'debit', 3, '15',  false],
            ['158', 'STOK DEĞER DÜŞÜKLÜĞÜ KARŞILIĞI (-)',     'asset', 'credit',3, '15',  false],
            ['159', 'VERİLEN SİPARİŞ AVANSLARI',              'asset', 'debit', 3, '15',  false],

            ['18',  'GELECEK AYLARA AİT GİDERLER VE GELİR TAHAKKUKLARI', 'asset', 'debit', 2, '1', false],
            ['180', 'GELECEK AYLARA AİT GİDERLER',            'asset', 'debit', 3, '18',  false],
            ['181', 'GELİR TAHAKKUKLARI',                     'asset', 'debit', 3, '18',  false],

            ['19',  'DİĞER DÖNEN VARLIKLAR',                  'asset', 'debit', 2, '1',   false],
            ['190', 'DEVREDEN KDV',                            'asset', 'debit', 3, '19',  true],
            ['191', 'İNDİRİLECEK KDV',                        'asset', 'debit', 3, '19',  true],
            ['192', 'DİĞER KDV',                               'asset', 'debit', 3, '19',  false],
            ['193', 'PEŞİN ÖDENEN VERGİLER VE FONLAR',        'asset', 'debit', 3, '19',  false],
            ['194', 'PEŞİN ÖDENEN VERGİLER',                  'asset', 'debit', 3, '19',  false],
            ['195', 'İŞ AVANSLARI',                            'asset', 'debit', 3, '19',  false],
            ['196', 'PERSONEL AVANSLARI',                     'asset', 'debit', 3, '19',  false],
            ['197', 'SAYIM VE TESELLÜM NOKSANLARI',           'asset', 'debit', 3, '19',  false],
            ['198', 'DİĞER ÇEŞİTLİ DÖNEN VARLIKLAR',         'asset', 'debit', 3, '19',  false],
            ['199', 'DİĞER DÖNEN VARLIKLAR KARŞILIĞI (-)',    'asset', 'credit',3, '19',  false],

            // ════════════════════════════════════════════════════
            // 2. DURAN VARLIKLAR
            // ════════════════════════════════════════════════════
            ['2',   'DURAN VARLIKLAR',                         'asset', 'debit', 1, null,  true],
            ['22',  'TİCARİ ALACAKLAR',                        'asset', 'debit', 2, '2',   false],
            ['220', 'ALICILAR',                                 'asset', 'debit', 3, '22',  false],
            ['221', 'ALACAK SENETLERİ',                        'asset', 'debit', 3, '22',  false],
            ['226', 'VERİLEN DEPOZİTO VE TEMİNATLAR',         'asset', 'debit', 3, '22',  false],

            ['24',  'MALİ DURAN VARLIKLAR',                   'asset', 'debit', 2, '2',   false],
            ['240', 'BAĞLI MENKUL KIYMETLER',                 'asset', 'debit', 3, '24',  false],
            ['241', 'BAĞLI MENKUL KIYMETLER DEĞER DÜŞÜKLÜĞÜ KARŞILIĞI (-)', 'asset', 'credit', 3, '24', false],
            ['242', 'İŞTİRAKLER',                              'asset', 'debit', 3, '24',  false],
            ['243', 'İŞTİRAKLER SERMAYE TAAHHÜTLERI (-)',     'asset', 'credit',3, '24',  false],
            ['245', 'BAĞLI ORTAKLIKLAR',                       'asset', 'debit', 3, '24',  false],

            ['25',  'MADDİ DURAN VARLIKLAR',                  'asset', 'debit', 2, '2',   true],
            ['250', 'ARAZİ VE ARSALAR',                        'asset', 'debit', 3, '25',  false],
            ['251', 'YER ALTI VE YER ÜSTÜ DÜZENLEMELERİ',    'asset', 'debit', 3, '25',  false],
            ['252', 'BİNALAR',                                 'asset', 'debit', 3, '25',  false],
            ['253', 'TESİS, MAKİNE VE CİHAZLAR',             'asset', 'debit', 3, '25',  false],
            ['254', 'TAŞITLAR',                                'asset', 'debit', 3, '25',  false],
            ['255', 'DEMİRBAŞLAR',                            'asset', 'debit', 3, '25',  true],
            ['256', 'DİĞER MADDİ DURAN VARLIKLAR',            'asset', 'debit', 3, '25',  false],
            ['257', 'BİRİKMİŞ AMORTİSMANLAR (-)',             'asset', 'credit',3, '25',  true],
            ['258', 'YAPILMAKTA OLAN YATIRIMLAR',              'asset', 'debit', 3, '25',  false],
            ['259', 'VERİLEN AVANSLAR',                       'asset', 'debit', 3, '25',  false],

            ['26',  'MADDİ OLMAYAN DURAN VARLIKLAR',          'asset', 'debit', 2, '2',   false],
            ['260', 'HAKLAR',                                  'asset', 'debit', 3, '26',  false],
            ['261', 'ŞEREFİYE',                               'asset', 'debit', 3, '26',  false],
            ['262', 'KURULUŞ VE ÖRGÜTLENME GİDERLERİ',       'asset', 'debit', 3, '26',  false],
            ['263', 'ARAŞTIRMA VE GELİŞTİRME GİDERLERİ',    'asset', 'debit', 3, '26',  false],
            ['264', 'ÖZEL MALİYETLER',                        'asset', 'debit', 3, '26',  false],
            ['267', 'DİĞER MADDİ OLMAYAN DURAN VARLIKLAR',   'asset', 'debit', 3, '26',  false],
            ['268', 'BİRİKMİŞ AMORTİSMANLAR (-)',            'asset', 'credit',3, '26',  false],
            ['269', 'VERİLEN AVANSLAR',                       'asset', 'debit', 3, '26',  false],

            ['28',  'GELECEK YILLARA AİT GİDERLER VE GELİR TAHAKKUKLARI', 'asset', 'debit', 2, '2', false],
            ['280', 'GELECEK YILLARA AİT GİDERLER',          'asset', 'debit', 3, '28',  false],
            ['281', 'GELİR TAHAKKUKLARI',                     'asset', 'debit', 3, '28',  false],

            ['29',  'DİĞER DURAN VARLIKLAR',                  'asset', 'debit', 2, '2',   false],
            ['291', 'GELECEK YILLARDA İNDİRİLECEK KDV',      'asset', 'debit', 3, '29',  false],
            ['292', 'DİĞER KDV',                              'asset', 'debit', 3, '29',  false],
            ['293', 'GELECEK YILLAR İÇİN PEŞİN ÖDENEN VERGİLER', 'asset', 'debit', 3, '29', false],
            ['294', 'TESLİM EDİLECEK STOKLAR VE SIPARIŞ AVANSLARI', 'asset', 'debit', 3, '29', false],
            ['297', 'DİĞER ÇEŞİTLİ DURAN VARLIKLAR',        'asset', 'debit', 3, '29',  false],
            ['298', 'STOK DEĞER DÜŞÜKLÜĞÜ KARŞILIĞI (-)',    'asset', 'credit',3, '29',  false],
            ['299', 'BİRİKMİŞ AMORTİSMANLAR (-)',            'asset', 'credit',3, '29',  false],

            // ════════════════════════════════════════════════════
            // 3. KISA VADELİ YABANCI KAYNAKLAR
            // ════════════════════════════════════════════════════
            ['3',   'KISA VADELİ YABANCI KAYNAKLAR',          'liability', 'credit', 1, null, true],
            ['30',  'MALİ BORÇLAR',                            'liability', 'credit', 2, '3',  false],
            ['300', 'BANKA KREDİLERİ',                         'liability', 'credit', 3, '30', false],
            ['301', 'FİNANSAL KİRALAMA İŞLEMLERİNDEN BORÇLAR','liability', 'credit', 3, '30', false],
            ['303', 'UZUN VADELİ KREDİLERİN ANAPARA TAKSİTLERİ', 'liability', 'credit', 3, '30', false],
            ['304', 'TAHVİL ANAPARA BORÇ TAKSİTLERİ',        'liability', 'credit', 3, '30', false],
            ['305', 'ÇIKARILMIŞ BONOLAR VE SENETLER',         'liability', 'credit', 3, '30', false],
            ['308', 'MENKUL KIYMETLER İHRAÇ FARKI (-)',       'liability', 'debit',  3, '30', false],
            ['309', 'DİĞER MALİ BORÇLAR',                     'liability', 'credit', 3, '30', false],

            ['32',  'TİCARİ BORÇLAR',                          'liability', 'credit', 2, '3',  true],
            ['320', 'SATICILAR',                               'liability', 'credit', 3, '32', true],
            ['321', 'BORÇ SENETLERİ',                          'liability', 'credit', 3, '32', false],
            ['322', 'BORÇ SENETLERİ REESKONTU (-)',            'liability', 'debit',  3, '32', false],
            ['326', 'ALINAN DEPOZİTO VE TEMİNATLAR',          'liability', 'credit', 3, '32', false],
            ['329', 'DİĞER TİCARİ BORÇLAR',                   'liability', 'credit', 3, '32', false],

            ['33',  'DİĞER KISA VADELİ BORÇLAR',              'liability', 'credit', 2, '3',  false],
            ['331', 'ORTAKLARA BORÇLAR',                       'liability', 'credit', 3, '33', false],
            ['332', 'İŞTİRAKLERE BORÇLAR',                    'liability', 'credit', 3, '33', false],
            ['333', 'BAĞLI ORTAKLIKLARA BORÇLAR',              'liability', 'credit', 3, '33', false],
            ['335', 'PERSONELE BORÇLAR',                       'liability', 'credit', 3, '33', false],
            ['336', 'DİĞER ÇEŞİTLİ BORÇLAR',                  'liability', 'credit', 3, '33', false],
            ['337', 'DİĞER BORÇ SENETLERİ REESKONTU (-)',     'liability', 'debit',  3, '33', false],

            ['34',  'ALINAN AVANSLAR',                         'liability', 'credit', 2, '3',  false],
            ['340', 'ALINAN SİPARİŞ AVANSLARI',               'liability', 'credit', 3, '34', false],
            ['349', 'ALINAN DİĞER AVANSLAR',                  'liability', 'credit', 3, '34', false],

            ['36',  'ÖDENECEKVERGİ VE DİĞER YÜKÜMLÜLÜKLER',  'liability', 'credit', 2, '3',  true],
            ['360', 'ÖDENECEK VERGİ VE FONLAR',               'liability', 'credit', 3, '36', true],
            ['361', 'ÖDENECEK SOSYAL GÜVENLİK KESİNTİLERİ', 'liability', 'credit', 3, '36', false],
            ['362', 'GELİR VERGİLERİ KARŞILIĞI',             'liability', 'credit', 3, '36', false],
            ['368', 'HESAPLANAN KDV',                          'liability', 'credit', 3, '36', true],
            ['369', 'DİĞER ÖDENECEKVERGİ VE YÜKÜMLÜLÜKLER', 'liability', 'credit', 3, '36', false],

            ['37',  'BORÇ VE GİDER KARŞILIKLARI',             'liability', 'credit', 2, '3',  false],
            ['370', 'DÖNEM KÂRI VERGİ VE DİĞER YASAL YÜKÜMLÜLÜK KARŞILIKLARI', 'liability', 'credit', 3, '37', false],
            ['371', 'DÖNEM KÂRININ PEŞİN ÖDENEN VERGİ VE DİĞER YÜKÜMLÜLÜKLER (-)', 'liability', 'debit', 3, '37', false],
            ['372', 'KIDEM TAZMİNATI KARŞILIĞI',              'liability', 'credit', 3, '37', false],
            ['379', 'DİĞER BORÇ VE GİDER KARŞILIKLARI',      'liability', 'credit', 3, '37', false],

            ['38',  'GELECEK AYLARA AİT GELİRLER VE GİDER TAHAKKUKLARI', 'liability', 'credit', 2, '3', false],
            ['380', 'GELECEK AYLARA AİT GELİRLER',            'liability', 'credit', 3, '38', false],
            ['381', 'GİDER TAHAKKUKLARI',                     'liability', 'credit', 3, '38', false],

            ['39',  'DİĞER KISA VADELİ YABANCI KAYNAKLAR',   'liability', 'credit', 2, '3',  false],
            ['391', 'HESAPLANAN KDV',                          'liability', 'credit', 3, '39', false],
            ['392', 'DİĞER KDV',                               'liability', 'credit', 3, '39', false],
            ['393', 'MERKEZ VE ŞUBELER CARI HESABI',          'liability', 'credit', 3, '39', false],
            ['397', 'SAYIM VE TESELLÜM FAZLALARI',            'liability', 'credit', 3, '39', false],
            ['399', 'DİĞER ÇEŞİTLİ KISA VADELİ YABANCI KAYNAKLAR', 'liability', 'credit', 3, '39', false],

            // ════════════════════════════════════════════════════
            // 4. UZUN VADELİ YABANCI KAYNAKLAR
            // ════════════════════════════════════════════════════
            ['4',   'UZUN VADELİ YABANCI KAYNAKLAR',          'liability', 'credit', 1, null, false],
            ['40',  'MALİ BORÇLAR',                            'liability', 'credit', 2, '4',  false],
            ['400', 'BANKA KREDİLERİ',                         'liability', 'credit', 3, '40', false],
            ['401', 'FİNANSAL KİRALAMA İŞLEMLERİNDEN BORÇLAR','liability', 'credit', 3, '40', false],
            ['405', 'ÇIKARILMIŞ TAHVİLLER',                   'liability', 'credit', 3, '40', false],
            ['409', 'DİĞER MALİ BORÇLAR',                     'liability', 'credit', 3, '40', false],

            ['42',  'TİCARİ BORÇLAR',                          'liability', 'credit', 2, '4',  false],
            ['420', 'SATICILAR',                               'liability', 'credit', 3, '42', false],
            ['421', 'BORÇ SENETLERİ',                          'liability', 'credit', 3, '42', false],
            ['426', 'ALINAN DEPOZİTO VE TEMİNATLAR',          'liability', 'credit', 3, '42', false],

            ['43',  'DİĞER UZUN VADELİ BORÇLAR',              'liability', 'credit', 2, '4',  false],
            ['431', 'ORTAKLARA BORÇLAR',                       'liability', 'credit', 3, '43', false],
            ['432', 'İŞTİRAKLERE BORÇLAR',                    'liability', 'credit', 3, '43', false],
            ['438', 'KAMUYA OLAN ERTELENMİŞ VEYA TAKSİTLENDİRİLMİŞ BORÇLAR', 'liability', 'credit', 3, '43', false],
            ['439', 'DİĞER ÇEŞİTLİ UZUN VADELİ BORÇLAR',    'liability', 'credit', 3, '43', false],

            ['44',  'ALINAN AVANSLAR',                         'liability', 'credit', 2, '4',  false],
            ['440', 'ALINAN SİPARİŞ AVANSLARI',               'liability', 'credit', 3, '44', false],
            ['449', 'ALINAN DİĞER AVANSLAR',                  'liability', 'credit', 3, '44', false],

            ['47',  'BORÇ VE GİDER KARŞILIKLARI',             'liability', 'credit', 2, '4',  false],
            ['472', 'KIDEM TAZMİNATI KARŞILIĞI',              'liability', 'credit', 3, '47', false],
            ['479', 'DİĞER BORÇ VE GİDER KARŞILIKLARI',      'liability', 'credit', 3, '47', false],

            ['48',  'GELECEK YILLARA AİT GELİRLER VE GİDER TAHAKKUKLARI', 'liability', 'credit', 2, '4', false],
            ['480', 'GELECEK YILLARA AİT GELİRLER',           'liability', 'credit', 3, '48', false],
            ['481', 'GİDER TAHAKKUKLARI',                     'liability', 'credit', 3, '48', false],

            ['49',  'DİĞER UZUN VADELİ YABANCI KAYNAKLAR',   'liability', 'credit', 2, '4',  false],
            ['492', 'ERTELENMİŞ VERGİ YÜKÜMLÜLÜĞÜ',          'liability', 'credit', 3, '49', false],
            ['499', 'DİĞER ÇEŞİTLİ UZUN VADELİ YABANCI KAYNAKLAR', 'liability', 'credit', 3, '49', false],

            // ════════════════════════════════════════════════════
            // 5. ÖZ KAYNAKLAR
            // ════════════════════════════════════════════════════
            ['5',   'ÖZ KAYNAKLAR',                            'equity', 'credit', 1, null,  true],
            ['50',  'ÖDENMİŞ SERMAYE',                         'equity', 'credit', 2, '5',   true],
            ['500', 'SERMAYE',                                  'equity', 'credit', 3, '50',  true],
            ['501', 'ÖDENMEMİŞ SERMAYE (-)',                  'equity', 'debit',  3, '50',  false],
            ['502', 'SERMAYE DÜZELTMESİ FARKLARI',            'equity', 'credit', 3, '50',  false],

            ['52',  'SERMAYE YEDEKLERİ',                      'equity', 'credit', 2, '5',   false],
            ['520', 'HİSSE SENEDİ İHRAÇ PRİMLERİ',           'equity', 'credit', 3, '52',  false],
            ['521', 'HİSSE SENEDİ İPTAL KÂRLARI',            'equity', 'credit', 3, '52',  false],
            ['522', 'MADDİ DURAN VARLIK YENİDEN DEĞERLEME ARTIŞLARI', 'equity', 'credit', 3, '52', false],
            ['529', 'DİĞER SERMAYE YEDEKLERİ',               'equity', 'credit', 3, '52',  false],

            ['54',  'KÂR YEDEKLERİ',                          'equity', 'credit', 2, '5',   false],
            ['540', 'YASAL YEDEKLER',                          'equity', 'credit', 3, '54',  false],
            ['541', 'STATÜ YEDEKLERİ',                        'equity', 'credit', 3, '54',  false],
            ['542', 'OLAĞANÜSTÜ YEDEKLER',                    'equity', 'credit', 3, '54',  false],
            ['548', 'DİĞER KÂR YEDEKLERİ',                   'equity', 'credit', 3, '54',  false],
            ['549', 'ÖZEL FONLAR',                             'equity', 'credit', 3, '54',  false],

            ['57',  'GEÇMİŞ YILLAR KÂRLARI',                 'equity', 'credit', 2, '5',   false],
            ['570', 'GEÇMİŞ YILLAR KÂRLARI',                 'equity', 'credit', 3, '57',  false],

            ['58',  'GEÇMİŞ YILLAR ZARARLARI (-)',           'equity', 'debit',  2, '5',   false],
            ['580', 'GEÇMİŞ YILLAR ZARARLARI (-)',           'equity', 'debit',  3, '58',  false],

            ['59',  'DÖNEM NET KÂRI (ZARARI)',                'equity', 'credit', 2, '5',   true],
            ['590', 'DÖNEM NET KÂRI',                         'equity', 'credit', 3, '59',  true],
            ['591', 'DÖNEM NET ZARARI (-)',                   'equity', 'debit',  3, '59',  true],

            // ════════════════════════════════════════════════════
            // 6. GELİR TABLOSU HESAPLARI — GELİRLER
            // ════════════════════════════════════════════════════
            ['6',   'GELİR TABLOSU HESAPLARI',                'revenue', 'credit', 1, null, true],

            ['60',  'BRÜT SATIŞLAR',                           'revenue', 'credit', 2, '6',  true],
            ['600', 'YURTİÇİ SATIŞLAR',                       'revenue', 'credit', 3, '60', true],
            ['601', 'YURT DIŞI SATIŞLAR',                     'revenue', 'credit', 3, '60', false],
            ['602', 'DİĞER GELİRLER',                         'revenue', 'credit', 3, '60', false],

            ['61',  'SATIŞ İNDİRİMLERİ (-)',                  'revenue', 'debit',  2, '6',  false],
            ['610', 'SATIŞTAN İADELER (-)',                   'revenue', 'debit',  3, '61', false],
            ['611', 'SATIŞ İSKONTOLARI (-)',                  'revenue', 'debit',  3, '61', false],
            ['612', 'DİĞER İNDİRİMLER (-)',                   'revenue', 'debit',  3, '61', false],

            ['62',  'SATIŞLARIN MALİYETİ (-)',                'cost',    'debit',  2, '6',  true],
            ['620', 'SATILAN TİCARİ MALLAR MALİYETİ (-)',    'cost',    'debit',  3, '62', true],
            ['621', 'SATILAN MAMÜLLER MALİYETİ (-)',          'cost',    'debit',  3, '62', false],
            ['622', 'SATILAN HİZMET MALİYETİ (-)',            'cost',    'debit',  3, '62', false],
            ['623', 'DİĞER SATIŞLARIN MALİYETİ (-)',         'cost',    'debit',  3, '62', false],

            ['63',  'FAALİYET GİDERLERİ (-)',                 'expense', 'debit',  2, '6',  true],
            ['630', 'ARAŞTIRMA VE GELİŞTİRME GİDERLERİ (-)', 'expense', 'debit', 3, '63', false],
            ['631', 'PAZARLAMA SATIŞ VE DAĞITIM GİDERLERİ (-)', 'expense', 'debit', 3, '63', false],
            ['632', 'GENEL YÖNETİM GİDERLERİ (-)',           'expense', 'debit',  3, '63', true],

            ['64',  'DİĞER FAALİYETLERDEN OLAĞAN GELİR VE KÂRLAR', 'revenue', 'credit', 2, '6', false],
            ['640', 'İŞTİRAKLER KÂRLARI',                    'revenue', 'credit', 3, '64', false],
            ['641', 'BAĞLI ORTAKLIKLAR KÂRLARI',              'revenue', 'credit', 3, '64', false],
            ['642', 'FAİZ GELİRLERİ',                         'revenue', 'credit', 3, '64', false],
            ['643', 'KOMİSYON GELİRLERİ',                    'revenue', 'credit', 3, '64', false],
            ['644', 'KONUSU KALMAYAN KARŞILIKLAR',            'revenue', 'credit', 3, '64', false],
            ['645', 'MENKUL KIYMET SATIŞ KÂRLARI',           'revenue', 'credit', 3, '64', false],
            ['646', 'KAMBİYO KÂRLARI',                        'revenue', 'credit', 3, '64', false],
            ['647', 'REESKONT FAİZ GELİRLERİ',               'revenue', 'credit', 3, '64', false],
            ['648', 'ENFLASYON DÜZELTMESİ KÂRLARI',          'revenue', 'credit', 3, '64', false],
            ['649', 'DİĞER OLAĞAN GELİR VE KÂRLAR',          'revenue', 'credit', 3, '64', false],

            ['65',  'DİĞER FAALİYETLERDEN OLAĞAN GİDER VE ZARARLAR (-)', 'expense', 'debit', 2, '6', false],
            ['653', 'KOMİSYON GİDERLERİ (-)',                'expense', 'debit',  3, '65', false],
            ['654', 'KARŞILIK GİDERLERİ (-)',                'expense', 'debit',  3, '65', false],
            ['656', 'KAMBİYO ZARARLARI (-)',                  'expense', 'debit',  3, '65', false],
            ['657', 'REESKONT FAİZ GİDERLERİ (-)',           'expense', 'debit',  3, '65', false],
            ['658', 'ENFLASYON DÜZELTMESİ ZARARLARI (-)',    'expense', 'debit',  3, '65', false],
            ['659', 'DİĞER OLAĞAN GİDER VE ZARARLAR (-)',    'expense', 'debit',  3, '65', false],

            ['66',  'FİNANSMAN GİDERLERİ (-)',               'expense', 'debit',  2, '6',  false],
            ['660', 'KISA VADELİ BORÇLANMA GİDERLERİ (-)',   'expense', 'debit',  3, '66', false],
            ['661', 'UZUN VADELİ BORÇLANMA GİDERLERİ (-)',   'expense', 'debit',  3, '66', false],

            ['67',  'OLAĞANDIŞI GELİR VE KÂRLAR',            'revenue', 'credit', 2, '6',  false],
            ['671', 'ÖNCEKİ DÖNEM GELİR VE KÂRLARI',        'revenue', 'credit', 3, '67', false],
            ['679', 'DİĞER OLAĞANDIŞI GELİR VE KÂRLAR',     'revenue', 'credit', 3, '67', false],

            ['68',  'OLAĞANDIŞI GİDER VE ZARARLAR (-)',      'expense', 'debit',  2, '6',  false],
            ['681', 'ÖNCEKİ DÖNEM GİDER VE ZARARLARI (-)',  'expense', 'debit',  3, '68', false],
            ['689', 'DİĞER OLAĞANDIŞI GİDER VE ZARARLAR (-)', 'expense', 'debit', 3, '68', false],

            ['69',  'DÖNEM NET KÂRI VEYA ZARARI',            'equity',  'credit', 2, '6',  true],
            ['690', 'DÖNEM KÂRI VEYA ZARARI',                'equity',  'credit', 3, '69', true],
            ['691', 'DÖNEM KÂRI VERGİ VE DİĞER YASAL YÜKÜMLÜLÜK KARŞILIKLARI (-)', 'expense', 'debit', 3, '69', false],
            ['692', 'DÖNEM NET KÂRI',                        'equity',  'credit', 3, '69', true],

            // ════════════════════════════════════════════════════
            // 7. MALİYET HESAPLARI (7/A - 7/B)
            // ════════════════════════════════════════════════════
            ['7',   'GİDER HESAPLARI',                        'expense', 'debit',  1, null, false],
            ['70',  'MALİYET MUHASEBESİ BAĞLANTI HESAPLARI', 'expense', 'debit',  2, '7',  false],
            ['700', 'MALİYET MUHASEBESİ BAĞLANTI HESABI',   'expense', 'debit',  3, '70', false],
            ['701', 'MALİYET MUHASEBESİ YANSITMA HESABI',   'expense', 'credit', 3, '70', false],

            ['71',  'ÜRETIM MALİYETİ HESAPLARI',             'expense', 'debit',  2, '7',  false],
            ['710', 'DİREKT İLK MADDE VE MALZEME GİDERLERİ', 'expense', 'debit', 3, '71', false],
            ['720', 'DİREKT İŞÇİLİK GİDERLERİ',             'expense', 'debit',  3, '71', false],
            ['730', 'GENEL ÜRETİM GİDERLERİ',               'expense', 'debit',  3, '71', false],

            ['75',  'ARAŞTIRMA VE GELİŞTİRME GİDERLERİ',   'expense', 'debit',  2, '7',  false],
            ['750', 'ARAŞTIRMA VE GELİŞTİRME GİDERLERİ',   'expense', 'debit',  3, '75', false],
            ['760', 'PAZARLAMA SATIŞ DAĞITIM GİDERLERİ',    'expense', 'debit',  3, '75', false],
            ['770', 'GENEL YÖNETİM GİDERLERİ',              'expense', 'debit',  3, '75', true],
            ['780', 'FİNANSMAN GİDERLERİ',                  'expense', 'debit',  3, '75', false],
        ];
    }
}
