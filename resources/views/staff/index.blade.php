@extends('layouts.app')
@section('title', 'Personeller')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <form method="GET" class="flex items-end gap-4 flex-wrap">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Arama</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Personel adı, e-posta..." class="w-full rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">
                <i class="fas fa-search mr-1"></i> Ara
            </button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ad Soyad</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">E-Posta</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telefon</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rol</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlem</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($staff as $member)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-medium">{{ $member->name }}</td>
                        <td class="px-4 py-3 text-sm">{{ $member->email ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm">{{ $member->phone ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                {{ $member->role ?? 'Personel' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $member->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $member->is_active ? 'Aktif' : 'Pasif' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <a href="{{ route('staff.show', $member) }}" class="text-indigo-600 hover:underline"><i class="fas fa-eye"></i> Detay</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                            <i class="fas fa-users text-3xl mb-2"></i><p>Personel bulunamadı.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">{{ $staff->links() }}</div>
    </div>
</div>
@endsection
