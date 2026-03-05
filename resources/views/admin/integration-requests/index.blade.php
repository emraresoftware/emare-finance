@extends('layouts.app')
@section('title', 'Entegrasyon Başvuruları')

@section('content')
<div x-data="integrationRequests()" class="space-y-6">

    {{-- Bildirimler --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-center gap-3" x-data="{show:true}" x-show="show" x-transition>
            <i class="fas fa-check-circle text-emerald-600"></i>
            <span class="text-sm text-emerald-800 flex-1">{{ session('success') }}</span>
            <button @click="show=false" class="text-emerald-400 hover:text-emerald-600"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- Başlık --}}
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-3">
                <div class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-inbox text-purple-600"></i>
                </div>
                Entegrasyon Başvuruları
            </h2>
            <p class="text-sm text-gray-500 mt-1">Kullanıcılardan gelen entegrasyon taleplerini yönetin</p>
        </div>
    </div>

    {{-- İstatistikler --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-layer-group text-gray-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                    <p class="text-xs text-gray-500">Toplam</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-amber-100 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-hourglass-half text-amber-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-amber-600">{{ $stats['pending'] }}</p>
                    <p class="text-xs text-gray-500">Beklemede</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-emerald-100 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-emerald-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-emerald-600">{{ $stats['approved'] }}</p>
                    <p class="text-xs text-gray-500">Onaylanan</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-red-100 p-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['rejected'] }}</p>
                    <p class="text-xs text-gray-500">Reddedilen</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtreler --}}
    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[200px]">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Entegrasyon veya kullanıcı ara..."
                           class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400 transition">
                </div>
            </div>
            <select name="status" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-200 focus:border-indigo-400">
                <option value="">Tüm Durumlar</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Beklemede</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Onaylanan</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Reddedilen</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                <i class="fas fa-filter mr-1"></i> Filtrele
            </button>
            @if(request()->hasAny(['search', 'status', 'type']))
                <a href="{{ route('admin.integration-requests.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                    <i class="fas fa-times mr-1"></i> Temizle
                </a>
            @endif
        </form>
    </div>

    {{-- Başvuru Tablosu --}}
    @if($requests->count() > 0)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-5 py-3.5 text-left font-medium text-gray-500">#</th>
                    <th class="px-5 py-3.5 text-left font-medium text-gray-500">Kullanıcı</th>
                    <th class="px-5 py-3.5 text-left font-medium text-gray-500">Entegrasyon</th>
                    <th class="px-5 py-3.5 text-left font-medium text-gray-500">Mesaj</th>
                    <th class="px-5 py-3.5 text-center font-medium text-gray-500">Durum</th>
                    <th class="px-5 py-3.5 text-left font-medium text-gray-500">Tarih</th>
                    <th class="px-5 py-3.5 text-center font-medium text-gray-500">İşlem</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($requests as $req)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-4 text-gray-400 font-mono text-xs">{{ $req->id }}</td>
                    <td class="px-5 py-4">
                        <div>
                            <p class="font-medium text-gray-800">{{ $req->user->name ?? '-' }}</p>
                            <p class="text-xs text-gray-400">{{ $req->user->email ?? '' }}</p>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <div>
                            <p class="font-medium text-gray-800">{{ $req->integration_name }}</p>
                            <p class="text-xs text-gray-400">{{ $req->integration_type }}</p>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <span class="text-gray-600 truncate block max-w-[180px]" title="{{ $req->message }}">
                            {{ Str::limit($req->message, 50) ?: '—' }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-center">
                        @if($req->status === 'pending')
                            <span class="inline-flex items-center gap-1 text-xs font-medium text-amber-700 bg-amber-50 px-2.5 py-1 rounded-full border border-amber-200">
                                <i class="fas fa-hourglass-half text-[10px] animate-pulse"></i> Beklemede
                            </span>
                        @elseif($req->status === 'approved')
                            <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                                <i class="fas fa-check-circle text-[10px]"></i> Onaylandı
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-xs font-medium text-red-700 bg-red-50 px-2.5 py-1 rounded-full border border-red-200">
                                <i class="fas fa-times-circle text-[10px]"></i> Reddedildi
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-gray-500 text-xs whitespace-nowrap">
                        {{ $req->created_at->format('d.m.Y H:i') }}
                    </td>
                    <td class="px-5 py-4 text-center">
                        @if($req->status === 'pending')
                            <div class="flex items-center justify-center gap-1">
                                <button @click="openApprove({{ $req->id }}, '{{ $req->integration_name }}')"
                                        class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-lg transition" title="Onayla">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button @click="openReject({{ $req->id }}, '{{ $req->integration_name }}')"
                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Reddet">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @else
                            <span class="text-xs text-gray-400">
                                {{ $req->reviewer->name ?? '—' }}
                            </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $requests->links() }}</div>

    @else
    <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-inbox text-2xl text-gray-400"></i>
        </div>
        <h3 class="font-medium text-gray-700">Başvuru bulunamadı</h3>
        <p class="text-sm text-gray-500 mt-1">Henüz entegrasyon başvurusu yapılmamış veya filtrenize uygun sonuç yok.</p>
    </div>
    @endif

    {{-- ═══ Onay Modalı ═══ --}}
    <div x-show="showApproveModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition>
        <div class="absolute inset-0 bg-black/50" @click="showApproveModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.outside="showApproveModal = false">
            <div class="px-6 py-5 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800">Başvuruyu Onayla</h3>
                <p class="text-sm text-gray-500 mt-0.5">
                    <span class="font-medium text-emerald-600" x-text="actionName"></span> entegrasyon başvurusu onaylanacak
                </p>
            </div>
            <form :action="approveUrl" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Admin Notu <span class="text-gray-400 font-normal">(İsteğe bağlı)</span></label>
                    <textarea name="admin_note" rows="3" placeholder="Kullanıcıya iletmek istediğiniz bir not..."
                              class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-200 focus:border-emerald-400 transition resize-none"></textarea>
                </div>
                <div class="flex items-center gap-3">
                    <button type="submit"
                            class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-xl transition">
                        <i class="fas fa-check"></i> Onayla
                    </button>
                    <button type="button" @click="showApproveModal = false"
                            class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition">
                        İptal
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══ Red Modalı ═══ --}}
    <div x-show="showRejectModal" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-transition>
        <div class="absolute inset-0 bg-black/50" @click="showRejectModal = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md" @click.outside="showRejectModal = false">
            <div class="px-6 py-5 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800">Başvuruyu Reddet</h3>
                <p class="text-sm text-gray-500 mt-0.5">
                    <span class="font-medium text-red-600" x-text="actionName"></span> entegrasyon başvurusu reddedilecek
                </p>
            </div>
            <form :action="rejectUrl" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Red Gerekçesi <span class="text-red-500">*</span></label>
                    <textarea name="admin_note" rows="3" required placeholder="Başvurunun neden reddedildiğini açıklayın..."
                              class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-red-200 focus:border-red-400 transition resize-none"></textarea>
                </div>
                <div class="flex items-center gap-3">
                    <button type="submit"
                            class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-xl transition">
                        <i class="fas fa-times"></i> Reddet
                    </button>
                    <button type="button" @click="showRejectModal = false"
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
function integrationRequests() {
    return {
        showApproveModal: false,
        showRejectModal: false,
        actionId: null,
        actionName: '',

        get approveUrl() {
            return `/admin/entegrasyon-basvurulari/${this.actionId}/onayla`;
        },
        get rejectUrl() {
            return `/admin/entegrasyon-basvurulari/${this.actionId}/reddet`;
        },
        openApprove(id, name) {
            this.actionId = id;
            this.actionName = name;
            this.showApproveModal = true;
        },
        openReject(id, name) {
            this.actionId = id;
            this.actionName = name;
            this.showRejectModal = true;
        }
    };
}
</script>
@endpush
@endsection
