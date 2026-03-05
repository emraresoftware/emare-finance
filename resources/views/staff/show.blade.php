@extends('layouts.app')
@section('title', $staff->name . ' - Personel Detay')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-800">{{ $staff->name }}</h2>
            <a href="{{ route('staff.index') }}" class="text-indigo-600 hover:underline text-sm">
                <i class="fas fa-arrow-left mr-1"></i> Geri Dön
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div><span class="text-sm text-gray-500">E-Posta:</span><p class="font-medium">{{ $staff->email ?? '-' }}</p></div>
            <div><span class="text-sm text-gray-500">Telefon:</span><p class="font-medium">{{ $staff->phone ?? '-' }}</p></div>
            <div><span class="text-sm text-gray-500">Rol:</span><p class="font-medium">{{ $staff->role ?? 'Personel' }}</p></div>
            <div><span class="text-sm text-gray-500">Durum:</span>
                <p><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $staff->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $staff->is_active ? 'Aktif' : 'Pasif' }}
                </span></p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border p-6 text-center">
            <div class="text-3xl font-bold text-indigo-600">{{ $staff->motions_count ?? $staff->motions->count() }}</div>
            <div class="text-sm text-gray-500 mt-1">Toplam Hareket</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-6 text-center">
            <div class="text-3xl font-bold text-green-600">{{ $staff->tasks_count ?? $staff->tasks->count() }}</div>
            <div class="text-sm text-gray-500 mt-1">Toplam Görev</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-6 text-center">
            <div class="text-3xl font-bold text-orange-600">{{ $staff->created_at?->format('d.m.Y') }}</div>
            <div class="text-sm text-gray-500 mt-1">Kayıt Tarihi</div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="px-6 py-4 border-b"><h3 class="font-semibold text-gray-800">Son Hareketler</h3></div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlem</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Açıklama</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentMotions as $m)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ $m->action }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm">{{ $m->description ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $m->action_date?->format('d.m.Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-gray-400">Hareket bulunamadı.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
