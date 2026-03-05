{{-- Floating AI Chat Widget — Tüm sayfalarda sağ alt köşede --}}
<div x-data="chatWidget()" x-init="initChat()" x-cloak
     @open-chat-widget.window="isOpen = true; $nextTick(() => { scrollToBottom(); $refs.widgetChatInput?.focus(); })"
     @keydown.escape.window="isOpen = false">

    {{-- Floating Buton --}}
    <button @click="toggleChat()"
            class="fixed bottom-6 right-6 z-[9999] w-14 h-14 bg-gradient-to-br from-blue-600 to-purple-600 rounded-full shadow-2xl flex items-center justify-center hover:scale-110 transition-all duration-300 group"
            :class="isOpen ? 'scale-0 opacity-0 pointer-events-none' : 'scale-100 opacity-100'">
        <i class="fas fa-robot text-white text-xl group-hover:animate-bounce"></i>
        {{-- Bildirim noktası --}}
        <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full border-2 border-white animate-pulse"
              x-show="unreadCount > 0"></span>
    </button>

    {{-- Chat Paneli --}}
    <div class="fixed bottom-6 right-6 z-[9999] transition-all duration-300 origin-bottom-right"
         :class="isOpen ? 'scale-100 opacity-100' : 'scale-0 opacity-0 pointer-events-none'"
         style="width: 400px; height: 560px; max-height: calc(100vh - 100px);">

        <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 flex flex-col h-full overflow-hidden">

            {{-- Header --}}
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-3 flex items-center justify-between shrink-0 rounded-t-2xl">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                        <i class="fas fa-robot text-white text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white">Emare AI Asistan</h3>
                        <p class="text-[10px] text-blue-100">Gemini destekli</p>
                    </div>
                </div>
                <div class="flex items-center space-x-1">
                    <button @click="clearChat()" class="w-8 h-8 flex items-center justify-center text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition-colors" title="Temizle">
                        <i class="fas fa-trash-alt text-xs"></i>
                    </button>
                    <button @click="toggleFullscreen()" class="w-8 h-8 flex items-center justify-center text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition-colors" title="Tam Ekran">
                        <i class="fas fa-expand text-xs"></i>
                    </button>
                    <button @click="toggleChat()" class="w-8 h-8 flex items-center justify-center text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition-colors" title="Kapat">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            </div>

            {{-- Mesaj Alanı --}}
            <div class="flex-1 overflow-y-auto px-3 py-4 space-y-3 bg-gray-50 chat-widget-messages" x-ref="widgetChatContainer">

                {{-- Boş durum --}}
                <template x-if="messages.length === 0">
                    <div class="flex flex-col items-center justify-center h-full text-center px-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mb-4 shadow-lg">
                            <i class="fas fa-sparkles text-white text-xl"></i>
                        </div>
                        <h4 class="text-base font-bold text-gray-800 mb-1">Merhaba! 👋</h4>
                        <p class="text-xs text-gray-500 mb-4">Size nasıl yardımcı olabilirim?</p>
                        <div class="grid grid-cols-2 gap-2 w-full">
                            <button @click="sendQuickMessage('Satış raporumu özetle')"
                                    class="p-2 bg-white rounded-lg border border-gray-200 text-left hover:border-blue-300 hover:shadow transition-all text-xs">
                                <i class="fas fa-chart-line text-blue-500 mr-1"></i> Satış Raporu
                            </button>
                            <button @click="sendQuickMessage('Stok yönetimi tavsiyeleri')"
                                    class="p-2 bg-white rounded-lg border border-gray-200 text-left hover:border-green-300 hover:shadow transition-all text-xs">
                                <i class="fas fa-boxes-stacked text-green-500 mr-1"></i> Stok Yönetimi
                            </button>
                            <button @click="sendQuickMessage('E-fatura nasıl kesilir?')"
                                    class="p-2 bg-white rounded-lg border border-gray-200 text-left hover:border-purple-300 hover:shadow transition-all text-xs">
                                <i class="fas fa-file-invoice text-purple-500 mr-1"></i> E-Fatura
                            </button>
                            <button @click="sendQuickMessage('Nakit akışı iyileştirme')"
                                    class="p-2 bg-white rounded-lg border border-gray-200 text-left hover:border-amber-300 hover:shadow transition-all text-xs">
                                <i class="fas fa-coins text-amber-500 mr-1"></i> Nakit Akışı
                            </button>
                        </div>
                    </div>
                </template>

                {{-- Mesajlar --}}
                <template x-for="(msg, index) in messages" :key="index">
                    <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                        <div :class="msg.role === 'user'
                                ? 'bg-blue-600 text-white rounded-2xl rounded-br-sm max-w-[85%]'
                                : 'bg-white text-gray-800 rounded-2xl rounded-bl-sm max-w-[85%] border border-gray-100 shadow-sm'"
                             class="px-3 py-2">
                            {{-- AI avatar --}}
                            <div x-show="msg.role === 'assistant'" class="flex items-center space-x-1.5 mb-1.5 pb-1.5 border-b border-gray-100">
                                <div class="w-5 h-5 bg-gradient-to-br from-blue-500 to-purple-600 rounded-md flex items-center justify-center">
                                    <i class="fas fa-robot text-white" style="font-size: 8px;"></i>
                                </div>
                                <span class="text-[10px] font-semibold text-gray-400">Emare AI</span>
                            </div>
                            <div x-show="msg.role === 'user'" class="text-xs whitespace-pre-wrap" x-text="msg.content"></div>
                            <div x-show="msg.role === 'assistant'" class="prose prose-xs max-w-none text-gray-700 chat-markdown text-xs" x-html="renderMarkdown(msg.content)"></div>
                        </div>
                    </div>
                </template>

                {{-- Yazıyor --}}
                <template x-if="isLoading">
                    <div class="flex justify-start">
                        <div class="bg-white rounded-2xl rounded-bl-sm px-3 py-2 border border-gray-100 shadow-sm">
                            <div class="flex items-center space-x-1.5 mb-1.5 pb-1.5 border-b border-gray-100">
                                <div class="w-5 h-5 bg-gradient-to-br from-blue-500 to-purple-600 rounded-md flex items-center justify-center">
                                    <i class="fas fa-robot text-white" style="font-size: 8px;"></i>
                                </div>
                                <span class="text-[10px] font-semibold text-gray-400">Emare AI</span>
                            </div>
                            <div class="flex items-center space-x-1">
                                <div class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                                <div class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                                <div class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Hata --}}
                <template x-if="errorMessage">
                    <div class="flex justify-center">
                        <div class="bg-red-50 text-red-600 rounded-lg px-3 py-2 text-xs border border-red-200">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            <span x-text="errorMessage"></span>
                            <button @click="errorMessage = null" class="ml-1 text-red-400 hover:text-red-600"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Giriş Alanı --}}
            <div class="bg-white border-t px-3 py-3 shrink-0 rounded-b-2xl">
                <form @submit.prevent="sendMessage()" class="flex items-end space-x-2">
                    <div class="flex-1 relative">
                        <textarea x-ref="widgetChatInput"
                                  x-model="newMessage"
                                  @keydown.enter.prevent="if (!$event.shiftKey) sendMessage()"
                                  @input="autoResize($event.target)"
                                  rows="1"
                                  :disabled="isLoading"
                                  placeholder="Mesajınızı yazın..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none text-xs bg-gray-50 focus:bg-white transition-colors disabled:opacity-50"
                                  style="max-height: 80px; min-height: 36px;"></textarea>
                    </div>
                    <button type="submit"
                            :disabled="isLoading || newMessage.trim().length === 0"
                            class="px-3 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-md">
                        <i :class="isLoading ? 'fas fa-spinner fa-spin' : 'fas fa-paper-plane'" class="text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Marked.js & DOMPurify CDN --}}
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dompurify@3.0.6/dist/purify.min.js"></script>

<style>
    /* Chat widget markdown */
    .chat-markdown h1, .chat-markdown h2, .chat-markdown h3 { font-weight: 700; margin-top: 0.5rem; margin-bottom: 0.25rem; }
    .chat-markdown h1 { font-size: 1rem; }
    .chat-markdown h2 { font-size: 0.9rem; }
    .chat-markdown h3 { font-size: 0.85rem; }
    .chat-markdown p { margin-bottom: 0.35rem; }
    .chat-markdown ul, .chat-markdown ol { margin-left: 1rem; margin-bottom: 0.35rem; }
    .chat-markdown ul { list-style-type: disc; }
    .chat-markdown ol { list-style-type: decimal; }
    .chat-markdown li { margin-bottom: 0.15rem; }
    .chat-markdown code { background: #f3f4f6; padding: 0.1rem 0.3rem; border-radius: 0.2rem; font-size: 0.8em; color: #e11d48; }
    .chat-markdown pre { background: #1f2937; color: #e5e7eb; padding: 0.5rem 0.75rem; border-radius: 0.375rem; overflow-x: auto; margin-bottom: 0.5rem; font-size: 0.75rem; }
    .chat-markdown pre code { background: transparent; color: inherit; padding: 0; }
    .chat-markdown blockquote { border-left: 3px solid #3b82f6; padding-left: 0.5rem; color: #6b7280; margin-bottom: 0.35rem; }
    .chat-markdown table { width: 100%; border-collapse: collapse; margin-bottom: 0.5rem; font-size: 0.75rem; }
    .chat-markdown th, .chat-markdown td { border: 1px solid #e5e7eb; padding: 0.25rem 0.5rem; text-align: left; }
    .chat-markdown th { background: #f9fafb; font-weight: 600; }
    .chat-markdown strong { font-weight: 700; }
    .chat-markdown a { color: #3b82f6; text-decoration: underline; }

    .chat-widget-messages::-webkit-scrollbar { width: 4px; }
    .chat-widget-messages::-webkit-scrollbar-track { background: transparent; }
    .chat-widget-messages::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 2px; }
    .chat-widget-messages::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
</style>

<script>
function chatWidget() {
    return {
        isOpen: false,
        messages: [],
        newMessage: '',
        isLoading: false,
        errorMessage: null,
        unreadCount: 0,

        initChat() {
            const saved = localStorage.getItem('emare_chat_messages');
            if (saved) {
                try { this.messages = JSON.parse(saved); } catch (e) { this.messages = []; }
            }
            if (typeof marked !== 'undefined') {
                marked.setOptions({ breaks: true, gfm: true });
            }

            // Eğer URL'de openChat=1 parametresi varsa otomatik aç
            if (new URLSearchParams(window.location.search).get('openChat') === '1') {
                this.isOpen = true;
                this.$nextTick(() => this.scrollToBottom());
            }
        },

        toggleChat() {
            this.isOpen = !this.isOpen;
            this.unreadCount = 0;
            if (this.isOpen) {
                this.$nextTick(() => {
                    this.scrollToBottom();
                    this.$refs.widgetChatInput?.focus();
                });
            }
        },

        toggleFullscreen() {
            window.location.href = '/sohbet';
        },

        renderMarkdown(text) {
            if (!text) return '';
            try {
                return DOMPurify.sanitize(marked.parse(text));
            } catch (e) {
                return text.replace(/\n/g, '<br>');
            }
        },

        autoResize(el) {
            el.style.height = 'auto';
            el.style.height = Math.min(el.scrollHeight, 80) + 'px';
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const c = this.$refs.widgetChatContainer;
                if (c) c.scrollTop = c.scrollHeight;
            });
        },

        saveMessages() {
            localStorage.setItem('emare_chat_messages', JSON.stringify(this.messages.slice(-100)));
        },

        clearChat() {
            if (confirm('Sohbet geçmişini silmek istiyor musunuz?')) {
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

            this.messages.push({ role: 'user', content: text });
            this.newMessage = '';
            this.errorMessage = null;
            this.isLoading = true;
            this.saveMessages();
            this.scrollToBottom();

            this.$nextTick(() => {
                const input = this.$refs.widgetChatInput;
                if (input) input.style.height = '36px';
            });

            const apiMessages = this.messages.map(m => ({ role: m.role, content: m.content }));

            try {
                const response = await fetch('/sohbet/gonder', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'text/event-stream',
                    },
                    body: JSON.stringify({ messages: apiMessages }),
                });

                if (!response.ok) {
                    const errData = await response.json().catch(() => ({}));
                    throw new Error(errData.error || 'HTTP Hatası: ' + response.status);
                }

                this.messages.push({ role: 'assistant', content: '' });
                const idx = this.messages.length - 1;
                const reader = response.body.getReader();
                const decoder = new TextDecoder();
                let buffer = '';

                while (true) {
                    const { done, value } = await reader.read();
                    if (done) break;
                    buffer += decoder.decode(value, { stream: true });
                    const lines = buffer.split('\n');
                    buffer = lines.pop() || '';

                    for (const line of lines) {
                        const trimmed = line.trim();
                        if (!trimmed || !trimmed.startsWith('data: ')) continue;
                        const dataStr = trimmed.substring(6);
                        if (dataStr === '[DONE]') continue;
                        try {
                            const data = JSON.parse(dataStr);
                            if (data.error) { this.errorMessage = data.error; continue; }
                            if (data.text) {
                                this.messages[idx].content += data.text;
                                this.scrollToBottom();
                            }
                        } catch (e) {}
                    }
                }

                if (!this.messages[idx].content) {
                    this.messages[idx].content = 'Yanıt alınamadı. Lütfen tekrar deneyin.';
                }

                if (!this.isOpen) this.unreadCount++;

            } catch (error) {
                console.error('Chat Widget Error:', error);
                this.errorMessage = error.message.includes('API') ? error.message : 'Bağlantı hatası. Tekrar deneyin.';
                const lastMsg = this.messages[this.messages.length - 1];
                if (lastMsg && lastMsg.role === 'assistant' && !lastMsg.content) this.messages.pop();
            } finally {
                this.isLoading = false;
                this.saveMessages();
                this.scrollToBottom();
            }
        },
    };
}
</script>
