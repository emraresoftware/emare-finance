<?php

use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\SaleApiController;
use App\Http\Controllers\Api\CustomerApiController;
use App\Http\Controllers\Api\ReportApiController;
use App\Http\Controllers\Api\StockApiController;
use Illuminate\Support\Facades\Route;

// ══════════════════════════════════════════════════════════════
// API v1 — auth:sanctum + module + permission korumalı
// ══════════════════════════════════════════════════════════════
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {

    // ─── Dashboard ───
    Route::get('/dashboard', [DashboardApiController::class, 'index']);

    // ─── Ürünler ─── (permission: products.view)
    Route::middleware('permission:products.view')->group(function () {
        Route::get('/products', [ProductApiController::class, 'index']);
        Route::get('/products/low-stock', [ProductApiController::class, 'lowStock']);
        Route::get('/products/categories', [ProductApiController::class, 'categories']);
        Route::get('/products/{product}', [ProductApiController::class, 'show']);
    });

    // ─── Satışlar ─── (permission: sales.view)
    Route::middleware('permission:sales.view')->group(function () {
        Route::get('/sales', [SaleApiController::class, 'index']);
        Route::get('/sales/summary', [SaleApiController::class, 'summary']);
        Route::get('/sales/{sale}', [SaleApiController::class, 'show']);
    });

    // ─── Cariler ─── (permission: customers.view)
    Route::middleware('permission:customers.view')->group(function () {
        Route::get('/customers', [CustomerApiController::class, 'index']);
        Route::get('/customers/{customer}', [CustomerApiController::class, 'show']);
        Route::get('/customers/{customer}/sales', [CustomerApiController::class, 'sales']);
    });

    // ─── Raporlar ─── (permission: reports.view)
    Route::middleware('permission:reports.view')->group(function () {
        Route::get('/reports/daily', [ReportApiController::class, 'daily']);
        Route::get('/reports/top-products', [ReportApiController::class, 'topProducts']);
        Route::get('/reports/revenue-chart', [ReportApiController::class, 'revenueChart']);
        Route::get('/reports/payment-methods', [ReportApiController::class, 'paymentMethods']);
    });

    // ─── Stok ─── (permission: stock.view)
    Route::middleware('permission:stock.view')->group(function () {
        Route::get('/stock/overview', [StockApiController::class, 'overview']);
        Route::get('/stock/movements', [StockApiController::class, 'movements']);
        Route::get('/stock/alerts', [StockApiController::class, 'alerts']);
    });
});

// ══════════════════════════════════════════════════════════════
// Legacy API (geriye uyumluluk — ileride kaldırılacak)
// ══════════════════════════════════════════════════════════════
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/dashboard', [DashboardApiController::class, 'index']);
    Route::get('/products', [ProductApiController::class, 'index']);
    Route::get('/products/low-stock', [ProductApiController::class, 'lowStock']);
    Route::get('/products/categories', [ProductApiController::class, 'categories']);
    Route::get('/products/{product}', [ProductApiController::class, 'show']);
    Route::get('/sales', [SaleApiController::class, 'index']);
    Route::get('/sales/summary', [SaleApiController::class, 'summary']);
    Route::get('/sales/{sale}', [SaleApiController::class, 'show']);
    Route::get('/customers', [CustomerApiController::class, 'index']);
    Route::get('/customers/{customer}', [CustomerApiController::class, 'show']);
    Route::get('/customers/{customer}/sales', [CustomerApiController::class, 'sales']);
    Route::get('/reports/daily', [ReportApiController::class, 'daily']);
    Route::get('/reports/top-products', [ReportApiController::class, 'topProducts']);
    Route::get('/reports/revenue-chart', [ReportApiController::class, 'revenueChart']);
    Route::get('/reports/payment-methods', [ReportApiController::class, 'paymentMethods']);
    Route::get('/stock/overview', [StockApiController::class, 'overview']);
    Route::get('/stock/movements', [StockApiController::class, 'movements']);
    Route::get('/stock/alerts', [StockApiController::class, 'alerts']);
});
