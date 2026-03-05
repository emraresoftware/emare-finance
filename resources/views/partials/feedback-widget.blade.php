{{-- Floating Geri Bildirim Widget — Sağ alt köşede (AI butonunun solunda) --}}
<div x-data="feedbackWidget()" x-init="init()" x-cloak
     @open-feedback-widget.window="isOpen = true; $nextTick(() => $refs.feedbackInput?.focus())"
     @keydown.escape.window="isOpen = false">

    {{-- Floating Buton --}}
    <button @click="togglePanel()"
            class="fixed bottom-6 right-24 z-[9998] w-14 h-14 bg-gradient-to-br from-amber-500 to-orange-600 rounded-full shadow-2xl flex items-center justify-center hover:scale-110 transition-all duration-300 group"
            :class="isOpen ? 'scale-0 opacity-0 pointer-events-none' : 'scale-100 opacity-100'"
            title="Sorun Bildir / Geri Bildirim">
        <i class="fas fa-comment-dots text-white text-xl group-hover:animate-bounce"></i>
        <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full border-2 border-white animate-pulse"
              x-show="hasUnread"></span>
    </button>

    {{-- Panel --}}
    <div class="fixed bottom-6 right-24 z-[9998] transition-all duration-300 origin-bottom-right"
         :class="isOpen ? 'scale-100 opacity-100' : 'scale-0 opacity-0 pointer-events-none'"
         style="width: 380px; height: 520px; max-height: calc(100vh - 100px);">

        <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 flex flex-col h-full overflow-hidden">

            {{-- Header --}}
            <div class="bg-gradient-to-r from-amber-500 to-orange-600 px-4 py-3 flex items-center justify-between shrink-0 rounded-t-2xl">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                        <i class="fas fa-comment-dots text-white text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white">Geri Bildirim</h3>
                        <p class="text-[10px] text-amber-100">Sorun bildirin, önerinizi iletin</p>
                    </div>
                </div>
                <div class="flex items-center space-x-1">
                    {{-- Tab Switch --}}
                    <button @click="activeTab = 'new'" class="px-2 py-1 rounded-lg text-xs font-medium transition-colors"
                            :class="activeTab === 'new' ? 'bg-white/30 text-white' : 'text-white/70 hover:text-white hover:bg-white/10'">
                        <i class="fas fa-plus mr-0.5"></i> Yeni
                    </button>
                    <button @click="activeTab = 'list'; loadMyFeedback()" class="px-2 py-1 rounded-lg text-xs font-medium transition-colors"
                            :class="activeTab === 'list' ? 'bg-white/30 text-white' : 'text-white/70 hover:text-white hover:bg-white/10'">
                        <i class="fas fa-list mr-0.5"></i> Geçmiş
                    </button>
                    <button @click="togglePanel()" class="w-8 h-8 flex items-center justify-center text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition-colors ml-1" title="Kapat">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            </div>

            {{-- TAB: Yeni Geri Bildirim --}}
            <div x-show="activeTab === 'new'" class="flex-1 flex flex-col overflow-hidden">

                {{-- Bilgi kutusu --}}
                <div class="px-4 py-3 bg-amber-50 border-b border-amber-100">
                    <p class="text-xs text-amber-800">
                        <i class="fas fa-info-circle mr-1"></i>
                        Test sırasında karşılaştığınız sorunları veya önerilerinizi buradan iletebilirsiniz.
                    </p>
                </div>

                <div class="flex-1 px-4 py-3 overflow-y-auto space-y-3">
                    {{-- Kategori seçimi --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kategori</label>
                        <div class="grid grid-cols-4 gap-1.5">
                            <button type="button" @click="category = 'bug'"
                                    class="px-2 py-1.5 rounded-lg text-xs font-medium border transition-all text-center"
                                    :class="category === 'bug' ? 'bg-red-50 border-red-300 text-red-700 ring-1 ring-red-300' : 'bg-white border-gray-200 text-gray-500 hover:border-red-200'">
                                <i class="fas fa-bug block text-sm mb-0.5"></i>
                                Hata
                            </button>
                            <button type="button" @click="category = 'suggestion'"
                                    class="px-2 py-1.5 rounded-lg text-xs font-medium border transition-all text-center"
                                    :class="category === 'suggestion' ? 'bg-blue-50 border-blue-300 text-blue-700 ring-1 ring-blue-300' : 'bg-white border-gray-200 text-gray-500 hover:border-blue-200'">
                                <i class="fas fa-lightbulb block text-sm mb-0.5"></i>
                                Öneri
                            </button>
                            <button type="button" @click="category = 'question'"
                                    class="px-2 py-1.5 rounded-lg text-xs font-medium border transition-all text-center"
                                    :class="category === 'question' ? 'bg-purple-50 border-purple-300 text-purple-700 ring-1 ring-purple-300' : 'bg-white border-gray-200 text-gray-500 hover:border-purple-200'">
                                <i class="fas fa-question-circle block text-sm mb-0.5"></i>
                                Soru
                            </button>
                            <button type="button" @click="category = 'other'"
                                    class="px-2 py-1.5 rounded-lg text-xs font-medium border transition-all text-center"
                                    :class="category === 'other' ? 'bg-gray-100 border-gray-400 text-gray-700 ring-1 ring-gray-300' : 'bg-white border-gray-200 text-gray-500 hover:border-gray-300'">
                                <i class="fas fa-comment block text-sm mb-0.5"></i>
                                Diğer
                            </button>
                        </div>
                    </div>

                    {{-- Öncelik --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Öncelik</label>
                        <div class="flex space-x-1.5">
                            <button type="button" @click="priority = 'low'"
                                    class="flex-1 px-2 py-1 rounded text-xs font-medium border transition-all"
                                    :class="priority === 'low' ? 'bg-gray-100 border-gray-400 text-gray-700' : 'bg-white border-gray-200 text-gray-400 hover:border-gray-300'">
                                Düşük
                            </button>
                            <button type="button" @click="priority = 'normal'"
                                    class="flex-1 px-2 py-1 rounded text-xs font-medium border transition-all"
                                    :class="priority === 'normal' ? 'bg-blue-50 border-blue-400 text-blue-700' : 'bg-white border-gray-200 text-gray-400 hover:border-blue-200'">
                                Normal
                            </button>
                            <button type="button" @click="priority = 'high'"
                                    class="flex-1 px-2 py-1 rounded text-xs font-medium border transition-all"
                                    :class="priority === 'high' ? 'bg-orange-50 border-orange-400 text-orange-700' : 'bg-white border-gray-200 text-gray-400 hover:border-orange-200'">
                                Yüksek
                            </button>
                            <button type="button" @click="priority = 'critical'"
                                    class="flex-1 px-2 py-1 rounded text-xs font-medium border transition-all"
                                    :class="priority === 'critical' ? 'bg-red-50 border-red-400 text-red-700' : 'bg-white border-gray-200 text-gray-400 hover:border-red-200'">
                                Kritik
                            </button>
                        </div>
                    </div>

                    {{-- Mesaj --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Açıklama</label>
                        <textarea x-ref="feedbackInput"
                                  x-model="message"
                                  rows="4"
                                  placeholder="Sorununuzu veya önerinizi detaylı bir şekilde yazın...&#10;Örn: 'Satışlar sayfasında filtre çalışmıyor'"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-xl text-xs focus:ring-2 focus:ring-amber-500 focus:border-transparent resize-none bg-gray-50 focus:bg-white transition-colors placeholder:text-gray-400"
                                  style="min-height: 80px;"></textarea>
                        <p class="text-[10px] text-gray-400 mt-0.5 text-right" x-text="message.length + '/2000'"></p>
                    </div>

                    {{-- Mevcut sayfa --}}
                    <div class="flex items-center space-x-2 text-[10px] text-gray-400 bg-gray-50 rounded-lg px-2 py-1.5">
                        <i class="fas fa-link"></i>
                        <span class="truncate" x-text="currentPage"></span>
                    </div>
                </div>

                {{-- Gönder Butonu --}}
                <div class="px-4 py-3 border-t bg-white shrink-0 rounded-b-2xl">
                    {{-- Başarı mesajı --}}
                    <div x-show="successMsg" x-transition class="mb-2 p-2 bg-green-50 border border-green-200 rounded-lg text-xs text-green-700">
                        <i class="fas fa-check-circle mr-1"></i>
                        <span x-text="successMsg"></span>
                    </div>
                    {{-- Hata mesajı --}}
                    <div x-show="errorMsg" x-transition class="mb-2 p-2 bg-red-50 border border-red-200 rounded-lg text-xs text-red-600">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        <span x-text="errorMsg"></span>
                    </div>

                    <button @click="submitFeedback()"
                            :disabled="sending || message.trim().length < 3"
                            class="w-full py-2.5 bg-gradient-to-r from-amber-500 to-orange-600 text-white text-sm font-semibold rounded-xl hover:from-amber-600 hover:to-orange-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-md">
                        <template x-if="!sending">
                            <span><i class="fas fa-paper-plane mr-1.5"></i> Gönder</span>
                        </template>
                        <template x-if="sending">
                            <span><i class="fas fa-spinner fa-spin mr-1.5"></i> Gönderiliyor...</span>
                        </template>
                    </button>
                </div>
            </div>

            {{-- TAB: Geçmiş Geri Bildirimler --}}
            <div x-show="activeTab === 'list'" class="flex-1 flex flex-col overflow-hidden">
                <div class="flex-1 overflow-y-auto px-3 py-3 space-y-2 feedback-list-scroll">

                    {{-- Yükleniyor --}}
                    <template x-if="loadingList">
                        <div class="flex items-center justify-center h-32">
                            <i class="fas fa-spinner fa-spin text-amber-500 text-lg"></i>
                        </div>
                    </template>

                    {{-- Boş --}}
                    <template x-if="!loadingList && myMessages.length === 0">
                        <div class="flex flex-col items-center justify-center h-32 text-center">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-2">
                                <i class="fas fa-inbox text-gray-400 text-lg"></i>
                            </div>
                            <p class="text-xs text-gray-500">Henüz geri bildirim göndermediniz.</p>
                        </div>
                    </template>

                    {{-- Mesaj listesi --}}
                    <template x-for="msg in myMessages" :key="msg.id">
                        <div class="bg-white border rounded-xl p-3 hover:shadow-sm transition-shadow">
                            {{-- Üst satır: kategori + durum --}}
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold"
                                      :class="{
                                          'bg-red-100 text-red-700': msg.category === 'bug',
                                          'bg-blue-100 text-blue-700': msg.category === 'suggestion',
                                          'bg-purple-100 text-purple-700': msg.category === 'question',
                                          'bg-gray-100 text-gray-700': msg.category === 'other'
                                      }">
                                    <i class="fas mr-1" :class="msg.category_icon"></i>
                                    <span x-text="msg.category_label"></span>
                                </span>
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold"
                                      :class="{
                                          'bg-yellow-100 text-yellow-700': msg.status === 'open',
                                          'bg-blue-100 text-blue-700': msg.status === 'in_progress',
                                          'bg-green-100 text-green-700': msg.status === 'resolved',
                                          'bg-gray-100 text-gray-600': msg.status === 'closed'
                                      }">
                                    <span x-text="msg.status_label"></span>
                                </span>
                            </div>
                            {{-- Mesaj --}}
                            <p class="text-xs text-gray-700 mb-1.5 line-clamp-3" x-text="msg.message"></p>
                            {{-- Tarih --}}
                            <p class="text-[10px] text-gray-400" x-text="msg.created_at"></p>

                            {{-- Admin yanıtı --}}
                            <template x-if="msg.admin_reply">
                                <div class="mt-2 p-2 bg-indigo-50 border border-indigo-100 rounded-lg">
                                    <div class="flex items-center space-x-1 mb-1">
                                        <div class="w-4 h-4 bg-indigo-500 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user-shield text-white" style="font-size: 7px;"></i>
                                        </div>
                                        <span class="text-[10px] font-semibold text-indigo-600">Admin Yanıtı</span>
                                        <span class="text-[10px] text-gray-400" x-text="msg.replied_at"></span>
                                    </div>
                                    <p class="text-xs text-indigo-800" x-text="msg.admin_reply"></p>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .feedback-list-scroll::-webkit-scrollbar { width: 4px; }
    .feedback-list-scroll::-webkit-scrollbar-track { background: transparent; }
    .feedback-list-scroll::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 2px; }
    .feedback-list-scroll::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

<script>
function feedbackWidget() {
    return {
        isOpen: false,
        activeTab: 'new',
        category: 'bug',
        priority: 'normal',
        message: '',
        sending: false,
        successMsg: null,
        errorMsg: null,
        currentPage: window.location.pathname,
        myMessages: [],
        loadingList: false,
        hasUnread: false,

        init() {
            // Sayfa URL'sini takip et
            this.currentPage = window.location.pathname;
        },

        togglePanel() {
            this.isOpen = !this.isOpen;
            this.hasUnread = false;
            if (this.isOpen) {
                this.$nextTick(() => this.$refs.feedbackInput?.focus());
            }
        },

        async submitFeedback() {
            if (this.message.trim().length < 3 || this.sending) return;

            this.sending = true;
            this.successMsg = null;
            this.errorMsg = null;

            try {
                const response = await fetch('/geribildirim/gonder', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        message: this.message,
                        category: this.category,
                        priority: this.priority,
                        page_url: this.currentPage,
                    }),
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.successMsg = data.message;
                    this.message = '';
                    this.category = 'bug';
                    this.priority = 'normal';

                    // 3 saniye sonra başarı mesajını temizle
                    setTimeout(() => { this.successMsg = null; }, 3000);
                } else {
                    this.errorMsg = data.message || 'Bir hata oluştu.';
                }
            } catch (error) {
                console.error('Feedback Error:', error);
                this.errorMsg = 'Bağlantı hatası. Lütfen tekrar deneyin.';
            } finally {
                this.sending = false;
            }
        },

        async loadMyFeedback() {
            if (this.loadingList) return;
            this.loadingList = true;

            try {
                const response = await fetch('/geribildirim/benim', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });
                const data = await response.json();
                this.myMessages = data.messages || [];
            } catch (error) {
                console.error('Load feedback error:', error);
            } finally {
                this.loadingList = false;
            }
        },
    };
}
</script>
