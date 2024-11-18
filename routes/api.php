<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeliverypersonController;
use App\Http\Controllers\DeliveryapiController;
use App\Http\Controllers\CustomerapiController;
use App\Http\Controllers\web\pages\subscriptionListController;
use App\Http\Controllers\web\pages\inventryListController;
use App\Http\Controllers\web\pages\ClientWaletList;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Carbon;
use App\Http\Controllers\web\pages\deleviryListController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('test-time', function (Request $request) {

    $currentDateTime = Carbon::now();

    return response()->json([
        'current_time' => $currentDateTime->toDateTimeString(),
        'timezone' => config('app.timezone'),
    ]);
});


Route::post('test-api', function (Request $request) {
    // Log incoming request headers
    Log::info('Incoming request headers:', $request->headers->all());
    $headers = $request->headers->all();


    // Get all POST data
    $postData = $request->all();

    $token = $request->header('Authorization');

    // If the token is in the format "Bearer {token}", you might want to extract just the token part
    if (preg_match('/Bearer\s(\S+)/', $token, $matches)) {
        $token = $matches[1];
    }

    // print_r($token);

    // Combine both the token and POST data into a response
    return response()->json([
        'token' => $headers,
        'data' => $postData,
    ]);
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(DeliveryapiController::class)->group(function () {
    Route::post('delivery_login', 'delivery_login')->name('delivery.login');
});
//selva..
Route::post('get-area-list', [App\Http\Controllers\Master\AreaController::class, 'getarea']);
Route::post('get-customers-list', [App\Http\Controllers\CustomerController::class, 'getcustomer']);
Route::post('delivery_line', [App\Http\Controllers\CustomerController::class, 'getcustomer']);
Route::post('save-area-route-list', [App\Http\Controllers\Route\RouteassignController::class, 'saveroute']);
Route::post('get-delivery-line-list', [App\Http\Controllers\Route\RouteassignController::class, 'getdellist']);


Route::controller(CustomerapiController::class)->group(function () {
    Route::post('customer_login', 'customer_login')->name('customer.login');
    Route::post('check_otp', 'check_otp')->name('check.otp');
});

//prem-using-start
Route::post('subscription-product-web', [App\Http\Controllers\web\pages\deleviryListController::class, 'create']);
Route::post('delivery_list_cust_dash', [App\Http\Controllers\web\pages\deleviryListController::class, 'delivery_list_cust_dash']);
Route::post('order_list_view', [ClientWaletList::class, 'order_list_view']);
Route::post('cust_list_view', [ClientWaletList::class, 'cust_list_view']);
Route::post('get-delivery-schedule', [App\Http\Controllers\Route\DeliverymappingControllor::class, 'get_old_datas']);









//prem-using-end
Route::middleware(['check_cus.remember_token', 'throttle:1000,1'])->group(function () {
    Route::post('customer_person_fcm_save', [CustomerapiController::class, 'save_fcm'])->name('get.save_fcm');
    Route::get('get_pincodes', [CustomerapiController::class, 'get_pincodes'])->name('get.pincode');
    Route::post('get-area', [CustomerapiController::class, 'get_area'])->name('get.area');
    Route::post('get-city', [CustomerapiController::class, 'get_city'])->name('get.city');
    Route::post('get-product-names', [CustomerapiController::class, 'product_names']);
    Route::post('store_address', [CustomerapiController::class, 'store_address'])->name('store.address');
    Route::post('check_addressverification', [CustomerapiController::class, 'check_addressverification']);
    Route::get('get_productcategory', [CustomerapiController::class, 'get_productcategory'])->name('product.category');
    Route::post('get_productbycategory', [CustomerapiController::class, 'get_productbycategory'])->name('productby.category');
    Route::get('get_subscriptionplans', [CustomerapiController::class, 'get_subscriptionplans'])->name('subscription.plans');
    Route::post('get_subscriptionprice', [CustomerapiController::class, 'get_subscriptionprice'])->name('subscription.price');
    Route::post('add_tempcart', [CustomerapiController::class, 'addproduct_tempcart'])->name('add.tempcart');
    Route::post('show_cart', [CustomerapiController::class, 'show_cart'])->name('show.tempcart');
    Route::post('update_cart', [CustomerapiController::class, 'update_cart'])->name('update.tempcart');
    Route::post('add_cart', [CustomerapiController::class, 'add_cart'])->name('add.cart');
    Route::post('delete_item', [CustomerapiController::class, 'delete_cartitem'])->name('delete.cartitem');
    Route::post('save_order', [CustomerapiController::class, 'save_order'])->name('save.order');
    Route::post('save_subscription', [CustomerapiController::class, 'save_subscription'])->name('save.subscription');
    Route::post('get_address', [CustomerapiController::class, 'get_address'])->name('show.address');
    Route::post('edit_address', [CustomerapiController::class, 'edit_address']);
    Route::post('show_subscription', [CustomerapiController::class, 'show_subscription'])->name('show.subscription');
    Route::post('get_editdata', [CustomerapiController::class, 'get_editdata'])->name('edit.data');
    Route::post('edit_profile', [CustomerapiController::class, 'edit_profile'])->name('edit.profile');
    Route::post('removeproduct_subscription', [CustomerapiController::class, 'removeproduct_subscription'])->name('remove.product');
    Route::post('reduce_litters', [CustomerapiController::class, 'reduce_liters'])->name('reduce_litters.product');
    Route::post('check_subscription', [CustomerapiController::class, 'check_subscription'])->name('check.subscription');
    Route::post('check_subscription2', [CustomerapiController::class, 'check_subscription2'])->name('check.subscription2');
    Route::post('wallet_recharge', [CustomerapiController::class, 'wallet_recharge'])->name('wallet.recharge');
    Route::post('wallet_history', [CustomerapiController::class, 'wallet_history'])->name('wallet.wallet_history');
    Route::post('check_address', [CustomerapiController::class, 'check_address'])->name('check.address');
    Route::post('save_complaints', [CustomerapiController::class, 'save_complaints'])->name('save.complaints');
    Route::post('payment_details', [CustomerapiController::class, 'payment_details'])->name('payment.details');
    Route::post('get_walletamount', [CustomerapiController::class, 'get_walletamount'])->name('wallet.amount');
    Route::post('payment_history', [CustomerapiController::class, 'payment_history'])->name('payment.history');
    Route::post('supscription_confrim', [CustomerapiController::class, 'supscription_confrim'])->name('payment.supscription_confrim');
    // Route::post('reduce_litters', [CustomerapiController::class, 'reduce_litters'])->name('payment.reduce_litters');
});





    Route::post('version_check', [CustomerapiController::class, 'version_check'])->name('version.check');
// Route::middleware(['check_del.remember_token_delivery', 'throttle:1000,1'])->group(function () {
    Route::post('delivery_person_fcm_save', [DeliveryapiController::class, 'save_fcm'])->name('save_fcm');
    Route::post('get_profile', [DeliveryapiController::class, 'get_profile'])->name('profile');
    Route::post('edit_profiledata', [DeliveryapiController::class, 'edit_profiledata']);
    Route::post('ratings', [subscriptionListController::class, 'save_ratings']);
    Route::post('update_inventory/edit/{id}', [inventryListController::class, 'update']);
    Route::post('delivery-list', [ClientWaletList::class, 'deliverylist']);
    Route::post('to_be_delivered', [ClientWaletList::class, 'to_be_delivered']);
    Route::post('delivery_status_update', [ClientWaletList::class, 'delivery_status_update']);
    Route::post('trip-start', [ClientWaletList::class, 'tripstart']);
    Route::post('trip-end', [ClientWaletList::class, 'tripend']);
    Route::post('update_cus_lat_long', [CustomerapiController::class, 'update_cus_lat_long']);
    Route::post('client_home', [CustomerapiController::class, 'client_home']);
    Route::post('check-notifications', [deleviryListController::class, 'check_notifications']);
    Route::post('check-notifications2', [deleviryListController::class, 'check_notifications2']);
// });

Route::post('client_walet', [ClientWaletList::class, 'store']);
Route::post('gift_payment', [ClientWaletList::class, 'view']);
Route::post('client_detail', [ClientWaletList::class, 'client_detail']);
Route::post('wallet_balance', [ClientWaletList::class, 'wallet_balance']);
Route::post('subscription', [ClientWaletList::class, 'subscription']);
Route::post('order_count', [ClientWaletList::class, 'order_count']);
Route::post('add_gift_amount', [ClientWaletList::class, 'add_gift_amount']);
Route::post('add_gift_amount2', [ClientWaletList::class, 'add_gift_amount2']);
Route::post('pro_sts', [ClientWaletList::class, 'pro_sts']);
Route::post('add_on', [ClientWaletList::class, 'add_on']);
Route::post('rating_star', [ClientWaletList::class, 'rating_star']);
Route::post('sub_plans', [ClientWaletList::class, 'sub_plans']);
Route::post('payment_plan', [subscriptionListController::class, 'payment_plan']);
Route::post('get_to_date', [subscriptionListController::class, 'get_to_date']);
Route::post('calculate', [subscriptionListController::class, 'calculate']);
Route::post('save_subscription_web', [CustomerapiController::class, 'save_subscription2'])->name('save.subscription');
Route::get('full_delivery_list', [deleviryListController::class, 'full_delivery_list'])->name('save.full_delivery_list');