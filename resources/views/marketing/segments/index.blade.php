@extends('layouts.app')
@section('title', 'Müşteri Segmentleri')

@section('content')
{{-- Başlık --}}
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('marketing.index') }}" class="text-gray-400 hover:text-gray-600">
            <i class="fas fa-arrow-left text-lg"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Müşteri Segmentleri</h1>
            <p class="text-sm text-gray-500 mt-1">Müşterilerinizi segmentlere ayırarak hedefli kampanyalar oluşturun</p>
        </div>
    </div>
</div>

{{-- Yeni Segment Oluştur --}}
<div class="bg-white rounded-xl shadow-sm border p-6 mb-6" x-data="{ open: false }">
    <div class="flex items-center justify-between">
        <h3 class="text-md font-semibold text-gray-800"><i class="fas fa-plus-circle text-purple-500 mr-2"></i>Yeni Segment</h3>
        <button @click="open = !open" class="bg-purple-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-purple-700 font-medium">
            <i class="fas fa-plus mr-1"></i> Segment Oluştur
        </button>
    </div>
    <form method="POST" action="{{ route('marketing.segments.store') }}" x-show="open" x-cloak x-transition class="mt-4">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Segment Adı <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Ör: VIP Müşteriler"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
                @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
                <input type="text" name="description" value="{{ old('description') }}" placeholder="Kısa açıklama..."
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Renk</label>
                <select name="color" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="blue">Mavi</option>
                    <option value="green">Yeşil</option>
                    <option value="purple">Mor</option>
                    <option value="red">Kırmızı</option>
                    <option value="yellow">Sarı</option>
                    <option value="orange">Turuncu</option>
                    <option value="pink">Pembe</option>
                    <option value="indigo">İndigo</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">İkon</label>
                <select name="icon" class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="users">Kullanıcılar</option>
                    <option value="star">Yıldız</option>
                    <option value="crown">Taç</option>
                    <option value="heart">Kalp</option>
                    <option value="fire">Ateş</option>
                    <option value="gem">Mücevher</option>
                    <option value="tag">Etiket</option>
                    <option value="building">Bina</option>
                </select>
            </div>
        </div>
        <div class="mt-4 flex items-center gap-3">
            <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg text-sm hover:bg-purple-700 font-medium">
                <i class="fas fa-save mr-1"></i> Kaydet
            </button>
            <button type="button" @click="open = false" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200">İptal</button>
        </div>
    </form>
</div>

{{-- Segment Kartları --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($segments as $segment)
        <a href="{{ route('marketing.segments.show', $segment) }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md hover:border-{{ $segment->color ?? 'indigo' }}-300 transition group">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-{{ $segment->color ?? 'indigo' }}-100 rounded-xl flex items-center justify-center group-hover:bg-{{ $segment->color ?? 'indigo' }}-200 transition">
                        <i class="fas fa-{{ $segment->icon ?? 'users' }} text-{{ $segment->color ?? 'indigo' }}-600"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800 group-hover:text-{{ $segment->color ?? 'indigo' }}-600 transition">{{ $segment->name }}</h3>
                        @if($segment->description)
                            <p class="text-xs text-gray-500">{{ Str::limit($segment->description, 40) }}</p>
                        @endif
                    </div>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $segment->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $segment->is_active ? 'Aktif' : 'Pasif' }}
                </span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center gap-1 text-gray-500">
                    <i class="fas fa-user-group text-xs"></i>
                    <span class="font-semibold text-gray-800">{{ $segment->members_count ?? 0 }}</span>
                    <span>üye</span>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                    {{ $segment->type === 'manual' ? 'Manuel' : 'Otomatik' }}
                </span>
            </div>
        </a>
    @empty
        <div class="col-span-3 bg-white rounded-xl shadow-sm border p-12 text-center text-gray-400">
            <i class="fas fa-users text-4xl mb-3"></i>
            <p class="text-lg">Henüz segment oluşturulmamış</p>
            <p class="text-sm mt-1">Yukarıdaki formdan ilk segmentinizi oluşturun</p>
        </div>
    @endforelse
</div>

@if($segments->hasPages())
    <div class="mt-6 flex items-center justify-between">
        <span class="text-sm text-gray-500">Toplam {{ $segments->total() }} segment</span>
        <div>{{ $segments->links() }}</div>
    </div>
@endif
@endsection
