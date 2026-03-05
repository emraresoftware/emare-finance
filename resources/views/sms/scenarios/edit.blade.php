@extends('layouts.app')
@section('title', 'SMS Senaryosu Düzenle')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Senaryoyu Düzenle</h1>
        <p class="text-sm text-gray-500 mt-1">{{ $scenario->name }}</p>
    </div>
    <a href="{{ route('sms.scenarios.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
        <i class="fas fa-arrow-left mr-2"></i> Senaryolara Dön
    </a>
</div>

@if(session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
        <i class="fas fa-times-circle mr-2"></i>{{ session('error') }}
    </div>
@endif

<form action="{{ route('sms.scenarios.update', $scenario->id) }}" method="POST"
      x-data="scenarioForm()" class="space-y-6">
    @csrf
    @method('PUT')

    {{-- Temel Bilgiler --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800">Temel Bilgiler</h3>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Senaryo Adı <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $scenario->name) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                       placeholder="Örn: Yeni Müşteri Hoş Geldin SMS">
                @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="trigger_event" class="block text-sm font-medium text-gray-700 mb-1">Tetikleyici Olay <span class="text-red-500">*</span></label>
                <select name="trigger_event" id="trigger_event" x-model="trigger_event"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Olay Seçin</option>
                    @foreach($triggerEvents as $eventKey => $eventLabel)
                        <option value="{{ $eventKey }}" {{ old('trigger_event', $scenario->trigger_event) == $eventKey ? 'selected' : '' }}>{{ $eventLabel }}</option>
                    @endforeach
                </select>
                @error('trigger_event') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="template_id" class="block text-sm font-medium text-gray-700 mb-1">SMS Şablonu <span class="text-red-500">*</span></label>
                <select name="template_id" id="template_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Şablon Seçin</option>
                    @foreach($templates as $template)
                        <option value="{{ $template->id }}" {{ old('template_id', $scenario->template_id) == $template->id ? 'selected' : '' }}>{{ $template->name }}</option>
                    @endforeach
                </select>
                @error('template_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Öncelik</label>
                <input type="number" name="priority" id="priority" value="{{ old('priority', $scenario->priority ?? 0) }}" min="0" max="100"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <p class="text-xs text-gray-400 mt-1">Düşük sayı = yüksek öncelik</p>
                @error('priority') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    {{-- Hedef Kitle --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800">Hedef Kitle</h3>
            <p class="text-sm text-gray-500 mt-1">SMS'in kime gönderileceğini belirleyin</p>
        </div>
        <div class="p-6 space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Hedef Tipi <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach($targetTypes as $typeKey => $typeLabel)
                        <label class="relative cursor-pointer">
                            <input type="radio" name="target_type" value="{{ $typeKey }}" x-model="target_type"
                                   class="sr-only peer" {{ old('target_type', $scenario->target_type) == $typeKey ? 'checked' : '' }}>
                            <div class="border-2 border-gray-200 rounded-lg p-3 text-center transition
                                        peer-checked:border-indigo-500 peer-checked:bg-indigo-50 hover:border-gray-300">
                                <p class="text-sm font-medium text-gray-700">{{ $typeLabel }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('target_type') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div x-show="target_type === 'customer_type'" x-cloak>
                <label for="customer_type" class="block text-sm font-medium text-gray-700 mb-1">Müşteri Tipi</label>
                <select name="customer_type" id="customer_type"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Müşteri Tipi Seçin</option>
                    @foreach($customerTypes as $ctKey => $ctLabel)
                        <option value="{{ $ctKey }}" {{ old('customer_type', $scenario->customer_type) == $ctKey ? 'selected' : '' }}>{{ $ctLabel }}</option>
                    @endforeach
                </select>
                @error('customer_type') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div x-show="target_type === 'segment'" x-cloak>
                <label for="segment_id" class="block text-sm font-medium text-gray-700 mb-1">Müşteri Segmenti</label>
                <select name="segment_id" id="segment_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Segment Seçin</option>
                    @foreach($segments as $segment)
                        <option value="{{ $segment->id }}" {{ old('segment_id', $scenario->segment_id) == $segment->id ? 'selected' : '' }}>{{ $segment->name }}</option>
                    @endforeach
                </select>
                @error('segment_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div x-show="target_type === 'all'" x-cloak>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 flex items-start gap-2">
                    <i class="fas fa-exclamation-triangle text-yellow-500 mt-0.5"></i>
                    <p class="text-sm text-yellow-700">Bu senaryo tetiklendiğinde tüm müşterilere SMS gönderilecektir.</p>
                </div>
            </div>

            <div x-show="target_type === 'manual'" x-cloak>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 flex items-start gap-2">
                    <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                    <p class="text-sm text-blue-700">Manuel hedef seçildiğinde, telefon numaraları elle girilecektir.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Zamanlama --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800">Zamanlama</h3>
        </div>
        <div class="p-6 space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Zamanlama Tipi <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach($scheduleTypes as $schKey => $schLabel)
                        <label class="relative cursor-pointer">
                            <input type="radio" name="schedule_type" value="{{ $schKey }}" x-model="schedule_type"
                                   class="sr-only peer" {{ old('schedule_type', $scenario->schedule_type) == $schKey ? 'checked' : '' }}>
                            <div class="border-2 border-gray-200 rounded-lg p-3 text-center transition
                                        peer-checked:border-indigo-500 peer-checked:bg-indigo-50 hover:border-gray-300">
                                <p class="text-sm font-medium text-gray-700">{{ $schLabel }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('schedule_type') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div x-show="schedule_type === 'delayed'" x-cloak>
                <label for="delay_minutes" class="block text-sm font-medium text-gray-700 mb-1">Gecikme Süresi (Dakika)</label>
                <input type="number" name="delay_minutes" id="delay_minutes" value="{{ old('delay_minutes', $scenario->delay_minutes ?? 5) }}" min="1"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                @error('delay_minutes') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div x-show="schedule_type === 'scheduled'" x-cloak>
                <label for="send_time" class="block text-sm font-medium text-gray-700 mb-1">Gönderim Zamanı</label>
                <input type="time" name="send_time" id="send_time" value="{{ old('send_time', $scenario->send_time) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                @error('send_time') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div x-show="schedule_type === 'recurring'" x-cloak class="space-y-4">
                <div>
                    <label for="cron_expression" class="block text-sm font-medium text-gray-700 mb-1">Cron İfadesi</label>
                    <input type="text" name="cron_expression" id="cron_expression" value="{{ old('cron_expression', $scenario->cron_expression ?? '0 9 * * *') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="0 9 * * *">
                    <p class="text-xs text-gray-400 mt-1">Örnek: "0 9 * * *" = Her gün saat 09:00</p>
                    @error('cron_expression') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="recurring_send_time" class="block text-sm font-medium text-gray-700 mb-1">Gönderim Saati</label>
                    <input type="time" name="send_time" id="recurring_send_time" value="{{ old('send_time', $scenario->send_time) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('send_time') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Koşullar --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100"
         x-show="['sale_completed', 'payment_received', 'invoice_created', 'customer_inactive'].includes(trigger_event)" x-cloak>
        <div class="p-6 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800">Koşullar</h3>
            <p class="text-sm text-gray-500 mt-1">Senaryonun tetiklenmesi için ek koşullar</p>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-5">
            <div x-show="['sale_completed', 'payment_received', 'invoice_created'].includes(trigger_event)" x-cloak>
                <label for="min_amount" class="block text-sm font-medium text-gray-700 mb-1">Minimum Tutar (₺)</label>
                <input type="number" name="min_amount" id="min_amount" value="{{ old('min_amount', $scenario->min_amount) }}" min="0" step="0.01"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                @error('min_amount') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div x-show="['sale_completed', 'payment_received', 'invoice_created'].includes(trigger_event)" x-cloak>
                <label for="max_amount" class="block text-sm font-medium text-gray-700 mb-1">Maksimum Tutar (₺)</label>
                <input type="number" name="max_amount" id="max_amount" value="{{ old('max_amount', $scenario->max_amount) }}" min="0" step="0.01"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                @error('max_amount') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div x-show="trigger_event === 'customer_inactive'" x-cloak>
                <label for="inactive_days" class="block text-sm font-medium text-gray-700 mb-1">İnaktif Gün Sayısı</label>
                <input type="number" name="inactive_days" id="inactive_days" value="{{ old('inactive_days', $scenario->inactive_days ?? 30) }}" min="1"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                @error('inactive_days') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    {{-- Aktif & Kaydet --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                           {{ old('is_active', $scenario->is_active) ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:ring-2 peer-focus:ring-indigo-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                </label>
                <span class="text-sm font-medium text-gray-700">Senaryoyu Aktif Et</span>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('sms.scenarios.index') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                    İptal
                </a>
                <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-save mr-2"></i> Değişiklikleri Kaydet
                </button>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
function scenarioForm() {
    return {
        trigger_event: '{{ old('trigger_event', $scenario->trigger_event ?? '') }}',
        target_type: '{{ old('target_type', $scenario->target_type ?? '') }}',
        schedule_type: '{{ old('schedule_type', $scenario->schedule_type ?? 'immediate') }}',
    }
}
</script>
@endpush
@endsection
