<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Donanım Sürücüleri Yapılandırması
    |--------------------------------------------------------------------------
    |
    | Tüm yazıcı, terazi, barkod okuyucu ve kasa çekmecesi ayarları.
    | "tak-çalıştır" prensibiyle çalışır: cihazı bağla, ayarla, kullan.
    |
    */

    // Varsayılan yazıcı türleri ve bağlantı bilgileri
    'device_types' => [
        'receipt_printer' => [
            'label'       => 'Fiş Yazıcı (Termal)',
            'icon'        => 'fas fa-receipt',
            'color'       => 'indigo',
            'protocols'   => ['escpos', 'star', 'citizen'],
            'connections' => ['usb', 'serial', 'network', 'bluetooth'],
            'description' => 'Satış fişi ve fiş çıktısı yazdırma',
        ],
        'label_printer' => [
            'label'       => 'Etiket Yazıcı',
            'icon'        => 'fas fa-tags',
            'color'       => 'purple',
            'protocols'   => ['zpl', 'epl', 'tspl', 'escpos'],
            'connections' => ['usb', 'serial', 'network'],
            'description' => 'Barkod etiketi ve fiyat etiketi yazdırma',
        ],
        'a4_printer' => [
            'label'       => 'A4 Yazıcı (Lazer/Mürekkep)',
            'icon'        => 'fas fa-print',
            'color'       => 'blue',
            'protocols'   => ['system'],
            'connections' => ['usb', 'network'],
            'description' => 'Fatura, rapor ve belge yazdırma',
        ],
        'barcode_scanner' => [
            'label'       => 'Barkod Okuyucu',
            'icon'        => 'fas fa-barcode',
            'color'       => 'green',
            'protocols'   => ['keyboard_wedge', 'serial', 'hid'],
            'connections' => ['usb', 'bluetooth', 'serial'],
            'description' => 'Ürün barkodu okuma — klavye modu veya seri port',
        ],
        'scale' => [
            'label'       => 'Elektronik Terazi',
            'icon'        => 'fas fa-weight-scale',
            'color'       => 'amber',
            'protocols'   => ['cas', 'dibal', 'digi', 'mettler', 'custom'],
            'connections' => ['serial', 'usb', 'network'],
            'description' => 'Ağırlık okuma ve barkodlu terazi entegrasyonu',
        ],
        'cash_drawer' => [
            'label'       => 'Kasa Çekmecesi',
            'icon'        => 'fas fa-cash-register',
            'color'       => 'emerald',
            'protocols'   => ['escpos_kick', 'rj11', 'direct'],
            'connections' => ['printer', 'serial', 'usb'],
            'description' => 'Para çekmecesi açma — yazıcı üzerinden veya direkt',
        ],
        'customer_display' => [
            'label'       => 'Müşteri Ekranı',
            'icon'        => 'fas fa-tv',
            'color'       => 'cyan',
            'protocols'   => ['pole_display', 'lcd', 'vfd'],
            'connections' => ['serial', 'usb'],
            'description' => 'Müşteriye fiyat gösterim ekranı',
        ],
    ],

    // Bilinen yazıcı modelleri (tak-çalıştır için otomatik tanıma)
    'known_devices' => [
        // Fiş Yazıcıları
        ['vendor_id' => '04b8', 'product_id' => '0202', 'name' => 'Epson TM-T20II',     'type' => 'receipt_printer', 'protocol' => 'escpos', 'width' => 48],
        ['vendor_id' => '04b8', 'product_id' => '0e28', 'name' => 'Epson TM-T88V',      'type' => 'receipt_printer', 'protocol' => 'escpos', 'width' => 48],
        ['vendor_id' => '04b8', 'product_id' => '0e03', 'name' => 'Epson TM-T88VI',     'type' => 'receipt_printer', 'protocol' => 'escpos', 'width' => 48],
        ['vendor_id' => '04b8', 'product_id' => '0e15', 'name' => 'Epson TM-m30',       'type' => 'receipt_printer', 'protocol' => 'escpos', 'width' => 48],
        ['vendor_id' => '0519', 'product_id' => '0001', 'name' => 'Star TSP100',        'type' => 'receipt_printer', 'protocol' => 'star',   'width' => 48],
        ['vendor_id' => '0519', 'product_id' => '0003', 'name' => 'Star TSP143',        'type' => 'receipt_printer', 'protocol' => 'star',   'width' => 48],
        ['vendor_id' => '1fc9', 'product_id' => '2016', 'name' => 'Bixolon SRP-350III', 'type' => 'receipt_printer', 'protocol' => 'escpos', 'width' => 48],
        ['vendor_id' => '0416', 'product_id' => '5011', 'name' => 'Citizen CT-S310II',  'type' => 'receipt_printer', 'protocol' => 'citizen','width' => 48],
        ['vendor_id' => '0fe6', 'product_id' => '811e', 'name' => 'Xprinter XP-80C',    'type' => 'receipt_printer', 'protocol' => 'escpos', 'width' => 48],
        ['vendor_id' => '0483', 'product_id' => '5743', 'name' => 'Rongta RP80',        'type' => 'receipt_printer', 'protocol' => 'escpos', 'width' => 48],

        // Etiket Yazıcıları
        ['vendor_id' => '0a5f', 'product_id' => '0009', 'name' => 'Zebra ZD220',        'type' => 'label_printer', 'protocol' => 'zpl'],
        ['vendor_id' => '0a5f', 'product_id' => '008c', 'name' => 'Zebra GK420d',       'type' => 'label_printer', 'protocol' => 'zpl'],
        ['vendor_id' => '0a5f', 'product_id' => '00a0', 'name' => 'Zebra ZD420',        'type' => 'label_printer', 'protocol' => 'zpl'],
        ['vendor_id' => '1203', 'product_id' => '0641', 'name' => 'TSC TDP-225',        'type' => 'label_printer', 'protocol' => 'tspl'],
        ['vendor_id' => '1203', 'product_id' => '0642', 'name' => 'TSC TE200',          'type' => 'label_printer', 'protocol' => 'tspl'],
        ['vendor_id' => '04f9', 'product_id' => '2042', 'name' => 'Brother QL-800',     'type' => 'label_printer', 'protocol' => 'epl'],
        ['vendor_id' => '0745', 'product_id' => '0005', 'name' => 'Godex G500',         'type' => 'label_printer', 'protocol' => 'ezpl'],
        ['vendor_id' => '195d', 'product_id' => '7006', 'name' => 'Xprinter XP-360B',   'type' => 'label_printer', 'protocol' => 'tspl'],

        // Barkod Okuyucular
        ['vendor_id' => '05e0', 'product_id' => '1200', 'name' => 'Symbol DS2208',      'type' => 'barcode_scanner', 'protocol' => 'hid'],
        ['vendor_id' => '05e0', 'product_id' => '1300', 'name' => 'Symbol LS2208',      'type' => 'barcode_scanner', 'protocol' => 'keyboard_wedge'],
        ['vendor_id' => '0c2e', 'product_id' => '0720', 'name' => 'Honeywell Xenon 1900','type' => 'barcode_scanner', 'protocol' => 'hid'],
        ['vendor_id' => '0c2e', 'product_id' => '0200', 'name' => 'Honeywell Voyager 1400g','type' => 'barcode_scanner', 'protocol' => 'hid'],
        ['vendor_id' => '0c2e', 'product_id' => '0206', 'name' => 'Honeywell Voyager 1250g','type' => 'barcode_scanner', 'protocol' => 'keyboard_wedge'],
        ['vendor_id' => '040b', 'product_id' => '6543', 'name' => 'Datalogic QW2120',   'type' => 'barcode_scanner', 'protocol' => 'keyboard_wedge'],
        ['vendor_id' => '040b', 'product_id' => '6544', 'name' => 'Datalogic QD2500',   'type' => 'barcode_scanner', 'protocol' => 'hid'],
        ['vendor_id' => '2dd6', 'product_id' => '2503', 'name' => 'Netum NT-1228BL',    'type' => 'barcode_scanner', 'protocol' => 'keyboard_wedge'],
        ['vendor_id' => '2dd6', 'product_id' => '2504', 'name' => 'Netum NT-L6X',       'type' => 'barcode_scanner', 'protocol' => 'keyboard_wedge'],
        ['vendor_id' => '1eab', 'product_id' => '8203', 'name' => 'Newland HR22 Dorado','type' => 'barcode_scanner', 'protocol' => 'hid'],
        ['vendor_id' => '065a', 'product_id' => '0009', 'name' => 'Opticon OPI-3601',   'type' => 'barcode_scanner', 'protocol' => 'hid'],

        // A4 Yazıcılar (Lazer / Mürekkep — IPP/system protokolü)
        ['vendor_id' => '03f0', 'product_id' => '002a', 'name' => 'HP LaserJet Pro M15w',        'type' => 'a4_printer', 'protocol' => 'system'],
        ['vendor_id' => '03f0', 'product_id' => '0b2a', 'name' => 'HP LaserJet Pro M28w',        'type' => 'a4_printer', 'protocol' => 'system'],
        ['vendor_id' => '03f0', 'product_id' => '1c2a', 'name' => 'HP LaserJet Pro M404dn',      'type' => 'a4_printer', 'protocol' => 'system'],
        ['vendor_id' => '03f0', 'product_id' => '2d2a', 'name' => 'HP LaserJet Pro M428fdw',     'type' => 'a4_printer', 'protocol' => 'system'],
        ['vendor_id' => '03f0', 'product_id' => '4f2a', 'name' => 'HP Color LaserJet M255dw',    'type' => 'a4_printer', 'protocol' => 'system'],
        ['vendor_id' => '03f0', 'product_id' => '5c2a', 'name' => 'HP OfficeJet Pro 9015e',      'type' => 'a4_printer', 'protocol' => 'system'],
        ['vendor_id' => '04a9', 'product_id' => '10c3', 'name' => 'Canon imageCLASS LBP226dw',   'type' => 'a4_printer', 'protocol' => 'system'],
        ['vendor_id' => '04a9', 'product_id' => '10c4', 'name' => 'Canon imageCLASS MF445dw',    'type' => 'a4_printer', 'protocol' => 'system'],
        ['vendor_id' => '04a9', 'product_id' => '176b', 'name' => 'Canon LBP6030',               'type' => 'a4_printer', 'protocol' => 'system'],
        ['vendor_id' => '04a9', 'product_id' => '1855', 'name' => 'Canon PIXMA G3020',           'type' => 'a4_printer', 'protocol' => 'system'],
        ['vendor_id' => '04b8', 'product_id' => '1143', 'name' => 'Epson EcoTank L3250',         'type' => 'a4_printer', 'protocol' => 'system'],
        ['vendor_id' => '04b8', 'product_id' => '1144', 'name' => 'Epson EcoTank L5290',         'type' => 'a4_printer', 'protocol' => 'system'],
        ['vendor_id' => '04b8', 'product_id' => '08d1', 'name' => 'Epson WorkForce WF-2850',     'type' => 'a4_printer', 'protocol' => 'system'],
        ['vendor_id' => '04f9', 'product_id' => '0042', 'name' => 'Brother HL-L2350DW',          'type' => 'a4_printer', 'protocol' => 'system'],
        ['vendor_id' => '04f9', 'product_id' => '0043', 'name' => 'Brother HL-L2395DW',          'type' => 'a4_printer', 'protocol' => 'system'],
        ['vendor_id' => '04f9', 'product_id' => '0044', 'name' => 'Brother MFC-L2710DW',         'type' => 'a4_printer', 'protocol' => 'system'],
        ['vendor_id' => '04f9', 'product_id' => '0055', 'name' => 'Brother HL-L3270CDW',         'type' => 'a4_printer', 'protocol' => 'system'],
        ['vendor_id' => '04e8', 'product_id' => '3460', 'name' => 'Samsung Xpress M2020W',       'type' => 'a4_printer', 'protocol' => 'system'],
        ['vendor_id' => '04e8', 'product_id' => '3461', 'name' => 'Samsung Xpress M2070FW',      'type' => 'a4_printer', 'protocol' => 'system'],
        ['vendor_id' => '0482', 'product_id' => '0036', 'name' => 'Kyocera ECOSYS P2040dn',      'type' => 'a4_printer', 'protocol' => 'system'],
        ['vendor_id' => '0482', 'product_id' => '0037', 'name' => 'Kyocera ECOSYS M2135dn',      'type' => 'a4_printer', 'protocol' => 'system'],
        ['vendor_id' => '0924', 'product_id' => '4293', 'name' => 'Xerox Phaser 3020',           'type' => 'a4_printer', 'protocol' => 'system'],
        ['vendor_id' => '0924', 'product_id' => '4294', 'name' => 'Xerox B210',                  'type' => 'a4_printer', 'protocol' => 'system'],
        ['vendor_id' => '0924', 'product_id' => '4295', 'name' => 'Xerox C230',                  'type' => 'a4_printer', 'protocol' => 'system'],

        // Elektronik Teraziler (genellikle seri port — vendor_id yoksa null)
        ['vendor_id' => null, 'product_id' => null, 'name' => 'CAS SW-1S',              'type' => 'scale', 'protocol' => 'cas',     'serial_config' => ['baud_rate' => 9600]],
        ['vendor_id' => null, 'product_id' => null, 'name' => 'CAS ER Junior',          'type' => 'scale', 'protocol' => 'cas',     'serial_config' => ['baud_rate' => 9600]],
        ['vendor_id' => null, 'product_id' => null, 'name' => 'CAS PR-II',              'type' => 'scale', 'protocol' => 'cas',     'serial_config' => ['baud_rate' => 9600]],
        ['vendor_id' => null, 'product_id' => null, 'name' => 'CAS CL-5200',            'type' => 'scale', 'protocol' => 'cas',     'serial_config' => ['baud_rate' => 9600]],
        ['vendor_id' => null, 'product_id' => null, 'name' => 'Dibal G-310',            'type' => 'scale', 'protocol' => 'dibal',   'serial_config' => ['baud_rate' => 9600]],
        ['vendor_id' => null, 'product_id' => null, 'name' => 'Dibal G-325',            'type' => 'scale', 'protocol' => 'dibal',   'serial_config' => ['baud_rate' => 9600]],
        ['vendor_id' => null, 'product_id' => null, 'name' => 'Dibal M-525',            'type' => 'scale', 'protocol' => 'dibal',   'serial_config' => ['baud_rate' => 9600]],
        ['vendor_id' => null, 'product_id' => null, 'name' => 'DIGI SM-100',            'type' => 'scale', 'protocol' => 'digi',    'serial_config' => ['baud_rate' => 4800]],
        ['vendor_id' => null, 'product_id' => null, 'name' => 'DIGI SM-5100',           'type' => 'scale', 'protocol' => 'digi',    'serial_config' => ['baud_rate' => 4800]],
        ['vendor_id' => null, 'product_id' => null, 'name' => 'DIGI DS-781B',           'type' => 'scale', 'protocol' => 'digi',    'serial_config' => ['baud_rate' => 4800]],
        ['vendor_id' => null, 'product_id' => null, 'name' => 'Mettler Toledo bRite',   'type' => 'scale', 'protocol' => 'mettler', 'serial_config' => ['baud_rate' => 9600]],
        ['vendor_id' => null, 'product_id' => null, 'name' => 'Mettler Toledo Ariva-S', 'type' => 'scale', 'protocol' => 'mettler', 'serial_config' => ['baud_rate' => 9600]],
        ['vendor_id' => null, 'product_id' => null, 'name' => 'Bizerba SC-II 800',      'type' => 'scale', 'protocol' => 'custom',  'serial_config' => ['baud_rate' => 9600]],
        ['vendor_id' => null, 'product_id' => null, 'name' => 'Ohaus Ranger 7000',      'type' => 'scale', 'protocol' => 'custom',  'serial_config' => ['baud_rate' => 9600]],
        ['vendor_id' => null, 'product_id' => null, 'name' => 'Baykon BX-11',           'type' => 'scale', 'protocol' => 'custom',  'serial_config' => ['baud_rate' => 9600]],

        // Kasa Çekmeceleri (genellikle yazıcı RJ11 üzerinden veya direkt USB)
        ['vendor_id' => '0519', 'product_id' => '0003', 'name' => 'Star CD3-1616',       'type' => 'cash_drawer', 'protocol' => 'escpos_kick', 'kick_pin' => 0],
        ['vendor_id' => null,   'product_id' => null,   'name' => 'APG Vasario VB320',    'type' => 'cash_drawer', 'protocol' => 'escpos_kick', 'kick_pin' => 0],
        ['vendor_id' => null,   'product_id' => null,   'name' => 'APG Vasario VB554A',   'type' => 'cash_drawer', 'protocol' => 'escpos_kick', 'kick_pin' => 0],
        ['vendor_id' => null,   'product_id' => null,   'name' => 'Posiflex CR-4100',     'type' => 'cash_drawer', 'protocol' => 'rj11',        'kick_pin' => 0],
        ['vendor_id' => null,   'product_id' => null,   'name' => 'Posiflex CR-3100',     'type' => 'cash_drawer', 'protocol' => 'rj11',        'kick_pin' => 0],
        ['vendor_id' => null,   'product_id' => null,   'name' => 'MAKEN EK-330',         'type' => 'cash_drawer', 'protocol' => 'rj11',        'kick_pin' => 0],
        ['vendor_id' => null,   'product_id' => null,   'name' => 'MAKEN EK-410',         'type' => 'cash_drawer', 'protocol' => 'rj11',        'kick_pin' => 0],
        ['vendor_id' => null,   'product_id' => null,   'name' => 'Partner Tech CD-101',  'type' => 'cash_drawer', 'protocol' => 'rj11',        'kick_pin' => 0],
        ['vendor_id' => null,   'product_id' => null,   'name' => 'Safescan 4100',        'type' => 'cash_drawer', 'protocol' => 'escpos_kick', 'kick_pin' => 0],
        ['vendor_id' => null,   'product_id' => null,   'name' => 'VPOS EC-410',          'type' => 'cash_drawer', 'protocol' => 'escpos_kick', 'kick_pin' => 0],

        // Müşteri Ekranları (VFD/LCD — genellikle USB veya seri port)
        ['vendor_id' => '0d3a', 'product_id' => '0207', 'name' => 'Posiflex PD-2605',    'type' => 'customer_display', 'protocol' => 'vfd',  'line_length' => 20],
        ['vendor_id' => '0d3a', 'product_id' => '0208', 'name' => 'Posiflex PD-2608',    'type' => 'customer_display', 'protocol' => 'lcd',  'line_length' => 20],
        ['vendor_id' => null,   'product_id' => null,   'name' => 'Birch BDM-2000',       'type' => 'customer_display', 'protocol' => 'vfd',  'line_length' => 20],
        ['vendor_id' => null,   'product_id' => null,   'name' => 'Logic Controls PD6200','type' => 'customer_display', 'protocol' => 'vfd',  'line_length' => 20],
        ['vendor_id' => '04b8', 'product_id' => '0e20', 'name' => 'Epson DM-D110',       'type' => 'customer_display', 'protocol' => 'pole_display', 'line_length' => 20],
        ['vendor_id' => '04b8', 'product_id' => '0e21', 'name' => 'Epson DM-D30',        'type' => 'customer_display', 'protocol' => 'lcd',  'line_length' => 20],
        ['vendor_id' => null,   'product_id' => null,   'name' => 'Partner Tech CD-7220', 'type' => 'customer_display', 'protocol' => 'vfd',  'line_length' => 20],
        ['vendor_id' => '1fc9', 'product_id' => '2020', 'name' => 'Bixolon BCD-1100',    'type' => 'customer_display', 'protocol' => 'vfd',  'line_length' => 20],
        ['vendor_id' => null,   'product_id' => null,   'name' => 'Firich FV-2030',       'type' => 'customer_display', 'protocol' => 'vfd',  'line_length' => 20],
        ['vendor_id' => null,   'product_id' => null,   'name' => 'Custom VKP80',         'type' => 'customer_display', 'protocol' => 'lcd',  'line_length' => 40],
    ],

    // Seri port varsayılan ayarları (terazi, yazıcı, okuyucu)
    'serial_defaults' => [
        'baud_rate'  => 9600,
        'data_bits'  => 8,
        'stop_bits'  => 1,
        'parity'     => 'none',
        'flow_control' => 'none',
    ],

    // ESC/POS termal yazıcı varsayılanları
    'escpos' => [
        'paper_width' => 80,  // mm — 80 veya 58
        'char_width'  => 48,  // karakter — 80mm=48, 58mm=32
        'encoding'    => 'WPC1254',  // Türkçe karakter seti
        'cut_type'    => 'partial',  // full veya partial
        'font'        => 'A',       // A veya B
        'density'     => 7,         // 0-15 baskı yoğunluğu
    ],

    // Etiket yazıcı varsayılanları
    'label' => [
        'width'     => 50,  // mm
        'height'    => 30,  // mm
        'gap'       => 3,   // mm — etiketler arası boşluk
        'speed'     => 4,   // inch/saniye
        'darkness'  => 10,  // 0-15
        'direction' => 0,   // 0 veya 1
    ],

    // Terazi protokol ayarları
    'scale_protocols' => [
        'cas' => [
            'name' => 'CAS',
            'request_cmd' => 'W',
            'response_pattern' => '/ST,GS,\s*(\d+\.?\d*)\s*(kg|g)/i',
            'baud_rate' => 9600,
        ],
        'dibal' => [
            'name' => 'Dibal',
            'request_cmd' => "\x05",
            'response_pattern' => '/(\d+\.?\d*)/i',
            'baud_rate' => 9600,
        ],
        'digi' => [
            'name' => 'DIGI',
            'request_cmd' => 'R',
            'response_pattern' => '/(\d+\.?\d*)\s*(kg|g|lb)/i',
            'baud_rate' => 4800,
        ],
        'mettler' => [
            'name' => 'Mettler Toledo',
            'request_cmd' => 'S',
            'response_pattern' => '/S\s+S\s+(\d+\.?\d*)\s*(kg|g)/i',
            'baud_rate' => 9600,
        ],
        'custom' => [
            'name' => 'Özel/Genel',
            'request_cmd' => '',
            'response_pattern' => '/(\d+\.?\d*)/',
            'baud_rate' => 9600,
        ],
    ],

    // Fiş şablonu ayarları
    'receipt' => [
        'company_name'  => 'Emare Finance',
        'company_slogan' => 'Finansal Çözümler',
        'header_lines'  => [],
        'footer_lines'  => [
            'Teşekkür ederiz!',
            'İyi günler dileriz.',
        ],
        'show_logo'     => false,
        'show_barcode'  => true,
        'show_qr'       => false,
    ],

];
