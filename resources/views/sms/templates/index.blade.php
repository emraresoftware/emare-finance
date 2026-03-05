@extends('layouts.app')
@section('title', 'SMS Şablonları')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">SMS Şablonları</h1>
        <p class="text-sm text-gray-500 mt-1">Mesaj şablonlarını yönetin ve düzenleyin</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('sms.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
            <i class="fas fa-arrow-left mr-2"></i> SMS Paneli
        </a>
        <a href="{{ route('sms.templates.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
            <i class="fas fa-plus mr-2"></i> Yeni Şablon
        </a>
    </div>
</div>

@if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-700">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
        <i class="fas fa-times-circle mr-2"></i>{{ session('error') }}
    </div>
@endif

{{-- Kategori Filtreleri --}}
<div class="mb-6 flex flex-wrap gap-2">
    <a href="{{ route('sms.templates.index') }}"
       class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium transition
              {{ !request('category') ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
        Tümü
    </a>
    @foreach($categories as $catKey => $catLabel)
        <a href="{{ route('sms.templates.index', ['category' => $catKey]) }}"
           class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium transition
                  {{ request('category') == $catKey ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            {{ $catLabel }}
        </a>
    @endforeach
</div>

{{-- Tablo --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Şablon Adı</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kod</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İçerik</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Senaryo</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($templates as $template)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <p class="text-sm font-medium text-gray-900">{{ $template->name }}</p>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <code class="text-xs bg-gray-100 px-2 py-1 rounded text-gray-600">{{ $template->code }}</code>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($template->category)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $categories[$template->category] ?? $template->category }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-600 truncate max-w-xs" title="{{ $template->content }}">{{ Str::limit($template->content, 60) }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                {{ $template->scenarios_count ?? 0 }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($template->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-circle text-[6px] mr-1"></i> Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                    <i class="fas fa-circle text-[6px] mr-1"></i> Pasif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('sms.templates.edit', $template->id) }}"
                                   class="inline-flex items-center px-2.5 py-1.5 bg-white border border-gray-300 text-gray-600 text-xs rounded-lg hover:bg-gray-50 transition"
                                   title="Düzenle">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('sms.templates.destroy', $template->id) }}" method="POST"
                                      onsubmit="return confirm('Bu şablonu silmek istediğinize emin misiniz?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center px-2.5 py-1.5 bg-white border border-red-300 text-red-600 text-xs rounded-lg hover:bg-red-50 transition"
                                            title="Sil">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="text-gray-400">
                                <i class="fas fa-file-alt text-4xl mb-3"></i>
                                <p class="text-sm">Henüz SMS şablonu oluşturulmamış</p>
                                <a href="{{ route('sms.templates.create') }}" class="mt-3 inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800">
                                    <i class="fas fa-plus mr-1"></i> İlk şablonu oluştur
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($templates->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $templates->links() }}
        </div>
    @endif
</div>
@endsection
