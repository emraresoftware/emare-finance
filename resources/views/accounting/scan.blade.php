@extends('layouts.app')
@section('title', 'QR Fiş Tarama')

@section('content')
<div class="max-w-lg mx-auto space-y-5">

    {{-- Başlık --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('accounting.dashboard') }}" class="text-gray-400 hover:text-gray-600"><i class="fas fa-arrow-left"></i></a>
        <div>
            <h2 class="text-2xl font-bold text-gray-900">QR Fiş Tarama</h2>
            <p class="text-sm text-gray-500">Kamera ile fiş QR kodunu tarayın</p>
        </div>
    </div>

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm text-red-700 flex items-center gap-2">
        <i class="fas fa-triangle-exclamation text-red-500"></i>{{ session('error') }}
    </div>
    @endif

    {{-- Kamera QR Tarayıcı --}}
    <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
        <div class="bg-indigo-600 px-5 py-3 flex items-center gap-2">
            <i class="fas fa-qrcode text-white text-lg"></i>
            <h3 class="font-semibold text-white">Kamera ile Tara</h3>
        </div>

        <div class="p-5">
            <div id="qr-reader" class="w-full rounded-xl overflow-hidden border-2 border-dashed border-gray-300 bg-gray-50" style="min-height:300px"></div>
            <div id="qr-result" class="mt-3 text-sm text-center text-gray-500 hidden">
                <i class="fas fa-circle-notch fa-spin mr-2"></i> Fiş aranıyor…
            </div>
        </div>
    </div>

    {{-- Manuel Fiş No Girişi --}}
    <div class="bg-white rounded-xl border shadow-sm p-5">
        <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
            <i class="fas fa-keyboard text-gray-400"></i> Manuel Fiş Numarası
        </h3>
        <form action="{{ route('accounting.scan.result') }}" method="GET" class="flex gap-2">
            <input type="text" name="code" placeholder="örn: FIS-2026-0001" required
                   class="flex-1 border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500">
            <button type="submit" class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-indigo-700">
                <i class="fas fa-search mr-1"></i> Bul
            </button>
        </form>
    </div>

    {{-- Bilgi --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex gap-3 text-sm text-blue-700">
        <i class="fas fa-circle-info text-blue-500 mt-0.5 flex-shrink-0"></i>
        <div>
            <p class="font-medium mb-1">Nasıl Kullanılır?</p>
            <ul class="space-y-0.5 text-blue-600">
                <li>• Yazdırılmış fişin üzerindeki QR kodu kamerayla tarayın</li>
                <li>• Fiş otomatik olarak açılır</li>
                <li>• Veya fiş numarasını manuel girin</li>
            </ul>
        </div>
    </div>

    {{-- İpuçları: Telefon bağlantısı --}}
    <div class="bg-white rounded-xl border p-4">
        <h3 class="font-semibold text-gray-800 mb-3 text-sm flex items-center gap-2">
            <i class="fas fa-mobile-alt text-indigo-500"></i> Telefon ile Tara
        </h3>
        <p class="text-xs text-gray-500 mb-3">Bu sayfanın QR kodunu telefonunuzla tarayarak doğrudan telefon kamerasıyla fiş tarayabilirsiniz.</p>

        {{-- Bu sayfanın URL'inin QR kodu --}}
        <div class="flex justify-center py-2">
            <div id="page-qr" class="border-2 border-gray-200 rounded-xl p-2 bg-white"></div>
        </div>
        <p class="text-center text-xs text-gray-400 mt-2">{{ url('/muhasebe/tara') }}</p>
    </div>

</div>

{{-- html5-qrcode CDN --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
{{-- QRCode.js CDN (sayfanın QR kodunu üretmek için) --}}
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

<script>
// ── Kamera ile QR tarama ──────────────────────────────────────
const html5QrCode = new Html5Qrcode("qr-reader");
const config = { fps: 10, qrbox: { width: 250, height: 250 } };

function onScanSuccess(decodedText) {
    html5QrCode.stop();
    document.getElementById('qr-result').classList.remove('hidden');
    // Sunucuya yönlendir
    window.location.href = "{{ route('accounting.scan.result') }}?code=" + encodeURIComponent(decodedText);
}

function onScanFailure() { /* sessiz */ }

// Kamerayı başlat
Html5Qrcode.getCameras().then(cams => {
    if (cams.length > 0) {
        // Arka kamerayı tercih et
        const cam = cams.find(c => c.label.toLowerCase().includes('back')) ?? cams[cams.length - 1];
        html5QrCode.start(cam.id, config, onScanSuccess, onScanFailure).catch(() => {
            // Kamera erişimi reddedildi
            document.getElementById('qr-reader').innerHTML =
                '<div class="p-8 text-center text-gray-400"><i class="fas fa-camera-slash text-4xl mb-3 block"></i>Kamera erişimine izin verin veya' +
                ' manuel fiş numarası girin.</div>';
        });
    }
}).catch(() => {
    document.getElementById('qr-reader').innerHTML =
        '<div class="p-8 text-center text-gray-400"><i class="fas fa-camera-slash text-4xl mb-3 block"></i>Bu cihazda kamera bulunamadı.</div>';
});

// ── Bu sayfanın QR kodunu oluştur ────────────────────────────
new QRCode(document.getElementById("page-qr"), {
    text: "{{ url('/muhasebe/tara') }}",
    width: 140, height: 140,
    colorDark: "#1e1b4b",
    colorLight: "#ffffff",
    correctLevel: QRCode.CorrectLevel.M
});
</script>
@endsection
