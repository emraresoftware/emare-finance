// Para formatı: 1234.56 → "1.234,56 ₺"
export function formatMoney(value, showSymbol = true) {
  if (value === null || value === undefined) return '0,00' + (showSymbol ? ' ₺' : '');
  const num = parseFloat(value);
  const formatted = num
    .toFixed(2)
    .replace('.', ',')
    .replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  return showSymbol ? `${formatted} ₺` : formatted;
}

// Kısa para formatı: 1500 → "1,5K ₺"
export function formatMoneyShort(value) {
  const num = parseFloat(value) || 0;
  if (num >= 1000000) return `${(num / 1000000).toFixed(1).replace('.', ',')}M ₺`;
  if (num >= 1000) return `${(num / 1000).toFixed(1).replace('.', ',')}K ₺`;
  return formatMoney(num);
}

// Tarih formatı: "2026-03-01" → "01 Mar 2026"
export function formatDate(dateStr) {
  if (!dateStr) return '-';
  const date = new Date(dateStr);
  const months = ['Oca', 'Şub', 'Mar', 'Nis', 'May', 'Haz', 'Tem', 'Ağu', 'Eyl', 'Eki', 'Kas', 'Ara'];
  const day = String(date.getDate()).padStart(2, '0');
  return `${day} ${months[date.getMonth()]} ${date.getFullYear()}`;
}

// Tarih + saat: "01 Mar 2026 14:30"
export function formatDateTime(dateStr) {
  if (!dateStr) return '-';
  const date = new Date(dateStr);
  const hours = String(date.getHours()).padStart(2, '0');
  const mins = String(date.getMinutes()).padStart(2, '0');
  return `${formatDate(dateStr)} ${hours}:${mins}`;
}

// Göreli tarih: "3 dakika önce", "2 saat önce"
export function timeAgo(dateStr) {
  if (!dateStr) return '-';
  const now = new Date();
  const date = new Date(dateStr);
  const diff = Math.floor((now - date) / 1000);

  if (diff < 60) return 'Az önce';
  if (diff < 3600) return `${Math.floor(diff / 60)} dk önce`;
  if (diff < 86400) return `${Math.floor(diff / 3600)} saat önce`;
  if (diff < 604800) return `${Math.floor(diff / 86400)} gün önce`;
  return formatDate(dateStr);
}

// Ödeme yöntemi etiketi
export function paymentMethodLabel(method) {
  const labels = {
    cash: 'Nakit',
    card: 'Kredi Kartı',
    mixed: 'Karışık',
    credit: 'Veresiye',
  };
  return labels[method] || method;
}

// Ödeme yöntemi rengi
export function paymentMethodColor(method) {
  const colors = {
    cash: '#10B981',
    card: '#3B82F6',
    mixed: '#8B5CF6',
    credit: '#F59E0B',
  };
  return colors[method] || '#6B7280';
}

// Stok durumu
export function stockStatus(qty, critical) {
  if (qty <= 0) return { label: 'Tükendi', color: '#EF4444', bg: '#FEE2E2' };
  if (critical > 0 && qty <= critical) return { label: 'Düşük', color: '#F59E0B', bg: '#FEF3C7' };
  return { label: 'Yeterli', color: '#10B981', bg: '#D1FAE5' };
}

// Sayıyı kısalt: 1500 → "1,5K"
export function formatNumber(num) {
  if (num === null || num === undefined) return '0';
  num = parseFloat(num);
  if (num >= 1000000) return `${(num / 1000000).toFixed(1).replace('.', ',')}M`;
  if (num >= 1000) return `${(num / 1000).toFixed(1).replace('.', ',')}K`;
  return num.toLocaleString('tr-TR');
}
