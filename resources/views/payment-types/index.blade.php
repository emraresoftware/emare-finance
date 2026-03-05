@extends('layouts.app')
@section('title', 'Ödeme Tipleri')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="font-semibold text-gray-800">Ödeme Tipleri</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ad</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kod</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Oluşturulma</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($paymentTypes as $i => $pt)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm">{{ $i + 1 }}</td>
                        <td class="px-4 py-3 text-sm font-medium">{{ $pt->name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $pt->code ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pt->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $pt->is_active ? 'Aktif' : 'Pasif' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $pt->created_at?->format('d.m.Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-400">
                            <i class="fas fa-credit-card text-3xl mb-2"></i><p>Ödeme tipi bulunamadı.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
