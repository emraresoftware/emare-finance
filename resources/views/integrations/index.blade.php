@extends('layouts.app')
@section('title', 'Entegrasyon Merkezi')

@section('content')
<div x-data="integrationHub()" class="space-y-6">

    {{-- Bildirimler --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-center gap-3" x-data="{show:true}" x-show="show" x-transition>
            <i class="fas fa-check-circle text-emerald-600"></i>
            <span class="text-sm text-emerald-800 flex-1">{{ session('success') }}</span>
            <button @click="show=false" class="text-emerald-400 hover:text-emerald-600"><i class="fas fa-times"></i></button>
        </div>
    @endif
    @if(session('warning'))
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-center gap-3" x-data="{show:true}" x-show="show" x-transition>
            <i class="fas fa-exclamation-triangle text-amber-600"></i>
            <span class="text-sm text-amber-800 flex-1">{{ session('warning') }}</span>
            <button @click="show=false" class="text-amber-400 hover:text-amber-600"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- Başlık --}}
    <div class="bg-gradient-to-r from-indigo-50 via-purple-50 to-pink-50 rounded-2xl p-6 border border-indigo-100">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
                    <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-plug text-indigo-600"></i>
                    </div>
                    Entegrasyon Merkezi
                </h2>
                <p class="text-gray-500 mt-1">İhtiyacınız olan entegrasyona başvurun, admin onayladığında aktif edilecektir.</p>
            </div>
            <div class="flex items-center gap-3 text-sm">
                <a href="{{ route('integrations.my_requests') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white text-indigo-600 rounded-xl border border-indigo-200 hover:bg-indigo-50 transition text-sm font-medium">
                    <i class="fas fa-list-check"></i>
                    Başvurularım
                    @if($myRequests->where('status', 'pending')->count() > 0)
                        <span class="bg-amber-500 text-white text-xs rounded-full px-2 py-0.5">{{ $myRequests->where('status', 'pending')->count() }}</span>
                    @endif
                </a>
            </div>
        </div>

        {{-- Arama --}}
        <div class="mt-4 relative">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" x-model="search" placeholder="Entegrasyon ara... (örn: Trendyol, iyzico, Logo)"
                   class="w-full pl-11 pr-4 py-3 bg-white/80 backdrop-blur border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 transition">
        </div>

        {{-- Kategori Filtreleri --}}
        <div class="mt-4 flex flex-wrap gap-2">
            <button @click="activeCategory = ''" :class="activeCategory === '' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-gray-600 hover:bg-gray-50'"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium border transition">
                Tümü
            </button>
            @foreach($integrations as $key => $cat)
            <button @click="activeCategory = '{{ $key }}'"
                    :class="activeCategory === '{{ $key }}' ? 'text-white shadow-md' : 'bg-white text-gray-600 hover:bg-gray-50'"
                    :style="activeCategory === '{{ $key }}' ? 'background-color: {{ $cat['color'] }}' : ''"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium border transition">
                <i class="fas {{ $cat['icon'] }} mr-1"></i> {{ $cat['title'] }}
            </button>
            @endforeach
        </div>
    </div>

    {{-- Entegrasyon Kategorileri --}}
    @foreach($integrations as $key => $category)
    <div x-show="(activeCategory === '' || activeCategory === '{{ $key }}') && filteredItems('{{ $key }}').length > 0"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        {{-- Kategori Başlık --}}
        <div class="px-6 py-4 border-b border-gray-50 flex items-center gap-3" style="background-color: {{ $category['bg'] }}">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background-color: {{ $category['color'] }}20">
                <i class="fas {{ $category['icon'] }}" style="color: {{ $category['color'] }}"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-800">{{ $category['title'] }}</h3>
                <p class="text-xs text-gray-500">{{ $category['description'] }}</p>
            </div>
            <span class="ml-auto text-xs font-medium px-2.5 py-1 rounded-full" style="background-color: {{ $category['color'] }}15; color: {{ $category['color'] }}">
                {{ count($category['items']) }} entegrasyon
            </span>
        </div>

        {{-- Entegrasyon Kartları --}}
        <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
            @foreach($category['items'] as $item)
            @php
                $req = $myRequests->get($item['name']);
                $hasPending  = $req && $req->status === 'pending';
                $hasApproved = $req && $req->status === 'approved';
                $hasRejected = $req && $req->status === 'rejected';
            @endphp
            <div x-show="matchSearch('{{ strtolower($item['name']) }}')"
                 class="group relative bg-gray-50 hover:bg-white rounded-xl p-4 border border-gray-100 hover:border-gray-200 hover:shadow-md transition-all duration-200">

                <div class="flex items-start gap-3">
                    <div class="text-2xl w-10 h-10 flex items-center justify-center rounded-lg bg-white shadow-sm border">
                        {{ $item['logo'] }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <h4 class="font-medium text-gray-800 text-sm truncate">{{ $item['name'] }}</h4>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ $item['desc'] }}</p>
                    </div>
                </div>

                {{-- Durum ve Başvuru --}}
                <div class="mt-3 flex items-center justify-between">
                    @if($hasPending)
                        <span class="inline-flex items-center gap-1 text-xs font-medium text-amber-700 bg-amber-50 px-2 py-1 rounded-md border border-amber-100">
                            <i class="fas fa-hourglass-half text-[10px] animate-pulse"></i> Başvuru Beklemede
                        </span>
                    @elseif($hasApproved)
                        <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-700 bg-emerald-50 px-2 py-1 rounded-md border border-emerald-100">
                            <i class="fas fa-check-circle text-[10px]"></i> Onaylandı
                        </span>
                    @elseif($hasRejected)
                        <span class="inline-flex items-center gap-1 text-xs font-medium text-red-700 bg-red-50 px-2 py-1 rounded-md border border-red-100">
                            <i class="fas fa-times-circle text-[10px]"></i> Reddedildi
                        </span>
                        <button @click="openModal('{{ $key }}', '{{ $item['name'] }}')"
                                class="text-xs text-indigo-600 hover:text-indigo-800 font-medium opacity-0 group-hover:opacity-100 transition">
                            Tekrar Başvur <i class="fas fa-redo ml-1"></i>
                        </button>
                    @else
                        <span class="inline-flex items-center gap-1 text-xs font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded-md border border-gray-200">
                            <i class="fas fa-plug text-[10px]"></i> Entegrasyon
                        </span>
                        <button @click="openModal('{{ $key }}', '{{ $item['name'] }}')"
                                class="text-xs text-indigo-600 hover:text-indigo-800 font-medium opacity-0 group-hover:opacity-100 transition">
                            Başvur <i class="fas fa-paper-plane ml-1"></i>
                        </button>
                    @endif
                </div>

                {{-- Red notu --}}
                @if($hasRejected && $req->admin_note)
                <div class="mt-2 text-xs text-red-600 bg-red-50 rounded-md p-2 border border-red-100">
                    <i class="fas fa-info-circle mr-1"></i> {{ $req->admin_note }}
                </div>
                @endif

                {{-- Onay notu --}}
                @if($hasApproved && $req->admin_note)
                <div class="mt-2 text-xs text-emerald-600 bg-emerald-50 rounded-md p-2 border border-emerald-100">
                    <i class="fas fa-info-circle mr-1"></i> {{ $req->admin_note }}
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    {{-- Boş Durum --}}
    <div x-show="totalVisible === 0" class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-search text-2xl text-gray-400"></i>
        </div>
        <h3 class="font-medium text-gray-700">Sonuç bulunamadı</h3>
        <p class="text-sm text-gray-500 mt-1">Arama kriterlerinize uygun entegrasyon bulunamadı.</p>
    </div>

    {{-- ═══ Başvuru Modalı ═══ --}}
    <div x-show="showModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div class="absolute inset-0 bg-black/50" @click="showModal = false"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             @click.outside="showModal = false">

            <div class="px-6 py-5 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Entegrasyon Başvurusu</h3>
                        <p class="text-sm text-gray-500 mt-0.5">
                            <span class="font-medium text-indigo-600" x-text="modalName"></span> entegrasyonu için başvuru
                        </p>
                    </div>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 transition">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>

            <form method="POST" action="{{ route('integrations.request') }}" class="px-6 py-5 space-y-4">
                @csrf
                <input type="hidden" name="integration_type" :value="modalType">
                <input type="hidden" name="integration_name" :value="modalName">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Mesajınız <span class="text-gray-400 font-normal">(İsteğe bağlı)</span>
                    </label>
                    <textarea name="message" rows="4"
                              placeholder="Bu entegrasyonu neden kullanmak istiyorsunuz? Özel gereksinimleriniz var mı?"
                              class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 transition resize-none"></textarea>
                </div>

                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                    <div class="flex gap-3">
                        <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                        <div class="text-xs text-blue-700 space-y-1">
                            <p class="font-medium">Başvuru süreci nasıl işler?</p>
                            <ul class="list-disc list-inside space-y-0.5 text-blue-600">
                                <li>Başvurunuz admin'e iletilecektir</li>
                                <li>Admin inceledikten sonra onay/red bilgisi alacaksınız</li>
                                <li>Onaylanan entegrasyonlar kurulum için hazırlanacaktır</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                            class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl shadow-sm transition">
                        <i class="fas fa-paper-plane"></i> Başvuru Gönder
                    </button>
                    <button type="button" @click="showModal = false"
                            class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition">
                        İptal
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
function integrationHub() {
    const allItems = @json(collect($integrations)->map(fn($cat, $key) => collect($cat['items'])->map(fn($item) => array_merge($item, ['category' => $key])))->flatten(1));

    return {
        search: '',
        activeCategory: '',
        showModal: false,
        modalType: '',
        modalName: '',

        get readyCount() {
            return allItems.filter(i => i.status === 'ready').length;
        },
        get plannedCount() {
            return allItems.filter(i => i.status === 'planned').length;
        },
        get totalVisible() {
            return allItems.filter(i => this.matchSearch(i.name.toLowerCase()) &&
                (this.activeCategory === '' || i.category === this.activeCategory)).length;
        },
        matchSearch(name) {
            if (!this.search) return true;
            return name.includes(this.search.toLowerCase());
        },
        filteredItems(category) {
            return allItems.filter(i => i.category === category && this.matchSearch(i.name.toLowerCase()));
        },
        openModal(type, name) {
            this.modalType = type;
            this.modalName = name;
            this.showModal = true;
        }
    };
}
</script>
@endpush
@endsection
