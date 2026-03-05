/**
 * ═══════════════════════════════════════════════════════════════
 *  Emare Finance — Hardware Driver Manager
 *  Tak-Çalıştır Donanım Sürücü Kütüphanesi
 * ═══════════════════════════════════════════════════════════════
 *
 *  Desteklenen Cihazlar:
 *  • Termal Fiş Yazıcı (ESC/POS, Star, Citizen)
 *  • Etiket Yazıcı (ZPL, TSPL, EPL)
 *  • A4 Yazıcı — Lazer/Mürekkep (IPP/System Print)
 *  • Barkod Okuyucu (Klavye Wedge, HID, Seri)
 *  • Elektronik Terazi (CAS, Dibal, DIGI, Mettler)
 *  • Kasa Çekmecesi (ESC/POS Kick, RJ11)
 *  • Müşteri Ekranı (Pole Display, VFD)
 *
 *  Bağlantı Yöntemleri:
 *  • WebUSB API — USB cihazlara doğrudan erişim
 *  • Web Serial API — Seri port (RS232/COM)
 *  • Ağ (TCP/IP) — network yazıcılar (proxy üzerinden)
 *  • Bluetooth — Web Bluetooth API
 *  • System Print — Tarayıcı print dialog (A4 yazıcılar)
 *
 *  Kullanım:
 *    const hw = new HardwareManager();
 *    await hw.init();
 *    await hw.printReceipt(receiptData);
 *    await hw.printA4(documentData);
 *    const weight = await hw.readScale();
 *    await hw.openCashDrawer();
 */

// ─────────────────────────────────────────────────────────────
// ESC/POS Komut Sabitleri
// ─────────────────────────────────────────────────────────────
const ESC = 0x1B;
const GS  = 0x1D;
const FS  = 0x1C;
const DLE = 0x10;
const EOT = 0x04;
const LF  = 0x0A;
const CR  = 0x0D;
const HT  = 0x09;
const FF  = 0x0C;

const ESCPOS = {
    // Başlatma & Sıfırlama
    INIT:           [ESC, 0x40],
    // Metin Hizalama
    ALIGN_LEFT:     [ESC, 0x61, 0x00],
    ALIGN_CENTER:   [ESC, 0x61, 0x01],
    ALIGN_RIGHT:    [ESC, 0x61, 0x02],
    // Yazı Biçimi
    BOLD_ON:        [ESC, 0x45, 0x01],
    BOLD_OFF:       [ESC, 0x45, 0x00],
    UNDERLINE_ON:   [ESC, 0x2D, 0x01],
    UNDERLINE_OFF:  [ESC, 0x2D, 0x00],
    DOUBLE_ON:      [GS, 0x21, 0x11],   // Çift boyut (genişlik+yükseklik)
    DOUBLE_OFF:     [GS, 0x21, 0x00],
    DOUBLE_W_ON:    [GS, 0x21, 0x10],   // Sadece çift genişlik
    DOUBLE_H_ON:    [GS, 0x21, 0x01],   // Sadece çift yükseklik
    FONT_A:         [ESC, 0x4D, 0x00],  // 12x24
    FONT_B:         [ESC, 0x4D, 0x01],  // 9x17
    // Kağıt Kesme
    CUT_FULL:       [GS, 0x56, 0x00],
    CUT_PARTIAL:    [GS, 0x56, 0x01],
    CUT_FEED:       [GS, 0x56, 0x42, 0x03], // 3 satır besle + kes
    // Satır Besleme
    FEED_1:         [LF],
    FEED_3:         [ESC, 0x64, 0x03],
    FEED_5:         [ESC, 0x64, 0x05],
    // Kasa Çekmecesi
    KICK_DRAWER_1:  [ESC, 0x70, 0x00, 0x19, 0xFA], // Pin 2
    KICK_DRAWER_2:  [ESC, 0x70, 0x01, 0x19, 0xFA], // Pin 5
    // Barkod
    BARCODE_HEIGHT: (h) => [GS, 0x68, h],
    BARCODE_WIDTH:  (w) => [GS, 0x77, w],
    BARCODE_POS:    (p) => [GS, 0x48, p],  // 0=no print, 1=above, 2=below, 3=both
    BARCODE_CODE128: (data) => {
        const d = new TextEncoder().encode(data);
        return [GS, 0x6B, 0x49, d.length, ...d];
    },
    BARCODE_EAN13: (data) => {
        const d = new TextEncoder().encode(data);
        return [GS, 0x6B, 0x02, ...d, 0x00];
    },
    // QR Kod
    QR_MODEL:      [GS, 0x28, 0x6B, 0x04, 0x00, 0x31, 0x41, 0x32, 0x00],
    QR_SIZE:       (s) => [GS, 0x28, 0x6B, 0x03, 0x00, 0x31, 0x43, s],
    QR_ERROR:      [GS, 0x28, 0x6B, 0x03, 0x00, 0x31, 0x45, 0x31], // L=48,M=49,Q=50,H=51
    QR_STORE:      (data) => {
        const d = new TextEncoder().encode(data);
        const len = d.length + 3;
        return [GS, 0x28, 0x6B, len & 0xFF, (len >> 8) & 0xFF, 0x31, 0x50, 0x30, ...d];
    },
    QR_PRINT:      [GS, 0x28, 0x6B, 0x03, 0x00, 0x31, 0x51, 0x30],
    // Türkçe Karakter Seti (WPC1254)
    CHARSET_TR:    [ESC, 0x74, 0x25],  // Code page 37 = WPC1254 (Türkçe)
    INTL_TR:       [ESC, 0x52, 0x0C],  // Türkiye uluslararası karakter seti
    // Durum Sorgu
    STATUS_QUERY:  [DLE, EOT, 0x01],
};

// ─────────────────────────────────────────────────────────────
// Türkçe Karakter Dönüşüm Tablosu (UTF-8 → WPC1254)
// ─────────────────────────────────────────────────────────────
const TR_CHAR_MAP = {
    'ç': 0xE7, 'Ç': 0xC7,
    'ğ': 0xF0, 'Ğ': 0xD0,
    'ı': 0xFD, 'İ': 0xDD,
    'ö': 0xF6, 'Ö': 0xD6,
    'ş': 0xFE, 'Ş': 0xDE,
    'ü': 0xFC, 'Ü': 0xDC,
};

function encodeTurkish(text) {
    const bytes = [];
    for (const ch of text) {
        if (TR_CHAR_MAP[ch] !== undefined) {
            bytes.push(TR_CHAR_MAP[ch]);
        } else {
            const code = ch.charCodeAt(0);
            bytes.push(code < 256 ? code : 0x3F); // bilinmeyenler '?'
        }
    }
    return bytes;
}


// ═══════════════════════════════════════════════════════════════
// ANA SINIF: HardwareManager
// ═══════════════════════════════════════════════════════════════
class HardwareManager {
    constructor(options = {}) {
        this.devices = new Map();
        this.activeConnections = new Map();
        this.apiBase = options.apiBase || '/api/hardware';
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        // Varsayılan ayarlar
        this.settings = {
            paperWidth: options.paperWidth || 80,
            charWidth: options.charWidth || 48,
            encoding: options.encoding || 'WPC1254',
            ...options,
        };

        // Event sistemi
        this._listeners = {};
    }

    // ── Event Sistemi ──
    on(event, callback) {
        if (!this._listeners[event]) this._listeners[event] = [];
        this._listeners[event].push(callback);
        return this;
    }

    emit(event, data) {
        (this._listeners[event] || []).forEach(cb => cb(data));
    }

    // ── Başlatma ──
    async init() {
        // Kayıtlı cihazları sunucudan yükle
        try {
            const response = await fetch(this.apiBase + '/devices', {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrfToken }
            });
            if (response.ok) {
                const data = await response.json();
                (data.devices || []).forEach(d => this.devices.set(d.id, d));
            }
        } catch (e) {
            console.warn('[HW] Sunucu bağlantısı yok, yerel modda çalışılıyor', e);
        }

        // WebUSB cihaz bağlantı/koparma olaylarını dinle
        if (navigator.usb) {
            navigator.usb.addEventListener('connect', (e) => this._onUsbConnect(e));
            navigator.usb.addEventListener('disconnect', (e) => this._onUsbDisconnect(e));
        }

        this.emit('initialized', { deviceCount: this.devices.size });
        return this;
    }

    // ── API Yardımcıları ──
    async _apiPost(endpoint, body) {
        const res = await fetch(this.apiBase + endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken,
            },
            body: JSON.stringify(body),
        });
        return res.json();
    }


    // ═══════════════════════════════════════════════════════════
    // USB BAĞLANTI YÖNETİMİ (WebUSB)
    // ═══════════════════════════════════════════════════════════

    /**
     * USB cihaz seçme diyaloğunu aç ve bağlan.
     */
    async connectUSB(deviceType = null) {
        if (!navigator.usb) {
            throw new Error('WebUSB desteklenmiyor. Chrome/Edge tarayıcı gerekli.');
        }

        let filters = [];
        if (deviceType) {
            const known = (window.__hardwareConfig?.known_devices || [])
                .filter(d => d.type === deviceType);
            filters = known.map(d => ({
                vendorId: parseInt(d.vendor_id, 16),
                productId: parseInt(d.product_id, 16),
            }));
        }

        try {
            const usbDevice = await navigator.usb.requestDevice({
                filters: filters.length > 0 ? filters : undefined
            });

            await usbDevice.open();

            // Arayüz seçimi
            if (usbDevice.configuration === null) {
                await usbDevice.selectConfiguration(1);
            }

            const iface = usbDevice.configuration.interfaces[0];
            await usbDevice.claimInterface(iface.interfaceNumber);

            // Endpoint bulma
            const outEndpoint = iface.alternate.endpoints.find(e => e.direction === 'out');
            const inEndpoint = iface.alternate.endpoints.find(e => e.direction === 'in');

            const connId = `usb_${usbDevice.vendorId}_${usbDevice.productId}`;
            const conn = {
                type: 'usb',
                device: usbDevice,
                outEndpoint: outEndpoint?.endpointNumber,
                inEndpoint: inEndpoint?.endpointNumber,
                interfaceNumber: iface.interfaceNumber,
            };

            this.activeConnections.set(connId, conn);

            // Bilinen cihaz mı kontrol et
            const vendorHex = usbDevice.vendorId.toString(16).padStart(4, '0');
            const productHex = usbDevice.productId.toString(16).padStart(4, '0');
            const known = (window.__hardwareConfig?.known_devices || [])
                .find(d => d.vendor_id === vendorHex && d.product_id === productHex);

            const deviceInfo = {
                connId,
                vendorId: vendorHex,
                productId: productHex,
                name: known?.name || usbDevice.productName || 'Bilinmeyen Cihaz',
                type: known?.type || deviceType || 'unknown',
                protocol: known?.protocol || 'escpos',
                manufacturer: usbDevice.manufacturerName,
            };

            this.emit('connected', deviceInfo);
            this._showNotification(`✅ ${deviceInfo.name} bağlandı!`, 'success');

            return deviceInfo;
        } catch (err) {
            if (err.name !== 'NotFoundError') { // Kullanıcı iptal etmediyse
                this.emit('error', { message: err.message });
                this._showNotification(`❌ USB bağlantı hatası: ${err.message}`, 'error');
            }
            throw err;
        }
    }

    /**
     * USB cihaza veri gönder.
     */
    async _usbSend(connId, data) {
        const conn = this.activeConnections.get(connId);
        if (!conn || conn.type !== 'usb') throw new Error('USB bağlantısı bulunamadı');

        const buffer = data instanceof Uint8Array ? data : new Uint8Array(data);
        await conn.device.transferOut(conn.outEndpoint, buffer);
    }

    /**
     * USB cihazdan veri oku.
     */
    async _usbRead(connId, length = 64) {
        const conn = this.activeConnections.get(connId);
        if (!conn || !conn.inEndpoint) throw new Error('USB okuma endpoint yok');

        const result = await conn.device.transferIn(conn.inEndpoint, length);
        return new Uint8Array(result.data.buffer);
    }

    _onUsbConnect(event) {
        const d = event.device;
        const vendorHex = d.vendorId.toString(16).padStart(4, '0');
        const productHex = d.productId.toString(16).padStart(4, '0');
        const known = (window.__hardwareConfig?.known_devices || [])
            .find(k => k.vendor_id === vendorHex && k.product_id === productHex);

        if (known) {
            this._showNotification(`🔌 ${known.name} algılandı! Bağlanmak için tıklayın.`, 'info');
        }
        this.emit('usb_detected', { vendorId: vendorHex, productId: productHex, known });
    }

    _onUsbDisconnect(event) {
        // Aktif bağlantıyı temizle
        const d = event.device;
        const connId = `usb_${d.vendorId}_${d.productId}`;
        if (this.activeConnections.has(connId)) {
            this.activeConnections.delete(connId);
            this._showNotification('⚠️ USB cihaz çıkarıldı', 'warning');
            this.emit('disconnected', { connId });
        }
    }


    // ═══════════════════════════════════════════════════════════
    // SERİ PORT BAĞLANTI (Web Serial API)
    // ═══════════════════════════════════════════════════════════

    /**
     * Seri port seçme diyaloğu ve bağlantı.
     */
    async connectSerial(options = {}) {
        if (!navigator.serial) {
            throw new Error('Web Serial API desteklenmiyor. Chrome/Edge gerekli.');
        }

        const port = await navigator.serial.requestPort();
        await port.open({
            baudRate: options.baudRate || 9600,
            dataBits: options.dataBits || 8,
            stopBits: options.stopBits || 1,
            parity: options.parity || 'none',
            flowControl: options.flowControl || 'none',
        });

        const connId = `serial_${Date.now()}`;
        this.activeConnections.set(connId, {
            type: 'serial',
            port: port,
            reader: null,
            writer: port.writable.getWriter(),
        });

        this.emit('connected', { connId, type: 'serial' });
        this._showNotification('✅ Seri port bağlandı!', 'success');

        return connId;
    }

    /**
     * Seri porta veri gönder.
     */
    async _serialSend(connId, data) {
        const conn = this.activeConnections.get(connId);
        if (!conn || conn.type !== 'serial') throw new Error('Seri bağlantı bulunamadı');

        const buffer = data instanceof Uint8Array ? data : new Uint8Array(data);
        await conn.writer.write(buffer);
    }

    /**
     * Seri porttan veri oku (terazi vb.).
     */
    async _serialRead(connId, timeout = 3000) {
        const conn = this.activeConnections.get(connId);
        if (!conn || conn.type !== 'serial') throw new Error('Seri bağlantı bulunamadı');

        const reader = conn.port.readable.getReader();
        let result = '';

        try {
            const timer = setTimeout(() => reader.cancel(), timeout);
            while (true) {
                const { value, done } = await reader.read();
                if (done) break;
                result += new TextDecoder().decode(value);
                if (result.includes('\r') || result.includes('\n')) break;
            }
            clearTimeout(timer);
        } finally {
            reader.releaseLock();
        }

        return result.trim();
    }


    // ═══════════════════════════════════════════════════════════
    // FİŞ YAZICI (ESC/POS Termal Yazıcı)
    // ═══════════════════════════════════════════════════════════

    /**
     * Satış fişi yazdır.
     *
     * @param {Object} receipt - Fiş verileri
     * @param {string} receipt.receiptNo - Fiş numarası
     * @param {string} receipt.date - Tarih
     * @param {string} receipt.cashier - Kasiyer
     * @param {string} receipt.customer - Müşteri adı
     * @param {Array}  receipt.items - [{name, qty, price, total, discount}]
     * @param {number} receipt.subtotal
     * @param {number} receipt.tax
     * @param {number} receipt.discount
     * @param {number} receipt.total
     * @param {string} receipt.paymentMethod
     */
    async printReceipt(receipt, connId = null) {
        const conn = connId || this._findConnection('receipt_printer');
        const charW = this.settings.charWidth;

        let cmd = [];

        // Başlatma ve Türkçe karakter seti
        cmd.push(...ESCPOS.INIT);
        cmd.push(...ESCPOS.CHARSET_TR);
        cmd.push(...ESCPOS.INTL_TR);

        // ── Firma Başlığı ──
        cmd.push(...ESCPOS.ALIGN_CENTER);
        cmd.push(...ESCPOS.DOUBLE_ON);
        cmd.push(...encodeTurkish(receipt.companyName || 'Emare Finance'));
        cmd.push(...ESCPOS.FEED_1);
        cmd.push(...ESCPOS.DOUBLE_OFF);

        if (receipt.companySlogan) {
            cmd.push(...ESCPOS.FONT_B);
            cmd.push(...encodeTurkish(receipt.companySlogan));
            cmd.push(...ESCPOS.FEED_1);
            cmd.push(...ESCPOS.FONT_A);
        }

        // Başlık ek satırları
        (receipt.headerLines || []).forEach(line => {
            cmd.push(...encodeTurkish(line));
            cmd.push(...ESCPOS.FEED_1);
        });

        // Ayırıcı çizgi
        cmd.push(...ESCPOS.ALIGN_LEFT);
        cmd.push(...encodeTurkish('─'.repeat(charW)));
        cmd.push(...ESCPOS.FEED_1);

        // ── Fiş Bilgileri ──
        cmd.push(...encodeTurkish(this._padLine('Fiş No:', receipt.receiptNo || '-', charW)));
        cmd.push(...ESCPOS.FEED_1);
        cmd.push(...encodeTurkish(this._padLine('Tarih:', receipt.date || new Date().toLocaleString('tr-TR'), charW)));
        cmd.push(...ESCPOS.FEED_1);
        if (receipt.cashier) {
            cmd.push(...encodeTurkish(this._padLine('Kasiyer:', receipt.cashier, charW)));
            cmd.push(...ESCPOS.FEED_1);
        }
        if (receipt.customer && receipt.customer !== 'Genel') {
            cmd.push(...encodeTurkish(this._padLine('Müşteri:', receipt.customer, charW)));
            cmd.push(...ESCPOS.FEED_1);
        }

        cmd.push(...encodeTurkish('═'.repeat(charW)));
        cmd.push(...ESCPOS.FEED_1);

        // ── Satış Kalemleri ──
        cmd.push(...ESCPOS.BOLD_ON);
        cmd.push(...encodeTurkish(this._padColumns(['Ürün', 'Adet', 'Fiyat', 'Toplam'], [charW - 20, 5, 7, 8], charW)));
        cmd.push(...ESCPOS.FEED_1);
        cmd.push(...ESCPOS.BOLD_OFF);
        cmd.push(...encodeTurkish('─'.repeat(charW)));
        cmd.push(...ESCPOS.FEED_1);

        (receipt.items || []).forEach(item => {
            // Ürün adı (uzunsa birden fazla satır)
            const name = (item.name || '').substring(0, charW - 22);
            const qty = String(item.qty || item.quantity || 1);
            const price = this._formatMoney(item.price || item.unit_price || 0);
            const total = this._formatMoney(item.total || 0);

            cmd.push(...encodeTurkish(this._padColumns([name, qty, price, total], [charW - 20, 5, 7, 8], charW)));
            cmd.push(...ESCPOS.FEED_1);

            if (item.discount > 0) {
                cmd.push(...encodeTurkish(`  İndirim: -₺${this._formatMoney(item.discount)}`));
                cmd.push(...ESCPOS.FEED_1);
            }
        });

        cmd.push(...encodeTurkish('═'.repeat(charW)));
        cmd.push(...ESCPOS.FEED_1);

        // ── Toplamlar ──
        cmd.push(...encodeTurkish(this._padLine('Ara Toplam:', `₺${this._formatMoney(receipt.subtotal || 0)}`, charW)));
        cmd.push(...ESCPOS.FEED_1);

        if (receipt.tax > 0) {
            cmd.push(...encodeTurkish(this._padLine('KDV:', `₺${this._formatMoney(receipt.tax)}`, charW)));
            cmd.push(...ESCPOS.FEED_1);
        }

        if (receipt.discount > 0) {
            cmd.push(...encodeTurkish(this._padLine('İndirim:', `-₺${this._formatMoney(receipt.discount)}`, charW)));
            cmd.push(...ESCPOS.FEED_1);
        }

        cmd.push(...ESCPOS.FEED_1);
        cmd.push(...ESCPOS.BOLD_ON);
        cmd.push(...ESCPOS.DOUBLE_W_ON);
        cmd.push(...encodeTurkish(this._padLine('TOPLAM:', `₺${this._formatMoney(receipt.total || 0)}`, Math.floor(charW / 2))));
        cmd.push(...ESCPOS.FEED_1);
        cmd.push(...ESCPOS.DOUBLE_OFF);
        cmd.push(...ESCPOS.BOLD_OFF);

        // Ödeme yöntemi
        cmd.push(...encodeTurkish(this._padLine('Ödeme:', receipt.paymentMethod || 'Nakit', charW)));
        cmd.push(...ESCPOS.FEED_1);
        cmd.push(...encodeTurkish('─'.repeat(charW)));
        cmd.push(...ESCPOS.FEED_1);

        // ── Barkod (Fiş No) ──
        if (receipt.receiptNo) {
            cmd.push(...ESCPOS.ALIGN_CENTER);
            cmd.push(...ESCPOS.BARCODE_HEIGHT(50));
            cmd.push(...ESCPOS.BARCODE_WIDTH(2));
            cmd.push(...ESCPOS.BARCODE_POS(2)); // Barkod altında numara
            cmd.push(...ESCPOS.BARCODE_CODE128(receipt.receiptNo));
            cmd.push(...ESCPOS.FEED_1);
        }

        // ── Alt Bilgi ──
        cmd.push(...ESCPOS.ALIGN_CENTER);
        cmd.push(...ESCPOS.FONT_B);
        (receipt.footerLines || ['Teşekkür ederiz!', 'İyi günler dileriz.']).forEach(line => {
            cmd.push(...encodeTurkish(line));
            cmd.push(...ESCPOS.FEED_1);
        });
        cmd.push(...ESCPOS.FONT_A);

        // Kağıt kes
        cmd.push(...ESCPOS.FEED_5);
        cmd.push(...ESCPOS.CUT_FEED);

        // Gönder
        await this._send(conn, new Uint8Array(cmd));
        this._showNotification('🧾 Fiş yazdırıldı!', 'success');
    }


    // ═══════════════════════════════════════════════════════════
    // ETİKET YAZICI
    // ═══════════════════════════════════════════════════════════

    /**
     * Barkodlu ürün etiketi yazdır.
     *
     * @param {Array} labels - [{name, barcode, price, unit}]
     * @param {Object} settings - {width, height, gap, columns, protocol}
     */
    async printLabels(labels, settings = {}) {
        const conn = this._findConnection('label_printer') || this._findConnection('receipt_printer');
        const protocol = settings.protocol || 'tspl';

        let commands;

        switch (protocol) {
            case 'zpl':
                commands = this._buildZPLLabels(labels, settings);
                break;
            case 'tspl':
                commands = this._buildTSPLLabels(labels, settings);
                break;
            case 'escpos':
                commands = this._buildESCPOSLabels(labels, settings);
                break;
            default:
                commands = this._buildTSPLLabels(labels, settings);
        }

        await this._send(conn, commands);
        this._showNotification(`🏷️ ${labels.length} etiket yazdırıldı!`, 'success');
    }

    _buildTSPLLabels(labels, settings) {
        const w = settings.width || 50;
        const h = settings.height || 30;
        const gap = settings.gap || 3;

        let tspl = '';
        tspl += `SIZE ${w} mm, ${h} mm\r\n`;
        tspl += `GAP ${gap} mm, 0 mm\r\n`;
        tspl += `SPEED 4\r\n`;
        tspl += `DENSITY 8\r\n`;
        tspl += `DIRECTION 1\r\n`;
        tspl += `CODEPAGE UTF-8\r\n`;

        labels.forEach(label => {
            tspl += `CLS\r\n`;
            // Ürün adı
            tspl += `TEXT 10,10,"3",0,1,1,"${(label.name || '').substring(0, 30)}"\r\n`;
            // Fiyat
            if (label.price !== undefined) {
                const priceStr = `${Number(label.price).toFixed(2).replace('.', ',')} TL`;
                tspl += `TEXT 10,${h * 4 - 80},"4",0,1,1,"${priceStr}"\r\n`;
            }
            // Barkod
            if (label.barcode) {
                tspl += `BARCODE 10,${Math.round(h * 4 / 2) - 20},"128",60,1,0,2,4,"${label.barcode}"\r\n`;
            }
            tspl += `PRINT 1,1\r\n`;
        });

        return new TextEncoder().encode(tspl);
    }

    _buildZPLLabels(labels, settings) {
        let zpl = '';
        labels.forEach(label => {
            zpl += '^XA\r\n';                  // Etiket başla
            zpl += '^CI28\r\n';                // UTF-8
            zpl += '^FO20,20^A0N,30,30\r\n';  // Ürün adı konumu
            zpl += `^FD${(label.name || '').substring(0, 30)}^FS\r\n`;
            // Fiyat
            if (label.price !== undefined) {
                zpl += `^FO20,60^A0N,40,40\r\n`;
                zpl += `^FD${Number(label.price).toFixed(2).replace('.', ',')} TL^FS\r\n`;
            }
            // Barkod
            if (label.barcode) {
                zpl += `^FO20,110^BY2\r\n`;
                zpl += `^BCN,60,Y,N,N\r\n`;
                zpl += `^FD${label.barcode}^FS\r\n`;
            }
            zpl += '^XZ\r\n';                  // Etiket bitir
        });

        return new TextEncoder().encode(zpl);
    }

    _buildESCPOSLabels(labels, settings) {
        let cmd = [];
        const charW = this.settings.charWidth;

        labels.forEach(label => {
            cmd.push(...ESCPOS.INIT);
            cmd.push(...ESCPOS.CHARSET_TR);
            cmd.push(...ESCPOS.ALIGN_CENTER);

            // Ürün adı
            cmd.push(...ESCPOS.BOLD_ON);
            cmd.push(...encodeTurkish(label.name || ''));
            cmd.push(...ESCPOS.FEED_1);
            cmd.push(...ESCPOS.BOLD_OFF);

            // Fiyat
            if (label.price !== undefined) {
                cmd.push(...ESCPOS.DOUBLE_ON);
                cmd.push(...encodeTurkish(`₺${Number(label.price).toFixed(2).replace('.', ',')}`));
                cmd.push(...ESCPOS.FEED_1);
                cmd.push(...ESCPOS.DOUBLE_OFF);
            }

            // Barkod
            if (label.barcode) {
                cmd.push(...ESCPOS.BARCODE_HEIGHT(40));
                cmd.push(...ESCPOS.BARCODE_WIDTH(2));
                cmd.push(...ESCPOS.BARCODE_POS(2));
                if (label.barcode.length === 13) {
                    cmd.push(...ESCPOS.BARCODE_EAN13(label.barcode));
                } else {
                    cmd.push(...ESCPOS.BARCODE_CODE128(label.barcode));
                }
                cmd.push(...ESCPOS.FEED_1);
            }

            cmd.push(...ESCPOS.FEED_3);
            cmd.push(...ESCPOS.CUT_FEED);
        });

        return new Uint8Array(cmd);
    }


    // ═══════════════════════════════════════════════════════════
    // TERAZİ OKUMA (Scale)
    // ═══════════════════════════════════════════════════════════

    /**
     * Teraziden ağırlık oku.
     *
     * @param {string} protocol - cas, dibal, digi, mettler, custom
     * @param {string} connId - Bağlantı ID'si
     * @returns {Object} { weight: number, unit: string, stable: boolean }
     */
    async readScale(protocol = 'cas', connId = null) {
        const conn = connId || this._findConnection('scale');
        if (!conn) throw new Error('Terazi bağlantısı bulunamadı. Önce bağlanın.');

        const protocols = {
            cas:     { cmd: 'W\r\n', pattern: /ST,(?:GS|NT),\s*(\d+\.?\d*)\s*(kg|g)/i },
            dibal:   { cmd: '\x05', pattern: /(\d+\.?\d*)/i },
            digi:    { cmd: 'R\r\n', pattern: /(\d+\.?\d*)\s*(kg|g|lb)/i },
            mettler: { cmd: 'S\r\n', pattern: /S\s+S\s+(\d+\.?\d*)\s*(kg|g)/i },
            custom:  { cmd: '\r\n', pattern: /(\d+\.?\d*)/ },
        };

        const p = protocols[protocol] || protocols.custom;

        // Komut gönder
        await this._serialSend(conn, new TextEncoder().encode(p.cmd));

        // Yanıt oku
        const response = await this._serialRead(conn, 3000);

        // Ağırlık çıkar
        const match = response.match(p.pattern);
        if (!match) {
            throw new Error(`Terazi yanıtı okunamadı: "${response}"`);
        }

        const weight = parseFloat(match[1]);
        const unit = match[2] || 'kg';
        const stable = response.includes('ST') || !response.includes('US');

        this.emit('scale_read', { weight, unit, stable });
        return { weight, unit, stable };
    }

    /**
     * Terazi sürekli okuma (polling).
     */
    startScalePolling(callback, interval = 500, protocol = 'cas', connId = null) {
        this._scalePolling = setInterval(async () => {
            try {
                const reading = await this.readScale(protocol, connId);
                callback(reading);
            } catch (e) {
                // Sessiz devam et
            }
        }, interval);
        return this._scalePolling;
    }

    stopScalePolling() {
        if (this._scalePolling) {
            clearInterval(this._scalePolling);
            this._scalePolling = null;
        }
    }


    // ═══════════════════════════════════════════════════════════
    // KASA ÇEKMECESİ
    // ═══════════════════════════════════════════════════════════

    /**
     * Kasa çekmecesini aç.
     * Genellikle yazıcı üzerinden RJ11 bağlantılı.
     */
    async openCashDrawer(pin = 1, connId = null) {
        const conn = connId || this._findConnection('cash_drawer') || this._findConnection('receipt_printer');
        if (!conn) throw new Error('Kasa çekmecesi veya yazıcı bağlantısı bulunamadı.');

        const cmd = pin === 2 ? ESCPOS.KICK_DRAWER_2 : ESCPOS.KICK_DRAWER_1;
        await this._send(conn, new Uint8Array(cmd));

        this._showNotification('💰 Kasa çekmecesi açıldı!', 'success');
        this.emit('drawer_opened', { pin });
    }


    // ═══════════════════════════════════════════════════════════
    // BARKOD OKUYUCU
    // ═══════════════════════════════════════════════════════════

    /**
     * Barkod okuyucu listener başlat.
     * Klavye wedge modundaki okuyucular için.
     */
    startBarcodeListener(callback, options = {}) {
        const targetInput = options.targetInput || null;
        const minLength = options.minLength || 4;
        const maxDelay = options.maxDelay || 50; // ms — okuyucu hızlı yazar

        let buffer = '';
        let lastKeyTime = 0;

        const handler = (e) => {
            // Eğer belirli bir input'a odaklanılmışsa, sadece orada çalış
            if (targetInput && document.activeElement !== targetInput) return;

            const now = Date.now();

            // Uzun aralıkla gelen tuşlar klavyeden yazılıyordur
            if (now - lastKeyTime > maxDelay && buffer.length > 0) {
                buffer = '';
            }
            lastKeyTime = now;

            if (e.key === 'Enter') {
                if (buffer.length >= minLength) {
                    e.preventDefault();
                    e.stopPropagation();
                    callback(buffer.trim());
                    this.emit('barcode_scanned', { barcode: buffer.trim() });
                }
                buffer = '';
            } else if (e.key.length === 1) {
                buffer += e.key;
            }
        };

        document.addEventListener('keydown', handler, true);
        this._barcodeHandler = handler;

        this._showNotification('📷 Barkod okuyucu dinleniyor...', 'info');
        return handler;
    }

    stopBarcodeListener() {
        if (this._barcodeHandler) {
            document.removeEventListener('keydown', this._barcodeHandler, true);
            this._barcodeHandler = null;
        }
    }


    // ═══════════════════════════════════════════════════════════
    // MÜŞTERİ EKRANI (Pole Display / VFD)
    // ═══════════════════════════════════════════════════════════

    /**
     * Müşteri ekranına mesaj gönder.
     */
    async displayMessage(line1, line2 = '', connId = null) {
        const conn = connId || this._findConnection('customer_display');
        if (!conn) return; // Sessiz devam

        const cmd = [
            0x1B, 0x40,              // Init
            0x1B, 0x51, 0x41,        // Overwrite mode, line 1
            ...encodeTurkish(line1.padEnd(20).substring(0, 20)),
            0x0D,
            0x1B, 0x51, 0x42,        // Line 2
            ...encodeTurkish(line2.padEnd(20).substring(0, 20)),
            0x0D,
        ];

        await this._send(conn, new Uint8Array(cmd));
    }


    // ═══════════════════════════════════════════════════════════
    // A4 YAZICI (Lazer / Mürekkep — System Print)
    // ═══════════════════════════════════════════════════════════

    /**
     * A4 belge yazdır — fatura, rapor, vb.
     *
     * @param {Object} doc - Belge verileri
     * @param {string} doc.type - 'invoice', 'report', 'receipt', 'custom'
     * @param {string} doc.title - Belge başlığı
     * @param {string} doc.html - Hazır HTML içerik (type='custom' için)
     * @param {Object} doc.data - Belge verileri
     * @param {Object} options - {orientation, margin, copies, paperSize}
     */
    async printA4(doc, options = {}) {
        const orientation = options.orientation || 'portrait';
        const margin = options.margin || '10mm';
        const paperSize = options.paperSize || 'A4';

        let html = '';

        if (doc.type === 'custom' && doc.html) {
            html = doc.html;
        } else if (doc.type === 'invoice') {
            html = this._buildInvoiceHTML(doc.data, options);
        } else if (doc.type === 'report') {
            html = this._buildReportHTML(doc.data, options);
        } else if (doc.type === 'receipt') {
            html = this._buildA4ReceiptHTML(doc.data, options);
        } else {
            html = doc.html || '<p>İçerik bulunamadı</p>';
        }

        const printWindow = window.open('', '_blank', 'width=800,height=600');
        printWindow.document.write(`<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>${doc.title || 'Yazdır'}</title>
    <style>
        @page {
            size: ${paperSize} ${orientation};
            margin: ${margin};
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Arial, sans-serif; font-size: 11pt; color: #333; line-height: 1.5; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th, td { padding: 6px 8px; text-align: left; border-bottom: 1px solid #e0e0e0; font-size: 10pt; }
        th { background: #f5f5f5; font-weight: 600; border-bottom: 2px solid #ccc; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #333; }
        .header h1 { font-size: 18pt; color: #1a1a1a; }
        .header .meta { text-align: right; font-size: 9pt; color: #666; }
        .footer { margin-top: 20px; padding-top: 8px; border-top: 1px solid #ccc; font-size: 8pt; color: #999; text-align: center; }
        .total-row td { font-weight: bold; font-size: 12pt; border-top: 2px solid #333; }
        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
        }
        ${options.css || ''}
    </style>
</head>
<body>${html}</body>
</html>`);
        printWindow.document.close();

        // Yazdır ve kapat
        setTimeout(() => {
            printWindow.focus();
            printWindow.print();
            setTimeout(() => printWindow.close(), 1000);
        }, 300);

        this._showNotification('🖨️ A4 belge yazdırılıyor...', 'info');
        this.emit('a4_printed', { type: doc.type, title: doc.title });
    }

    /**
     * Fatura HTML şablonu oluştur
     */
    _buildInvoiceHTML(data, options = {}) {
        const items = data.items || [];
        const itemRows = items.map((item, i) => `
            <tr>
                <td>${i + 1}</td>
                <td>${item.name || ''}</td>
                <td class="text-center">${item.qty || 1}</td>
                <td>${item.unit || 'Adet'}</td>
                <td class="text-right">${this._formatMoney(item.price || 0)} ₺</td>
                <td class="text-right">${this._formatMoney(item.discount || 0)} ₺</td>
                <td class="text-right">${this._formatMoney(item.total || (item.qty * item.price))} ₺</td>
            </tr>
        `).join('');

        return `
        <div class="header">
            <div>
                <h1>${data.companyName || 'Emare Finance'}</h1>
                <p style="font-size:9pt;color:#666;">${data.companyAddress || ''}</p>
                <p style="font-size:9pt;color:#666;">VKN: ${data.taxId || ''}</p>
            </div>
            <div class="meta">
                <p style="font-size:14pt;font-weight:bold;">${data.documentType || 'FATURA'}</p>
                <p>No: ${data.invoiceNo || ''}</p>
                <p>Tarih: ${data.date || new Date().toLocaleDateString('tr-TR')}</p>
                <p>Vade: ${data.dueDate || '-'}</p>
            </div>
        </div>

        <table style="margin-bottom:16px;">
            <tr>
                <td style="border:none;width:50%;vertical-align:top;">
                    <strong>Müşteri / Cari:</strong><br>
                    ${data.customerName || ''}<br>
                    <span style="font-size:9pt;color:#666;">${data.customerAddress || ''}</span><br>
                    <span style="font-size:9pt;color:#666;">VKN: ${data.customerTaxId || ''}</span>
                </td>
                <td style="border:none;width:50%;vertical-align:top;text-align:right;">
                    <strong>Ödeme:</strong> ${data.paymentMethod || ''}<br>
                    <strong>Şube:</strong> ${data.branch || ''}
                </td>
            </tr>
        </table>

        <table>
            <thead>
                <tr>
                    <th style="width:30px;">#</th>
                    <th>Ürün / Hizmet</th>
                    <th class="text-center" style="width:60px;">Miktar</th>
                    <th style="width:50px;">Birim</th>
                    <th class="text-right" style="width:90px;">Birim Fiyat</th>
                    <th class="text-right" style="width:80px;">İskonto</th>
                    <th class="text-right" style="width:100px;">Tutar</th>
                </tr>
            </thead>
            <tbody>
                ${itemRows}
            </tbody>
            <tfoot>
                <tr><td colspan="6" class="text-right">Ara Toplam:</td><td class="text-right">${this._formatMoney(data.subtotal || 0)} ₺</td></tr>
                <tr><td colspan="6" class="text-right">İskonto:</td><td class="text-right">-${this._formatMoney(data.discount || 0)} ₺</td></tr>
                <tr><td colspan="6" class="text-right">KDV (%${data.taxRate || 20}):</td><td class="text-right">${this._formatMoney(data.tax || 0)} ₺</td></tr>
                <tr class="total-row"><td colspan="6" class="text-right">GENEL TOPLAM:</td><td class="text-right">${this._formatMoney(data.total || 0)} ₺</td></tr>
            </tfoot>
        </table>

        ${data.notes ? `<p style="margin-top:12px;font-size:9pt;color:#666;"><strong>Notlar:</strong> ${data.notes}</p>` : ''}

        <div class="footer">
            <p>Bu belge ${data.companyName || 'Emare Finance'} tarafından oluşturulmuştur.</p>
            <p>${new Date().toLocaleString('tr-TR')}</p>
        </div>`;
    }

    /**
     * Rapor HTML şablonu oluştur
     */
    _buildReportHTML(data, options = {}) {
        const columns = data.columns || [];
        const rows = data.rows || [];

        const headerCells = columns.map(c => `<th class="${c.align === 'right' ? 'text-right' : ''}">${c.label}</th>`).join('');
        const bodyRows = rows.map(row => {
            const cells = columns.map(c => `<td class="${c.align === 'right' ? 'text-right' : ''}">${row[c.key] ?? ''}</td>`).join('');
            return `<tr>${cells}</tr>`;
        }).join('');

        return `
        <div class="header">
            <div>
                <h1>${data.title || 'Rapor'}</h1>
                <p style="font-size:9pt;color:#666;">${data.subtitle || ''}</p>
            </div>
            <div class="meta">
                <p>${data.dateRange || new Date().toLocaleDateString('tr-TR')}</p>
                <p>${data.branch || 'Tüm Şubeler'}</p>
            </div>
        </div>

        ${data.summary ? `
        <div style="display:flex;gap:16px;margin-bottom:16px;">
            ${Object.entries(data.summary).map(([k, v]) => `
                <div style="flex:1;padding:8px 12px;background:#f5f5f5;border-radius:4px;">
                    <div style="font-size:8pt;color:#666;">${k}</div>
                    <div style="font-size:14pt;font-weight:bold;">${v}</div>
                </div>
            `).join('')}
        </div>` : ''}

        <table>
            <thead><tr>${headerCells}</tr></thead>
            <tbody>${bodyRows}</tbody>
        </table>

        <div class="footer">
            <p>Emare Finance Rapor — ${new Date().toLocaleString('tr-TR')}</p>
            <p>Toplam ${rows.length} kayıt</p>
        </div>`;
    }

    /**
     * A4 boyutunda fiş HTML şablonu
     */
    _buildA4ReceiptHTML(data, options = {}) {
        return this._buildInvoiceHTML({
            ...data,
            documentType: 'SATIŞ FİŞİ',
        }, options);
    }


    // ═══════════════════════════════════════════════════════════
    // SÜRÜCÜ KATALOĞU API ERİŞİMİ
    // ═══════════════════════════════════════════════════════════

    /**
     * Sürücü kataloğundan cihaz ara
     */
    async searchDrivers(query = '', type = null) {
        const params = new URLSearchParams();
        if (query) params.set('q', query);
        if (type) params.set('type', type);

        const res = await fetch(`${this.apiBase}/drivers?${params}`, {
            headers: { 'Accept': 'application/json' }
        });
        return (await res.json()).drivers || [];
    }

    /**
     * Sürücü istatistiklerini getir
     */
    async getDriverStats() {
        const res = await fetch(`${this.apiBase}/drivers/stats`, {
            headers: { 'Accept': 'application/json' }
        });
        return await res.json();
    }

    /**
     * Cihaz türüne göre üreticileri getir
     */
    async getDriverManufacturers(type = null) {
        const params = type ? `?type=${type}` : '';
        const res = await fetch(`${this.apiBase}/drivers/manufacturers${params}`, {
            headers: { 'Accept': 'application/json' }
        });
        return (await res.json()).manufacturers || [];
    }

    /**
     * Üreticiye göre modelleri getir
     */
    async getDriverModels(manufacturer, type = null) {
        const params = new URLSearchParams({ manufacturer });
        if (type) params.set('type', type);
        const res = await fetch(`${this.apiBase}/drivers/models?${params}`, {
            headers: { 'Accept': 'application/json' }
        });
        return (await res.json()).models || [];
    }


    // ═══════════════════════════════════════════════════════════
    // TARAYICI İLE YAZDIR (window.print Geliştirilmiş)
    // ═══════════════════════════════════════════════════════════

    /**
     * Tarayıcının print dialog'unu akıllı şekilde aç.
     * Donanım yazıcı yoksa fallback olarak kullanılır.
     */
    browserPrint(elementId = null, options = {}) {
        if (elementId) {
            const el = document.getElementById(elementId);
            if (!el) return;

            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <title>${options.title || 'Yazdır'}</title>
                    <style>
                        * { margin: 0; padding: 0; box-sizing: border-box; }
                        body { font-family: 'Courier New', monospace; font-size: 12px; padding: 5mm; }
                        table { width: 100%; border-collapse: collapse; }
                        th, td { padding: 2px 4px; text-align: left; border-bottom: 1px solid #ddd; }
                        .text-right { text-align: right; }
                        .text-center { text-align: center; }
                        .bold { font-weight: bold; }
                        .big { font-size: 16px; }
                        .separator { border-top: 2px dashed #000; margin: 4px 0; }
                        @media print { body { padding: 0; } }
                        ${options.css || ''}
                    </style>
                </head>
                <body>${el.innerHTML}</body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
            printWindow.close();
        } else {
            window.print();
        }
    }


    // ═══════════════════════════════════════════════════════════
    // CİHAZ TEST
    // ═══════════════════════════════════════════════════════════

    /**
     * Bağlı yazıcıya test sayfası gönder.
     */
    async testPrinter(connId = null) {
        const conn = connId || this._findConnection('receipt_printer');
        if (!conn) throw new Error('Yazıcı bağlantısı bulunamadı.');

        const charW = this.settings.charWidth;
        let cmd = [];

        cmd.push(...ESCPOS.INIT);
        cmd.push(...ESCPOS.CHARSET_TR);
        cmd.push(...ESCPOS.INTL_TR);

        cmd.push(...ESCPOS.ALIGN_CENTER);
        cmd.push(...ESCPOS.DOUBLE_ON);
        cmd.push(...encodeTurkish('TEST SAYFASI'));
        cmd.push(...ESCPOS.FEED_1);
        cmd.push(...ESCPOS.DOUBLE_OFF);

        cmd.push(...encodeTurkish('═'.repeat(charW)));
        cmd.push(...ESCPOS.FEED_1);

        cmd.push(...ESCPOS.ALIGN_LEFT);
        cmd.push(...encodeTurkish('Emare Finance Yazıcı Testi'));
        cmd.push(...ESCPOS.FEED_1);
        cmd.push(...encodeTurkish(`Tarih: ${new Date().toLocaleString('tr-TR')}`));
        cmd.push(...ESCPOS.FEED_1);

        // Türkçe karakter testi
        cmd.push(...encodeTurkish('Türkçe: ÇçĞğİıÖöŞşÜü'));
        cmd.push(...ESCPOS.FEED_1);

        // Yazı formatları
        cmd.push(...ESCPOS.BOLD_ON);
        cmd.push(...encodeTurkish('Kalın Yazı'));
        cmd.push(...ESCPOS.BOLD_OFF);
        cmd.push(...ESCPOS.FEED_1);

        cmd.push(...ESCPOS.UNDERLINE_ON);
        cmd.push(...encodeTurkish('Altı Çizili'));
        cmd.push(...ESCPOS.UNDERLINE_OFF);
        cmd.push(...ESCPOS.FEED_1);

        // Barkod testi
        cmd.push(...ESCPOS.ALIGN_CENTER);
        cmd.push(...ESCPOS.BARCODE_HEIGHT(50));
        cmd.push(...ESCPOS.BARCODE_WIDTH(2));
        cmd.push(...ESCPOS.BARCODE_POS(2));
        cmd.push(...ESCPOS.BARCODE_CODE128('TEST12345'));
        cmd.push(...ESCPOS.FEED_1);

        cmd.push(...encodeTurkish('─'.repeat(charW)));
        cmd.push(...ESCPOS.FEED_1);
        cmd.push(...encodeTurkish('Yazıcı başarıyla çalışıyor! ✓'));
        cmd.push(...ESCPOS.FEED_5);
        cmd.push(...ESCPOS.CUT_FEED);

        await this._send(conn, new Uint8Array(cmd));
        this._showNotification('🖨️ Test sayfası yazdırıldı!', 'success');
    }

    /**
     * Kasa çekmecesi testi.
     */
    async testDrawer(connId = null) {
        await this.openCashDrawer(1, connId);
    }


    // ═══════════════════════════════════════════════════════════
    // YARDIMCI FONKSİYONLAR
    // ═══════════════════════════════════════════════════════════

    _findConnection(deviceType) {
        for (const [id, conn] of this.activeConnections) {
            if (conn.deviceType === deviceType) return id;
        }
        // Herhangi bir aktif bağlantı
        const first = this.activeConnections.keys().next();
        return first.done ? null : first.value;
    }

    async _send(connId, data) {
        if (!connId) throw new Error('Bağlantı bulunamadı. Önce bir cihaz bağlayın.');

        const conn = this.activeConnections.get(connId);
        if (!conn) throw new Error(`Bağlantı mevcut değil: ${connId}`);

        if (conn.type === 'usb') {
            await this._usbSend(connId, data);
        } else if (conn.type === 'serial') {
            await this._serialSend(connId, data);
        } else if (conn.type === 'network') {
            await this._networkSend(connId, data);
        }
    }

    async _networkSend(connId, data) {
        // Ağ yazıcısına proxy üzerinden gönder
        const conn = this.activeConnections.get(connId);
        await this._apiPost('/print-network', {
            ip: conn.ip_address,
            port: conn.port || 9100,
            data: Array.from(data),
        });
    }

    _padLine(left, right, width) {
        const space = width - left.length - right.length;
        return left + ' '.repeat(Math.max(1, space)) + right;
    }

    _padColumns(values, widths, totalWidth) {
        return values.map((v, i) => {
            const w = widths[i] || 10;
            const s = String(v);
            return i === values.length - 1
                ? s.padStart(w)
                : s.substring(0, w).padEnd(w);
        }).join('');
    }

    _formatMoney(amount) {
        return Number(amount).toFixed(2).replace('.', ',');
    }

    _showNotification(message, type = 'info') {
        // Alpine.js veya basit toast bildirim
        const colors = {
            success: 'bg-green-500',
            error:   'bg-red-500',
            warning: 'bg-yellow-500',
            info:    'bg-blue-500',
        };

        const toast = document.createElement('div');
        toast.className = `fixed bottom-4 right-4 ${colors[type] || colors.info} text-white px-6 py-3 rounded-lg shadow-xl z-[9999] text-sm font-medium transition-all transform translate-y-0 opacity-100`;
        toast.style.animation = 'slideUp 0.3s ease-out';
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(20px)';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // ── Bağlantı Durumu ──
    getStatus() {
        return {
            totalDevices: this.devices.size,
            activeConnections: this.activeConnections.size,
            connections: Array.from(this.activeConnections.entries()).map(([id, c]) => ({
                id,
                type: c.type,
                deviceType: c.deviceType,
            })),
            webUsbSupported: !!navigator.usb,
            webSerialSupported: !!navigator.serial,
            webBluetoothSupported: !!navigator.bluetooth,
        };
    }

    /**
     * Tüm bağlantıları kapat.
     */
    async disconnectAll() {
        for (const [id, conn] of this.activeConnections) {
            try {
                if (conn.type === 'usb' && conn.device.opened) {
                    await conn.device.releaseInterface(conn.interfaceNumber);
                    await conn.device.close();
                } else if (conn.type === 'serial') {
                    conn.writer.releaseLock();
                    await conn.port.close();
                }
            } catch (e) {
                console.warn(`[HW] Bağlantı kapatma hatası (${id}):`, e);
            }
        }
        this.activeConnections.clear();
        this.stopBarcodeListener();
        this.stopScalePolling();
        this.emit('all_disconnected');
    }
}

// Toast animasyon CSS'ini ekle
const style = document.createElement('style');
style.textContent = `
@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
}
`;
document.head.appendChild(style);


// ═══════════════════════════════════════════════════════════════
// Global erişim
// ═══════════════════════════════════════════════════════════════
window.HardwareManager = HardwareManager;
window.ESCPOS = ESCPOS;
window.encodeTurkish = encodeTurkish;
