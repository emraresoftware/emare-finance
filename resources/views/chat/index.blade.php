@extends('layouts.app')

@section('title', 'AI Sohbet - Gemini')

@section('content')
<div x-data="chatApp()" x-init="initChat()" class="flex flex-col h-[calc(100vh-4rem)]">

    {{-- Başlık --}}
    <div class="bg-white border-b px-6 py-4 flex items-center justify-between shrink-0">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-robot text-white text-lg"></i>
            </div>
            <div>
                <h1 class="text-lg font-bold text-gray-800">Emare AI Asistan</h1>
                <p class="text-xs text-gray-500">Gemini ile desteklenmektedir</p>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <button @click="clearChat()" class="px-3 py-2 text-sm text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Sohbeti Temizle">
                <i class="fas fa-trash-alt mr-1"></i>
                <span class="hidden sm:inline">Temizle</span>
            </button>
            <span class="px-3 py-1 text-xs font-medium rounded-full"
                  :class="isConnected ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                <i class="fas fa-circle text-[6px] mr-1" :class="isConnected ? 'text-green-500' : 'text-red-500'"></i>
                <span x-text="isConnected ? 'Bağlı' : 'Bağlantı Yok'"></span>
            </span>
        </div>
    </div>

    {{-- Mesaj Alanı --}}
    <div id="chatMessages" class="flex-1 overflow-y-auto px-4 py-6 space-y-4 bg-gray-50" x-ref="chatContainer">

        {{-- Boş durum --}}
        <template x-if="messages.length === 0">
            <div class="flex flex-col items-center justify-center h-full text-center">
                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6 shadow-xl">
                    <i class="fas fa-sparkles text-white text-3xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Merhaba! 👋</h2>
                <p class="text-gray-500 mb-8 max-w-md">
                    Ben Emare AI asistanınız. Finans, muhasebe, stok yönetimi ve işletme operasyonları hakkında sorularınızı yanıtlayabilirim.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-w-lg">
                    <button @click="sendQuickMessage('Bugünkü satış raporumu özetle')"
                            class="p-3 bg-white rounded-xl border border-gray-200 text-left hover:border-blue-300 hover:shadow-md transition-all group">
                        <div class="flex items-center space-x-2 mb-1">
                            <i class="fas fa-chart-line text-blue-500 group-hover:scale-110 transition-transform"></i>
                            <span class="text-sm font-medium text-gray-700">Satış Raporu</span>
                        </div>
                        <p class="text-xs text-gray-400">Bugünkü satış raporumu özetle</p>
                    </button>
                    <button @click="sendQuickMessage('Stok yönetimi için en iyi pratikler nelerdir?')"
                            class="p-3 bg-white rounded-xl border border-gray-200 text-left hover:border-green-300 hover:shadow-md transition-all group">
                        <div class="flex items-center space-x-2 mb-1">
                            <i class="fas fa-boxes-stacked text-green-500 group-hover:scale-110 transition-transform"></i>
                            <span class="text-sm font-medium text-gray-700">Stok Yönetimi</span>
                        </div>
                        <p class="text-xs text-gray-400">En iyi pratikler nelerdir?</p>
                    </button>
                    <button @click="sendQuickMessage('E-fatura kesme adımlarını anlat')"
                            class="p-3 bg-white rounded-xl border border-gray-200 text-left hover:border-purple-300 hover:shadow-md transition-all group">
                        <div class="flex items-center space-x-2 mb-1">
                            <i class="fas fa-file-invoice text-purple-500 group-hover:scale-110 transition-transform"></i>
                            <span class="text-sm font-medium text-gray-700">E-Fatura</span>
                        </div>
                        <p class="text-xs text-gray-400">E-fatura kesme adımlarını anlat</p>
                    </button>
                    <button @click="sendQuickMessage('Nakit akışı nasıl iyileştirilir?')"
                            class="p-3 bg-white rounded-xl border border-gray-200 text-left hover:border-amber-300 hover:shadow-md transition-all group">
                        <div class="flex items-center space-x-2 mb-1">
                            <i class="fas fa-coins text-amber-500 group-hover:scale-110 transition-transform"></i>
                            <span class="text-sm font-medium text-gray-700">Nakit Akışı</span>
                        </div>
                        <p class="text-xs text-gray-400">Nakit akışı nasıl iyileştirilir?</p>
                    </button>
                </div>
            </div>
        </template>

        {{-- Mesajlar --}}
        <template x-for="(msg, index) in messages" :key="index">
            <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                <div :class="msg.role === 'user'
                        ? 'bg-blue-600 text-white rounded-2xl rounded-br-md max-w-[80%] sm:max-w-[60%]'
                        : 'bg-white text-gray-800 rounded-2xl rounded-bl-md max-w-[80%] sm:max-w-[75%] border border-gray-100 shadow-sm'"
                     class="px-4 py-3">

                    {{-- AI avatar --}}
                    <div x-show="msg.role === 'assistant'" class="flex items-center space-x-2 mb-2 pb-2 border-b border-gray-100">
                        <div class="w-6 h-6 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-robot text-white text-xs"></i>
                        </div>
                        <span class="text-xs font-semibold text-gray-500">Emare AI</span>
                    </div>

                    {{-- İçerik --}}
                    <div x-show="msg.role === 'user'" class="text-sm whitespace-pre-wrap" x-text="msg.content"></div>
                    <div x-show="msg.role === 'assistant'" class="prose prose-sm max-w-none text-gray-700 chat-markdown" x-html="renderMarkdown(msg.content)"></div>
                </div>
            </div>
        </template>

        {{-- Yazıyor animasyonu --}}
        <template x-if="isLoading">
            <div class="flex justify-start">
                <div class="bg-white rounded-2xl rounded-bl-md px-4 py-3 border border-gray-100 shadow-sm">
                    <div class="flex items-center space-x-2 mb-2 pb-2 border-b border-gray-100">
                        <div class="w-6 h-6 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-robot text-white text-xs"></i>
                        </div>
                        <span class="text-xs font-semibold text-gray-500">Emare AI</span>
                    </div>
                    <div class="flex items-center space-x-1.5">
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                    </div>
                </div>
            </div>
        </template>

        {{-- Hata mesajı --}}
        <template x-if="errorMessage">
            <div class="flex justify-center">
                <div class="bg-red-50 text-red-700 rounded-xl px-4 py-3 text-sm border border-red-200 max-w-md">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span x-text="errorMessage"></span>
                    <button @click="errorMessage = null" class="ml-2 text-red-400 hover:text-red-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </template>

    </div>

    {{-- Giriş Alanı --}}
    <div class="bg-white border-t px-4 py-4 shrink-0">
        <form @submit.prevent="sendMessage()" class="max-w-4xl mx-auto">
            <div class="flex items-end space-x-3">
                <div class="flex-1 relative">
                    <textarea x-ref="chatInput"
                              x-model="newMessage"
                              @keydown.enter.prevent="if (!$event.shiftKey) sendMessage()"
                              @input="autoResize($event.target)"
                              rows="1"
                              :disabled="isLoading"
                              placeholder="Mesajınızı yazın... (Shift+Enter yeni satır)"
                              class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none text-sm bg-gray-50 focus:bg-white transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                              style="max-height: 120px; min-height: 44px;"></textarea>
                    <div class="absolute right-3 bottom-3 text-xs text-gray-400" x-show="newMessage.length > 0">
                        <span x-text="newMessage.length"></span>/10000
                    </div>
                </div>
                <button type="submit"
                        :disabled="isLoading || newMessage.trim().length === 0"
                        class="px-4 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 disabled:transform-none disabled:shadow-lg">
                    <i :class="isLoading ? 'fas fa-spinner fa-spin' : 'fas fa-paper-plane'" class="text-sm"></i>
                </button>
            </div>
            <p class="text-center text-xs text-gray-400 mt-2">
                <i class="fas fa-shield-alt mr-1"></i>
                Gemini AI tarafından desteklenmektedir. Yanıtlar bilgi amaçlıdır.
            </p>
        </form>
    </div>

</div>

{{-- Marked.js CDN (Markdown rendering) --}}
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

{{-- DOMPurify CDN (XSS koruması) --}}
<script src="https://cdn.jsdelivr.net/npm/dompurify@3.0.6/dist/purify.min.js"></script>

<style>
    /* Chat markdown styling */
    .chat-markdown h1, .chat-markdown h2, .chat-markdown h3 {
        font-weight: 700;
        margin-top: 0.75rem;
        margin-bottom: 0.5rem;
    }
    .chat-markdown h1 { font-size: 1.25rem; }
    .chat-markdown h2 { font-size: 1.1rem; }
    .chat-markdown h3 { font-size: 1rem; }
    .chat-markdown p { margin-bottom: 0.5rem; }
    .chat-markdown ul, .chat-markdown ol {
        margin-left: 1.25rem;
        margin-bottom: 0.5rem;
    }
    .chat-markdown ul { list-style-type: disc; }
    .chat-markdown ol { list-style-type: decimal; }
    .chat-markdown li { margin-bottom: 0.25rem; }
    .chat-markdown code {
        background: #f3f4f6;
        padding: 0.125rem 0.375rem;
        border-radius: 0.25rem;
        font-size: 0.85em;
        color: #e11d48;
    }
    .chat-markdown pre {
        background: #1f2937;
        color: #e5e7eb;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        overflow-x: auto;
        margin-bottom: 0.75rem;
    }
    .chat-markdown pre code {
        background: transparent;
        color: inherit;
        padding: 0;
    }
    .chat-markdown blockquote {
        border-left: 3px solid #3b82f6;
        padding-left: 0.75rem;
        color: #6b7280;
        margin-bottom: 0.5rem;
    }
    .chat-markdown table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0.75rem;
    }
    .chat-markdown th, .chat-markdown td {
        border: 1px solid #e5e7eb;
        padding: 0.375rem 0.75rem;
        text-align: left;
    }
    .chat-markdown th {
        background: #f9fafb;
        font-weight: 600;
    }
    .chat-markdown strong { font-weight: 700; }
    .chat-markdown a {
        color: #3b82f6;
        text-decoration: underline;
    }

    /* Scrollbar */
    #chatMessages::-webkit-scrollbar { width: 6px; }
    #chatMessages::-webkit-scrollbar-track { background: transparent; }
    #chatMessages::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 3px; }
    #chatMessages::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
</style>

<script>
function chatApp() {
    return {
        messages: [],
        newMessage: '',
        isLoading: false,
        isConnected: true,
        errorMessage: null,

        initChat() {
            // localStorage'dan mesajları yükle
            const saved = localStorage.getItem('emare_chat_messages');
            if (saved) {
                try {
                    this.messages = JSON.parse(saved);
                } catch (e) {
                    this.messages = [];
                }
            }

            // Marked.js ayarları
            if (typeof marked !== 'undefined') {
                marked.setOptions({
                    breaks: true,
                    gfm: true,
                });
            }

            this.$nextTick(() => this.scrollToBottom());
        },

        renderMarkdown(text) {
            if (!text) return '';
            try {
                const html = marked.parse(text);
                return DOMPurify.sanitize(html);
            } catch (e) {
                return text.replace(/\n/g, '<br>');
            }
        },

        autoResize(el) {
            el.style.height = 'auto';
            el.style.height = Math.min(el.scrollHeight, 120) + 'px';
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const container = this.$refs.chatContainer;
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        },

        saveMessages() {
            // Son 100 mesajı sakla
            const toSave = this.messages.slice(-100);
            localStorage.setItem('emare_chat_messages', JSON.stringify(toSave));
        },

        clearChat() {
            if (confirm('Tüm sohbet geçmişini silmek istediğinize emin misiniz?')) {
                this.messages = [];
                localStorage.removeItem('emare_chat_messages');
            }
        },

        sendQuickMessage(text) {
            this.newMessage = text;
            this.sendMessage();
        },

        async sendMessage() {
            const text = this.newMessage.trim();
            if (!text || this.isLoading) return;

            // Kullanıcı mesajını ekle
            this.messages.push({
                role: 'user',
                content: text,
            });

            this.newMessage = '';
            this.errorMessage = null;
            this.isLoading = true;
            this.saveMessages();
            this.scrollToBottom();

            // Textarea'yı sıfırla
            this.$nextTick(() => {
                const input = this.$refs.chatInput;
                if (input) {
                    input.style.height = '44px';
                }
            });

            // Gemini API formatı için mesajları hazırla
            const apiMessages = this.messages.map(m => ({
                role: m.role,
                content: m.content,
            }));

            try {
                const response = await fetch('{{ route("chat.send") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'text/event-stream',
                    },
                    body: JSON.stringify({ messages: apiMessages }),
                });

                if (!response.ok) {
                    const errData = await response.json().catch(() => ({}));
                    throw new Error(errData.error || `HTTP Hatası: ${response.status}`);
                }

                // Boş asistan mesajı ekle (streaming ile dolacak)
                this.messages.push({
                    role: 'assistant',
                    content: '',
                });

                const assistantIndex = this.messages.length - 1;
                const reader = response.body.getReader();
                const decoder = new TextDecoder();
                let buffer = '';

                while (true) {
                    const { done, value } = await reader.read();
                    if (done) break;

                    buffer += decoder.decode(value, { stream: true });

                    // SSE satırlarını ayrıştır
                    const lines = buffer.split('\n');
                    buffer = lines.pop() || '';

                    for (const line of lines) {
                        const trimmed = line.trim();
                        if (!trimmed || !trimmed.startsWith('data: ')) continue;

                        const dataStr = trimmed.substring(6);

                        if (dataStr === '[DONE]') continue;

                        try {
                            const data = JSON.parse(dataStr);
                            if (data.error) {
                                this.errorMessage = data.error;
                                continue;
                            }
                            if (data.text) {
                                this.messages[assistantIndex].content += data.text;
                                this.scrollToBottom();
                            }
                        } catch (e) {
                            // JSON parse hatası, göz ardı et
                        }
                    }
                }

                // Eğer yanıt boş kaldıysa
                if (!this.messages[assistantIndex].content) {
                    this.messages[assistantIndex].content = 'Yanıt alınamadı. Lütfen tekrar deneyin.';
                }

                this.isConnected = true;

            } catch (error) {
                console.error('Chat Error:', error);
                this.isConnected = false;

                if (error.message.includes('API anahtarı')) {
                    this.errorMessage = error.message;
                } else {
                    this.errorMessage = 'Bağlantı hatası oluştu. Lütfen tekrar deneyin.';
                }

                // Eğer boş bir asistan mesajı kaldıysa kaldır
                const lastMsg = this.messages[this.messages.length - 1];
                if (lastMsg && lastMsg.role === 'assistant' && !lastMsg.content) {
                    this.messages.pop();
                }
            } finally {
                this.isLoading = false;
                this.saveMessages();
                this.scrollToBottom();
            }
        },
    };
}
</script>
@endsection
