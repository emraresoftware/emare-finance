@extends('layouts.app')
@section('title', 'Personel Hareketleri')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <form method="GET" class="flex items-end gap-4 flex-wrap">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Personel</label>
                <select name="staff_id" class="rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    <option value="">Tüm Personeller</option>
                    @foreach($staffList as $s)
                    <option value="{{ $s->id }}" {{ request('staff_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">İşlem Türü</label>
                <select name="action" class="rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    <option value="">Tümü</option>
                    <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Giriş</option>
                    <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>Çıkış</option>
                    <option value="delete_item" {{ request('action') == 'delete_item' ? 'selected' : '' }}>Ürün Silme</option>
                    <option value="delete_receipt" {{ request('action') == 'delete_receipt' ? 'selected' : '' }}>Fiş Silme</option>
                </select>
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">
                <i class="fas fa-search mr-1"></i> Filtrele
            </button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Personel</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlem</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Açıklama</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Uygulama</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($motions as $i => $m)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm">{{ $motions->firstItem() + $i }}</td>
                        <td class="px-4 py-3 text-sm font-medium">{{ $m->staff?->name ?? $m->staff_name ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $m->action }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">{{ $m->description ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $m->application ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $m->action_date?->format('d.m.Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                            <i class="fas fa-history text-3xl mb-2"></i><p>Hareket bulunamadı.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">{{ $motions->links() }}</div>
    </div>
</div>
@endsection
