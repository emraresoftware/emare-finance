@extends('layouts.app')
@section('title', 'Segment - ' . $segment->name)

@section('content')
<div class="space-y-6">
    {{-- Başlık --}}
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('marketing.segments.index') }}" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <div class="w-10 h-10 bg-{{ $segment->color ?? 'indigo' }}-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-{{ $segment->icon ?? 'users' }} text-{{ $segment->color ?? 'indigo' }}-600"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800">{{ $segment->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $segment->description ?? 'Müşteri segmenti' }}</p>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $segment->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $segment->is_active ? 'Aktif' : 'Pasif' }}
                </span>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('marketing.messages.create', ['segment_id' => $segment->id]) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 font-medium">
                    <i class="fas fa-envelope mr-1"></i> Mesaj Gönder
                </a>
            </div>
        </div>
    </div>

    {{-- İstatistikler --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <p class="text-sm text-gray-500">Toplam Üye</p>
            <p class="text-2xl font-bold text-gray-800">{{ $segment->members->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <p class="text-sm text-gray-500">Tür</p>
            <p class="text-2xl font-bold text-purple-600">{{ $segment->type === 'manual' ? 'Manuel' : 'Otomatik' }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <p class="text-sm text-gray-500">Renk</p>
            <div class="flex items-center gap-2 mt-1">
                <span class="w-6 h-6 rounded-full bg-{{ $segment->color ?? 'indigo' }}-500"></span>
                <span class="text-sm font-medium capitalize">{{ $segment->color ?? 'indigo' }}</span>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <p class="text-sm text-gray-500">Oluşturulma</p>
            <p class="text-lg font-bold text-gray-800">{{ $segment->created_at->format('d.m.Y') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Üye Listesi --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="p-6 pb-0 flex items-center justify-between">
                <h3 class="text-md font-semibold text-gray-800"><i class="fas fa-users text-purple-500 mr-2"></i>Segment Üyeleri ({{ $segment->members->count() }})</h3>
            </div>
            <div class="overflow-x-auto mt-4">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Müşteri</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telefon</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Eklenme</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">İşlem</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($segment->members as $member)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <span class="font-medium text-gray-800">{{ $member->name }}</span>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $member->phone ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $member->email ?? '-' }}</td>
                                <td class="px-4 py-3 text-center text-gray-500 text-xs">{{ $member->pivot->created_at?->format('d.m.Y') ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <form method="POST" action="{{ route('marketing.segments.remove_member', [$segment, $member]) }}" class="inline" onsubmit="return confirm('Bu üyeyi segmentten çıkarmak istediğinize emin misiniz?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600 text-xs">
                                            <i class="fas fa-user-minus"></i> Çıkar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">
                                <i class="fas fa-user-group text-3xl mb-2"></i><p>Bu segmentte henüz üye yok</p>
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Üye Ekle --}}
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="text-md font-semibold text-gray-800 mb-4"><i class="fas fa-user-plus text-purple-500 mr-2"></i>Üye Ekle</h3>
            <form method="POST" action="{{ route('marketing.segments.add_members', $segment) }}">
                @csrf
                <div class="mb-4" x-data="{ search: '' }">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Müşteri Seç</label>
                    <input type="text" x-model="search" placeholder="Müşteri ara..." class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 mb-2">
                    <div class="max-h-64 overflow-y-auto border rounded-lg">
                        @forelse($availableCustomers as $customer)
                            <label class="flex items-center gap-2 px-3 py-2 hover:bg-gray-50 cursor-pointer border-b last:border-b-0"
                                   x-show="!search || '{{ strtolower($customer->name) }}'.includes(search.toLowerCase()) || '{{ $customer->phone }}'.includes(search) || '{{ $customer->email }}'.includes(search)">
                                <input type="checkbox" name="customer_ids[]" value="{{ $customer->id }}" class="rounded text-purple-600 focus:ring-purple-500">
                                <div>
                                    <span class="text-sm text-gray-800">{{ $customer->name }}</span>
                                    @if($customer->phone)
                                        <span class="text-xs text-gray-400 ml-1">{{ $customer->phone }}</span>
                                    @endif
                                </div>
                            </label>
                        @empty
                            <div class="px-3 py-4 text-center text-sm text-gray-400">
                                Eklenecek müşteri bulunamadı
                            </div>
                        @endforelse
                    </div>
                </div>
                @if($availableCustomers->count() > 0)
                    <button type="submit" class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg text-sm hover:bg-purple-700 font-medium">
                        <i class="fas fa-user-plus mr-1"></i> Seçilenleri Ekle
                    </button>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection
