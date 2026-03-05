@extends('layouts.app')
@section('title', 'Cari Hesaplar')

@section('content')
{{-- İstatistikler --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Toplam Cari</p>
        <p class="text-xl font-bold">{{ number_format($stats['total']) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Firma</p>
        <p class="text-xl font-bold text-blue-600">{{ $stats['company'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Bireysel</p>
        <p class="text-xl font-bold text-purple-600">{{ $stats['individual'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Borçlu Cari</p>
        <p class="text-xl font-bold text-red-600">{{ $stats['with_debt'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Toplam Borç</p>
        <p class="text-xl font-bold text-red-600">₺{{ number_format($stats['total_debt'], 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Toplam Alacak</p>
        <p class="text-xl font-bold text-green-600">₺{{ number_format($stats['total_credit'], 2, ',', '.') }}</p>
    </div>
</div>

{{-- Filtreler --}}
<div class="bg-white rounded-xl shadow-sm p-4 mb-6 border">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs text-gray-500 mb-1">Ara</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Ad, telefon veya email..."
                   class="w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Tür</label>
            <select name="type" class="border rounded-lg px-3 py-2 text-sm">
                <option value="">Tümü</option>
                <option value="company" {{ request('type') == 'company' ? 'selected' : '' }}>Firma</option>
                <option value="individual" {{ request('type') == 'individual' ? 'selected' : '' }}>Bireysel</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Sırala</label>
            <select name="sort" class="border rounded-lg px-3 py-2 text-sm">
                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Ad</option>
                <option value="balance" {{ request('sort') == 'balance' ? 'selected' : '' }}>Bakiye</option>
                <option value="sales_count" {{ request('sort') == 'sales_count' ? 'selected' : '' }}>Satış Sayısı</option>
                <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Kayıt Tarihi</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Yön</label>
            <select name="dir" class="border rounded-lg px-3 py-2 text-sm">
                <option value="asc" {{ request('dir') == 'asc' ? 'selected' : '' }}>↑ Artan</option>
                <option value="desc" {{ request('dir') == 'desc' ? 'selected' : '' }}>↓ Azalan</option>
            </select>
        </div>
        <label class="flex items-center text-sm gap-1">
            <input type="checkbox" name="has_debt" value="1" {{ request('has_debt') ? 'checked' : '' }}> Borçlu
        </label>
        <label class="flex items-center text-sm gap-1">
            <input type="checkbox" name="has_credit" value="1" {{ request('has_credit') ? 'checked' : '' }}> Alacaklı
        </label>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
            <i class="fas fa-search mr-1"></i> Filtrele
        </button>
        <a href="{{ route('customers.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200">Temizle</a>
        <a href="{{ route('customers.export') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700">
            <i class="fas fa-file-csv mr-1"></i> CSV
        </a>
        @permission('customers.create')
        <a href="{{ route('customers.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 font-medium">
            <i class="fas fa-plus mr-1"></i> Yeni Müşteri
        </a>
        @endpermission
    </form>
</div>

{{-- Tablo --}}
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ad</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tür</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telefon</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Satış</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Toplam Alış</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Bakiye</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($customers as $customer)
                <tr class="hover:bg-gray-50 {{ $customer->balance < 0 ? 'bg-red-50' : '' }}">
                    <td class="px-4 py-3">
                        <a href="{{ route('customers.show', $customer) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">{{ $customer->name }}</a>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $customer->type === 'company' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                            {{ $customer->type === 'company' ? 'Firma' : 'Bireysel' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $customer->phone ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $customer->email ?? '-' }}</td>
                    <td class="px-4 py-3 text-center">{{ $customer->sales_count }}</td>
                    <td class="px-4 py-3 text-right text-gray-600">₺{{ number_format($customer->sales_sum_grand_total ?? 0, 2, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right font-semibold {{ $customer->balance < 0 ? 'text-red-600' : 'text-green-600' }}">
                        ₺{{ number_format(abs($customer->balance), 2, ',', '.') }}
                        <span class="text-xs font-normal">{{ $customer->balance >= 0 ? 'Alacak' : 'Borç' }}</span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">
                    <i class="fas fa-users text-3xl mb-2"></i><p>Cari bulunamadı</p>
                </td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
    <div class="px-4 py-3 border-t flex items-center justify-between">
        <span class="text-sm text-gray-500">Toplam {{ $customers->total() }} kayıt, Sayfa {{ $customers->currentPage() }}/{{ $customers->lastPage() }}</span>
        <div>{{ $customers->links() }}</div>
    </div>
</div>
@endsection
