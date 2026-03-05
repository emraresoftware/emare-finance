<?php

/**
 * Tüm modülleri test eden PHP scripti
 * Kullanım: php artisan tinker < tests/test_all_modules.php
 */

// Auth simüle et
$user = \App\Models\User::where('email', 'emre@emareas.com')->first();
if (!$user) {
    echo "❌ User not found!\n";
    exit(1);
}
echo "✅ User: {$user->name} (ID: {$user->id}, Super Admin: " . ($user->is_super_admin ? 'Yes' : 'No') . ")\n";
echo "   Firm ID: {$user->firm_id}\n\n";

$firm = \App\Models\Firm::find($user->firm_id);
echo "✅ Firm: {$firm->name} (ID: {$firm->id})\n\n";

// Tabloların kayıt sayılarını kontrol et
$tables = [
    'Sales' => \App\Models\Sale::class,
    'SaleItems' => \App\Models\SaleItem::class,
    'Products' => \App\Models\Product::class,
    'Categories' => \App\Models\Category::class,
    'Customers' => \App\Models\Customer::class,
    'Branches' => \App\Models\Branch::class,
    'Staff' => \App\Models\Staff::class,
    'Expenses' => \App\Models\Expense::class,
    'Incomes' => \App\Models\Income::class,
    'PurchaseInvoices' => \App\Models\PurchaseInvoice::class,
    'PurchaseInvoiceItems' => \App\Models\PurchaseInvoiceItem::class,
    'StockMovements' => \App\Models\StockMovement::class,
    'AccountTransactions' => \App\Models\AccountTransaction::class,
    'StaffMotions' => \App\Models\StaffMotion::class,
    'Tasks' => \App\Models\Task::class,
    'StockCounts' => \App\Models\StockCount::class,
    'StockCountItems' => \App\Models\StockCountItem::class,
    'PaymentTypes' => \App\Models\PaymentType::class,
];

echo "=== TABLO KAYIT SAYILARI ===\n";
foreach ($tables as $name => $model) {
    try {
        $count = $model::where('firm_id', $user->firm_id)->count();
        echo "  {$name}: {$count}\n";
    } catch (\Exception $e) {
        try {
            $count = $model::count();
            echo "  {$name}: {$count} (no firm_id filter)\n";
        } catch (\Exception $e2) {
            echo "  ❌ {$name}: " . $e2->getMessage() . "\n";
        }
    }
}

echo "\n=== CONTROLLER TESTLERİ ===\n";

// Auth simülasyonu
auth()->login($user);
app()->instance('request', \Illuminate\Http\Request::create('/panel', 'GET'));

$errors = [];
$successes = [];

function testController($name, $callback) {
    global $errors, $successes;
    try {
        $result = $callback();
        $successes[] = $name;
        echo "  ✅ {$name}\n";
        return $result;
    } catch (\Throwable $e) {
        $errorMsg = get_class($e) . ': ' . $e->getMessage();
        $file = $e->getFile() . ':' . $e->getLine();
        $errors[] = ['module' => $name, 'error' => $errorMsg, 'file' => $file];
        echo "  ❌ {$name}: {$errorMsg}\n     at {$file}\n";
        return null;
    }
}

// 1. Dashboard
echo "\n--- DASHBOARD ---\n";
testController('DashboardController@index', function() {
    $controller = app(\App\Http\Controllers\DashboardController::class);
    $request = \Illuminate\Http\Request::create('/panel', 'GET');
    $request->setUserResolver(function() { return auth()->user(); });
    return $controller->index($request);
});

// 2. Sales
echo "\n--- SATIŞLAR ---\n";
testController('SaleController@index', function() {
    $controller = app(\App\Http\Controllers\SaleController::class);
    $request = \Illuminate\Http\Request::create('/satislar', 'GET');
    $request->setUserResolver(function() { return auth()->user(); });
    return $controller->index($request);
});

testController('SaleController@show (first sale)', function() {
    $sale = \App\Models\Sale::where('firm_id', auth()->user()->firm_id)->first();
    if (!$sale) throw new \Exception('No sale found');
    $controller = app(\App\Http\Controllers\SaleController::class);
    return $controller->show($sale->id);
});

// 3. Products
echo "\n--- ÜRÜNLER ---\n";
testController('ProductController@index', function() {
    $controller = app(\App\Http\Controllers\ProductController::class);
    $request = \Illuminate\Http\Request::create('/urunler', 'GET');
    $request->setUserResolver(function() { return auth()->user(); });
    return $controller->index($request);
});

testController('ProductController@create', function() {
    $controller = app(\App\Http\Controllers\ProductController::class);
    return $controller->create();
});

testController('ProductController@show (first product)', function() {
    $product = \App\Models\Product::where('firm_id', auth()->user()->firm_id)->first();
    if (!$product) throw new \Exception('No product found');
    $controller = app(\App\Http\Controllers\ProductController::class);
    return $controller->show($product->id);
});

testController('ProductController@edit (first product)', function() {
    $product = \App\Models\Product::where('firm_id', auth()->user()->firm_id)->first();
    if (!$product) throw new \Exception('No product found');
    $controller = app(\App\Http\Controllers\ProductController::class);
    return $controller->edit($product->id);
});

// 4. Customers
echo "\n--- CARİLER ---\n";
testController('CustomerController@index', function() {
    $controller = app(\App\Http\Controllers\CustomerController::class);
    $request = \Illuminate\Http\Request::create('/cariler', 'GET');
    $request->setUserResolver(function() { return auth()->user(); });
    return $controller->index($request);
});

testController('CustomerController@create', function() {
    $controller = app(\App\Http\Controllers\CustomerController::class);
    return $controller->create();
});

testController('CustomerController@show (first customer)', function() {
    $customer = \App\Models\Customer::where('firm_id', auth()->user()->firm_id)->first();
    if (!$customer) throw new \Exception('No customer found');
    $controller = app(\App\Http\Controllers\CustomerController::class);
    return $controller->show($customer->id);
});

// 5. Firms
echo "\n--- FİRMALAR ---\n";
testController('FirmController@index', function() {
    $controller = app(\App\Http\Controllers\FirmController::class);
    $request = \Illuminate\Http\Request::create('/firmalar', 'GET');
    $request->setUserResolver(function() { return auth()->user(); });
    return $controller->index($request);
});

// 6. Purchase Invoices
echo "\n--- ALIŞ FATURALARI ---\n";
testController('PurchaseInvoiceController@index', function() {
    $controller = app(\App\Http\Controllers\PurchaseInvoiceController::class);
    $request = \Illuminate\Http\Request::create('/alis-faturalari', 'GET');
    $request->setUserResolver(function() { return auth()->user(); });
    return $controller->index($request);
});

testController('PurchaseInvoiceController@create', function() {
    $controller = app(\App\Http\Controllers\PurchaseInvoiceController::class);
    return $controller->create();
});

// 7. Stock
echo "\n--- STOK ---\n";
testController('StockMovementController@index', function() {
    $controller = app(\App\Http\Controllers\StockMovementController::class);
    $request = \Illuminate\Http\Request::create('/stok/hareketler', 'GET');
    $request->setUserResolver(function() { return auth()->user(); });
    return $controller->index($request);
});

testController('StockCountController@index', function() {
    $controller = app(\App\Http\Controllers\StockCountController::class);
    $request = \Illuminate\Http\Request::create('/stok/sayim', 'GET');
    $request->setUserResolver(function() { return auth()->user(); });
    return $controller->index($request);
});

// 8. Staff
echo "\n--- PERSONEL ---\n";
testController('StaffController@index', function() {
    $controller = app(\App\Http\Controllers\StaffController::class);
    $request = \Illuminate\Http\Request::create('/personeller', 'GET');
    $request->setUserResolver(function() { return auth()->user(); });
    return $controller->index($request);
});

// 9. Income/Expense
echo "\n--- GELİR/GİDER ---\n";
testController('IncomeController@index', function() {
    $controller = app(\App\Http\Controllers\IncomeController::class);
    $request = \Illuminate\Http\Request::create('/gelir-gider/gelirler', 'GET');
    $request->setUserResolver(function() { return auth()->user(); });
    return $controller->index($request);
});

testController('ExpenseController@index', function() {
    $controller = app(\App\Http\Controllers\ExpenseController::class);
    $request = \Illuminate\Http\Request::create('/gelir-gider/giderler', 'GET');
    $request->setUserResolver(function() { return auth()->user(); });
    return $controller->index($request);
});

// 10. Reports
echo "\n--- RAPORLAR ---\n";
testController('ReportController@index', function() {
    $controller = app(\App\Http\Controllers\ReportController::class);
    return $controller->index();
});

testController('ReportController@daily', function() {
    $controller = app(\App\Http\Controllers\ReportController::class);
    $request = \Illuminate\Http\Request::create('/raporlar/gunluk', 'GET');
    $request->setUserResolver(function() { return auth()->user(); });
    return $controller->daily($request);
});

testController('ReportController@historical', function() {
    $controller = app(\App\Http\Controllers\ReportController::class);
    $request = \Illuminate\Http\Request::create('/raporlar/tarihsel', 'GET');
    $request->setUserResolver(function() { return auth()->user(); });
    return $controller->historical($request);
});

testController('ReportController@products', function() {
    $controller = app(\App\Http\Controllers\ReportController::class);
    $request = \Illuminate\Http\Request::create('/raporlar/urunsel', 'GET');
    $request->setUserResolver(function() { return auth()->user(); });
    return $controller->products($request);
});

testController('ReportController@groups', function() {
    $controller = app(\App\Http\Controllers\ReportController::class);
    $request = \Illuminate\Http\Request::create('/raporlar/grupsal', 'GET');
    $request->setUserResolver(function() { return auth()->user(); });
    return $controller->groups($request);
});

testController('ReportController@sales', function() {
    $controller = app(\App\Http\Controllers\ReportController::class);
    $request = \Illuminate\Http\Request::create('/raporlar/satislar', 'GET');
    $request->setUserResolver(function() { return auth()->user(); });
    return $controller->sales($request);
});

testController('ReportController@profit', function() {
    $controller = app(\App\Http\Controllers\ReportController::class);
    $request = \Illuminate\Http\Request::create('/raporlar/kar', 'GET');
    $request->setUserResolver(function() { return auth()->user(); });
    return $controller->profit($request);
});

testController('ReportController@stockMovement', function() {
    $controller = app(\App\Http\Controllers\ReportController::class);
    $request = \Illuminate\Http\Request::create('/raporlar/stok-hareket', 'GET');
    $request->setUserResolver(function() { return auth()->user(); });
    return $controller->stockMovement($request);
});

testController('ReportController@staffMovement', function() {
    $controller = app(\App\Http\Controllers\ReportController::class);
    $request = \Illuminate\Http\Request::create('/raporlar/personel-hareket', 'GET');
    $request->setUserResolver(function() { return auth()->user(); });
    return $controller->staffMovement($request);
});

// 11. Tasks
echo "\n--- GÖREVLER ---\n";
testController('TaskController@index', function() {
    $controller = app(\App\Http\Controllers\TaskController::class);
    $request = \Illuminate\Http\Request::create('/gorevler', 'GET');
    $request->setUserResolver(function() { return auth()->user(); });
    return $controller->index($request);
});

// 12. Payment Types
echo "\n--- ÖDEME TİPLERİ ---\n";
testController('PaymentTypeController@index', function() {
    $controller = app(\App\Http\Controllers\PaymentTypeController::class);
    $request = \Illuminate\Http\Request::create('/odeme-tipleri', 'GET');
    $request->setUserResolver(function() { return auth()->user(); });
    return $controller->index($request);
});

// 13. Admin
echo "\n--- ADMİN ---\n";
testController('Admin\ModuleController@index', function() {
    $controller = app(\App\Http\Controllers\Admin\ModuleController::class);
    $request = \Illuminate\Http\Request::create('/admin/moduller', 'GET');
    $request->setUserResolver(function() { return auth()->user(); });
    return $controller->index($request);
});

testController('Admin\RoleController@index', function() {
    $controller = app(\App\Http\Controllers\Admin\RoleController::class);
    $request = \Illuminate\Http\Request::create('/admin/roller', 'GET');
    $request->setUserResolver(function() { return auth()->user(); });
    return $controller->index($request);
});

testController('Admin\UserController@index', function() {
    $controller = app(\App\Http\Controllers\Admin\UserController::class);
    $request = \Illuminate\Http\Request::create('/admin/kullanicilar', 'GET');
    $request->setUserResolver(function() { return auth()->user(); });
    return $controller->index($request);
});

// 14. Marketing
echo "\n--- PAZARLAMA ---\n";
testController('Marketing\DashboardController@index', function() {
    $controller = app(\App\Http\Controllers\Marketing\DashboardController::class);
    $request = \Illuminate\Http\Request::create('/pazarlama', 'GET');
    $request->setUserResolver(function() { return auth()->user(); });
    return $controller->index($request);
});

// 15. SMS
echo "\n--- SMS ---\n";
testController('Sms\DashboardController@index', function() {
    $controller = app(\App\Http\Controllers\Sms\DashboardController::class);
    $request = \Illuminate\Http\Request::create('/sms', 'GET');
    $request->setUserResolver(function() { return auth()->user(); });
    return $controller->index($request);
});

// Summary
echo "\n\n=============================\n";
echo "=== TEST SONUÇLARI ===\n";
echo "=============================\n";
echo "✅ Başarılı: " . count($successes) . "\n";
echo "❌ Hatalı: " . count($errors) . "\n\n";

if (count($errors) > 0) {
    echo "=== HATALAR ===\n";
    foreach ($errors as $i => $err) {
        echo ($i + 1) . ". {$err['module']}\n";
        echo "   Hata: {$err['error']}\n";
        echo "   Dosya: {$err['file']}\n\n";
    }
}
