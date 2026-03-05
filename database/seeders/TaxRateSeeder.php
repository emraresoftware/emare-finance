<?php

namespace Database\Seeders;

use App\Models\TaxRate;
use Illuminate\Database\Seeder;

class TaxRateSeeder extends Seeder
{
    public function run(): void
    {
        $taxes = [
            // ── KDV (Katma Değer Vergisi) ──
            ['name' => 'KDV %0', 'code' => 'KDV', 'rate' => 0, 'type' => 'percentage', 'description' => 'KDV İstisna', 'is_default' => false, 'sort_order' => 1],
            ['name' => 'KDV %1', 'code' => 'KDV', 'rate' => 1, 'type' => 'percentage', 'description' => 'Temel gıda, gazete, dergi', 'is_default' => false, 'sort_order' => 2],
            ['name' => 'KDV %8', 'code' => 'KDV', 'rate' => 8, 'type' => 'percentage', 'description' => 'Temel ihtiyaç maddeleri', 'is_default' => false, 'sort_order' => 3],
            ['name' => 'KDV %10', 'code' => 'KDV', 'rate' => 10, 'type' => 'percentage', 'description' => 'İndirimli oran', 'is_default' => false, 'sort_order' => 4],
            ['name' => 'KDV %18', 'code' => 'KDV', 'rate' => 18, 'type' => 'percentage', 'description' => 'Eski genel oran', 'is_default' => false, 'sort_order' => 5],
            ['name' => 'KDV %20', 'code' => 'KDV', 'rate' => 20, 'type' => 'percentage', 'description' => 'Genel KDV oranı', 'is_default' => true, 'sort_order' => 6],

            // ── ÖTV (Özel Tüketim Vergisi) ──
            ['name' => 'ÖTV %0', 'code' => 'OTV', 'rate' => 0, 'type' => 'percentage', 'description' => 'ÖTV İstisna', 'is_default' => false, 'sort_order' => 10],
            ['name' => 'ÖTV %3', 'code' => 'OTV', 'rate' => 3, 'type' => 'percentage', 'description' => 'Kolalı gazozlar', 'is_default' => false, 'sort_order' => 11],
            ['name' => 'ÖTV %7', 'code' => 'OTV', 'rate' => 7, 'type' => 'percentage', 'description' => 'Motor yağları, bazı araçlar', 'is_default' => false, 'sort_order' => 12],
            ['name' => 'ÖTV %10', 'code' => 'OTV', 'rate' => 10, 'type' => 'percentage', 'description' => 'Alkollü içkiler (düşük)', 'is_default' => false, 'sort_order' => 13],
            ['name' => 'ÖTV %20', 'code' => 'OTV', 'rate' => 20, 'type' => 'percentage', 'description' => 'Kozmetik ürünleri', 'is_default' => false, 'sort_order' => 14],
            ['name' => 'ÖTV %25', 'code' => 'OTV', 'rate' => 25, 'type' => 'percentage', 'description' => 'Tütün mamulleri (düşük)', 'is_default' => false, 'sort_order' => 15],
            ['name' => 'ÖTV %40', 'code' => 'OTV', 'rate' => 40, 'type' => 'percentage', 'description' => 'Beyaz eşya, elektronik', 'is_default' => false, 'sort_order' => 16],
            ['name' => 'ÖTV %45', 'code' => 'OTV', 'rate' => 45, 'type' => 'percentage', 'description' => 'Otomobiller (1600cc altı)', 'is_default' => false, 'sort_order' => 17],
            ['name' => 'ÖTV %50', 'code' => 'OTV', 'rate' => 50, 'type' => 'percentage', 'description' => 'Alkollü içkiler (yüksek)', 'is_default' => false, 'sort_order' => 18],
            ['name' => 'ÖTV %80', 'code' => 'OTV', 'rate' => 80, 'type' => 'percentage', 'description' => 'Otomobiller (2000cc üstü)', 'is_default' => false, 'sort_order' => 19],
            ['name' => 'ÖTV %130', 'code' => 'OTV', 'rate' => 130, 'type' => 'percentage', 'description' => 'Otomobiller (lüks segment)', 'is_default' => false, 'sort_order' => 20],
            ['name' => 'ÖTV %220', 'code' => 'OTV', 'rate' => 220, 'type' => 'percentage', 'description' => 'Otomobiller (en üst segment)', 'is_default' => false, 'sort_order' => 21],

            // ── ÖİV (Özel İletişim Vergisi) ──
            ['name' => 'ÖİV %5', 'code' => 'OIV', 'rate' => 5, 'type' => 'percentage', 'description' => 'İnternet erişim hizmeti', 'is_default' => false, 'sort_order' => 30],
            ['name' => 'ÖİV %7.5', 'code' => 'OIV', 'rate' => 7.5, 'type' => 'percentage', 'description' => 'Telefon hizmetleri', 'is_default' => false, 'sort_order' => 31],
            ['name' => 'ÖİV %10', 'code' => 'OIV', 'rate' => 10, 'type' => 'percentage', 'description' => 'TRT bandrol ücreti', 'is_default' => false, 'sort_order' => 32],
            ['name' => 'ÖİV %15', 'code' => 'OIV', 'rate' => 15, 'type' => 'percentage', 'description' => 'GSM hizmetleri', 'is_default' => false, 'sort_order' => 33],
            ['name' => 'ÖİV %25', 'code' => 'OIV', 'rate' => 25, 'type' => 'percentage', 'description' => 'Uydu, kablo TV', 'is_default' => false, 'sort_order' => 34],

            // ── Damga Vergisi ──
            ['name' => 'Damga %0.189', 'code' => 'DAMGA', 'rate' => 0.189, 'type' => 'percentage', 'description' => 'Resmi daireler arası kağıtlar', 'is_default' => false, 'sort_order' => 40],
            ['name' => 'Damga %0.759', 'code' => 'DAMGA', 'rate' => 0.759, 'type' => 'percentage', 'description' => 'Sözleşmeler, taahhütnameler', 'is_default' => false, 'sort_order' => 41],
            ['name' => 'Damga %0.948', 'code' => 'DAMGA', 'rate' => 0.948, 'type' => 'percentage', 'description' => 'Kira kontratları', 'is_default' => false, 'sort_order' => 42],

            // ── BSMV (Banka ve Sigorta Muameleleri Vergisi) ──
            ['name' => 'BSMV %1', 'code' => 'BSMV', 'rate' => 1, 'type' => 'percentage', 'description' => 'Kambiyo işlemleri', 'is_default' => false, 'sort_order' => 50],
            ['name' => 'BSMV %5', 'code' => 'BSMV', 'rate' => 5, 'type' => 'percentage', 'description' => 'Banka işlemleri genel', 'is_default' => false, 'sort_order' => 51],
            ['name' => 'BSMV %15', 'code' => 'BSMV', 'rate' => 15, 'type' => 'percentage', 'description' => 'Sigorta muameleleri', 'is_default' => false, 'sort_order' => 52],

            // ── Konaklama Vergisi ──
            ['name' => 'Konaklama %2', 'code' => 'KONAKLAMA', 'rate' => 2, 'type' => 'percentage', 'description' => 'Otel konaklama vergisi', 'is_default' => false, 'sort_order' => 60],

            // ── Çevre Temizlik Vergisi ──
            ['name' => 'Çevre Temizlik', 'code' => 'CEVRE', 'rate' => 0, 'type' => 'fixed', 'description' => 'Çevre temizlik vergisi (sabit tutar, belediyeye göre değişir)', 'is_default' => false, 'sort_order' => 70],
        ];

        foreach ($taxes as $tax) {
            TaxRate::updateOrCreate(
                ['code' => $tax['code'], 'rate' => $tax['rate']],
                $tax
            );
        }
    }
}
