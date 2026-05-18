<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\TableController as AdminTableController;
use App\Http\Controllers\Api\CategoryController as ApiCategoryController;
use App\Http\Controllers\Api\ProductController as ApiProductController;
use App\Http\Controllers\Api\OrderController as ApiOrderController;
use App\Http\Controllers\Admin\AnalyticsController as AdminAnalyticsController;
use App\Http\Controllers\Api\Admin\DashboardController as ApiAdminDashboardController;
use App\Http\Controllers\Api\Admin\OrderController as ApiAdminOrderController;
use App\Http\Controllers\Api\Admin\ProductController as ApiAdminProductController;
use App\Http\Controllers\Api\Admin\AnalyticsController as ApiAdminAnalyticsController;
use App\Http\Controllers\Api\Admin\CategoryController as ApiAdminCategoryController;
use App\Http\Controllers\Api\Admin\TableController as ApiAdminTableController;

// Public pages
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/menu', [MenuController::class, 'index'])->name('menu');
Route::get('/product/{id}', [MenuController::class, 'show'])->name('product');
Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::get('/order/{orderNumber}', [OrderTrackingController::class, 'show'])->name('order.show');

// Public API
Route::prefix('api')->group(function () {
    Route::get('/categories', [ApiCategoryController::class, 'index']);
    Route::get('/products', [ApiProductController::class, 'index']);
    Route::get('/products/{id}', [ApiProductController::class, 'show']);
    Route::post('/orders', [ApiOrderController::class, 'store']);
    Route::get('/orders/{orderNumber}', [ApiOrderController::class, 'show']);
    Route::get('/tables', fn() => response()->json(
        \App\Models\CoffeeTable::orderBy('table_number')->get(['id', 'table_number', 'status'])
    ));
});

// Admin auth
Route::get('/admin/login', [AuthController::class, 'index'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Admin pages (protected)
Route::prefix('admin')->middleware('admin.auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('admin.orders');
    Route::get('/products', [AdminProductController::class, 'index'])->name('admin.products');
    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('admin.categories');
    Route::get('/tables', [AdminTableController::class, 'index'])->name('admin.tables');
    Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('admin.analytics');

    // Admin API (AJAX)
    Route::prefix('api')->group(function () {
        Route::get('/dashboard', [ApiAdminDashboardController::class, 'index']);
        Route::get('/orders', [ApiAdminOrderController::class, 'index']);
        Route::patch('/orders/{id}', [ApiAdminOrderController::class, 'update']);
        Route::get('/products', [ApiAdminProductController::class, 'index']);
        Route::post('/products', [ApiAdminProductController::class, 'store']);
        Route::put('/products/{id}', [ApiAdminProductController::class, 'update']);
        Route::delete('/products/{id}', [ApiAdminProductController::class, 'destroy']);
        Route::get('/categories', [ApiAdminCategoryController::class, 'index']);
        Route::post('/categories', [ApiAdminCategoryController::class, 'store']);
        Route::put('/categories/{id}', [ApiAdminCategoryController::class, 'update']);
        Route::delete('/categories/{id}', [ApiAdminCategoryController::class, 'destroy']);
        Route::get('/tables', [ApiAdminTableController::class, 'index']);
        Route::post('/tables', [ApiAdminTableController::class, 'store']);
        Route::put('/tables/{id}', [ApiAdminTableController::class, 'update']);
        Route::delete('/tables/{id}', [ApiAdminTableController::class, 'destroy']);
        Route::get('/analytics', [ApiAdminAnalyticsController::class, 'index']);
    });
});
