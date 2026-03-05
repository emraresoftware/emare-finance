@extends('layouts.app')
@section('title', 'Dijital Ekran Yönetimi')

@section('content')
<div x-data="signagePanel()" class="space-y-6">

    {{-- ═══════ BAŞLIK ═══════ --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-violet-500 to-purple-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-tv text-white text-lg"></i>
                </div>
                Dijital Ekran Yönetimi
            </h1>
            <p class="mt-1 text-sm text-gray-500">İçerik, playlist, cihaz ve zamanlama yönetimi</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 text-sm">
                <span class="flex items-center gap-1"><span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span> {{ collect($devices)->where('status','online')->count() }} Çevrimiçi</span>
                <span class="text-gray-300">|</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 bg-red-400 rounded-full"></span> {{ collect($devices)->where('status','offline')->count() }} Çevrimdışı</span>
            </div>
            <a href="{{ route('signage.display') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-violet-600 text-white rounded-lg text-sm font-medium hover:bg-violet-700 transition">
                <i class="fas fa-external-link-alt"></i> Canlı Önizleme
            </a>
        </div>
    </div>

    {{-- ═══════ SEKME NAVİGASYONU ═══════ --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="border-b border-gray-200 overflow-x-auto">
            <nav class="flex -mb-px">
                <template x-for="tab in tabs" :key="tab.id">
                    <button @click="activeTab = tab.id"
                            :class="activeTab === tab.id ? 'border-violet-500 text-violet-600 bg-violet-50/50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="flex items-center gap-2 px-5 py-3.5 border-b-2 text-sm font-medium whitespace-nowrap transition-colors">
                        <i :class="tab.icon" class="text-xs"></i>
                        <span x-text="tab.label"></span>
                        <span x-show="tab.badge" x-text="tab.badge"
                              class="ml-1 px-1.5 py-0.5 rounded-full text-xs font-semibold bg-violet-100 text-violet-700"></span>
                    </button>
                </template>
            </nav>
        </div>

        <div class="p-6">

            {{-- ══════════════════════════════════════════ --}}
            {{--  SEKME 1: İÇERİK KÜTÜPHANESİ            --}}
            {{-- ══════════════════════════════════════════ --}}
            <div x-show="activeTab === 'contents'" x-cloak>
                {{-- Üst Araç Çubuğu --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <input type="text" x-model="contentSearch" placeholder="İçerik ara..." class="pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500 w-64">
                            <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-sm"></i>
                        </div>
                        <select x-model="contentTypeFilter" class="border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-violet-500">
                            <option value="all">Tüm Türler</option>
                            <option value="image">Görseller</option>
                            <option value="video">Videolar</option>
                            <option value="widget">Widget'lar</option>
                            <option value="template">Şablonlar</option>
                        </select>
                        <select x-model="contentStatusFilter" class="border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-violet-500">
                            <option value="all">Tüm Durumlar</option>
                            <option value="active">Aktif</option>
                            <option value="draft">Taslak</option>
                            <option value="scheduled">Zamanlanmış</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="contentView = 'grid'" :class="contentView==='grid' ? 'bg-violet-100 text-violet-700' : 'bg-gray-100 text-gray-500'" class="p-2 rounded-lg transition"><i class="fas fa-th-large"></i></button>
                        <button @click="contentView = 'list'" :class="contentView==='list' ? 'bg-violet-100 text-violet-700' : 'bg-gray-100 text-gray-500'" class="p-2 rounded-lg transition"><i class="fas fa-list"></i></button>
                        <button @click="showUploadModal = true" class="ml-2 inline-flex items-center gap-2 px-4 py-2 bg-violet-600 text-white rounded-lg text-sm font-medium hover:bg-violet-700 transition">
                            <i class="fas fa-cloud-upload-alt"></i> İçerik Yükle
                        </button>
                    </div>
                </div>

                {{-- Grid Görünüm --}}
                <div x-show="contentView === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach($contents as $content)
                    <div class="group bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg hover:border-violet-200 transition-all duration-200">
                        {{-- Thumbnail --}}
                        <div class="relative h-36 bg-gradient-to-br {{ $content['thumbnail_color'] }} flex items-center justify-center">
                            <i class="fas {{ $content['icon'] }} text-white/80 text-4xl"></i>
                            {{-- Durum Rozetleri --}}
                            <div class="absolute top-2 left-2">
                                @if($content['status'] === 'active')
                                    <span class="px-2 py-0.5 bg-green-500 text-white text-xs rounded-full font-medium">Aktif</span>
                                @elseif($content['status'] === 'draft')
                                    <span class="px-2 py-0.5 bg-gray-500 text-white text-xs rounded-full font-medium">Taslak</span>
                                @else
                                    <span class="px-2 py-0.5 bg-amber-500 text-white text-xs rounded-full font-medium">Zamanlanmış</span>
                                @endif
                            </div>
                            {{-- Tür Rozeti --}}
                            <div class="absolute top-2 right-2">
                                <span class="px-2 py-0.5 bg-black/30 backdrop-blur text-white text-xs rounded-full">{{ $content['type_label'] }}</span>
                            </div>
                            {{-- Hover Aksiyonlar --}}
                            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                <button class="w-9 h-9 bg-white/20 backdrop-blur rounded-lg flex items-center justify-center text-white hover:bg-white/40 transition" title="Önizle">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="w-9 h-9 bg-white/20 backdrop-blur rounded-lg flex items-center justify-center text-white hover:bg-white/40 transition" title="Düzenle">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <form action="{{ route('signage.content.destroy', $content['id']) }}" method="POST" onsubmit="return confirm('Bu içerik silinsin mi?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-9 h-9 bg-red-500/60 backdrop-blur rounded-lg flex items-center justify-center text-white hover:bg-red-500/80 transition" title="Sil">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        {{-- Bilgiler --}}
                        <div class="p-3">
                            <h4 class="font-semibold text-sm text-gray-900 truncate">{{ $content['name'] }}</h4>
                            <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
                                <span><i class="fas fa-clock mr-1"></i> {{ $content['duration'] > 0 ? $content['duration'].'sn' : 'Sürekli' }}</span>
                                <span>{{ $content['resolution'] ?? 'Otomatik' }}</span>
                                <span>{{ $content['file_size'] ?? '-' }}</span>
                            </div>
                            <div class="mt-2 flex flex-wrap gap-1">
                                @foreach(($content['tags'] ?? []) as $tag)
                                    <span class="px-1.5 py-0.5 bg-gray-100 text-gray-600 text-xs rounded">{{ $tag }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Liste Görünüm --}}
                <div x-show="contentView === 'list'" class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">İçerik</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tür</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Çözünürlük</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Süre</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Boyut</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Durum</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tarih</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($contents as $content)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gradient-to-br {{ $content['thumbnail_color'] }} rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i class="fas {{ $content['icon'] }} text-white text-sm"></i>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">{{ $content['name'] }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $content['type_label'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 font-mono">{{ $content['resolution'] ?? 'Otomatik' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $content['duration'] > 0 ? $content['duration'].'sn' : '–' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $content['file_size'] ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @if($content['status'] === 'active')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                                    @elseif($content['status'] === 'draft')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Taslak</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Zamanlanmış</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $content['created_at'] ? \Carbon\Carbon::parse($content['created_at'])->format('d.m.Y') : '-' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button class="p-1.5 text-gray-400 hover:text-violet-600 transition" title="Önizle"><i class="fas fa-eye text-xs"></i></button>
                                        <button class="p-1.5 text-gray-400 hover:text-blue-600 transition" title="Düzenle"><i class="fas fa-pen text-xs"></i></button>
                                        <form action="{{ route('signage.content.destroy', $content['id']) }}" method="POST" onsubmit="return confirm('Silinsin mi?')" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500 transition" title="Sil"><i class="fas fa-trash text-xs"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ══════════════════════════════════════════ --}}
            {{--  SEKME 2: PLAYLİST YÖNETİMİ              --}}
            {{-- ══════════════════════════════════════════ --}}
            <div x-show="activeTab === 'playlists'" x-cloak>
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Oynatma Listeleri</h3>
                    <button @click="showPlaylistModal = true" class="inline-flex items-center gap-2 px-4 py-2 bg-violet-600 text-white rounded-lg text-sm font-medium hover:bg-violet-700 transition">
                        <i class="fas fa-plus"></i> Yeni Playlist
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach($playlists as $pl)
                    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-md transition-shadow">
                        <div class="p-5">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-11 h-11 rounded-xl flex items-center justify-center {{ $pl['status']==='active' ? 'bg-violet-100 text-violet-600' : 'bg-gray-100 text-gray-400' }}">
                                        <i class="fas fa-play-circle text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ $pl['name'] }}</h4>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $pl['schedule'] }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1">
                                    @if($pl['status'] === 'active')
                                        <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full font-medium">Aktif</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-gray-100 text-gray-500 text-xs rounded-full font-medium">Pasif</span>
                                    @endif
                                    @if($pl['loop'])
                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded-full font-medium"><i class="fas fa-repeat mr-1"></i>Döngü</span>
                                    @endif
                                </div>
                            </div>

                            {{-- İstatistikler --}}
                            <div class="mt-4 grid grid-cols-3 gap-3">
                                <div class="bg-gray-50 rounded-lg p-2.5 text-center">
                                    <div class="text-lg font-bold text-gray-900">{{ $pl['content_count'] }}</div>
                                    <div class="text-xs text-gray-500">İçerik</div>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-2.5 text-center">
                                    <div class="text-lg font-bold text-gray-900">{{ $pl['device_count'] }}</div>
                                    <div class="text-xs text-gray-500">Cihaz</div>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-2.5 text-center">
                                    <div class="text-sm font-bold text-gray-900">{{ $pl['total_duration'] }}</div>
                                    <div class="text-xs text-gray-500">Toplam Süre</div>
                                </div>
                            </div>

                            {{-- İçerik Sıralaması --}}
                            <div class="mt-4 space-y-2">
                                <h5 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">İçerik Sırası (Sürükle & Bırak)</h5>
                                @foreach($pl['item_ids'] as $idx => $contentId)
                                    @php $c = collect($contents)->firstWhere('id', $contentId); @endphp
                                    @if($c)
                                    <div class="flex items-center gap-3 p-2 bg-gray-50 rounded-lg cursor-grab hover:bg-violet-50 transition group">
                                        <i class="fas fa-grip-vertical text-gray-300 group-hover:text-violet-400"></i>
                                        <span class="w-6 h-6 bg-violet-100 text-violet-700 text-xs font-bold rounded flex items-center justify-center">{{ $idx + 1 }}</span>
                                        <div class="w-8 h-8 bg-gradient-to-br {{ $c['thumbnail_color'] }} rounded flex items-center justify-center flex-shrink-0">
                                            <i class="fas {{ $c['icon'] }} text-white text-xs"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <span class="text-sm text-gray-700 truncate block">{{ $c['name'] }}</span>
                                        </div>
                                        <span class="text-xs text-gray-400">{{ $c['duration'] > 0 ? $c['duration'].'sn' : '∞' }}</span>
                                        <button class="opacity-0 group-hover:opacity-100 text-red-400 hover:text-red-600 transition"><i class="fas fa-times text-xs"></i></button>
                                    </div>
                                    @endif
                                @endforeach
                            </div>

                            {{-- Playlist Aksiyonlar --}}
                            <div class="mt-4 flex items-center gap-2 pt-4 border-t border-gray-100">
                                <button class="flex-1 px-3 py-2 text-sm text-violet-600 bg-violet-50 rounded-lg hover:bg-violet-100 transition font-medium"><i class="fas fa-pen mr-1"></i> Düzenle</button>
                                <button class="flex-1 px-3 py-2 text-sm text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition font-medium"><i class="fas fa-plus mr-1"></i> İçerik Ekle</button>
                                <form action="{{ route('signage.playlist.destroy', $pl['id']) }}" method="POST" onsubmit="return confirm('Bu playlist silinsin mi?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-3 py-2 text-sm text-red-500 bg-red-50 rounded-lg hover:bg-red-100 transition"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- ══════════════════════════════════════════ --}}
            {{--  SEKME 3: CİHAZ YÖNETİMİ                 --}}
            {{-- ══════════════════════════════════════════ --}}
            <div x-show="activeTab === 'devices'" x-cloak>
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Bağlı Cihazlar</h3>
                    <div class="flex items-center gap-2">
                        <button class="inline-flex items-center gap-2 px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200 transition">
                            <i class="fas fa-sync-alt"></i> Tümünü Tara
                        </button>
                        <button class="inline-flex items-center gap-2 px-4 py-2 bg-violet-600 text-white rounded-lg text-sm font-medium hover:bg-violet-700 transition">
                            <i class="fas fa-plus"></i> Cihaz Ekle
                        </button>
                    </div>
                </div>

                <div class="space-y-6">
                    @foreach($devices as $device)
                    <div x-data="{ expanded: false }" class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-md transition-shadow">
                        {{-- Cihaz Özet Satırı --}}
                        <div class="p-5">
                            <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                                {{-- Sol: Durum + Bilgi --}}
                                <div class="flex items-center gap-4 flex-1 min-w-0">
                                    <div class="relative flex-shrink-0">
                                        <div class="w-14 h-14 rounded-xl flex items-center justify-center {{ $device['status']==='online' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-400' }}">
                                            <i class="fas fa-{{ $device['orientation']==='portrait' ? 'mobile-alt' : 'tv' }} text-2xl"></i>
                                        </div>
                                        <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full border-2 border-white {{ $device['status']==='online' ? 'bg-green-500' : 'bg-red-400' }}"></span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h4 class="font-semibold text-gray-900 truncate">{{ $device['name'] }}</h4>
                                        <p class="text-sm text-gray-500">{{ $device['model'] }} · {{ $device['location'] }}</p>
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $device['ip_address'] }} · {{ $device['os'] }}</p>
                                    </div>
                                </div>

                                {{-- Orta: Hızlı Metrikler --}}
                                <div class="flex items-center gap-6 flex-shrink-0">
                                    <div class="text-center">
                                        <div class="text-xs text-gray-500 mb-1">Çözünürlük</div>
                                        <span class="text-sm font-mono font-semibold text-gray-900">{{ $device['resolution'] }}</span>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-xs text-gray-500 mb-1">Yön</div>
                                        <span class="text-sm font-medium text-gray-700">
                                            <i class="fas fa-{{ $device['orientation']==='portrait' ? 'arrows-alt-v' : 'arrows-alt-h' }} mr-1"></i>
                                            {{ $device['orientation']==='portrait' ? 'Dikey' : ($device['orientation']==='landscape' ? 'Yatay' : 'Kare') }}
                                        </span>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-xs text-gray-500 mb-1">Parlaklık</div>
                                        <span class="text-sm font-semibold text-amber-600"><i class="fas fa-sun mr-1"></i>{{ $device['brightness'] }}%</span>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-xs text-gray-500 mb-1">Ses</div>
                                        <span class="text-sm font-semibold text-blue-600"><i class="fas fa-volume-{{ $device['volume'] > 0 ? 'up' : 'mute' }} mr-1"></i>{{ $device['volume'] }}%</span>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-xs text-gray-500 mb-1">Son Sinyal</div>
                                        <span class="text-sm text-gray-600">{{ $device['last_ping'] }}</span>
                                    </div>
                                </div>

                                {{-- Sağ: Butonlar --}}
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    @if($device['template'])
                                        <a href="{{ route('signage.preview', $device['template']) }}" target="_blank" class="px-3 py-2 text-sm bg-violet-50 text-violet-700 rounded-lg hover:bg-violet-100 transition" title="Şablonu Önizle">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endif
                                    <button class="px-3 py-2 text-sm bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition" title="Yeniden Başlat">
                                        <i class="fas fa-redo-alt"></i>
                                    </button>
                                    <button @click="expanded = !expanded" class="px-3 py-2 text-sm bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition">
                                        <i :class="expanded ? 'fa-chevron-up' : 'fa-chevron-down'" class="fas"></i> <span x-text="expanded ? 'Kapat' : 'Ayarlar'" class="ml-1"></span>
                                    </button>
                                </div>
                            </div>

                            {{-- Kaynak Kullanımı Barları --}}
                            @if($device['status'] === 'online')
                            <div class="mt-4 grid grid-cols-3 gap-4">
                                <div>
                                    <div class="flex items-center justify-between text-xs mb-1"><span class="text-gray-500">CPU</span><span class="font-medium">{{ $device['cpu_usage'] }}%</span></div>
                                    <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden"><div class="h-full bg-blue-500 rounded-full transition-all" style="width:{{ $device['cpu_usage'] }}%"></div></div>
                                </div>
                                <div>
                                    <div class="flex items-center justify-between text-xs mb-1"><span class="text-gray-500">RAM</span><span class="font-medium">{{ $device['memory_usage'] }}%</span></div>
                                    <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden"><div class="h-full bg-emerald-500 rounded-full transition-all" style="width:{{ $device['memory_usage'] }}%"></div></div>
                                </div>
                                <div>
                                    <div class="flex items-center justify-between text-xs mb-1"><span class="text-gray-500">Depolama</span><span class="font-medium">{{ $device['storage_usage'] }}%</span></div>
                                    <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden"><div class="h-full bg-amber-500 rounded-full transition-all" style="width:{{ $device['storage_usage'] }}%"></div></div>
                                </div>
                            </div>
                            @endif
                        </div>

                        {{-- ═══ GENİŞLETİLMİŞ AYAR PANELİ ═══ --}}
                        <div x-show="expanded" x-collapse class="border-t border-gray-100 bg-gray-50 p-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

                                {{-- EKRAN AYARLARI --}}
                                <div class="space-y-4">
                                    <h5 class="font-semibold text-sm text-gray-700 flex items-center gap-2"><i class="fas fa-display text-violet-500"></i> Ekran Ayarları</h5>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Çözünürlük</label>
                                        <select class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-violet-500">
                                            @foreach($resolutions as $group => $resList)
                                                <optgroup label="{{ $group === 'landscape' ? '🖥️ Yatay' : ($group === 'portrait' ? '📱 Dikey' : ($group === 'square' ? '⬜ Kare' : '🔧 Özel')) }}">
                                                    @foreach($resList as $res)
                                                        <option value="{{ $res['code'] }}" {{ $device['resolution'] === $res['code'] ? 'selected' : '' }}>
                                                            {{ $res['label'] }} ({{ $res['code'] }}) — {{ $res['ratio'] }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Yön</label>
                                        <div class="flex gap-2">
                                            <button class="flex-1 px-3 py-2 rounded-lg text-sm border transition {{ $device['orientation']==='landscape' ? 'bg-violet-100 border-violet-300 text-violet-700' : 'bg-white border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                                                <i class="fas fa-arrows-alt-h mr-1"></i> Yatay
                                            </button>
                                            <button class="flex-1 px-3 py-2 rounded-lg text-sm border transition {{ $device['orientation']==='portrait' ? 'bg-violet-100 border-violet-300 text-violet-700' : 'bg-white border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                                                <i class="fas fa-arrows-alt-v mr-1"></i> Dikey
                                            </button>
                                            <button class="flex-1 px-3 py-2 rounded-lg text-sm border transition {{ $device['orientation']==='square' ? 'bg-violet-100 border-violet-300 text-violet-700' : 'bg-white border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                                                <i class="fas fa-square mr-1"></i> Kare
                                            </button>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Döndürme Açısı</label>
                                        <div class="flex gap-2">
                                            <button class="flex-1 px-3 py-2 bg-violet-100 border border-violet-300 text-violet-700 rounded-lg text-sm font-medium">0°</button>
                                            <button class="flex-1 px-3 py-2 bg-white border border-gray-300 text-gray-600 rounded-lg text-sm hover:bg-gray-50">90°</button>
                                            <button class="flex-1 px-3 py-2 bg-white border border-gray-300 text-gray-600 rounded-lg text-sm hover:bg-gray-50">180°</button>
                                            <button class="flex-1 px-3 py-2 bg-white border border-gray-300 text-gray-600 rounded-lg text-sm hover:bg-gray-50">270°</button>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Ölçekleme Modu</label>
                                        <select class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-violet-500">
                                            <option value="fill">Doldur (Tam Ekran)</option>
                                            <option value="fit">Sığdır (Oranı Koru)</option>
                                            <option value="stretch">Ger (Deforme Et)</option>
                                            <option value="center">Ortala (Orijinal Boyut)</option>
                                            <option value="tile">Döşe (Tekrarla)</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- SES & PARLAKLIK --}}
                                <div class="space-y-4">
                                    <h5 class="font-semibold text-sm text-gray-700 flex items-center gap-2"><i class="fas fa-sliders-h text-amber-500"></i> Ses & Parlaklık</h5>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-2">Parlaklık: <span class="font-semibold text-amber-600">{{ $device['brightness'] }}%</span></label>
                                        <input type="range" min="0" max="100" value="{{ $device['brightness'] }}" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-amber-500">
                                        <div class="flex justify-between text-xs text-gray-400 mt-1"><span>0%</span><span>50%</span><span>100%</span></div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-2">Ses Seviyesi: <span class="font-semibold text-blue-600">{{ $device['volume'] }}%</span></label>
                                        <input type="range" min="0" max="100" value="{{ $device['volume'] }}" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-500">
                                        <div class="flex justify-between text-xs text-gray-400 mt-1"><span><i class="fas fa-volume-mute"></i></span><span><i class="fas fa-volume-down"></i></span><span><i class="fas fa-volume-up"></i></span></div>
                                    </div>

                                    <div class="bg-white rounded-lg border border-gray-200 p-3 space-y-2">
                                        <label class="flex items-center justify-between text-sm">
                                            <span class="text-gray-700">Otomatik Parlaklık</span>
                                            <div class="relative">
                                                <input type="checkbox" class="sr-only peer">
                                                <div class="w-10 h-5 bg-gray-200 rounded-full peer peer-checked:bg-violet-500 transition"></div>
                                                <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full peer-checked:translate-x-5 transition"></div>
                                            </div>
                                        </label>
                                        <label class="flex items-center justify-between text-sm">
                                            <span class="text-gray-700">Gece Modu (Düşük Mavi Işık)</span>
                                            <div class="relative">
                                                <input type="checkbox" class="sr-only peer">
                                                <div class="w-10 h-5 bg-gray-200 rounded-full peer peer-checked:bg-violet-500 transition"></div>
                                                <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full peer-checked:translate-x-5 transition"></div>
                                            </div>
                                        </label>
                                        <label class="flex items-center justify-between text-sm">
                                            <span class="text-gray-700">Ekran Yanması Koruması</span>
                                            <div class="relative">
                                                <input type="checkbox" checked class="sr-only peer">
                                                <div class="w-10 h-5 bg-gray-200 rounded-full peer peer-checked:bg-violet-500 transition"></div>
                                                <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full peer-checked:translate-x-5 transition"></div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                {{-- GÜÇ & ZAMANLAMA --}}
                                <div class="space-y-4">
                                    <h5 class="font-semibold text-sm text-gray-700 flex items-center gap-2"><i class="fas fa-power-off text-green-500"></i> Güç & Zamanlama</h5>

                                    <div class="bg-white rounded-lg border border-gray-200 p-3">
                                        <label class="flex items-center justify-between text-sm">
                                            <span class="text-gray-700 font-medium">Otomatik Açma/Kapama</span>
                                            <div class="relative">
                                                <input type="checkbox" {{ $device['auto_power'] ? 'checked' : '' }} class="sr-only peer">
                                                <div class="w-10 h-5 bg-gray-200 rounded-full peer peer-checked:bg-green-500 transition"></div>
                                                <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full peer-checked:translate-x-5 transition"></div>
                                            </div>
                                        </label>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1"><i class="fas fa-sunrise text-amber-500 mr-1"></i> Açılış Saati</label>
                                            <input type="time" value="{{ $device['power_on'] }}" class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-violet-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1"><i class="fas fa-moon text-indigo-500 mr-1"></i> Kapanış Saati</label>
                                            <input type="time" value="{{ $device['power_off'] }}" class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-violet-500">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Aktif Günler</label>
                                        <div class="flex gap-1">
                                            @foreach(['Pzt','Sal','Çar','Per','Cum','Cmt','Paz'] as $day)
                                                <button class="flex-1 px-1 py-2 rounded-lg text-xs font-medium border {{ in_array($day, ['Cmt','Paz']) ? 'bg-white border-gray-300 text-gray-400' : 'bg-violet-100 border-violet-300 text-violet-700' }} hover:bg-violet-50 transition">
                                                    {{ $day }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Atanmış Playlist</label>
                                        <select class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-violet-500">
                                            <option value="">— Playlist Seçin —</option>
                                            @foreach($playlists as $pl)
                                                <option value="{{ $pl['id'] }}" {{ $device['playlist_id'] == $pl['id'] ? 'selected' : '' }}>{{ $pl['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Boşta Kalma Süresi (dakika)</label>
                                        <select class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-violet-500">
                                            <option>Kapatma (Sürekli Açık)</option>
                                            <option>5 dakika</option>
                                            <option>10 dakika</option>
                                            <option>15 dakika</option>
                                            <option>30 dakika</option>
                                            <option>60 dakika</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- AĞ & TEKNİK --}}
                                <div class="space-y-4">
                                    <h5 class="font-semibold text-sm text-gray-700 flex items-center gap-2"><i class="fas fa-network-wired text-cyan-500"></i> Ağ & Teknik</h5>

                                    <div class="bg-white rounded-lg border border-gray-200 p-3 space-y-2">
                                        <div class="flex justify-between text-sm"><span class="text-gray-500">IP Adresi</span><span class="font-mono text-gray-900">{{ $device['ip_address'] }}</span></div>
                                        <div class="flex justify-between text-sm"><span class="text-gray-500">MAC Adresi</span><span class="font-mono text-gray-900 text-xs">{{ $device['mac_address'] }}</span></div>
                                        <div class="flex justify-between text-sm"><span class="text-gray-500">İşletim Sistemi</span><span class="text-gray-900">{{ $device['os'] }}</span></div>
                                        <div class="flex justify-between text-sm"><span class="text-gray-500">Cihaz Türü</span><span class="text-gray-900">{{ $device['device_type'] }}</span></div>
                                        <div class="flex justify-between text-sm"><span class="text-gray-500">Uptime</span><span class="text-gray-900">{{ $device['uptime'] }}</span></div>
                                    </div>
                                </div>

                                {{-- İÇERİK AYARLARI --}}
                                <div class="space-y-4">
                                    <h5 class="font-semibold text-sm text-gray-700 flex items-center gap-2"><i class="fas fa-photo-video text-pink-500"></i> İçerik Ayarları</h5>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Geçiş Efekti</label>
                                        <select class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-violet-500">
                                            <option>Yok (Anında)</option>
                                            <option selected>Fade (Solma)</option>
                                            <option>Slide Sol (Sola Kayma)</option>
                                            <option>Slide Sağ (Sağa Kayma)</option>
                                            <option>Slide Yukarı</option>
                                            <option>Slide Aşağı</option>
                                            <option>Zoom In (Yakınlaş)</option>
                                            <option>Zoom Out (Uzaklaş)</option>
                                            <option>Flip (Çevirme)</option>
                                            <option>Blur (Bulanıklaş)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Geçiş Süresi (ms)</label>
                                        <input type="number" value="500" min="0" max="5000" step="100" class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-violet-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Varsayılan İçerik Süresi (sn)</label>
                                        <input type="number" value="10" min="1" max="300" class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-violet-500">
                                    </div>
                                    <div class="bg-white rounded-lg border border-gray-200 p-3 space-y-2">
                                        <label class="flex items-center justify-between text-sm">
                                            <span class="text-gray-700">Saat & Tarih Göster</span>
                                            <div class="relative">
                                                <input type="checkbox" checked class="sr-only peer">
                                                <div class="w-10 h-5 bg-gray-200 rounded-full peer peer-checked:bg-violet-500 transition"></div>
                                                <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full peer-checked:translate-x-5 transition"></div>
                                            </div>
                                        </label>
                                        <label class="flex items-center justify-between text-sm">
                                            <span class="text-gray-700">Logo Watermark</span>
                                            <div class="relative">
                                                <input type="checkbox" class="sr-only peer">
                                                <div class="w-10 h-5 bg-gray-200 rounded-full peer peer-checked:bg-violet-500 transition"></div>
                                                <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full peer-checked:translate-x-5 transition"></div>
                                            </div>
                                        </label>
                                        <label class="flex items-center justify-between text-sm">
                                            <span class="text-gray-700">Alt Bant Haberleri</span>
                                            <div class="relative">
                                                <input type="checkbox" class="sr-only peer">
                                                <div class="w-10 h-5 bg-gray-200 rounded-full peer peer-checked:bg-violet-500 transition"></div>
                                                <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full peer-checked:translate-x-5 transition"></div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                {{-- UZAKTAN KONTROL --}}
                                <div class="space-y-4">
                                    <h5 class="font-semibold text-sm text-gray-700 flex items-center gap-2"><i class="fas fa-gamepad text-indigo-500"></i> Uzaktan Kontrol</h5>
                                    <div class="grid grid-cols-2 gap-2">
                                        <button class="flex items-center justify-center gap-2 px-3 py-3 bg-green-50 text-green-700 rounded-lg text-sm font-medium hover:bg-green-100 border border-green-200 transition">
                                            <i class="fas fa-power-off"></i> Aç
                                        </button>
                                        <button class="flex items-center justify-center gap-2 px-3 py-3 bg-red-50 text-red-700 rounded-lg text-sm font-medium hover:bg-red-100 border border-red-200 transition">
                                            <i class="fas fa-power-off"></i> Kapat
                                        </button>
                                        <button class="flex items-center justify-center gap-2 px-3 py-3 bg-blue-50 text-blue-700 rounded-lg text-sm font-medium hover:bg-blue-100 border border-blue-200 transition">
                                            <i class="fas fa-redo-alt"></i> Yeniden Başlat
                                        </button>
                                        <button class="flex items-center justify-center gap-2 px-3 py-3 bg-amber-50 text-amber-700 rounded-lg text-sm font-medium hover:bg-amber-100 border border-amber-200 transition">
                                            <i class="fas fa-camera"></i> Ekran Görüntüsü
                                        </button>
                                        <button class="flex items-center justify-center gap-2 px-3 py-3 bg-purple-50 text-purple-700 rounded-lg text-sm font-medium hover:bg-purple-100 border border-purple-200 transition">
                                            <i class="fas fa-bullhorn"></i> Tanımlama Sesi
                                        </button>
                                        <button class="flex items-center justify-center gap-2 px-3 py-3 bg-cyan-50 text-cyan-700 rounded-lg text-sm font-medium hover:bg-cyan-100 border border-cyan-200 transition">
                                            <i class="fas fa-download"></i> Güncelle
                                        </button>
                                    </div>
                                    <div class="mt-2 flex gap-2">
                                        <button class="flex-1 px-4 py-2 bg-violet-600 text-white rounded-lg text-sm font-medium hover:bg-violet-700 transition">
                                            <i class="fas fa-save mr-2"></i> Ayarları Kaydet
                                        </button>
                                        <button class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200 transition">
                                            <i class="fas fa-undo mr-1"></i> Sıfırla
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- ══════════════════════════════════════════ --}}
            {{--  SEKME 4: ZAMANLAMA                       --}}
            {{-- ══════════════════════════════════════════ --}}
            <div x-show="activeTab === 'schedules'" x-cloak>
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Zamanlama Takvimi</h3>
                    <button class="inline-flex items-center gap-2 px-4 py-2 bg-violet-600 text-white rounded-lg text-sm font-medium hover:bg-violet-700 transition">
                        <i class="fas fa-plus"></i> Yeni Zamanlama
                    </button>
                </div>

                {{-- Haftalık Zaman Çizelgesi --}}
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden mb-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 w-40">Saat</th>
                                    @foreach(['Pazartesi','Salı','Çarşamba','Perşembe','Cuma','Cumartesi','Pazar'] as $day)
                                        <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600">{{ $day }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach(['06:00','07:00','08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00','20:00','21:00','22:00','23:00'] as $hour)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-4 py-2 text-xs text-gray-500 font-mono">{{ $hour }}</td>
                                    @for($d = 0; $d < 7; $d++)
                                        @php
                                            $dayMap = [0=>'Pzt',1=>'Sal',2=>'Çar',3=>'Per',4=>'Cum',5=>'Cmt',6=>'Paz'];
                                            $schedule = collect($schedules)->first(function($s) use ($hour, $dayMap, $d) {
                                                return in_array($dayMap[$d], $s['days']) && $hour >= $s['time_start'] && $hour < $s['time_end'];
                                            });
                                        @endphp
                                        <td class="px-1 py-1">
                                            @if($schedule)
                                                @php
                                                    $colorPool = ['bg-blue-100 text-blue-700 border-blue-200','bg-violet-100 text-violet-700 border-violet-200','bg-rose-100 text-rose-700 border-rose-200','bg-emerald-100 text-emerald-700 border-emerald-200','bg-amber-100 text-amber-700 border-amber-200'];
                                                    $c = $colorPool[($schedule['playlist_id'] ?? 0) % count($colorPool)];
                                                @endphp
                                                <div class="px-1.5 py-1 {{ $c }} border rounded text-xs text-center font-medium truncate" title="{{ $schedule['playlist_name'] }}">
                                                    {{ \Illuminate\Support\Str::limit($schedule['playlist_name'], 12) }}
                                                </div>
                                            @else
                                                <div class="h-7"></div>
                                            @endif
                                        </td>
                                    @endfor
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Zamanlama Listesi --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($schedules as $schedule)
                    <div class="bg-white border border-gray-200 rounded-xl p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between">
                            <div>
                                <h4 class="font-semibold text-gray-900">{{ $schedule['name'] }}</h4>
                                <p class="text-sm text-gray-500 mt-1">
                                    <i class="fas fa-clock mr-1"></i> {{ $schedule['time_start'] }} — {{ $schedule['time_end'] }}
                                </p>
                            </div>
                            <span class="px-2 py-0.5 bg-violet-100 text-violet-700 text-xs rounded-full font-medium">Öncelik: {{ $schedule['priority'] }}</span>
                        </div>
                        <div class="mt-3 flex items-center gap-2">
                            <span class="text-xs text-gray-500">Günler:</span>
                            @foreach($schedule['days'] as $day)
                                <span class="px-2 py-0.5 bg-gray-100 text-gray-700 text-xs rounded font-medium">{{ $day }}</span>
                            @endforeach
                        </div>
                        <div class="mt-3 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-play-circle text-violet-500"></i>
                                <span class="text-sm font-medium text-gray-700">{{ $schedule['playlist_name'] }}</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <form action="{{ route('signage.schedule.destroy', $schedule['id']) }}" method="POST" onsubmit="return confirm('Bu zamanlama silinsin mi?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-500 transition"><i class="fas fa-trash text-xs"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- ══════════════════════════════════════════ --}}
            {{--  SEKME 5: ÇÖZÜNÜRLÜK MERKEZİ             --}}
            {{-- ══════════════════════════════════════════ --}}
            <div x-show="activeTab === 'resolutions'" x-cloak>
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Çözünürlük Merkezi</h3>
                        <p class="text-sm text-gray-500 mt-1">Desteklenen tüm ekran çözünürlükleri ve yapılandırmaları</p>
                    </div>
                </div>

                @php
                    $groupLabels = ['landscape'=>['🖥️ Yatay (Landscape)','from-blue-50 to-indigo-50','blue'],'portrait'=>['📱 Dikey (Portrait)','from-pink-50 to-rose-50','pink'],'square'=>['⬜ Kare (Square)','from-emerald-50 to-green-50','emerald'],'special'=>['🔧 Özel Formatlar','from-amber-50 to-orange-50','amber']];
                @endphp

                <div class="space-y-6">
                    @foreach($resolutions as $group => $resList)
                    @php $gl = $groupLabels[$group]; @endphp
                    <div class="bg-gradient-to-r {{ $gl[1] }} rounded-xl border border-gray-200 p-5">
                        <h4 class="font-semibold text-gray-800 mb-4">{{ $gl[0] }} <span class="text-sm font-normal text-gray-500 ml-2">({{ count($resList) }} çözünürlük)</span></h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                            @foreach($resList as $res)
                            <div class="bg-white rounded-lg border border-gray-200 p-3 hover:shadow-md hover:border-{{ $gl[2] }}-300 transition-all cursor-pointer group">
                                <div class="flex items-center justify-between">
                                    <span class="font-mono text-sm font-bold text-gray-900">{{ $res['code'] }}</span>
                                    @if($res['tier'] === 'recommended')
                                        <span class="px-1.5 py-0.5 bg-green-100 text-green-700 text-xs rounded font-medium">Önerilen</span>
                                    @elseif($res['tier'] === 'premium')
                                        <span class="px-1.5 py-0.5 bg-violet-100 text-violet-700 text-xs rounded font-medium">Premium</span>
                                    @elseif($res['tier'] === 'ultra')
                                        <span class="px-1.5 py-0.5 bg-amber-100 text-amber-700 text-xs rounded font-medium">Ultra</span>
                                    @elseif($res['tier'] === 'ultrawide')
                                        <span class="px-1.5 py-0.5 bg-cyan-100 text-cyan-700 text-xs rounded font-medium">UltraWide</span>
                                    @elseif($res['tier'] === 'special')
                                        <span class="px-1.5 py-0.5 bg-orange-100 text-orange-700 text-xs rounded font-medium">Özel</span>
                                    @endif
                                </div>
                                <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
                                    <span>{{ $res['label'] }}</span>
                                    <span class="font-semibold text-gray-600">{{ $res['ratio'] }}</span>
                                </div>
                                {{-- Görsel Oran Gösterimi --}}
                                @php
                                    $parts = explode('x', $res['code']);
                                    $w = (int)$parts[0]; $h = (int)$parts[1];
                                    $maxW = 80; $maxH = 30;
                                    if($w >= $h) { $dispW = $maxW; $dispH = max(6, round($maxW * $h / $w)); }
                                    else { $dispH = $maxH; $dispW = max(12, round($maxH * $w / $h)); }
                                @endphp
                                <div class="mt-2 flex items-center justify-center">
                                    <div class="bg-gradient-to-br from-gray-200 to-gray-300 rounded group-hover:from-{{ $gl[2] }}-200 group-hover:to-{{ $gl[2] }}-300 transition-colors flex items-center justify-center text-xs text-gray-400 group-hover:text-{{ $gl[2] }}-500"
                                         style="width:{{ $dispW }}px;height:{{ $dispH }}px;">
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- ══════════════════════════════════════════ --}}
            {{--  SEKME 6: ŞABLONLAR                       --}}
            {{-- ══════════════════════════════════════════ --}}
            <div x-show="activeTab === 'templates'" x-cloak>
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Ekran Şablonları</h3>
                    <p class="text-sm text-gray-500">{{ count($templates) }} hazır şablon</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">
                    @foreach($templates as $tpl)
                    <div class="group bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg hover:border-{{ $tpl['color'] }}-300 transition-all">
                        <div class="h-32 bg-gradient-to-br from-{{ $tpl['color'] }}-400 to-{{ $tpl['color'] }}-600 flex items-center justify-center relative">
                            <i class="fas {{ $tpl['icon'] }} text-white/80 text-4xl group-hover:scale-110 transition-transform"></i>
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3">
                                <a href="{{ route('signage.preview', $tpl['code']) }}" target="_blank" class="px-4 py-2 bg-white/90 text-gray-900 rounded-lg text-sm font-medium hover:bg-white transition">
                                    <i class="fas fa-eye mr-1"></i> Önizle
                                </a>
                                <a href="{{ route('signage.display', $tpl['code']) }}" target="_blank" class="px-4 py-2 bg-white/20 text-white rounded-lg text-sm font-medium hover:bg-white/30 transition backdrop-blur">
                                    <i class="fas fa-external-link-alt mr-1"></i> Tam Ekran
                                </a>
                            </div>
                        </div>
                        <div class="p-4">
                            <h4 class="font-semibold text-gray-900">{{ $tpl['name'] }}</h4>
                            <p class="text-xs text-gray-500 mt-1">{{ $tpl['desc'] }}</p>
                            <div class="mt-3 flex flex-wrap gap-1">
                                @foreach($tpl['features'] as $feat)
                                    <span class="px-2 py-0.5 bg-{{ $tpl['color'] }}-50 text-{{ $tpl['color'] }}-700 text-xs rounded-full">{{ $feat }}</span>
                                @endforeach
                            </div>
                            <div class="mt-3 pt-3 border-t border-gray-100 text-xs text-gray-500">
                                <i class="fas fa-bullseye mr-1"></i> {{ $tpl['best_for'] }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- ══════════════════════════════════════════ --}}
            {{--  SEKME 7: DONANIM UYUMLULUK              --}}
            {{-- ══════════════════════════════════════════ --}}
            <div x-show="activeTab === 'hardware'" x-cloak>
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Donanım Uyumluluğu</h3>
                    <p class="text-sm text-gray-500">{{ collect($compatibleHardware)->sum(fn($g)=>count($g['items'])) }}+ uyumlu cihaz</p>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach($compatibleHardware as $key => $group)
                    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                        <div class="px-5 py-3 bg-gray-50 border-b border-gray-200 flex items-center gap-3">
                            <i class="fas {{ $group['icon'] }} text-violet-500"></i>
                            <h4 class="font-semibold text-gray-800">{{ $group['title'] }}</h4>
                            <span class="ml-auto text-xs text-gray-500">{{ count($group['items']) }} cihaz</span>
                        </div>
                        <div class="divide-y divide-gray-50">
                            @foreach($group['items'] as $item)
                            <div class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 transition">
                                <div>
                                    <span class="text-sm font-medium text-gray-900">{{ $item['name'] }}</span>
                                    <p class="text-xs text-gray-500">{{ $item['note'] }}</p>
                                </div>
                                @if($item['status'] === 'full')
                                    <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full font-medium">✓ Tam</span>
                                @else
                                    <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-xs rounded-full font-medium">◐ Kısmi</span>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- ══════════════════════════════════════════ --}}
            {{--  SEKME 8: YAZILIM & KURULUM               --}}
            {{-- ══════════════════════════════════════════ --}}
            <div x-show="activeTab === 'software'" x-cloak>
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Yazılım & Platformlar</h3>
                </div>
                <div class="space-y-6">
                    @foreach($softwareList as $key => $group)
                    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                        <div class="px-5 py-3 bg-gray-50 border-b border-gray-200 flex items-center gap-3">
                            <i class="fas {{ $group['icon'] }} text-violet-500"></i>
                            <h4 class="font-semibold text-gray-800">{{ $group['title'] }}</h4>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 p-5">
                            @foreach($group['items'] as $item)
                            <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg hover:bg-violet-50 transition">
                                <div class="mt-0.5">
                                    @if($item['status'] === 'native')
                                        <span class="w-6 h-6 bg-violet-100 text-violet-600 rounded-full flex items-center justify-center text-xs"><i class="fas fa-star"></i></span>
                                    @elseif($item['status'] === 'ready')
                                        <span class="w-6 h-6 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-xs"><i class="fas fa-check"></i></span>
                                    @else
                                        <span class="w-6 h-6 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center text-xs"><i class="fas fa-clock"></i></span>
                                    @endif
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-900">{{ $item['name'] }}</span>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $item['desc'] }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Hızlı Kurulum Rehberi --}}
                <div class="mt-8 bg-gradient-to-br from-violet-50 to-indigo-50 rounded-xl border border-violet-200 p-6">
                    <h4 class="font-semibold text-gray-900 flex items-center gap-2 mb-4"><i class="fas fa-rocket text-violet-500"></i> Hızlı Kurulum Rehberi</h4>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-white rounded-lg p-4 border border-violet-100 text-center">
                            <div class="w-10 h-10 bg-violet-100 text-violet-600 rounded-full flex items-center justify-center mx-auto mb-3 text-lg font-bold">1</div>
                            <h5 class="font-semibold text-sm text-gray-800">Cihaz Seçin</h5>
                            <p class="text-xs text-gray-500 mt-1">Smart TV, media player veya tek kart bilgisayar tercih edin</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 border border-violet-100 text-center">
                            <div class="w-10 h-10 bg-violet-100 text-violet-600 rounded-full flex items-center justify-center mx-auto mb-3 text-lg font-bold">2</div>
                            <h5 class="font-semibold text-sm text-gray-800">Tarayıcı / Uygulama</h5>
                            <p class="text-xs text-gray-500 mt-1">Chrome/Chromium kiosk modu veya Android APK kurun</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 border border-violet-100 text-center">
                            <div class="w-10 h-10 bg-violet-100 text-violet-600 rounded-full flex items-center justify-center mx-auto mb-3 text-lg font-bold">3</div>
                            <h5 class="font-semibold text-sm text-gray-800">URL Girin</h5>
                            <p class="text-xs text-gray-500 mt-1"><code class="bg-gray-100 px-1 rounded text-xs">{{ url('/dijital-ekran/goruntule') }}/şablon</code></p>
                        </div>
                        <div class="bg-white rounded-lg p-4 border border-violet-100 text-center">
                            <div class="w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-3 text-lg font-bold">✓</div>
                            <h5 class="font-semibold text-sm text-gray-800">Hazır!</h5>
                            <p class="text-xs text-gray-500 mt-1">Panelden içerik yönetin, otomatik güncellenir</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ══════════════════════════════════════════ --}}
    {{--  MODAL: İÇERİK YÜKLEME                    --}}
    {{-- ══════════════════════════════════════════ --}}
    <div x-show="showUploadModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" @click.self="showUploadModal = false">
        <form action="{{ route('signage.content.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden" @click.stop>
            @csrf
            <div class="px-6 py-4 bg-gradient-to-r from-violet-500 to-purple-600 text-white flex items-center justify-between">
                <h3 class="font-semibold text-lg"><i class="fas fa-cloud-upload-alt mr-2"></i> İçerik Yükle</h3>
                <button type="button" @click="showUploadModal = false" class="text-white/70 hover:text-white transition"><i class="fas fa-times text-xl"></i></button>
            </div>
            <div class="p-6 space-y-5">
                {{-- Dosya Yükleme --}}
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-10 text-center hover:border-violet-400 hover:bg-violet-50/50 transition-colors cursor-pointer relative">
                    <input type="file" name="file" accept="image/*,video/*,.gif,.webm" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-300 mb-3"></i>
                    <p class="text-sm text-gray-600">Dosyalarınızı sürükleyip bırakın veya <span class="text-violet-600 font-medium underline">gözatın</span></p>
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG, MP4, WebM, GIF — Maks. 50 MB</p>
                </div>
                {{-- İçerik Bilgileri --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">İçerik Adı</label>
                        <input type="text" name="name" required placeholder="Kampanya görseli" class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-violet-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">İçerik Türü</label>
                        <select name="type" class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-violet-500">
                            <option value="image">Görsel</option>
                            <option value="video">Video</option>
                            <option value="widget">Widget</option>
                            <option value="template">Şablon</option>
                            <option value="url">Web URL</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Gösterim Süresi (sn)</label>
                        <input type="number" name="duration" value="10" min="1" max="300" class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-violet-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Web URL (opsiyonel)</label>
                        <input type="url" name="url" placeholder="https://..." class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-violet-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Etiketler</label>
                    <input type="text" name="tags" placeholder="kampanya, indirim, menü (virgülle ayırın)" class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-violet-500">
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3">
                <button type="button" @click="showUploadModal = false" class="px-4 py-2 text-sm text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">İptal</button>
                <button type="submit" class="px-5 py-2 text-sm bg-violet-600 text-white rounded-lg font-medium hover:bg-violet-700 transition"><i class="fas fa-upload mr-2"></i> Yükle</button>
            </div>
        </form>
    </div>

    {{-- ══════════════════════════════════════════ --}}
    {{--  MODAL: YENİ PLAYLİST                     --}}
    {{-- ══════════════════════════════════════════ --}}
    <div x-show="showPlaylistModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" @click.self="showPlaylistModal = false">
        <form action="{{ route('signage.playlist.store') }}" method="POST" class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden" @click.stop>
            @csrf
            <div class="px-6 py-4 bg-gradient-to-r from-violet-500 to-purple-600 text-white flex items-center justify-between">
                <h3 class="font-semibold text-lg"><i class="fas fa-list-ul mr-2"></i> Yeni Playlist</h3>
                <button type="button" @click="showPlaylistModal = false" class="text-white/70 hover:text-white transition"><i class="fas fa-times text-xl"></i></button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Playlist Adı</label>
                    <input type="text" name="name" required placeholder="Örn: Sabah Döngüsü" class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-violet-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Zamanlama Açıklaması</label>
                    <input type="text" name="schedule_text" placeholder="Örn: Her gün 08:00 – 22:00" class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-2 focus:ring-violet-500">
                </div>
                <div>
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="loop" value="1" checked class="rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                        Döngü (loop) modunda oynat
                    </label>
                </div>
                @if($contents->count())
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">İçerik Seç</label>
                    <div class="max-h-40 overflow-y-auto border border-gray-200 rounded-lg p-2 space-y-1">
                        @foreach($contents as $c)
                        <label class="flex items-center gap-2 p-1.5 rounded hover:bg-violet-50 cursor-pointer">
                            <input type="checkbox" name="content_ids[]" value="{{ $c->id }}" class="rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                            <i class="fas {{ $c->icon }} text-xs text-gray-400"></i>
                            <span class="text-sm text-gray-700">{{ $c->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3">
                <button type="button" @click="showPlaylistModal = false" class="px-4 py-2 text-sm text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">İptal</button>
                <button type="submit" class="px-5 py-2 text-sm bg-violet-600 text-white rounded-lg font-medium hover:bg-violet-700 transition"><i class="fas fa-plus mr-2"></i> Oluştur</button>
            </div>
        </form>
    </div>

</div>

@push('scripts')
<script>
function signagePanel() {
    return {
        activeTab: 'contents',
        tabs: [
            { id: 'contents',    label: 'İçerik Kütüphanesi', icon: 'fas fa-photo-video',  badge: '{{ count($contents) }}' },
            { id: 'playlists',   label: 'Playlistler',        icon: 'fas fa-list-ul',       badge: '{{ count($playlists) }}' },
            { id: 'devices',     label: 'Cihazlar',           icon: 'fas fa-tv',            badge: '{{ count($devices) }}' },
            { id: 'schedules',   label: 'Zamanlama',          icon: 'fas fa-calendar-alt',  badge: '{{ count($schedules) }}' },
            { id: 'resolutions', label: 'Çözünürlükler',      icon: 'fas fa-expand-arrows-alt', badge: null },
            { id: 'templates',   label: 'Şablonlar',          icon: 'fas fa-palette',       badge: '{{ count($templates) }}' },
            { id: 'hardware',    label: 'Donanım',            icon: 'fas fa-microchip',     badge: null },
            { id: 'software',    label: 'Yazılım & Kurulum',  icon: 'fas fa-cogs',          badge: null },
        ],
        contentView: 'grid',
        contentSearch: '',
        contentTypeFilter: 'all',
        contentStatusFilter: 'all',
        showUploadModal: false,
        showPlaylistModal: false,
    }
}
</script>
@endpush
@endsection
