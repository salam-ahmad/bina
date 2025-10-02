<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Deposit\DepositController;
use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\Payment\PartyPaymentsController;
use App\Http\Controllers\Payment\PaymentsController;
use App\Http\Controllers\Permission\PermissionController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Purchase\PurchaseController;
use App\Http\Controllers\Role\RoleController;
use App\Http\Controllers\Setting\SettingController;
use App\Http\Controllers\Supplier\SupplierController;
use App\Http\Controllers\Unit\UnitController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Welcome\WelcomeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return Inertia::render('Home/Index');
    })->name('home');

    Route::resource('customers', CustomerController::class)->names('customers');
    Route::resource('products', ProductController::class)->names('products');
    Route::resource('permissions', PermissionController::class)->names('permissions');
    Route::resource('roles', RoleController::class)->names('roles');
    Route::resource('deposits', DepositController::class)->names('deposits');
    Route::resource('orders', OrderController::class)->names('orders');
    Route::resource('suppliers', SupplierController::class)->names('suppliers');
    Route::resource('purchases', PurchaseController::class)->names('purchases');
    Route::resource('units', UnitController::class)->names('units');
    Route::get('/users/index', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::resource('setting', SettingController::class)->names('settings');

    Route::get('/suppliers/{supplier}/purchases', [SupplierController::class, 'purchases'])->name('suppliers.purchases');
    Route::get('/customers/{customer}/orders', [CustomerController::class, 'orders'])->name('customers.orders');
    Route::put('assign_permissions_to_roles', [RoleController::class, 'editRole']);
    Route::get('/role/{role}', [RoleController::class, 'getPermissionsByRoleId'])->name('role.permissions');
    Route::get('/welcome', [WelcomeController::class, 'index'])->name('welcome');


    Route::get('/register', [AuthController::class, 'create'])->name('register');
    Route::post('/register', [AuthController::class, 'store']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('deposits/{id}/order_id', [DepositController::class, 'depositByOrderId']);
    Route::get('create_order', [OrderController::class, 'create'])->name('create_order');

//    Reports
    Route::post('/orders/{order}/payments/receive', [PaymentsController::class, 'receiveForOrder'])->name('payments.receiveForOrder');
    Route::post('/purchases/{purchase}/payments/pay', [PaymentsController::class, 'payForPurchase'])->name('payments.payForPurchase');
// Pages (reports & simple forms)
    Route::get('/reports/ar', [PaymentsController::class, 'arReport'])->name('reports.ar');
    Route::get('/reports/ar-owing', [PaymentsController::class, 'arOnlyOwing'])->name('reports.ar_owing');
    Route::get('/reports/ap', [PaymentsController::class, 'apReport'])->name('reports.ap');
// Optional: lightweight forms for a single order/purchase payment
    Route::get('/orders/{order}/payments/receive', [PaymentsController::class, 'receiveForOrderPage'])->name('payments.receiveForOrderPage');
    Route::get('/purchases/{purchase}/payments/pay', [PaymentsController::class, 'payForPurchasePage'])->name('payments.payForPurchasePage');
    // routes/web.php
    Route::get('/payments/receive', [PaymentsController::class, 'receiveIndex'])->name('payments.receive_form');
    Route::get('/payments/pay', [PaymentsController::class, 'payIndex'])->name('payments.pay_form');
    Route::get('/dashboard/finance', [PaymentsController::class, 'dashboard'])->name('dashboard.finance');
    Route::get('/suppliers/{supplier}/payments', [PartyPaymentsController::class, 'supplier'])->name('suppliers.payments');

    Route::get('/customers/{customer}/payments', [PartyPaymentsController::class, 'customer'])->name('customers.payments');
    Route::get('/edit_password', [AuthController::class, 'editPassword'])->name('edit_password');
    Route::put('/updatePassword', [AuthController::class, 'updatePassword'])->name('updatePassword');
});
Route::middleware('guest')->group(function () {
    Route::inertia('/login', 'Auth/Login')->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'create'])->name('register');
    Route::post('/register', [AuthController::class, 'store']);
});
