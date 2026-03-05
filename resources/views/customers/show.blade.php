@extends('layouts.app')
@section('title', 'Müşteri Detay - ' . $customer->name)

@section('content')
<div class="space-y-6">
    {{-- Üst Başlık --}}
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('customers.index') }}" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Müşteri Detay</h2>
                    <p class="text-sm text-gray-500">{{ $customer->name }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('customers.edit', $customer) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 font-medium">
                    <i class="fas fa-edit mr-1"></i> Müşteriyi Güncelle
                </a>
                <button @click="$refs.paymentModal.showModal()" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 font-medium">
                    <i class="fas fa-hand-holding-dollar mr-1"></i> Tahsilat
                </button>
                <button onclick="window.print()" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-200">
                    <i class="fas fa-print mr-1"></i> Yazdır
                </button>
                @permission('customers.delete')
                <form action="{{ route('customers.destroy', $customer) }}" method="POST"
                    onsubmit="return confirm('{{ $customer->name }} müşterisini silmek istediğinize emin misiniz?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700">
                        <i class="fas fa-trash mr-1"></i> Sil
                    </button>
                </form>
                @endpermission
            </div>
        </div>
    </div>

    {{-- Müşteri Bilgileri --}}
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
            <div class="flex justify-between"><span class="text-gray-500">Müşteri Adı</span><span class="font-medium">{{ $customer->name }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Tür</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $customer->type === 'company' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                    {{ $customer->type === 'company' ? 'Firma' : 'Bireysel' }}
                </span>
            </div>
            <div class="flex justify-between"><span class="text-gray-500">Telefon</span><span>{{ $customer->phone ?? '-' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-500">Email</span><span>{{ $customer->email ?? '-' }}</span></div>
            @if($customer->tax_number)<div class="flex justify-between"><span class="text-gray-500">Vergi No</span><span>{{ $customer->tax_number }}</span></div>@endif
            @if($customer->tax_office)<div class="flex justify-between"><span class="text-gray-500">Vergi Dairesi</span><span>{{ $customer->tax_office }}</span></div>@endif
            @if($customer->address)<div class="flex justify-between md:col-span-2"><span class="text-gray-500">Adres</span><span class="text-right">{{ $customer->address }}{{ $customer->district ? ', '.$customer->district : '' }}{{ $customer->city ? ' / '.$customer->city : '' }}</span></div>@endif
        </div>
    </div>

    {{-- İstatistik Kartları --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Toplam Satış</p>
                    <p class="text-2xl font-bold text-gray-800">₺{{ number_format($customerStats['total_sales'], 2, ',', '.') }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Toplam Borç</p>
                    <p class="text-2xl font-bold text-red-600">₺{{ number_format($customerStats['total_debt'], 2, ',', '.') }}</p>
                    <p class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }} arasında</p>
                </div>
                <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-invoice-dollar text-red-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Ödeme</p>
                    <p class="text-2xl font-bold text-green-600">₺{{ number_format($customerStats['total_payments'], 2, ',', '.') }}</p>
                    <p class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }} arasında</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-hand-holding-dollar text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Kalan Borç</p>
                    <p class="text-2xl font-bold {{ $customerStats['remaining_debt'] < 0 ? 'text-red-600' : 'text-green-600' }}">
                        ₺{{ number_format(abs($customerStats['remaining_debt']), 2, ',', '.') }}
                    </p>
                </div>
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-scale-balanced text-orange-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Alışverişler --}}
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="font-semibold text-gray-800 mb-3"><i class="fas fa-shopping-bag mr-2"></i>Alışverişler</h3>
            <form method="GET" class="flex items-end gap-3 flex-wrap">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Başlangıç Tarihi</label>
                    <input type="date" name="start_date" value="{{ $startDate }}"
                        class="rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Bitiş Tarihi</label>
                    <input type="date" name="end_date" value="{{ $endDate }}"
                        class="rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                    <i class="fas fa-list mr-1"></i> Listele
                </button>
                <a href="{{ route('customers.show', ['customer' => $customer, 'all' => 1]) }}"
                    class="bg-indigo-50 text-indigo-600 px-4 py-2 rounded-lg text-sm hover:bg-indigo-100 border border-indigo-200">
                    <i class="fas fa-list-check mr-1"></i> Tüm İşlemler
                </a>
                <a href="{{ route('customers.export_sales', ['customer' => $customer, 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                    class="bg-green-50 text-green-600 px-4 py-2 rounded-lg text-sm hover:bg-green-100 border border-green-200">
                    <i class="fas fa-file-excel mr-1"></i> Excel İndir
                </a>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 w-10">Sıra</th>
                        <th class="px-4 py-3">Satış Kodu</th>
                        <th class="px-4 py-3 text-center">Toplam Ürün</th>
                        <th class="px-4 py-3 text-right">İskonto</th>
                        <th class="px-4 py-3 text-right">Toplam Tutar</th>
                        <th class="px-4 py-3 text-right">Kalan Borç</th>
                        <th class="px-4 py-3 text-center">Ödeme Tipi</th>
                        <th class="px-4 py-3">Not</th>
                        <th class="px-4 py-3">Personel</th>
                        <th class="px-4 py-3">Tarih</th>
                        <th class="px-4 py-3">Saat</th>
                        <th class="px-4 py-3 text-center">Detay</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($sales as $sale)
                    <tr class="hover:bg-gray-50 {{ $sale->status === 'cancelled' ? 'bg-red-50' : ($sale->status === 'refunded' ? 'bg-yellow-50' : '') }}">
                        <td class="px-4 py-2.5 text-gray-400 text-xs">{{ $loop->iteration + ($sales->currentPage() - 1) * $sales->perPage() }}</td>
                        <td class="px-4 py-2.5 font-mono text-xs">{{ $sale->receipt_no ?? '#'.$sale->id }}</td>
                        <td class="px-4 py-2.5 text-center">{{ $sale->total_items }}</td>
                        <td class="px-4 py-2.5 text-right {{ $sale->discount_total > 0 ? 'text-orange-600' : 'text-gray-400' }}">
                            ₺{{ number_format($sale->discount_total, 2, ',', '.') }}
                        </td>
                        <td class="px-4 py-2.5 text-right font-semibold">₺{{ number_format($sale->grand_total, 2, ',', '.') }}</td>
                        <td class="px-4 py-2.5 text-right {{ $sale->payment_method === 'credit' ? 'text-red-600 font-medium' : 'text-gray-400' }}">
                            {{ $sale->payment_method === 'credit' ? '₺'.number_format($sale->grand_total, 2, ',', '.') : '₺0,00' }}
                        </td>
                        <td class="px-4 py-2.5 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $sale->payment_method === 'cash' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $sale->payment_method === 'card' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $sale->payment_method === 'credit' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $sale->payment_method === 'mixed' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                {{ $sale->payment_method_label }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5 text-gray-500 text-xs max-w-[120px] truncate">{{ $sale->notes ?? '-' }}</td>
                        <td class="px-4 py-2.5 text-gray-600 text-xs">{{ $sale->staff_name ?? '-' }}</td>
                        <td class="px-4 py-2.5 text-gray-600 text-xs whitespace-nowrap">{{ $sale->sold_at?->format('d.m.Y') ?? '-' }}</td>
                        <td class="px-4 py-2.5 text-gray-600 text-xs">{{ $sale->sold_at?->format('H:i') ?? '-' }}</td>
                        <td class="px-4 py-2.5 text-center">
                            <a href="{{ route('sales.show', $sale) }}" class="text-indigo-600 hover:text-indigo-800">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="px-4 py-6 text-center text-gray-400">Kayıt bulunamadı.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($sales->hasPages())
        <div class="px-6 py-3 border-t bg-gray-50">
            {{ $sales->links() }}
        </div>
        @endif
        <div class="px-6 py-2 border-t text-xs text-gray-400">
            {{ $sales->total() }} kayıttan {{ $sales->firstItem() ?? 0 }} ile {{ $sales->lastItem() ?? 0 }} arasındakiler
        </div>
    </div>

    {{-- Alacaklar --}}
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="font-semibold text-gray-800"><i class="fas fa-exchange-alt mr-2"></i>Alacaklar</h3>
            <p class="text-xs text-gray-400 mt-1">Bu kısım müşterinin satın aldığını ve sizin ona yaptığınız ödemeleri göstermektedir.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-3 w-10">Sıra</th>
                        <th class="px-4 py-3">Türü</th>
                        <th class="px-4 py-3">Not</th>
                        <th class="px-4 py-3 text-right">Tutar</th>
                        <th class="px-4 py-3">Tarih</th>
                        <th class="px-4 py-3">Saat</th>
                        <th class="px-4 py-3 text-center">İşlem</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($transactions as $tx)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2.5 text-gray-400 text-xs">{{ $loop->iteration + ($transactions->currentPage() - 1) * $transactions->perPage() }}</td>
                        <td class="px-4 py-2.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $tx->type === 'sale' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $tx->type === 'payment' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $tx->type === 'refund' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $tx->type === 'adjustment' ? 'bg-gray-100 text-gray-800' : '' }}">
                                {{ $tx->type_label }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5 text-gray-500 text-xs max-w-[200px] truncate">{{ $tx->description ?? '-' }}</td>
                        <td class="px-4 py-2.5 text-right font-medium {{ $tx->amount >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            ₺{{ number_format(abs($tx->amount), 2, ',', '.') }}
                        </td>
                        <td class="px-4 py-2.5 text-gray-600 text-xs whitespace-nowrap">{{ $tx->transaction_date?->format('d.m.Y') ?? '-' }}</td>
                        <td class="px-4 py-2.5 text-gray-600 text-xs">{{ $tx->transaction_date?->format('H:i') ?? '-' }}</td>
                        <td class="px-4 py-2.5 text-center">
                            @if($tx->reference)
                                <span class="text-xs text-gray-400 font-mono">{{ $tx->reference }}</span>
                            @else
                                <span class="text-gray-300">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-gray-400">Kayıt bulunamadı.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transactions->hasPages())
        <div class="px-6 py-3 border-t bg-gray-50">
            {{ $transactions->links() }}
        </div>
        @endif
        <div class="px-6 py-2 border-t text-xs text-gray-400">
            {{ $transactions->total() }} kayıttan {{ $transactions->firstItem() ?? 0 }} ile {{ $transactions->lastItem() ?? 0 }} arasındakiler
        </div>
    </div>
</div>

<style>
@media print {
    .space-y-6 > :first-child { page-break-after: avoid; }
    button, a[href*="export"], a[href*="duzenle"], form[method="POST"] { display: none !important; }
    dialog { display: none !important; }
}
</style>

{{-- Tahsilat Modal --}}
<dialog x-ref="paymentModal" class="rounded-xl shadow-2xl border-0 p-0 w-full max-w-md backdrop:bg-black/50">
    <div class="p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800"><i class="fas fa-hand-holding-dollar mr-2 text-green-500"></i>Tahsilat Kaydet</h3>
            <button onclick="this.closest('dialog').close()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
        </div>
        <form action="{{ route('customers.add_payment', $customer) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tutar <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-400 text-sm">₺</span>
                    <input type="number" name="amount" step="0.01" min="0.01" required
                        class="w-full rounded-lg border-gray-300 shadow-sm text-sm pl-8 pr-3 py-2 border"
                        placeholder="0,00">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tarih</label>
                <input type="date" name="payment_date" value="{{ date('Y-m-d') }}"
                    class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
                <textarea name="description" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border"
                    placeholder="Ödeme açıklaması..."></textarea>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Mevcut Bakiye:</span>
                    <span class="font-semibold {{ $customer->balance < 0 ? 'text-red-600' : 'text-green-600' }}">
                        ₺{{ number_format(abs($customer->balance), 2, ',', '.') }}
                        <span class="text-xs font-normal">{{ $customer->balance >= 0 ? 'Alacak' : 'Borç' }}</span>
                    </span>
                </div>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="this.closest('dialog').close()" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-200">İptal</button>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 font-medium">
                    <i class="fas fa-check mr-1"></i> Tahsilatı Kaydet
                </button>
            </div>
        </form>
    </div>
</dialog>
@endsection
