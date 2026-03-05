@extends('layouts.app')
@section('title', $firm->name . ' - Firma Detay')

@section('content')
<div class="mb-4 flex items-center justify-between">
    <a href="{{ route('firms.index') }}" class="text-sm text-indigo-600"><i class="fas fa-arrow-left mr-1"></i> Firmalara Dön</a>
    <button onclick="window.print()" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200">
        <i class="fas fa-print mr-1"></i> Yazdır
    </button>
</div>

{{-- İstatistik Kartları --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Toplam Fatura</p>
        <p class="text-xl font-bold">{{ number_format($firmStats['total_invoices']) }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Toplam Alış</p>
        <p class="text-xl font-bold text-green-600">₺{{ number_format($firmStats['total_amount'], 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Ort. Fatura</p>
        <p class="text-xl font-bold text-blue-600">₺{{ number_format($firmStats['avg_amount'], 2, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4 border">
        <p class="text-xs text-gray-500">Son Fatura</p>
        <p class="text-xl font-bold text-gray-600">{{ $firmStats['last_invoice'] ? \Carbon\Carbon::parse($firmStats['last_invoice'])->format('d.m.Y') : '-' }}</p>
    </div>
</div>

{{-- Firma Bilgileri --}}
<div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
    <h2 class="text-xl font-bold text-gray-800 mb-4"><i class="fas fa-building mr-1 text-indigo-500"></i> {{ $firm->name }}</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
        <div class="flex justify-between md:block"><span class="text-gray-500">Telefon:</span><p class="font-medium">{{ $firm->phone ?? '-' }}</p></div>
        <div class="flex justify-between md:block"><span class="text-gray-500">E-Posta:</span><p class="font-medium">{{ $firm->email ?? '-' }}</p></div>
        <div class="flex justify-between md:block"><span class="text-gray-500">Vergi Dairesi:</span><p class="font-medium">{{ $firm->tax_office ?? '-' }}</p></div>
        <div class="flex justify-between md:block"><span class="text-gray-500">Vergi No:</span><p class="font-medium font-mono">{{ $firm->tax_number ?? '-' }}</p></div>
        <div class="flex justify-between md:block"><span class="text-gray-500">Adres:</span><p class="font-medium">{{ $firm->address ?? '-' }}</p></div>
        <div class="flex justify-between md:block"><span class="text-gray-500">Bakiye:</span>
            <p class="font-bold text-lg {{ ($firm->balance ?? 0) < 0 ? 'text-red-600' : 'text-green-600' }}">
                ₺{{ number_format(abs($firm->balance ?? 0), 2, ',', '.') }}
            </p>
        </div>
    </div>
</div>

{{-- Alış Faturaları --}}
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <div class="px-6 py-4 border-b">
        <h3 class="font-semibold text-gray-800"><i class="fas fa-file-invoice mr-1 text-indigo-500"></i> Alış Faturaları ({{ $invoices->total() }})</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fatura No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Şube</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ödeme</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Toplam</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($invoices as $inv)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <a href="{{ route('invoices.show', $inv) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">{{ $inv->invoice_no }}</a>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $inv->branch?->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $inv->payment_type ?? '-' }}</td>
                    <td class="px-4 py-3 text-right font-bold text-green-600">₺{{ number_format($inv->total_amount, 2, ',', '.') }}</td>
                    <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $inv->invoice_date?->format('d.m.Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-400">Fatura bulunamadı.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-4 py-3 border-t flex items-center justify-between">
        <span class="text-sm text-gray-500">Toplam {{ $invoices->total() }} kayıt, Sayfa {{ $invoices->currentPage() }}/{{ $invoices->lastPage() }}</span>
        <div>{{ $invoices->links() }}</div>
    </div>
</div>
@endsection
