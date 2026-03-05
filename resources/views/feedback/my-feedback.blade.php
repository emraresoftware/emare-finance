@extends('layouts.app')

@section('title', 'Geri Bildirimlerim')

@section('content')
<div class="space-y-6">

    {{-- Başlık --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Geri Bildirimlerim</h2>
            <p class="text-sm text-gray-500 mt-1">Gönderdiğiniz sorun bildirimleri ve öneriler</p>
        </div>
    </div>

    {{-- Liste --}}
    @if($messages->isEmpty())
        <div class="bg-white rounded-xl border p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-comment-slash text-2xl text-gray-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-700 mb-1">Henüz geri bildirim yok</h3>
            <p class="text-sm text-gray-500">Herhangi bir sayfada sağ alttaki düğmeyi kullanarak geri bildirim gönderebilirsiniz.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($messages as $m)
                @php
                    $statusColors = [
                        'open'        => 'bg-yellow-100 text-yellow-800',
                        'in_progress' => 'bg-blue-100 text-blue-800',
                        'resolved'    => 'bg-green-100 text-green-800',
                        'closed'      => 'bg-gray-100 text-gray-600',
                    ];
                    $statusLabels = [
                        'open'        => 'Açık',
                        'in_progress' => 'İnceleniyor',
                        'resolved'    => 'Çözüldü',
                        'closed'      => 'Kapatıldı',
                    ];
                    $catColors = [
                        'bug'        => 'text-red-600 bg-red-50',
                        'suggestion' => 'text-blue-600 bg-blue-50',
                        'question'   => 'text-purple-600 bg-purple-50',
                        'other'      => 'text-gray-600 bg-gray-50',
                    ];
                    $catIcons = [
                        'bug'        => 'fa-bug',
                        'suggestion' => 'fa-lightbulb',
                        'question'   => 'fa-question-circle',
                        'other'      => 'fa-comment',
                    ];
                    $catLabels = [
                        'bug'        => 'Hata / Sorun',
                        'suggestion' => 'Öneri',
                        'question'   => 'Soru',
                        'other'      => 'Diğer',
                    ];
                @endphp
                <div class="bg-white rounded-xl border p-5 space-y-3">
                    {{-- Üst satır: kategori + durum + tarih --}}
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $catColors[$m->category] ?? 'text-gray-600 bg-gray-50' }}">
                                <i class="fas {{ $catIcons[$m->category] ?? 'fa-comment' }}"></i>
                                {{ $catLabels[$m->category] ?? $m->category }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $statusColors[$m->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $statusLabels[$m->status] ?? $m->status }}
                            </span>
                        </div>
                        <span class="text-xs text-gray-400">{{ $m->created_at->format('d.m.Y H:i') }}</span>
                    </div>

                    {{-- Mesaj --}}
                    <p class="text-sm text-gray-700 leading-relaxed">{{ $m->message }}</p>

                    {{-- Admin yanıtı --}}
                    @if($m->admin_reply)
                        <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 mt-2">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-7 h-7 rounded-full bg-blue-600 flex items-center justify-center">
                                    <i class="fas fa-headset text-white text-xs"></i>
                                </div>
                                <span class="text-xs font-semibold text-blue-700">Destek Ekibi</span>
                                @if($m->replied_at)
                                    <span class="text-xs text-gray-400 ml-auto">{{ $m->replied_at->format('d.m.Y H:i') }}</span>
                                @endif
                            </div>
                            <p class="text-sm text-blue-900 leading-relaxed">{{ $m->admin_reply }}</p>
                        </div>
                    @else
                        <p class="text-xs text-gray-400 italic">
                            <i class="fas fa-clock mr-1"></i> Yanıt bekleniyor...
                        </p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
