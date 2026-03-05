@extends('layouts.app')
@section('title', 'Başvurularım')

@section('content')
<div class="space-y-6">

    {{-- Başlık --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-3">
                <div class="w-9 h-9 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-list-check text-indigo-600"></i>
                </div>
                Entegrasyon Başvurularım
            </h2>
            <p class="text-sm text-gray-500 mt-1">Yaptığınız entegrasyon başvurularının durumunu takip edin</p>
        </div>
        <a href="{{ route('integrations.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl transition">
            <i class="fas fa-plug"></i> Entegrasyon Merkezi
        </a>
    </div>

    {{-- Başvuru Listesi --}}
    @if($requests->count() > 0)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-3.5 text-left font-medium text-gray-500">Entegrasyon</th>
                    <th class="px-6 py-3.5 text-left font-medium text-gray-500">Kategori</th>
                    <th class="px-6 py-3.5 text-left font-medium text-gray-500">Mesaj</th>
                    <th class="px-6 py-3.5 text-center font-medium text-gray-500">Durum</th>
                    <th class="px-6 py-3.5 text-left font-medium text-gray-500">Admin Notu</th>
                    <th class="px-6 py-3.5 text-left font-medium text-gray-500">Tarih</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($requests as $req)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <span class="font-medium text-gray-800">{{ $req->integration_name }}</span>
                    </td>
                    <td class="px-6 py-4 text-gray-500">{{ $req->integration_type }}</td>
                    <td class="px-6 py-4">
                        <span class="text-gray-600 truncate block max-w-[200px]" title="{{ $req->message }}">
                            {{ $req->message ?? '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($req->status === 'pending')
                            <span class="inline-flex items-center gap-1 text-xs font-medium text-amber-700 bg-amber-50 px-2.5 py-1 rounded-full border border-amber-200">
                                <i class="fas fa-hourglass-half text-[10px] animate-pulse"></i> Beklemede
                            </span>
                        @elseif($req->status === 'approved')
                            <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                                <i class="fas fa-check-circle text-[10px]"></i> Onaylandı
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-xs font-medium text-red-700 bg-red-50 px-2.5 py-1 rounded-full border border-red-200">
                                <i class="fas fa-times-circle text-[10px]"></i> Reddedildi
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($req->admin_note)
                            <span class="text-gray-600 truncate block max-w-[200px]" title="{{ $req->admin_note }}">
                                {{ $req->admin_note }}
                            </span>
                        @else
                            <span class="text-gray-300">—</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-500 text-xs whitespace-nowrap">
                        {{ $req->created_at->format('d.m.Y H:i') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $requests->links() }}</div>

    @else
    <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
        <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-paper-plane text-2xl text-indigo-400"></i>
        </div>
        <h3 class="font-medium text-gray-700">Henüz başvurunuz yok</h3>
        <p class="text-sm text-gray-500 mt-1 mb-4">Entegrasyon merkezinden ihtiyacınız olan servislere başvurabilirsiniz.</p>
        <a href="{{ route('integrations.index') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl transition">
            <i class="fas fa-plug"></i> Entegrasyon Merkezine Git
        </a>
    </div>
    @endif

</div>
@endsection
