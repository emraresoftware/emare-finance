<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\SignageContent;
use App\Models\SignageDevice;
use App\Models\SignagePlaylist;
use App\Models\SignagePlaylistItem;
use App\Models\SignageSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class SignageController extends Controller
{
    // ═══════════════════════════════════════
    // ANA SAYFA — Dashboard
    // ═══════════════════════════════════════
    public function index()
    {
        $devices   = SignageDevice::orderBy('name')->get();
        $contents  = SignageContent::orderBy('created_at', 'desc')->get();
        $playlists = SignagePlaylist::with('contents', 'devices')->orderBy('name')->get();
        $schedules = SignageSchedule::with('playlist')->orderBy('priority')->get();
        $templates = $this->getTemplates();

        $compatibleHardware = $this->getCompatibleHardware();
        $softwareList       = $this->getSoftwareCompatibility();
        $resolutions        = $this->getResolutions();

        return view('signage.index', compact(
            'devices', 'contents', 'playlists', 'schedules', 'templates',
            'compatibleHardware', 'softwareList', 'resolutions'
        ));
    }

    // ═══════════════════════════════════════
    // GÖRÜNTÜLEME — Tam Ekran Signage
    // ═══════════════════════════════════════
    public function display(Request $request, string $template = 'menu-board')
    {
        $categories = Category::orderBy('name')->get();
        $products   = Product::orderBy('name')->get();
        return view('signage.display', compact('template', 'categories', 'products'));
    }

    public function preview(string $template)
    {
        $categories = Category::orderBy('name')->get();
        $products   = Product::orderBy('name')->get();
        return view('signage.display', [
            'template'   => $template,
            'categories' => $categories,
            'products'   => $products,
            'preview'    => true,
        ]);
    }

    // ═══════════════════════════════════════
    // İÇERİK CRUD
    // ═══════════════════════════════════════
    public function contentStore(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'type'     => 'required|in:image,video,template,widget,url',
            'file'     => 'nullable|file|max:51200',
            'url'      => 'nullable|url|max:500',
            'duration' => 'nullable|integer|min:1|max:3600',
            'tags'     => 'nullable|string|max:500',
        ]);

        $data = [
            'name'     => $request->name,
            'type'     => $request->type,
            'duration' => (int) ($request->duration ?? 10),
            'status'   => 'active',
            'tags'     => $request->tags ? array_map('trim', explode(',', $request->tags)) : [],
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $ext  = $file->getClientOriginalExtension();
            $name = Str::uuid() . '.' . $ext;
            $path = $file->storeAs('public/signage', $name);

            $data['file_path']  = $path;
            $data['file_url']   = Storage::url($path);
            $data['file_size']  = $this->formatFileSize($file->getSize());
            $data['resolution'] = $this->detectResolution($file);
        }

        if ($request->url) {
            $data['url'] = $request->url;
        }

        SignageContent::create($data);

        return redirect()->route('signage.index')->with('success', 'İçerik başarıyla yüklendi.');
    }

    public function contentUpdate(Request $request, SignageContent $content)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'type'     => 'required|in:image,video,template,widget,url',
            'duration' => 'nullable|integer|min:1|max:3600',
            'status'   => 'nullable|in:active,draft,scheduled,archived',
            'tags'     => 'nullable|string|max:500',
        ]);

        $content->update([
            'name'     => $request->name,
            'type'     => $request->type,
            'duration' => (int) ($request->duration ?? $content->duration),
            'status'   => $request->status ?? $content->status,
            'tags'     => $request->tags ? array_map('trim', explode(',', $request->tags)) : $content->tags,
        ]);

        return redirect()->route('signage.index')->with('success', 'İçerik güncellendi.');
    }

    public function contentDestroy(SignageContent $content)
    {
        if ($content->file_path) {
            Storage::delete($content->file_path);
        }
        $content->delete();

        return redirect()->route('signage.index')->with('success', 'İçerik silindi.');
    }

    // ═══════════════════════════════════════
    // CİHAZ CRUD
    // ═══════════════════════════════════════
    public function deviceStore(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'location'    => 'nullable|string|max:255',
            'resolution'  => 'nullable|string|max:20',
            'orientation' => 'nullable|in:landscape,portrait',
            'template'    => 'nullable|string|max:50',
            'device_type' => 'nullable|string|max:100',
            'model'       => 'nullable|string|max:100',
            'os'          => 'nullable|string|max:100',
            'ip_address'  => 'nullable|string|max:45',
            'brightness'  => 'nullable|integer|min:0|max:100',
            'volume'      => 'nullable|integer|min:0|max:100',
            'auto_power'  => 'nullable|boolean',
            'power_on'    => 'nullable|string|max:5',
            'power_off'   => 'nullable|string|max:5',
        ]);

        $device = SignageDevice::create(array_merge(
            $request->only([
                'name', 'location', 'resolution', 'orientation', 'template',
                'device_type', 'model', 'os', 'ip_address', 'brightness',
                'volume', 'auto_power', 'power_on', 'power_off',
            ]),
            ['api_token' => Str::random(64), 'status' => 'offline']
        ));

        return redirect()->route('signage.index')->with('success', "Cihaz '{$device->name}' eklendi.");
    }

    public function deviceUpdate(Request $request, SignageDevice $device)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'location'    => 'nullable|string|max:255',
            'resolution'  => 'nullable|string|max:20',
            'orientation' => 'nullable|in:landscape,portrait',
            'template'    => 'nullable|string|max:50',
            'brightness'  => 'nullable|integer|min:0|max:100',
            'volume'      => 'nullable|integer|min:0|max:100',
            'status'      => 'nullable|in:online,offline,maintenance',
        ]);

        $device->update($request->only([
            'name', 'location', 'resolution', 'orientation', 'template',
            'brightness', 'volume', 'status',
        ]));

        return redirect()->route('signage.index')->with('success', 'Cihaz güncellendi.');
    }

    public function deviceDestroy(SignageDevice $device)
    {
        $device->playlists()->detach();
        $device->delete();

        return redirect()->route('signage.index')->with('success', 'Cihaz silindi.');
    }

    public function deviceAssignPlaylist(Request $request, SignageDevice $device)
    {
        $request->validate(['playlist_id' => 'required|exists:signage_playlists,id']);
        $device->playlists()->syncWithoutDetaching([$request->playlist_id => ['priority' => 1]]);

        return redirect()->route('signage.index')->with('success', 'Playlist cihaza atandı.');
    }

    // ═══════════════════════════════════════
    // PLAYLİST CRUD
    // ═══════════════════════════════════════
    public function playlistStore(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'loop'          => 'nullable|boolean',
            'schedule_text' => 'nullable|string|max:255',
            'content_ids'   => 'nullable|array',
            'content_ids.*' => 'exists:signage_contents,id',
        ]);

        $playlist = SignagePlaylist::create([
            'name'          => $request->name,
            'loop'          => $request->boolean('loop', true),
            'schedule_text' => $request->schedule_text,
            'status'        => 'active',
        ]);

        if ($request->content_ids) {
            foreach ($request->content_ids as $idx => $contentId) {
                SignagePlaylistItem::create([
                    'playlist_id' => $playlist->id,
                    'content_id'  => $contentId,
                    'sort_order'  => $idx,
                ]);
            }
        }

        return redirect()->route('signage.index')->with('success', "Playlist '{$playlist->name}' oluşturuldu.");
    }

    public function playlistUpdate(Request $request, SignagePlaylist $playlist)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'loop'          => 'nullable|boolean',
            'status'        => 'nullable|in:active,inactive',
            'content_ids'   => 'nullable|array',
            'content_ids.*' => 'exists:signage_contents,id',
        ]);

        $playlist->update([
            'name'   => $request->name,
            'loop'   => $request->boolean('loop', true),
            'status' => $request->status ?? $playlist->status,
        ]);

        if ($request->has('content_ids')) {
            $playlist->playlistItems()->delete();
            foreach (($request->content_ids ?? []) as $idx => $contentId) {
                SignagePlaylistItem::create([
                    'playlist_id' => $playlist->id,
                    'content_id'  => $contentId,
                    'sort_order'  => $idx,
                ]);
            }
        }

        return redirect()->route('signage.index')->with('success', 'Playlist güncellendi.');
    }

    public function playlistDestroy(SignagePlaylist $playlist)
    {
        $playlist->delete();
        return redirect()->route('signage.index')->with('success', 'Playlist silindi.');
    }

    // ═══════════════════════════════════════
    // ZAMANLAMA CRUD
    // ═══════════════════════════════════════
    public function scheduleStore(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'playlist_id' => 'required|exists:signage_playlists,id',
            'time_start'  => 'required|date_format:H:i',
            'time_end'    => 'required|date_format:H:i',
            'days'        => 'required|array|min:1',
            'days.*'      => 'string',
            'priority'    => 'nullable|integer|min:1|max:10',
        ]);

        SignageSchedule::create([
            'name'        => $request->name,
            'playlist_id' => $request->playlist_id,
            'time_start'  => $request->time_start,
            'time_end'    => $request->time_end,
            'days'        => $request->days,
            'priority'    => (int) ($request->priority ?? 1),
            'is_active'   => true,
        ]);

        return redirect()->route('signage.index')->with('success', 'Zamanlama oluşturuldu.');
    }

    public function scheduleDestroy(SignageSchedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('signage.index')->with('success', 'Zamanlama silindi.');
    }

    // ═══════════════════════════════════════
    // API — Cihaz Ping
    // ═══════════════════════════════════════
    public function devicePing(Request $request)
    {
        $token = $request->bearerToken() ?? $request->input('token');
        if (!$token) return response()->json(['error' => 'Token gerekli'], 401);

        $device = SignageDevice::where('api_token', $token)->first();
        if (!$device) return response()->json(['error' => 'Geçersiz token'], 404);

        $device->update(['status' => 'online', 'last_ping_at' => now()]);

        $playlist = $device->playlists()->where('status', 'active')->first();
        $contents = $playlist ? $playlist->contents->map(fn ($c) => [
            'id'       => $c->id,
            'name'     => $c->name,
            'type'     => $c->type,
            'url'      => $c->file_url ?? $c->url,
            'duration' => $c->pivot->duration_override ?? $c->duration,
        ]) : [];

        return response()->json(['status' => 'ok', 'template' => $device->template, 'contents' => $contents]);
    }

    // ═══════════════════════════════════════
    // YARDIMCI METODLAR
    // ═══════════════════════════════════════

    private function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 1) . ' GB';
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024) return number_format($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }

    private function detectResolution($file): ?string
    {
        try {
            if (Str::startsWith($file->getMimeType(), 'image/')) {
                $size = getimagesize($file->getPathname());
                if ($size) return $size[0] . 'x' . $size[1];
            }
        } catch (\Throwable $e) {}
        return null;
    }

    private function getTemplates(): array
    {
        return [
            ['code'=>'menu-board','name'=>'Menü Panosu','desc'=>'Kafe, restoran menü tahtası.','icon'=>'fa-utensils','color'=>'amber','features'=>['Kategorili görünüm','Fiyat gösterimi','Otomatik döngü','Kampanya bandı'],'best_for'=>'Kafe, Restoran, Fast-food'],
            ['code'=>'price-list','name'=>'Fiyat Listesi','desc'=>'Mağaza, market için fiyat listesi.','icon'=>'fa-tags','color'=>'emerald','features'=>['Barkod gösterimi','İndirim vurgusu','Stok durumu','QR kod'],'best_for'=>'Market, Mağaza, Eczane'],
            ['code'=>'promo','name'=>'Kampanya & Duyuru','desc'=>'Slider kampanya ve duyuru.','icon'=>'fa-bullhorn','color'=>'rose','features'=>['Slider geçişleri','Video desteği','Zamanlama','Çoklu içerik'],'best_for'=>'Tüm İşletmeler'],
            ['code'=>'queue','name'=>'Sıra / Çağrı Ekranı','desc'=>'Müşteri sıra numarası ekranı.','icon'=>'fa-list-ol','color'=>'blue','features'=>['Sesli çağrı','Numara gösterimi','Bekleme süresi','Çoklu gişe'],'best_for'=>'Hastane, Banka'],
            ['code'=>'welcome','name'=>'Karşılama Ekranı','desc'=>'Giriş kapısı hoş geldiniz ekranı.','icon'=>'fa-hand-sparkles','color'=>'violet','features'=>['Logo gösterimi','Saat & hava durumu','Hoş geldiniz mesajı'],'best_for'=>'Ofis, Otel, Mağaza'],
            ['code'=>'dashboard-tv','name'=>'İşletme Dashboard TV','desc'=>'Canlı satış ve performans.','icon'=>'fa-chart-line','color'=>'cyan','features'=>['Canlı satış verisi','Grafik & KPI','Hedef takibi'],'best_for'=>'Yönetim Odası'],
            ['code'=>'social-wall','name'=>'Sosyal Medya Duvarı','desc'=>'Sosyal medya canlı akışı.','icon'=>'fa-hashtag','color'=>'pink','features'=>['Instagram feed','Google yorumları','Hashtag takibi'],'best_for'=>'Kafe, Restoran'],
            ['code'=>'wayfinding','name'=>'Yönlendirme Ekranı','desc'=>'Bina/mağaza yönlendirme.','icon'=>'fa-map-signs','color'=>'teal','features'=>['Kat planı','Dokunmatik harita','Bölüm arama'],'best_for'=>'AVM, Hastane'],
        ];
    }

    private function getResolutions(): array
    {
        return [
            'landscape' => [
                ['code'=>'1280x720','label'=>'HD (720p)','ratio'=>'16:9','tier'=>'basic'],
                ['code'=>'1920x1080','label'=>'Full HD (1080p)','ratio'=>'16:9','tier'=>'recommended'],
                ['code'=>'2560x1440','label'=>'QHD (2K)','ratio'=>'16:9','tier'=>'premium'],
                ['code'=>'3840x2160','label'=>'4K UHD','ratio'=>'16:9','tier'=>'premium'],
            ],
            'portrait' => [
                ['code'=>'1080x1920','label'=>'FHD Dikey','ratio'=>'9:16','tier'=>'recommended'],
                ['code'=>'1440x2560','label'=>'QHD Dikey','ratio'=>'9:16','tier'=>'premium'],
            ],
            'square' => [
                ['code'=>'1080x1080','label'=>'Kare HD','ratio'=>'1:1','tier'=>'standard'],
            ],
            'special' => [
                ['code'=>'1920x480','label'=>'Bar Ekranı','ratio'=>'4:1','tier'=>'special'],
                ['code'=>'480x1920','label'=>'Totem Ekranı','ratio'=>'1:4','tier'=>'special'],
            ],
        ];
    }

    private function getCompatibleHardware(): array
    {
        return [
            'smart_tv'=>['title'=>'Smart TV\'ler','icon'=>'fa-tv','items'=>[['name'=>'Samsung Tizen TV','status'=>'full','note'=>'Tizen 4.0+'],['name'=>'LG webOS TV','status'=>'full','note'=>'webOS 4.0+'],['name'=>'Sony Android TV','status'=>'full','note'=>'Android 8+'],['name'=>'Vestel Ticari','status'=>'full','note'=>'Vestel CMS']]],
            'media_players'=>['title'=>'Media Player','icon'=>'fa-hard-drive','items'=>[['name'=>'Amazon Fire TV Stick','status'=>'full','note'=>'Fire OS 5+'],['name'=>'Google Chromecast','status'=>'full','note'=>'Cast API'],['name'=>'Xiaomi Mi Box','status'=>'full','note'=>'Android TV']]],
            'sbc'=>['title'=>'Tek Kart Bilgisayar','icon'=>'fa-server','items'=>[['name'=>'Raspberry Pi 4/5','status'=>'full','note'=>'Chromium Kiosk'],['name'=>'Intel NUC','status'=>'full','note'=>'Win/Linux']]],
            'commercial'=>['title'=>'Ticari Ekranlar','icon'=>'fa-display','items'=>[['name'=>'Samsung Smart Signage','status'=>'full','note'=>'MagicINFO'],['name'=>'LG Commercial','status'=>'full','note'=>'SuperSign'],['name'=>'BenQ Signage','status'=>'full','note'=>'X-Sign']]],
        ];
    }

    private function getSoftwareCompatibility(): array
    {
        return [
            'cms_platforms'=>['title'=>'CMS Platformları','icon'=>'fa-sliders','items'=>[['name'=>'Emare Signage (Dahili)','status'=>'native','desc'=>'Yerleşik dijital ekran yönetimi'],['name'=>'Screenly','status'=>'ready','desc'=>'Raspberry Pi odaklı'],['name'=>'Xibo','status'=>'ready','desc'=>'Açık kaynak CMS']]],
            'protocols'=>['title'=>'Protokoller','icon'=>'fa-network-wired','items'=>[['name'=>'WebSocket','status'=>'native','desc'=>'Gerçek zamanlı push'],['name'=>'REST API','status'=>'native','desc'=>'JSON API'],['name'=>'MQTT','status'=>'ready','desc'=>'IoT mesajlaşma']]],
        ];
    }
}
