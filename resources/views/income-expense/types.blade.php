@extends('layouts.app')
@section('title', 'Gelir/Gider Türleri')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- Gelir Türleri --}}
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="px-6 py-4 border-b bg-green-50">
            <h3 class="font-semibold text-green-800"><i class="fas fa-arrow-down mr-2"></i>Gelir Türleri</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tür Adı</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Kayıt</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Toplam Tutar</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($incomeTypes as $type)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ $type->name }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $type->incomes_count ?? 0 }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-green-600">₺{{ number_format($type->incomes_sum_amount ?? 0, 2, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="px-4 py-8 text-center text-gray-400">Gelir türü bulunamadı.</td></tr>
                    @endforelse
                </tbody>
                @if($incomeTypes->count())
                <tfoot class="bg-green-50">
                    <tr>
                        <td class="px-4 py-3 font-bold">Toplam</td>
                        <td class="px-4 py-3 text-center font-bold">{{ $incomeTypes->sum('incomes_count') }}</td>
                        <td class="px-4 py-3 text-right font-bold text-green-600">₺{{ number_format($incomeTypes->sum('incomes_sum_amount'), 2, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- Gider Türleri --}}
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="px-6 py-4 border-b bg-red-50">
            <h3 class="font-semibold text-red-800"><i class="fas fa-arrow-up mr-2"></i>Gider Türleri</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tür Adı</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Kayıt</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Toplam Tutar</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($expenseTypes as $type)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ $type->name }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                {{ $type->expenses_count ?? 0 }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-red-600">₺{{ number_format($type->expenses_sum_amount ?? 0, 2, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="px-4 py-8 text-center text-gray-400">Gider türü bulunamadı.</td></tr>
                    @endforelse
                </tbody>
                @if($expenseTypes->count())
                <tfoot class="bg-red-50">
                    <tr>
                        <td class="px-4 py-3 font-bold">Toplam</td>
                        <td class="px-4 py-3 text-center font-bold">{{ $expenseTypes->sum('expenses_count') }}</td>
                        <td class="px-4 py-3 text-right font-bold text-red-600">₺{{ number_format($expenseTypes->sum('expenses_sum_amount'), 2, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
