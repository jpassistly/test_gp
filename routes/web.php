<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DeliverylineController;
use App\Http\Controllers\DeliverypersonController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PincodeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SubcriptionproductController;
use App\Http\Controllers\web\pages\Adminuser;
use App\Http\Controllers\web\pages\ClientWaletList;
use App\Http\Controllers\web\pages\deleviryListController;
use App\Http\Controllers\web\pages\inventryListController;
use App\Http\Controllers\web\pages\subscriptionListController;
use App\Http\Controllers\web\pages\Walletbalance;
use App\Http\Controllers\Auth\ForgotPasswordController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Razorpay\Api\Resource;




/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!!
|
 */

//Auth::routes(['verify' => true])..;
Auth::routes();
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

//Language Translation
Route::get('index/{locale}', [App\Http\Controllers\HomeController::class, 'lang']);
Route::get('test-notification', [App\Http\Controllers\NotificationController::class, 'test']);

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('root');

Route::get('contacts-profile', [App\Http\Controllers\HomeController::class, 'myprofile']);

Route::middleware(['auth'])->group(function () {

//Update User Details
    Route::post('/update-profile/{id}', [App\Http\Controllers\HomeController::class, 'updateProfile'])->name('updateProfile');
    Route::post('/update-password/{id}', [App\Http\Controllers\HomeController::class, 'updatePassword'])->name('updatePassword');

    Route::get('index', [App\Http\Controllers\HomeController::class, 'index'])->name('index');

    Route::controller(PincodeController::class)->group(function () {
        Route::get('pincode', 'list')->name('pincode');
        Route::get('add_pincode', 'add')->name('add_pincode');
        Route::get('update_pincode/{id?}', 'update')->name('update_pincode');
        Route::post('store_pincode', 'store')->name('store_pincode');
        Route::post('update_store_pincode', 'update_store')->name('update_store_pincode');
    });

    Route::controller(DeliverypersonController::class)->group(function () {
        Route::get('delivery-person', 'list')->name('delivery-person');
        Route::get('add_person', 'add')->name('add_person');
        Route::post('store_person', 'store')->name('store_person');
        Route::get('update_person/{id?}', 'update')->name('update_person');
        Route::post('update_store_person', 'update_store')->name('update_store_person');
    });

    Route::controller(DeliverylineController::class)->group(function () {
        Route::get('delivery-line', 'list')->name('delivery-line');
        Route::get('add_line', 'add')->name('add_line');
        Route::post('store_line', 'store')->name('store_line');
        Route::get('update_line/{id?}', 'update')->name('update_line');
        Route::post('update_store_line', 'update_store')->name('update_store_line');
    });

        // Route to display the update form (GET request)
        Route::get('update_category/{id}', [CategoryController::class, 'edit'])->name('category.edit');

        // Route to handle the update form submission (PUT request)
            Route::post('update_category/{id}', [CategoryController::class, 'update'])->name('category.update');

    Route::controller(CategoryController::class)->group(function () {
        Route::get('list_category', 'list')->name('list_category');
        Route::get('add_category', 'add')->name('add_category');
        Route::post('store_category', 'store')->name('store_category');
        Route::get('update_category/{id?}', 'update')->name('update_category');
        Route::post('update_store_category', 'update_store')->name('update_store_category');
    });


    Route::resource('route-mapping', App\Http\Controllers\map\routeMapping::class);
    Route::post('routemappings', [App\Http\Controllers\map\routeMapping::class,'update']);
    Route::resource('product-name', App\Http\Controllers\Master\ProductnameController::class);
    Route::resource('area', App\Http\Controllers\Master\AreaController::class);
    Route::resource('unit', App\Http\Controllers\Master\UnitnameController::class);
    Route::resource('measurement', App\Http\Controllers\Master\MeasurementController::class);
    Route::get('delivery-lins-mapping/create', [App\Http\Controllers\Route\DeliverymappingControllor::class, 'create']);
    Route::get('delivery-lins-mapping/{date}', [App\Http\Controllers\Route\DeliverymappingControllor::class, 'edit'])->name('delivery-lins-mapping-group');
    Route::resource('delivery-lins-mapping', App\Http\Controllers\Route\DeliverymappingControllor::class);
    Route::get('route-assign', [App\Http\Controllers\Route\RouteassignController::class, 'create'])->name('route-assign');
    Route::match(['get', 'post'],'route-list', [App\Http\Controllers\Route\RouteassignController::class, 'list'])->name('route-list');
    Route::post('delivery-lins-mapping-edit', [App\Http\Controllers\Route\DeliverymappingControllor::class, 'update']);

    Route::controller(ProductController::class)->group(function () {
        Route::get('list_product', 'list')->name('list_product');
        Route::get('list_product_e', 'list_e')->name('list_product_e');
        Route::get('add_product', 'add')->name('add_product');
        Route::get('add_products', 'add')->name('add_products');
        Route::post('storeorupdate', 'saveOrUpdate')->name('storeorupdate');
        Route::post('store_product', 'store')->name('store_product');
        Route::get('update_product/{id}', 'update')->name('update_product');
        Route::get('update_products/{id}', 'update')->name('update_product');
        Route::post('update_store_product', 'update_store')->name('update_store_product');
    });

    Route::controller(CustomerController::class)->group(function () {
        Route::get('list_customer', 'list')->name('list_customer');
        Route::get('new_customer', 'new_customer')->name('new_customer');
        Route::get('edited_customer', 'edited_customer')->name('edited_customer');
        Route::post('new_customer', 'new_customer')->name('new_customer');
        Route::get('customer_create', 'add')->name('customer_create');
        Route::POST('customer_create', 'customer_create')->name('customer_store');
        Route::get('add_customer', 'add')->name('add_customer');
        Route::post('store_customer', 'store')->name('store_customer');
        Route::get('update_customer/{id?}', 'update')->name('update_customer');
        Route::post('update_store_customer', 'update_store')->name('update_store_customer');
    });

    Route::controller(OrderController::class)->group(function () {
        Route::get('list_order', 'list')->name('list_order');
        Route::post('list_order', 'list')->name('list_order');
        Route::get('add_order', 'add')->name('add_order');
        Route::post('store_order', 'store')->name('store_order');
        Route::get('update_order/{id?}', 'update')->name('update_order');
        Route::post('update_store_order', 'update_store')->name('update_store_order');
    });

    Route::controller(SubcriptionproductController::class)->group(function () {
        Route::get('list_sproduct', 'list')->name('list_sproduct');
        Route::get('add_sproduct', 'add')->name('add_sproduct');
        Route::post('store_sproduct', 'store')->name('store_sproduct');
        Route::get('update_sproduct/{id?}', 'update')->name('update_sproduct');
        Route::post('update_store_sproduct', 'update_store')->name('update_store_sproduct');
    });

    Route::get('delivery_list', [deleviryListController::class, 'index']);
    Route::get('deliveries', [deleviryListController::class, 'index']);
    Route::get('rating_report', [deleviryListController::class, 'show']);
    Route::post('rating_report', [deleviryListController::class, 'show']);
    Route::post('delivery_list', [deleviryListController::class, 'index']);
    Route::post('order_list', [deleviryListController::class, 'store']);
    Route::get('order_list', [deleviryListController::class, 'store']);
    Route::get('subscriber_list', [subscriptionListController::class, 'index']);
    Route::get('supscription_plans', [subscriptionListController::class, 'create']);
    Route::get('update_plans/{id}', [subscriptionListController::class, 'update']);
    Route::post('update_plans', [subscriptionListController::class, 'edit']);
    Route::get('add_plans', [subscriptionListController::class, 'show']);
    Route::post('add_plans_value', [subscriptionListController::class, 'add_plans_value']);
    Route::get('inventry_list', [inventryListController::class, 'index']);
    Route::get('inventry_add', [inventryListController::class, 'create']);
    Route::get('inventry_add/edit/{id}', [inventryListController::class, 'edit']);
    Route::post('update_inventory/edit/{id}', [inventryListController::class, 'update']);
    Route::post('inventory_form', [inventryListController::class, 'store']);
    Route::get('client_wallet_bal', [ClientWaletList::class, 'index']);
    Route::get('client_payment_history', [ClientWaletList::class, 'create']);
    Route::get('client_wallet_history', [ClientWaletList::class, 'wallet_history']);
    Route::post('client_wallet_history', [ClientWaletList::class, 'wallet_history']);
    Route::get('transcation_list', [ClientWaletList::class, 'transcation_list']);
    Route::post('transcation_list', [ClientWaletList::class, 'transcation_list']);
    Route::post('payment_list', [ClientWaletList::class, 'create']);
    Route::get('add_gift_products/{id}', [ClientWaletList::class, 'show']);
    Route::get('add_product_cust/{id}', [ClientWaletList::class, 'add_product_cust']);
    Route::post('gift_product_save', [ClientWaletList::class, 'update']);
    Route::post('add_product_save', [ClientWaletList::class, 'update2']);
    Route::get('gift_products', [ClientWaletList::class, 'gift_products']);
    Route::post('gift_products', [ClientWaletList::class, 'gift_products']);
    Route::get('gift_amount', [ClientWaletList::class, 'gift_amount']);
    Route::post('gift_amount', [ClientWaletList::class, 'gift_amount']);
    Route::get('user_reg', [Adminuser::class, 'index']);
    Route::get('add_admin', [Adminuser::class, 'create']);
    Route::post('store_admin', [Adminuser::class, 'store']);
    Route::post('update_admin', [Adminuser::class, 'update']);
    Route::get('update_user/{id}', [Adminuser::class, 'show']);
    Route::get('deliver_list_person', [deleviryListController::class, 'create']);
    Route::post('deliver_list_person', [deleviryListController::class, 'create']);
    Route::get('wallet_plans', [Walletbalance::class, 'index'])->name('wallet.index');
    Route::get('add_walet_plans', [Walletbalance::class, 'create']);
    Route::POST('save_wallet_plan', [Walletbalance::class, 'store']);
    Route::POST('save_wallet_plan/{id}', [Walletbalance::class, 'store']);
    Route::get('update_walet_plans/{id}', [Walletbalance::class, 'edit']);
    Route::post('update_walet_plans', [Walletbalance::class, 'update']);
    Route::post('add_walet_plans_value', [Walletbalance::class, 'store']);
    Route::get('cust_list_view/{id}', [ClientWaletList::class, 'cust_list_view']);
    Route::get('cust_list_view2/{id}', [ClientWaletList::class, 'cust_list_view2']);
    Route::get('cust_list_view2_approve/{id}', [ClientWaletList::class, 'cust_list_view2_approve']);
    Route::post('cust_list_view3', [ClientWaletList::class, 'cust_list_view3']);
    Route::get('order_count/{id}', [ClientWaletList::class, 'order_count_dash']);
    Route::get('order_walet_dash/{id}', [ClientWaletList::class, 'order_walet_dash']);
    Route::get('order_subscription_dash/{id}', [ClientWaletList::class, 'order_subscription_dash']);
    Route::get('cust_loc/{id}', [ClientWaletList::class, 'cust_loc']);

    Route::get('vendor_buyer', [ClientWaletList::class, 'vendor_buyer']);
    Route::get('add_vendor_buyer', [ClientWaletList::class, 'add_vendor_buyer']);
    Route::post('add_vendor_buyer', [ClientWaletList::class, 'add_vendor_save']);
    Route::get('update_vendor/{id}', [ClientWaletList::class, 'update_vendor']);
    Route::post('update_vendor_update', [ClientWaletList::class, 'update_vendor_update']);
    Route::get('subscription_list', [subscriptionListController::class, 'subscription_list']);
    Route::post('subscription_list', [subscriptionListController::class, 'subscription_list']);
    Route::get('add_wallet/{id}', [subscriptionListController::class, 'add_wallet']);
    Route::post('save_wallet', [subscriptionListController::class, 'save_wallet']);
    Route::post('grceperiod', [subscriptionListController::class, 'grceperiod']);
    Route::post('full_delivery_list', [deleviryListController::class, 'full_delivery_list']);
    Route::get('subscription_log', [deleviryListController::class, 'subscription_log']);
    Route::post('check-notifications', [deleviryListController::class, 'check_notifications']);
    // Route::post('full_delivery_list', [deleviryListController::class, 'full_delivery_list']);
    Route::get('export-deliveries', [deleviryListController::class, 'exportDeliveries'])->name('export.deliveries');

});
Route::get('/clear-all', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('event:clear');
    return 'All caches cleared';
});
