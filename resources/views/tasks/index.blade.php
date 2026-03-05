@extends('layouts.app')
@section('title', 'Görevler')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border p-6 text-center">
            <div class="text-3xl font-bold text-indigo-600">{{ $stats['total'] ?? 0 }}</div>
            <div class="text-sm text-gray-500 mt-1">Toplam Görev</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-6 text-center">
            <div class="text-3xl font-bold text-yellow-600">{{ $stats['pending'] ?? 0 }}</div>
            <div class="text-sm text-gray-500 mt-1">Bekleyen</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-6 text-center">
            <div class="text-3xl font-bold text-blue-600">{{ $stats['in_progress'] ?? 0 }}</div>
            <div class="text-sm text-gray-500 mt-1">Devam Eden</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-6 text-center">
            <div class="text-3xl font-bold text-green-600">{{ $stats['completed'] ?? 0 }}</div>
            <div class="text-sm text-gray-500 mt-1">Tamamlanan</div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border p-6">
        <form method="GET" class="flex items-end gap-4 flex-wrap">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Durum</label>
                <select name="status" class="rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    <option value="">Tümü</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Bekleyen</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Devam Eden</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Tamamlanan</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>İptal</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Öncelik</label>
                <select name="priority" class="rounded-lg border-gray-300 shadow-sm text-sm px-3 py-2 border">
                    <option value="">Tümü</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Düşük</option>
                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Orta</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>Yüksek</option>
                    <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Acil</option>
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
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Başlık</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Atanan</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Öncelik</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Son Tarih</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Oluşturulma</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($tasks as $i => $task)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm">{{ $tasks->firstItem() + $i }}</td>
                        <td class="px-4 py-3 text-sm font-medium">{{ $task->title }}</td>
                        <td class="px-4 py-3 text-sm">{{ $task->assignedStaff?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm">
                            @php
                                $pColors = ['low' => 'bg-gray-100 text-gray-800', 'medium' => 'bg-blue-100 text-blue-800', 'high' => 'bg-orange-100 text-orange-800', 'urgent' => 'bg-red-100 text-red-800'];
                                $pLabels = ['low' => 'Düşük', 'medium' => 'Orta', 'high' => 'Yüksek', 'urgent' => 'Acil'];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pColors[$task->priority] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $pLabels[$task->priority] ?? $task->priority }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @php
                                $sColors = ['pending' => 'bg-yellow-100 text-yellow-800', 'in_progress' => 'bg-blue-100 text-blue-800', 'completed' => 'bg-green-100 text-green-800', 'cancelled' => 'bg-red-100 text-red-800'];
                                $sLabels = ['pending' => 'Bekleyen', 'in_progress' => 'Devam Eden', 'completed' => 'Tamamlanan', 'cancelled' => 'İptal'];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sColors[$task->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $sLabels[$task->status] ?? $task->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $task->due_date?->format('d.m.Y') ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $task->created_at?->format('d.m.Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                            <i class="fas fa-tasks text-3xl mb-2"></i><p>Görev bulunamadı.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t">{{ $tasks->links() }}</div>
    </div>
</div>
@endsection
