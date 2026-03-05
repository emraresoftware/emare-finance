@extends('layouts.app')

@section('title', 'Geri Bildirimler')

@section('content')
<div class="space-y-6">

    {{-- Başlık --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Geri Bildirimler</h2>
            <p class="text-sm text-gray-500 mt-1">Test kullanıcılarından gelen sorun bildirimleri ve önerileri</p>
        </div>
        <div class="flex items-center space-x-2">
            <span class="text-xs bg-amber-100 text-amber-800 px-3 py-1.5 rounded-full font-medium">
                <i class="fas fa-comment-dots mr-1"></i> {{ $stats['total'] }} Toplam
            </span>
        </div>
    </div>

    {{-- İstatistikler --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white rounded-xl border p-4 text-center">
            <div class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Toplam</div>
        </div>
        <div class="bg-white rounded-xl border p-4 text-center border-l-4 border-l-yellow-400">
            <div class="text-2xl font-bold text-yellow-600">{{ $stats['open'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Açık</div>
        </div>
        <div class="bg-white rounded-xl border p-4 text-center border-l-4 border-l-blue-400">
            <div class="text-2xl font-bold text-blue-600">{{ $stats['in_progress'] }}</div>
            <div class="text-xs text-gray-500 mt-1">İnceleniyor</div>
        </div>
        <div class="bg-white rounded-xl border p-4 text-center border-l-4 border-l-green-400">
            <div class="text-2xl font-bold text-green-600">{{ $stats['resolved'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Çözüldü</div>
        </div>
        <div class="bg-white rounded-xl border p-4 text-center border-l-4 border-l-red-400">
            <div class="text-2xl font-bold text-red-600">{{ $stats['bugs'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Açık Hata</div>
        </div>
        <div class="bg-white rounded-xl border p-4 text-center border-l-4 border-l-amber-400">
            <div class="text-2xl font-bold text-amber-600">{{ $stats['today'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Bugün</div>
        </div>
    </div>

    {{-- Filtreler --}}
    <div class="bg-white rounded-xl border p-4">
        <form method="GET" action="{{ route('feedback.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[180px]">
                <label class="block text-xs font-medium text-gray-600 mb-1">Ara</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Mesaj, sayfa URL, kullanıcı adı..."
                       class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Durum</label>
                <select name="status" class="px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-amber-500">
                    <option value="">Tümü</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Açık</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>İnceleniyor</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Çözüldü</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Kapatıldı</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Kategori</label>
                <select name="category" class="px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-amber-500">
                    <option value="">Tümü</option>
                    <option value="bug" {{ request('category') === 'bug' ? 'selected' : '' }}>Hata / Sorun</option>
                    <option value="suggestion" {{ request('category') === 'suggestion' ? 'selected' : '' }}>Öneri</option>
                    <option value="question" {{ request('category') === 'question' ? 'selected' : '' }}>Soru</option>
                    <option value="other" {{ request('category') === 'other' ? 'selected' : '' }}>Diğer</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Öncelik</label>
                <select name="priority" class="px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-amber-500">
                    <option value="">Tümü</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Düşük</option>
                    <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Yüksek</option>
                    <option value="critical" {{ request('priority') === 'critical' ? 'selected' : '' }}>Kritik</option>
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="px-4 py-2 bg-amber-500 text-white rounded-lg text-sm font-medium hover:bg-amber-600 transition">
                    <i class="fas fa-search mr-1"></i> Filtrele
                </button>
                <a href="{{ route('feedback.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200 transition">
                    <i class="fas fa-times mr-1"></i> Temizle
                </a>
            </div>
        </form>
    </div>

    {{-- Geri Bildirim Listesi --}}
    <div class="space-y-3" x-data="{ replyingTo: null, replyText: '' }">
        @forelse($messages as $msg)
            <div class="bg-white rounded-xl border hover:shadow-md transition-shadow {{ $msg->priority === 'critical' ? 'border-l-4 border-l-red-500' : ($msg->priority === 'high' ? 'border-l-4 border-l-orange-400' : '') }}">
                <div class="p-4">
                    {{-- Üst satır --}}
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            {{-- Avatar --}}
                            <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0
                                {{ $msg->category === 'bug' ? 'bg-red-100' : ($msg->category === 'suggestion' ? 'bg-blue-100' : ($msg->category === 'question' ? 'bg-purple-100' : 'bg-gray-100')) }}">
                                <i class="fas {{ $msg->category_icon }}
                                    {{ $msg->category === 'bug' ? 'text-red-600' : ($msg->category === 'suggestion' ? 'text-blue-600' : ($msg->category === 'question' ? 'text-purple-600' : 'text-gray-600')) }}
                                    text-sm"></i>
                            </div>
                            <div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-semibold text-gray-900">{{ $msg->user->name ?? 'Silinmiş Kullanıcı' }}</span>
                                    {{-- Kategori badge --}}
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold
                                        {{ $msg->category === 'bug' ? 'bg-red-100 text-red-700' : '' }}
                                        {{ $msg->category === 'suggestion' ? 'bg-blue-100 text-blue-700' : '' }}
                                        {{ $msg->category === 'question' ? 'bg-purple-100 text-purple-700' : '' }}
                                        {{ $msg->category === 'other' ? 'bg-gray-100 text-gray-700' : '' }}">
                                        {{ $msg->category_label }}
                                    </span>
                                    {{-- Öncelik badge --}}
                                    @if($msg->priority === 'high')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-orange-100 text-orange-700">
                                            <i class="fas fa-arrow-up mr-0.5"></i> Yüksek
                                        </span>
                                    @elseif($msg->priority === 'critical')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-red-100 text-red-700 animate-pulse">
                                            <i class="fas fa-exclamation-triangle mr-0.5"></i> Kritik
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center space-x-2 mt-0.5">
                                    <span class="text-[10px] text-gray-400">{{ $msg->created_at->format('d.m.Y H:i') }}</span>
                                    @if($msg->page_url)
                                        <span class="text-[10px] text-gray-400">•</span>
                                        <span class="text-[10px] text-indigo-500 font-mono truncate max-w-[200px]" title="{{ $msg->page_url }}">{{ $msg->page_url }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Durum & Aksiyonlar --}}
                        <div class="flex items-center space-x-2">
                            {{-- Durum badge --}}
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                {{ $msg->status === 'open' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                {{ $msg->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $msg->status === 'resolved' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $msg->status === 'closed' ? 'bg-gray-100 text-gray-600' : '' }}">
                                {{ $msg->status_label }}
                            </span>

                            {{-- Durum değiştirme dropdown --}}
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                                    <i class="fas fa-ellipsis-v text-xs"></i>
                                </button>
                                <div x-show="open" @click.away="open = false" x-transition
                                     class="absolute right-0 mt-1 w-40 bg-white rounded-lg shadow-lg border py-1 z-10">
                                    @foreach(['open' => 'Açık', 'in_progress' => 'İnceleniyor', 'resolved' => 'Çözüldü', 'closed' => 'Kapatıldı'] as $statusKey => $statusLabel)
                                        @if($msg->status !== $statusKey)
                                            <form method="POST" action="{{ route('feedback.status', $msg) }}">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="status" value="{{ $statusKey }}">
                                                <button type="submit" class="w-full text-left px-3 py-1.5 text-xs hover:bg-gray-50 transition">
                                                    {{ $statusLabel }}
                                                </button>
                                            </form>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Mesaj içeriği --}}
                    <div class="ml-12 mb-3">
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $msg->message }}</p>
                    </div>

                    {{-- Admin yanıtı varsa --}}
                    @if($msg->admin_reply)
                        <div class="ml-12 p-3 bg-indigo-50 border border-indigo-100 rounded-lg mb-3">
                            <div class="flex items-center space-x-2 mb-1">
                                <div class="w-5 h-5 bg-indigo-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user-shield text-white" style="font-size: 8px;"></i>
                                </div>
                                <span class="text-xs font-semibold text-indigo-700">{{ $msg->repliedByUser?->name ?? 'Admin' }}</span>
                                <span class="text-[10px] text-gray-400">{{ $msg->replied_at?->format('d.m.Y H:i') }}</span>
                            </div>
                            <p class="text-sm text-indigo-800 whitespace-pre-wrap">{{ $msg->admin_reply }}</p>
                        </div>
                    @endif

                    {{-- Yanıt formu --}}
                    <div class="ml-12">
                        <template x-if="replyingTo === {{ $msg->id }}">
                            <form method="POST" action="{{ route('feedback.reply', $msg) }}" class="flex items-end space-x-2">
                                @csrf
                                <div class="flex-1">
                                    <textarea name="admin_reply" x-model="replyText" rows="2"
                                              placeholder="Yanıtınızı yazın..."
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-xs focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none"></textarea>
                                </div>
                                <div class="flex space-x-1">
                                    <button type="submit" :disabled="replyText.trim().length < 2"
                                            class="px-3 py-2 bg-indigo-600 text-white rounded-lg text-xs font-medium hover:bg-indigo-700 transition disabled:opacity-50">
                                        <i class="fas fa-reply mr-1"></i> Gönder
                                    </button>
                                    <button type="button" @click="replyingTo = null; replyText = ''"
                                            class="px-3 py-2 bg-gray-100 text-gray-600 rounded-lg text-xs font-medium hover:bg-gray-200 transition">
                                        İptal
                                    </button>
                                </div>
                            </form>
                        </template>
                        <template x-if="replyingTo !== {{ $msg->id }}">
                            <button @click="replyingTo = {{ $msg->id }}; replyText = '{{ addslashes($msg->admin_reply ?? '') }}'"
                                    class="text-xs text-indigo-600 hover:text-indigo-800 font-medium transition">
                                <i class="fas fa-reply mr-1"></i> {{ $msg->admin_reply ? 'Yanıtı Düzenle' : 'Yanıtla' }}
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border p-12 text-center">
                <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-comment-dots text-amber-500 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Henüz geri bildirim yok</h3>
                <p class="text-sm text-gray-500">Kullanıcılar sağ alt köşedeki turuncu butona tıklayarak geri bildirim gönderebilir.</p>
            </div>
        @endforelse
    </div>

    {{-- Sayfalama --}}
    @if($messages->hasPages())
        <div class="flex justify-center">
            {{ $messages->links() }}
        </div>
    @endif
</div>
@endsection
