<?php

namespace App\Http\Controllers\web\pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wallet;
use App\Models\Customer;
use App\Models\Customerwallethistory;
use App\Models\ProductName;
use App\Models\Customerpayment;
use App\Models\Product;
use App\Models\Quantity;
use App\Models\Measurement;
use App\Models\Order;
use App\Models\Cart;
use App\Models\giftproduct;
use App\Models\giftamount;
use App\Models\delivery_line_mapping;
use Carbon\Carbon;
use App\Models\Unit;
use App\Models\Deliveryperson;
use App\Models\Customersubscription;
use App\Models\Subscriptionproduct;
use App\Models\Pincode;
use App\Models\Area;
use App\Models\Delivery_lines;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\vendor;
use App\Models\CustomerPlansTrack;
use App\Models\Subscriptionplan;
use App\Http\Controllers\NotificationController;
use App\Models\Customer_fcm;
// use App\Models\use App\Models\wallet_balance;;
use App\Models\wallet_balance;
use Illuminate\Support\Facades\Auth;

class ClientWaletList extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $wallet = Wallet::get();

        foreach ($wallet as $wall) {
            $cus = Customer::find($wall->customers_id);
            if ($cus) {
                $wall->customers_id = $cus->name;
                $wall->customers_mobile = $cus->mobile;
            }
        }
        return view('client_walet.index', compact('wallet'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
       
        $wallet_plans=wallet_balance::where('status','Active')->get();
        // dd($wallet_plans);
        if ($request->isMethod('post')) {
            // dd($request);
            if($request->plan ==""){
                $amount=0;
            }else{
                $amount=$request->plan;
            }
        //    dd($amount);
            $from_date = Carbon::parse($request->from_date)->startOfDay();
            $to_date = Carbon::parse($request->to_date)->endOfDay();

            // Get the delivery list within the specified date range
            // $delivery_list = Customerpayment::whereBetween('created_at', [$from_date, $to_date])
            //     ->orderBy('created_at', 'desc')
            //     ->where('payment_status', 'success')
            //     ->where('amount','>=',$amount)
            //     ->get();
            $delivery_list = Customerpayment::select('customer_payments_history.*','customer_payments_history.customers_id as cid') // Select all columns from Customerpayment
            ->selectSub(function ($query) {
                $query->select('last_gift_at')
                    ->from('customer_wallet')
                    ->whereColumn('customer_payments_history.customers_id', 'customer_wallet.customers_id')
                    ->orderBy('last_gift_at', 'desc') // Optional: order by last_gifted_date if needed
                    ->limit(1); // Limit to one record to get the latest gift date
            }, 'last_gift_at') // Alias for the subquery
            ->whereBetween('customer_payments_history.created_at', [$from_date, $to_date])
            ->where('customer_payments_history.payment_status', 'success')
            ->where('customer_payments_history.amount', '>=', $amount)
            ->orderBy('customer_payments_history.created_at', 'desc')
            ->get();
        // dd($delivery_list);
        //     $delivery_list = Customerpayment::whereBetween('created_at', [$from_date, $to_date])
        //     ->where('payment_status', 'success')
        //     ->where('amount', '>=', $amount)
        //     ->orderBy('created_at', 'desc');
        
        // // Output raw SQL query with placeholders
        // echo $delivery_list->toSql();
        
        // // If you want to see the full query with bindings:
        // $bindings = [$from_date, $to_date, 'success', $amount];
        // $sql = str_replace_array('?', $bindings, $delivery_list->toSql());
        
        // echo $sql;
           
        } else {
            $delivery_list = Customerpayment::select('customer_payments_history.*','customer_payments_history.customers_id as cid') // Select all columns from Customerpayment
            ->selectSub(function ($query) {
                $query->select('last_gift_at')
                    ->from('customer_wallet')
                    ->whereColumn('customer_payments_history.customers_id', 'customer_wallet.customers_id')
                    ->orderBy('last_gift_at', 'desc') // Optional: order by last_gifted_date if needed
                    ->limit(1); // Limit to one record to get the latest gift date
            }, 'last_gift_at') // Alias for the subquery
            ->where('payment_status', 'success')
            ->orderBy('created_at', 'desc')
            ->get();
        }        
// dd($delivery_list);
        foreach ($delivery_list as &$delv) {
            $cus = Customer::find($delv->customers_id);
            if ($cus) {
                // $delv->customers_id = $cus->name . " " . $cus->mobile;
                $delv->customers_id = $cus->name;
                $delv->customers_mobile = $cus->mobile;
            }
        }
        return view('payment.index', compact('delivery_list','wallet_plans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);
        $id = $request->id;
        // $wallet = Wallet::where('id', $id)->first();
        $wallet = Wallet::where('customers_id', $id)->first();
        // dd($wallet);
        $cus = Customer::find($wallet->customers_id);
        $clientName = $cus->name . " -" . $cus->mobile;
        $clientName = $cus->name;
        // $history = Customerwallethistory::where('customer_wallet_id', $id)->orderBy('created_at', 'DESC')->get();
        $history = Customerwallethistory::where('customer_id', $id)->orderBy('created_at', 'DESC')->get();

        $table = '<div class="row">';
        $table .= '<h4 class="text-center font-weight-bold">Customer Name : ' . $clientName . '</h4>';
        $table .= '<table class="table">';
        $table .= '<thead><tr><th>Date</th><th>Status</th><th>Amount</th><th>Remark</th></tr></thead>';
        $table .= '<tbody>';

        foreach ($history as $his) {
            $table .= '<tr>';
            $table .= '<td class="text-secondary text-center">' . $his->created_at->format('d-m-Y') . '</td>';
            $table .= '<td class="text-center text-success ' . ($his->debit_credit_status == 'debited' ? 'text-danger' : '') . ' ' . ($his->debit_credit_status == 'Your Gift Amount' ? 'text-info' : '') . '">' . $his->debit_credit_status . '</td>';
            $table .= '<td class="text-secondary text-center">' . $his->amount . '</td>';
            $table .= '<td>' . ($his->remarks != '' ? $his->remarks : $his->notes) . '</td>';
            $table .= '</tr>';
        }

        $table .= '</tbody>';
        $table .= '</table>';
        $table .= '</div>';

        return response()->json(['success' => true, 'message' => 'Updated successfully', 'data' => $table, 'name' => $clientName]);
    }

    // payment history gift view 

    public function view(Request $request)
{
    // Set static ID or retrieve from the request
    $id = $request->id; // Or $request->input('id') if passed via the request
    // $id = 4; // Or $request->input('id') if passed via the request

    // Retrieve the gift products for the customer
    $giftproducts = GiftProduct::where('customer_id', $id)->get();

    // Check if gift products exist
    if ($giftproducts->isEmpty()) {
        return response()->json(['success' => false, 'message' => 'No gift products found for this customer.']);
    }

    // Retrieve customer data
    $cus = Customer::find($id);
    if (!$cus) {
        return response()->json(['success' => false, 'message' => 'Customer not found.']);
    }

    // Prepare the client name
    $clientName = $cus->name . " -" . $cus->mobile;

    // Prepare the table HTML content
    $table = '<div class="row">';
    $table .= '<h5 class="text-center font-weight-bold">Customer Name : ' . $clientName . '</h5>';
    $table .= '<table class="table">';
    $table .= '<thead><tr><th>S.No.</th><th>Date</th><th>Product Name</th><th>Quantity</th><th>Measurment</th></tr></thead>';
    $table .= '<tbody>';

    // Initialize $i outside the loop to ensure it increments properly
    $i = 1;

    // Loop through the gift products and get product details
    foreach ($giftproducts as $wall) {
        // Get product details
        $product = Product::select(
            'products.*',
            'units.name as quantity_name',
            'measurements.name as measurement_name',
            'product_names.name as product_name',
            'giftproducts.quantity as qty'
        )
        ->leftJoin('units', 'products.quantity_id', '=', 'units.id')
        ->leftJoin('measurements', 'products.measurement_id', '=', 'measurements.id')
        ->leftJoin('product_names', 'products.product_id', '=', 'product_names.id')
        ->leftJoin('giftproducts', 'products.product_id', '=', 'giftproducts.product_id')
        ->where('products.id', $wall->product_id)
        ->first();

        // Skip if no product found
        if (!$product) {
            continue;
        }

        // Add table row for each product
        $table .= '<tr>';
        $table .= '<td class="text-secondary text-center">' . ($i) . '</td>';
        $table .= '<td class="text-secondary text-center">' . ($product->created_at ? $product->created_at->format('d-m-Y') : 'N/A') . '</td>';
        $table .= '<td class="text-secondary text-center">' . ($product->product_name) . '</td>';
        $table .= '<td class="text-secondary text-center">' . ($product->qty) . '</td>';
        $table .= '<td class="text-secondary text-center">' . ($product->quantity_name . ' ' . $product->measurement_name) . '</td>';
        $table .= '</tr>';

        // Increment $i after each iteration
        $i++;
    }

    $table .= '</tbody>';
    $table .= '</table>';
    $table .= '</div>';

    return response()->json([
        'success' => true,
        'message' => 'Updated successfully',
        'data' => $table,
        'name' => $clientName
    ]);
}

    


    /**
     * Display the specified resource.
     */
    public function delivery_status_update(Request $request)
    {
        // Explode the subscription and order IDs
        $subscription_ids = explode(',', $request->subscription_id);
        $order_ids = explode(',', $request->order_id);

        // Update delivery status for subscriptions
        $update = Customersubscription::whereIn('id', $subscription_ids)->update([
            'delivery_lat_lon' => $request->delivery_lat_lon,
            'delivery_status' => $request->delivery_status,
            'delivery_at' => now(),
        ]);

        // Update delivery status for orders
        Order::whereIn('id', $order_ids)->update([
            'delivery_lat_lon' => $request->delivery_lat_lon,
            'delivery_status' => $request->delivery_status,
            'delivery_at' => now(),
        ]);



        // Retrieve the first subscription from the provided subscription IDs
        $cus = Customersubscription::whereIn('id', $subscription_ids)->first();

        if ($cus) {
            // Get the FCM token and customer details
            $get_fcm = Customer_fcm::where('customer_id', $cus->subscription_customer_id)->first();
            $cus_details = Customer::where('id', $cus->subscription_customer_id)->first();

            if ($get_fcm && $cus_details) {
                // Prepare notification data

                if ($request->delivery_status == 'Delivered') {
                    $title = 'Product Delivered';
                    $message_body = 'Dear ' . $cus_details->name . ', Your Product Delivered Successfully. Give Your Valuable Feedback!';
                }

                if ($request->delivery_status == 'Undelivered') {
                    $title = 'Product Undelivered';
                    $message_body = 'Dear ' . $cus_details->name . ', Cant Reach-out You So Your Product is Undelivered';
                }

                $image = null; // Replace with your image URL if needed
                $link = null;  // Replace with your link if needed

                // Instantiate the NotificationController and send the notification
                $notificationController = new NotificationController();
                $notification = $notificationController->sendNotification($get_fcm->fcm_key, $title, $message_body, $image, $link);
            }
        }


        return response()->json(['message' => 'Delivery Status Update Successfully ']);
    }

    public function tripend(Request $request)
    {
        // Find the delivery line mapping by ID
        $trip_end = delivery_line_mapping::where('id', $request->delivery_line_id)->first();

        // Check if the delivery line mapping was found
        if ($trip_end) {
            // Set the trip_end to the current timestamp
            $trip_end->trip_end = now();

            // Save the changes to the database
            $trip_end->save();

            return response()->json(['message' => 'Trip End time updated successfully.']);
        } else {
            // Handle the case where the delivery line mapping was not found
            return response()->json(['error' => 'Delivery line not found.'], 404);
        }
    }
    public function tripstart(Request $request)
    {
        // Find the delivery line mapping by ID
        $trip_start = delivery_line_mapping::where('id', $request->delivery_line_id)->first();

        // Check if the delivery line mapping was found
        if ($trip_start) {
            // Set the trip_start to the current timestamp
            $trip_start->trip_start = now();

            // Save the changes to the database
            $trip_start->save();

            return response()->json(['message' => 'Trip Start time updated successfully.']);
        } else {
            // Handle the case where the delivery line mapping was not found
            return response()->json(['error' => 'Delivery line not found.'], 404);
        }
    }


    public function show(Request $request)
    {
        $product = Product::select(
            'products.*',
            'units.name as quantity_name',
            'measurements.name as measurement_name',
            'product_names.name as product_name',
        )
            ->join('units', 'products.quantity_id', '=', 'units.id')
            ->join('measurements', 'products.measurement_id', '=', 'measurements.id')
            ->join('product_names', 'products.product_id', '=', 'product_names.id')
            // ->where('products.status', 'Active')
            ->get();

        // dd($product);

        $quan = Unit::where('status', 'Active')->get();
        $meas = Measurement::where('status', 'Active')->get();
        return view('client_walet.add_form', compact('product', 'quan', 'meas'));
    }
    public function add_product_cust(Request $request, string $id)
    {
        $product = Product::where('status', 'Active')->get();
        $quan = Quantity::where('status', 'Active')->get();
        $meas = Measurement::where('status', 'Active')->get();
        return view('client_walet.add_pro', compact('product', 'quan', 'meas', 'id'));
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    private function generateOrderId()
    {
        do {
            $order_id = 'ord' . rand(100000, 999999);
        } while (Order::where('order_id', $order_id)->exists());

        return $order_id;
    }

    public function update(Request $request)
    {
        // dd($request);
        $wallet_id = $request->walet_id;
        $delivery_date = $request->date;
        $wallet = Wallet::where('id', $wallet_id)->first();

        $wallet->last_gift_at = now();
        $wallet->save();

        $cus = Customer::where('id', $wallet->customers_id)->first();

        $order_id = $this->generateOrderId();
        $order = Order::create([
            'order_id' => $order_id,
            'customer_id' => $wallet->customers_id,
            'price' => 0,
            'area' => $cus->area_id,
            'cus_lat_lon' => $cus->latlon,
            'pincode' => $cus->pincode_id,
            'delivery_status' => "yet to deliver",
            'delivery_date' => $delivery_date,
        ]);
        $cart = Cart::create([
            'customers_id' => $wallet->customers_id,
            'order_id' => $order_id,
            'product_id' => $request->product,
            'quantity' => $request->pack,
            'price' => 0
        ]);
        $giftproduct = giftproduct::create([
            'customer_id' => $wallet->customers_id,
            'order_id' => $order_id,
            'product_id' => $request->product,
            'quantity' => $request->pack,
            'price' => 0,
            'measurment' => $request->measurement,
            'delivery_status' => "Gift Reward",
            'delivery_date' => $delivery_date,
        ]);
        return redirect('client_wallet_bal')->with('success_message', 'Gift Created Sucessfully!..');
    }
    public function update2(Request $request)
    {
        // "product" => "1"
        // "quantity" => "1"
        // "measurement" => "2"
        // "pack" => "1"
        // "price" => "37"
        // "date" => "2024-07-18"
        // "cust_id" => "16"
        $cust_id = $request->cust_id;
        $date = $request->date;
        $price = $request->price;
        $pack = $request->pack;
        $final_price = $price * $pack;

        $cus = Customer::where('id', $cust_id)->first();
        $order_id = $this->generateOrderId();
        $order = Order::create([
            'order_id' => $order_id,
            'customer_id' => $cust_id,
            'price' => $final_price,
            'area' => $cus->area_id,
            'cus_lat_lon' => $cus->latlon,
            'pincode' => $cus->pincode_id,
            'delivery_status' => "yet to deliver",
            'delivery_date' => $date,
        ]);
        $cart = Cart::create([
            'customers_id' => $cust_id,
            'order_id' => $order_id,
            'product_id' => $request->product,
            'quantity' => $request->pack,
            'price' => $final_price
        ]);
        return redirect('list_order')->with('success_message', 'Gift Created Sucessfully!..');
    }
    public function to_be_delivered(Request $request)
    {
        // Retrieve the date and deliveryperson_id from the request
        $date = $request->date;
        $deliveryperson_id = $request->deliveryperson_id;

        // Get the delivery line ID for the given date and delivery person
        $delivery_line_mapping = delivery_line_mapping::where([
            'date' => $date,
            'delivery_staff_id' => $deliveryperson_id
        ])->first();

        if ($delivery_line_mapping) {
            // Get the delivery line name
            $delivery_line = Delivery_lines::find($delivery_line_mapping->delivery_line_id);

            // Get active product names
            $productname = ProductName::where(['status' => 'Y', 'del' => 0])->select('name', 'id')->get();

            // Get subscription quantities grouped by product ID
            $subscriptions = DB::table('customers_subscription')
                ->select('subscription_products_id', DB::raw('SUM(subscription_quantity) as total_quantity'))
                ->where('date', $date)
                ->where('deliveryperson_id', $deliveryperson_id)
                ->where('delivery_status', 'yet to deliver')
                ->groupBy('subscription_products_id')
                ->get();

            // Extract the product IDs
            $product_ids = $subscriptions->pluck('subscription_products_id');

            // Retrieve all product details for the relevant product IDs
            $pro_details = Product::select(
                'products.*',
                'products.price as product_price',
                'products.product_id as product_name_id',
                'products.name as product_name',
                'products.pic as product_image',
                'measurements.name as measurement_name',
                'units.name as quantity_value'
            )
                ->join('measurements', 'products.measurement_id', '=', 'measurements.id')
                ->join('units', 'products.quantity_id', '=', 'units.id')
                ->get();

            // Merge product details with subscription data
            foreach ($subscriptions as $subscription) {
                $details_all = $pro_details->where('id', $subscription->subscription_products_id)->first();
                if ($details_all) {
                    $subscription->product_name_id = $details_all->product_name_id;
                    $subscription->product_name = $details_all->product_name;
                    $subscription->measurement_name = $details_all->measurement_name;
                    $subscription->quantity_value = $details_all->quantity_value;
                    $subscription->product_image = asset($details_all->product_image);
                    $subscription->label = $subscription->total_quantity . ' * ' . $details_all->quantity_value . ' ' . $details_all->measurement_name;
                }
            }

            // Get order quantities grouped by product ID
            $orders = DB::table('orders')
                ->select('products_id', DB::raw('SUM(quantity) as total_quantity'))
                ->where('delivery_date', $date)
                ->where('delivered_by', $deliveryperson_id)
                ->where('delivery_status', 'yet to deliver')
                ->groupBy('products_id')
                ->get();

            // Merge orders with subscriptions
            foreach ($orders as $order) {
                $subscription = $subscriptions->where('subscription_products_id', $order->products_id)->first();
                $details_all = $pro_details->where('id', $order->products_id)->first();

                if ($subscription) {
                    $subscription->total_quantity += $order->total_quantity;
                    $subscription->label = $subscription->total_quantity . ' * ' . $details_all->quantity_value . ' ' . $details_all->measurement_name;
                } else {
                    $subscriptions->push((object) [
                        'subscription_products_id' => $order->products_id,
                        'total_quantity' => $order->total_quantity,
                        'product_name_id' => $details_all->product_name_id,
                        'product_name' => $details_all->product_name,
                        'measurement_name' => $details_all->measurement_name,
                        'quantity_value' => $details_all->quantity_value,
                        'product_image' => asset($details_all->product_image),
                        'label' => $order->total_quantity . ' * ' . $details_all->quantity_value . ' ' . $details_all->measurement_name,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'delivery_line_name' => $delivery_line ? $delivery_line->name : '',
                'delivery_line_id' => $delivery_line_mapping->id,
                'delivery_start_time' => $delivery_line_mapping->start_time,
                'delivery_trip_start' => $delivery_line_mapping->trip_start,
                'delivery_trip_end' => $delivery_line_mapping->trip_end,
                'product_name' => $productname,
                'data' => $subscriptions,
                'message' => 'Delivery List Fetched Successfully.'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => 'No data found.',
            ]);
        }
    }






    // public function deliverylist(Request $request)
    // {
    //     $data = [];
    //     $delivery_personid = $request->delivery_personid;
    //     $date = $request->date;

    //     // Get distinct customer subscriptions
    //     $distinctSubscriptions = Customersubscription::where('customers_subscription.date', $date)
    //         ->where('customers_subscription.deliveryperson_id', $delivery_personid)
    //         ->distinct()
    //         ->pluck('subscription_customer_id');

    //     // Collect product details in advance
    //     $pro_details = Product::select(
    //         'products.*',
    //         'products.name as product_name',
    //         'products.price as product_price',
    //         'products.pic as product_image',
    //         'measurements.name as measurement_name',
    //         'units.name as quantity_value'
    //     )
    //         ->join('measurements', 'products.measurement_id', '=', 'measurements.id')
    //         ->join('units', 'products.quantity_id', '=', 'units.id')
    //         ->get();
    //     foreach ($pro_details as $d) {
    //         $d->pic = asset($d->pic);
    //     }

    //     // For each distinct subscription customer ID
    //     foreach ($distinctSubscriptions as $subscription_customer_id) {
    //         // Get customer data
    //         $customer = Customer::where('id', $subscription_customer_id)->first();

    //         // Get subscription data for the customer
    //         $subscriptions = Customersubscription::where('subscription_customer_id', $subscription_customer_id)
    //             ->where('date', $date)
    //             ->where('deliveryperson_id', $delivery_personid)
    //             ->get();

    //         // Get orders for the customer
    //         $orders = Order::where('customer_id', $subscription_customer_id)
    //             ->where('delivery_date', $date)
    //             ->where('delivered_by', $delivery_personid)
    //             ->get();

    //         // Combine data
    //         $data[] = [
    //             'customer_id' => $customer->id,
    //             'customer_name' => $customer->name,
    //             'customer_lat_long' => $customer->latlon,
    //             'customer_address' => $customer->address,
    //             'customer_phone' => $customer->mobile,
    //             'subscription_product' => $subscriptions,
    //             'orders' => $orders,
    //         ];
    //     }

    //     return response()->json(['success' => true, 'data' => $data, 'products' => $pro_details, 'message' => 'Delivery List Fetched Successfully.']);
    // }

  

                        public function deliverylist(Request $request)
                    {
                        $data = [];
                        $delivery_personid = $request->delivery_personid;
                        $date = $request->date;
                        $today = Carbon::today();
                        $fiveDaysFromNow = Carbon::today()->addDays(5);

                        // Get distinct customer subscriptions
                        $distinctSubscriptions = Customersubscription::where('customers_subscription.date', $date)
                            ->where('customers_subscription.deliveryperson_id', $delivery_personid)
                            ->distinct()
                            ->pluck('subscription_customer_id');

                        // Collect product details in advance
                        $pro_details = Product::select(
                            'products.*',
                            'products.name as product_name',
                            'products.price as product_price',
                            'products.pic as product_image',
                            'measurements.name as measurement_name',
                            'units.name as quantity_value'
                        )
                        ->join('measurements', 'products.measurement_id', '=', 'measurements.id')
                        ->join('units', 'products.quantity_id', '=', 'units.id')
                        ->get();

                        foreach ($pro_details as $d) {
                            $d->pic = asset($d->pic);
                        }

                        // For each distinct subscription customer ID
                        foreach ($distinctSubscriptions as $subscription_customer_id) {
                            // Get customer data
                            $customer = Customer::where('id', $subscription_customer_id)->first();

                            // Get subscription data for the customer, including formatted from_date
                            $subscriptions = Customersubscription::where('subscription_customer_id', $subscription_customer_id)
                                ->where('date', $date)
                                ->where('deliveryperson_id', $delivery_personid)
                                ->where('delivery_status', '!=', 'cancelled')
                                ->get()
                                ->map(function ($subscription) {
                                    $subscription->from_date_formatted = Carbon::parse($subscription->from_date)->format('Y-m-d H:i:s');
                                    return $subscription;
                                });

                            // Check if any subscription has a `customer_track_id` that qualifies the customer as "new"
                            $isNew = false;
                            foreach ($subscriptions as $subscription) {
                                if ($subscription->customer_track_id) {
                                    $isNewQuery = CustomerPlansTrack::where('id', $subscription->customer_track_id)
                                        ->whereBetween(DB::raw("DATE(start_date)"), [$today, $fiveDaysFromNow]);

                                    // If any of the subscriptions meet the criteria, mark as new
                                    if ($isNewQuery->exists()) {
                                        $isNew = true;
                                        break;
                                    }
                                }
                            }

                            // Get orders for the customer
                            $orders = Order::where('customer_id', $subscription_customer_id)
                                ->where('delivery_date', $date)
                                ->where('delivered_by', $delivery_personid)
                                ->where('delivery_status', '!=', 'cancelled')
                                ->get();

                            // Combine data and conditionally include 'home_img' if it exists
                            $customer_data = [
                                'customer_id' => $customer->id,
                                'home_img' => url($customer->home_img),
                                'customer_name' => $customer->name,
                                'customer_lat_long' => $customer->latlon,
                                'customer_address' => $customer->address,
                                'customer_phone' => $customer->mobile,
                                'subscription_product' => $subscriptions,
                                'orders' => $orders,
                                'new' => $isNew, // Mark as new if within date range
                            ];

                            // Add the customer data to the $data array
                            $data[] = $customer_data;
                        }

                        return response()->json([
                            'success' => true,
                            'data' => $data,
                            'products' => $pro_details,
                            'message' => 'Delivery List Fetched Successfully.',
                        ]);
                    }

    public function ditemsbycusid(Request $request)
    {
        $data = [];
        $delivery_personid = $request->delivery_personid;
        $customer_id = $request->customer_id;
        $sub = Customersubscription::where('date', $request->date)->where('deliveryperson_id', $delivery_personid)->where('subscription_customer_id', $customer_id)->get();
        $data['subscription_product'] = $sub;

        $orders = Order::where('delivery_date', $request->date)->where('customer_id', $customer_id)->where('delivered_by', $delivery_personid)->get();

        $data['orders'] = $orders;
        return response()->json(['success' => true, 'data' => $data, 'message' => 'Delivery List Fetched Successfully.']);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function add_gift_amount(Request $request)
    {
        // dd($request);
        $cus_id = $request->cust_id;
        $amount = $request->amount;
        $remarks = $request->remarks;
        $uid = $request->uid;
        $current_amount = $request->current_amount;
        // dd($request);
        // Validate the amount to ensure it's a valid number
        if (!is_numeric($amount)) {
            return response()->json(['success' => false, 'message' => 'Invalid amount.']);
        }

        try {
            // Find the wallet entry for the given customer
            $wallet = Wallet::where('customers_id', $cus_id)->first();

            if ($wallet) {
                // Add the new amount to the current amount
                $wallet->current_amount += $amount;
                $wallet->last_gift_at = now();
                $wallet->save();

                // Insert into customer wallet history
                $cwhis = Customerwallethistory::create([
                    'customer_wallet_id' => $wallet->id, // Use wallet id instead of customers_id
                    'debit_credit_status' => 'Your Gift Amount',
                    'amount' => $amount,
                    'remarks' => $remarks,
                    'notes' => 'Gift from Gramthupal',
                    'customer_id' => $wallet->customers_id
                ]);

                // Insert into gift amount
                giftamount::create([
                    'walet_id' => $wallet->id,
                    'customer_id' => $wallet->customers_id,
                    'amount' => $amount,
                    'current_amount' => $current_amount,
                    'debit_credit_status' => 'Your Gift Amount',
                    'delivery_date' => date('Y-m-d'),
                    'notes' => 'Your Gift Amount',
                    'updated_by' => $uid,
                ]);

                return response()->json(['success' => true, 'message' => 'Amount added successfully.']);
            } else {
                return response()->json(['success' => false, 'message' => 'Wallet not found for the given customer.']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }
    public function add_gift_amount2(Request $request)
    {
        // dd($request);
        $cus_id = $request->cust_id;
        $amount = $request->amount;
        $remarks = $request->remarks;
        $uid = $request->uid;
        $current_amount = $request->current_amount;
        // dd($request);
        // Validate the amount to ensure it's a valid number
        if (!is_numeric($amount)) {
            return response()->json(['success' => false, 'message' => 'Invalid amount.']);
        }

        try {
            // Find the wallet entry for the given customer
            $wallet = Wallet::where('customers_id', $cus_id)->first();

            if ($wallet) {
                // Add the new amount to the current amount
                $wallet->current_amount += $amount;
                $wallet->last_gift_at = now();
                $wallet->save();

                // Insert into customer wallet history
                $cwhis = Customerwallethistory::create([
                    'customer_wallet_id' => $wallet->id, // Use wallet id instead of customers_id
                    'debit_credit_status' => 'credited',
                    'amount' => $amount,
                    'remarks' => $remarks,
                    'notes' => 'Cash',
                    'customer_id' => $wallet->customers_id
                ]);

                // Insert into gift amount
                // giftamount::create([
                //     'walet_id' => $wallet->id,
                //     'customer_id' => $wallet->customers_id,
                //     'amount' => $amount,
                //     'current_amount' => $current_amount,
                //     'debit_credit_status' => 'Your Gift Amount',
                //     'delivery_date' => date('Y-m-d'),
                //     'notes' => 'Your Gift Amount',
                //     'updated_by' => $uid,
                // ]);

                return response()->json(['success' => true, 'message' => 'Amount added successfully.']);
            } else {
                return response()->json(['success' => false, 'message' => 'Wallet not found for the given customer.']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    public function gift_products(Request $request)
    {

        if ($request->isMethod('post')) {
            $from_date = $request->from_date . ' 00:00:00'; // Start of the day
            $to_date = $request->to_date . ' 23:59:59'; // End of the day

            // Get the delivery list within the specified date range
            $query = giftproduct::whereBetween('created_at', [$from_date, $to_date]);

            $giftproduct = $query->orderBy('created_at', 'desc')->get();
        } else {
            $giftproduct = giftproduct::orderBy('created_at', 'desc')->get();
        }
        foreach ($giftproduct as $wall) {
            $cus = Customer::find($wall->customer_id);
            $product = Product::find($wall->product_id);
            // dd($wall->quantity);
            $quan = Quantity::find($wall->quantity);
            // dd($quan);
            $meas = Measurement::find($wall->measurment);
            if ($cus) {
                $wall->customer_id = $cus->name . " " . $cus->mobile;
                $wall->customer_id = $cus->name;
                $wall->customer_mobile = $cus->mobile;
            }
            if ($product) {
                $wall->product_id = $product->name;
                $wall->price = $product->price;
                $category = Category::where('id', $product->category_id)->first();
                $wall->category = $category->name;
            }
            if ($quan) {
                $wall->quantity_id = $quan->name;
            }
            if ($meas) {
                $wall->measurment = $meas->name;
            }
        }
        return view('gift.index', compact('giftproduct'));
    }
    // public function gift_amount(Request $request)
    // {
    //     if ($request->isMethod('post')) {
    //         $from_date = $request->from_date . ' 00:00:00'; // Start of the day
    //         $to_date = $request->to_date . ' 23:59:59'; // End of the day

    //         // Get the delivery list within the specified date range
    //         $query = giftamount::whereBetween('created_at', [$from_date, $to_date]);

    //         $giftproduct = $query->orderBy('created_at', 'desc')->get();
    //     } else {
    //         $giftproduct = giftamount::orderBy('created_at', 'desc')->get();
    //     }
    //     // dd($giftproduct);
    //     // $giftproduct = giftamount::get();
    //     foreach ($giftproduct as &$wall) {
    //         $cus = Wallet::where('id', $wall->customer_id)->first();

    //         // dd($cus);
    //         // if($cus){
    //         $cuss = Customer::find($cus);
    //     // }
    //         // dd($cuss);
    //         if ($cuss) {
    //             $wall->customer_id = $cuss->name??"";
    //             $wall->customer_mobile = $cuss->mobile;
    //         }
    //     }
    //     return view('gift.amount', compact('giftproduct'));
    // }
    public function gift_amount(Request $request)
{
    if ($request->isMethod('post')) {
        $from_date = $request->from_date . ' 00:00:00'; // Start of the day
        $to_date = $request->to_date . ' 23:59:59'; // End of the day

        // Get the delivery list within the specified date range
        $query = giftamount::whereBetween('created_at', [$from_date, $to_date]);

        $giftproduct = $query->orderBy('created_at', 'desc')->get();
    } else {
        $giftproduct = giftamount::orderBy('created_at', 'desc')->get();
    }

    foreach ($giftproduct as &$wall) {
        // Find the wallet associated with the customer ID
        $cus = Wallet::where('id', $wall->walet_id)->first();
// dd($cus);
        // Check if the wallet exists and retrieve the customer associated with it
        if ($cus) {
            $cuss = Customer::find($cus->customers_id); // Use $cus->customer_id to find the customer
            
            if ($cuss) {
                $wall->customer_id = $cuss->name ?? "";
                $wall->customer_mobile = $cuss->mobile;
            }
        }
    }

    return view('gift.amount', compact('giftproduct'));
}

    public function order_list_view(Request $request)
    {
        $id = $request->id;
        $view = Order::where('order_id', $id)->first();
        // dd($view);
        $sel = "";
        $sel .= '<table class="table">
                    <thead>
                    <tr>
                        <th scope="col">S.No</th>
                        <th scope="col">Customer Name</th>
                        <th scope="col">Product</th>

                        <th scope="col">Measurement</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Price</th>
                    </tr>
                    </thead>
                    <tbody>';




        $client = Customer::where('id', $view->customer_id)->first();
        $Cart = Cart::where('order_id', $id)->get();
        //   dd($Cart);
        $i = 1;
        foreach ($Cart as $Carts) {
            // echo $Carts->product_id;
            $pro = Product::where('id', $Carts->product_id)->first();
            // dd($pro);
            $mes = Measurement::where('id', $pro->measurement_id)->first();
            $qty = Unit::where('id', $pro->quantity_id)->first();
            $sel .= '<tr>
              <td scope="row">' . $i . '</td>
              <td>' . $client->name . '</td>
              <td>' . $pro->name . '</td>


              <td>' . $qty->name . ' ' . $mes->name . '</td>
              <td>' . $Carts->quantity . '</td>
              <td>' . $Carts->price . '</td>
              </tr>';
            $i++;
        }
        $sel .= ' </tbody>
                          </table>';

        return response()->json(['success' => true, 'message' => 'check out successfully', 'tabel' => $sel]);
    }
    public function cust_list_view(Request $request)
    {

        $id = $request->id;
        $wallet = Wallet::where('customers_id', $id)->first();
        $cust = Customersubscription::where('subscription_customer_id', $id)
            ->where('delivery_status', 'yet to deliver')
            ->get();
        // Format the data
        $data = [];

        foreach ($cust as $subscription) {
            $cus = Customer::find($subscription->subscription_customer_id);
            $pro = Product::find($subscription->subscription_products_id); // Use the correct model to find the product
            $cart = Cart::find($subscription->order_id);
            $del_very = Deliveryperson::find($subscription->deliveryperson_id);
            $mes = Measurement::find($pro->measurement_id); // Fetch measurement details
            $unit = Unit::find($pro->quantity_id); // Fetch unit details

            if ($cus) {
                $subscription->subscription_customer_id = $cus->name . " " . $cus->mobile;
            }
            if ($pro) {
                $subscription->subscription_products_id = $pro->name;
            }
            if ($del_very) {
                $subscription->deliveryperson_id = $del_very->name;
            }
            if ($mes) {
                $subscription->measurement_name = $mes->name; // Add measurement name
            }
            if ($unit) {
                $subscription->unit_name = $unit->name; // Add unit name
            }
            $data[] = [
                'date' => Carbon::parse($subscription->date), // Format the date
                'product' => $subscription->subscription_products_id, // Add necessary fields
                'quantity' => $subscription->subscription_quantity,
                'unit' => $subscription->unit_name,
                'measurement' => $subscription->measurement_name,
                // Add more fields if needed
            ];
        }

        $order_list = DB::table('orders')
            ->select('orders.*', 'Cart.*', 'products.name', 'products.quantity_id', 'products.measurement_id') // Select columns from all tables
            ->where('customer_id', $id)
            ->join('Cart', 'Cart.order_id', '=', 'orders.order_id') // Join with Cart table
            ->join('products', 'Cart.product_id', '=', 'products.id') // Join with products table
            ->orderBy('orders.delivery_date', 'asc') // Order by delivery_date ascending
            ->get();
        foreach ($order_list as &$del_list) {
            $cus = Customer::find($del_list->customer_id);
            $pro = Subscriptionproduct::find($del_list->product_id); // Use the correct model to find the product
            $mes = Measurement::find($del_list->measurement_id);
            if ($cus) {
                $del_list->customer_id = $cus->name . " " . $cus->mobile;
            }
            if ($pro) {
                $del_list->product_id = $pro->name;
            }
            if ($mes) {
                $del_list->measurement_id = $mes->name;
            }
        }
        $wallet = Wallet::where('customers_id', $id)->first();
        $history = Customerwallethistory::where('customer_wallet_id', $wallet->id)->get();

        //deliverd list

        $id = $request->id;
        $wallet = Wallet::where('customers_id', $id)->first();
        $cust = Customersubscription::where('subscription_customer_id', $id)
            ->where('delivery_status', 'Delivered')
            ->get();
        // Format the data
        $delivered = [];

        foreach ($cust as $subscription) {
            $cus = Customer::find($subscription->subscription_customer_id);
            $pro = Product::find($subscription->subscription_products_id); // Use the correct model to find the product
            $cart = Cart::find($subscription->order_id);
            $del_very = Deliveryperson::find($subscription->deliveryperson_id);
            $mes = Measurement::find($pro->measurement_id); // Fetch measurement details
            $unit = Unit::find($pro->quantity_id); // Fetch unit details

            if ($cus) {
                $subscription->subscription_customer_id = $cus->name . " " . $cus->mobile;
            }
            if ($pro) {
                $subscription->subscription_products_id = $pro->name;
            }
            if ($del_very) {
                $subscription->deliveryperson_id = $del_very->name;
            }
            if ($mes) {
                $subscription->measurement_name = $mes->name; // Add measurement name
            }
            if ($unit) {
                $subscription->unit_name = $unit->name; // Add unit name
            }
            $delivered[] = [
                'date' => Carbon::parse($subscription->date), // Format the date
                'product' => $subscription->subscription_products_id, // Add necessary fields
                'quantity' => $subscription->subscription_quantity,
                'unit' => $subscription->unit_name,
                'measurement' => $subscription->measurement_name,
                // Add more fields if needed
            ];
        }




        return view('customer.list', compact('data', 'wallet', 'order_list', 'history', 'wallet', 'delivered'));
    }
    public function cust_list_view2(Request $request)
    {
        $id = $request->id;

        $subscriber_user = Customersubscription::where('subscription_customer_id', $id)
            ->where('delivery_status', 'yet to deliver')
            ->selectRaw('DATE(date) as date, COUNT(*) as total')
            ->groupBy('date')
            ->get();
        $subscriber_user_count = $subscriber_user->count();

        $wallet = Wallet::where('customers_id', $id)->first();

        // $orderCount = Order::where('customer_id', $id)

        //     ->groupBy('order_id')
        //     ->selectRaw('COUNT(*) as total')
        //     ->get()
        //     ->count();//
        $orderCount = Order::where('customer_id', $id)

            ->count();


        $customer = Customer::where('id', $id)->first();

        $pincode = Pincode::where('id', $customer->pincode_id)->first();
        $area = Area::where('id', $customer->area_id)->first();
        $delivery_line = Delivery_lines::where('id', $customer->deliverylines_id)->first();

        $delper = Deliveryperson::where('status', 'Active')->get();
        $unitz = Unit::where('status', 'Active')->get();

        if ($request->isMethod('post')) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $cust_id = $request->cust_id;

            $query = Customersubscription::whereBetween('date', [$from_date, $to_date])
                ->where('subscription_customer_id', $cust_id);

            $delivery_list = $query->orderBy('date', 'asc')->get();
        } else {
            $query = Customersubscription::whereBetween('date', [date('Y-m-01'), date('Y-m-t')])
                ->where('subscription_customer_id', $id);
            $delivery_list = $query->orderBy('date', 'asc')->get();
        }

        $sel = "";
        $i = 1;
        foreach ($delivery_list as &$del_list) {
            $cus = Customer::find($del_list->subscription_customer_id);
            $pro = Product::find($del_list->subscription_products_id);
            $cart = Cart::find($del_list->order_id);
            $del_very = Deliveryperson::find($del_list->deliveryperson_id);
            $mes = Measurement::find($pro->measurement_id);
            $unit = Unit::find($pro->quantity_id);
            $pin = Pincode::find($del_list->pincode);
            $area = Area::find($del_list->area);
            $del_line = delivery_line_mapping::find($del_list->delivery_line_id);

            if ($cus) {
                $del_list->subscription_customer_id = $cus->name . " " . $cus->mobile;
            }
            if ($pro) {
                $del_list->subscription_products_id = $pro->name;
            }
            if ($del_very) {
                $del_list->deliveryperson_id = $del_very->name;
            }
            if ($mes) {
                $del_list->measurement_name = $mes->name;
            }
            if ($unit) {
                $del_list->unit_name = $unit->name;
            }
            if ($pin) {
                $del_list->pincode = $pin->pincode;
            }
            if ($area) {
                $del_list->area = $area->name;
            }
            if ($del_line) {
                $dl = Delivery_lines::where('id', $del_line->delivery_line_id)->first();
                $del_list->delivery_line_id = $dl->name;
            }
        }

        $giftproduct = giftproduct::where('customer_id', $id)->orderBy('created_at', 'desc')->get();
        foreach ($giftproduct as $wall) {
            $cus = Customer::find($wall->customer_id);
            $product = Product::find($wall->product_id);
            $quan = Quantity::find($wall->quantity);
            $meas = Measurement::find($wall->measurment);
            if ($cus) {
                $wall->customer_id = $cus->name . " " . $cus->mobile;
            }
            if ($product) {
                $wall->product_id = $product->name;
                $wall->price = $product->price;
                $category = Category::where('id', $product->category_id)->first();
                $wall->category = $category->name;
            }
            if ($quan) {
                $wall->quantity_id = $quan->name;
            }
            if ($meas) {
                $wall->measurment = $meas->name;
            }
        }

        $giftamount = giftamount::where('customer_id', $id)->orderBy('created_at', 'desc')->get();
        foreach ($giftamount as &$wall) {
            $cus = Wallet::where('id', $wall->customer_id)->first();
            if ($cus) {
                $cuss = Customer::find($cus->customers_id);
                if ($cuss) {
                    $wall->customer_id = $cuss->name . " " . $cuss->mobile;
                }
            }
        }

        return view(
            'customer.list_view',
            compact(
                'subscriber_user',
                'wallet',
                'orderCount',
                'delivery_line',
                'pincode',
                'area',
                'subscriber_user_count',
                'id',
                'delivery_list',
                'giftproduct',
                'giftamount'
            )
        );
    }
    public function cust_list_view2_approve(string $id, Request $request)
{
    // Find the customer record by ID
    $cus = Customer::find($id);

    if ($cus) {
        // Update fields
        $cus->deliverylines_id = $cus->temp_temp_deliverylines_id; // Assign the value
        $cus->edited_at = null; // Nullify edited_at
        $cus->edited_by = null; // Nullify edited_by

        // Save changes to the database
        $cus->save();

        // Redirect back to the previous page with a success message
        return redirect()->back()->with('success', 'Customer record updated successfully.');
    }

    // Redirect back with an error message if the customer is not found
    return redirect()->back()->with('error', 'Customer not found.');
}

    public function cust_list_view3(Request $request)
    {
        // dd($request);
        //     "_token" => "exfRSvDW1aZb12lEZBEVO96XSEUI0TqaBxbBTUWg"
        //   "from_date" => "2024-09-01"
        //   "to_date" => "2024-09-30"
        //   "cust_id" => "4"
        $id = $request->cust_id;
        // $from_date=$request->from_date;
        // $to_date=$request->to_date;

        $subscriber_user = Customersubscription::where('subscription_customer_id', $id)
            ->where('delivery_status', 'yet to deliver')
            ->selectRaw('DATE(date) as date, COUNT(*) as total')
            ->groupBy('date')
            ->get();
        $subscriber_user_count = $subscriber_user->count();

        $wallet = Wallet::where('customers_id', $id)->first();

        $orderCount = Order::where('customer_id', $id)
            ->where('delivery_status', 'yet to deliver')
            ->groupBy('order_id')
            ->selectRaw('COUNT(*) as total')
            ->get()
            ->count();

        $customer = Customer::where('id', $id)->first();

        $pincode = Pincode::where('id', $customer->pincode_id)->first();
        $area = Area::where('id', $customer->area_id)->first();
        $delivery_line = Delivery_lines::where('id', $customer->deliverylines_id)->first();

        $delper = Deliveryperson::where('status', 'Active')->get();
        $unitz = Unit::where('status', 'Active')->get();

        if ($request->isMethod('post')) {
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $cust_id = $request->cust_id;

            $query = Customersubscription::whereBetween('date', [$from_date, $to_date])
                ->where('subscription_customer_id', $cust_id);

            $delivery_list = $query->orderBy('date', 'asc')->get();
        } else {
            $query = Customersubscription::whereBetween('date', [date('Y-m-01'), date('Y-m-t')])
                ->where('subscription_customer_id', $id);
            $delivery_list = $query->orderBy('date', 'asc')->get();
        }

        $sel = "";
        $i = 1;
        foreach ($delivery_list as &$del_list) {
            $cus = Customer::find($del_list->subscription_customer_id);
            $pro = Product::find($del_list->subscription_products_id);
            $cart = Cart::find($del_list->order_id);
            $del_very = Deliveryperson::find($del_list->deliveryperson_id);
            $mes = Measurement::find($pro->measurement_id);
            $unit = Unit::find($pro->quantity_id);
            $pin = Pincode::find($del_list->pincode);
            $area = Area::find($del_list->area);
            $del_line = delivery_line_mapping::find($del_list->delivery_line_id);

            if ($cus) {
                $del_list->subscription_customer_id = $cus->name . " " . $cus->mobile;
            }
            if ($pro) {
                $del_list->subscription_products_id = $pro->name;
            }
            if ($del_very) {
                $del_list->deliveryperson_id = $del_very->name;
            }
            if ($mes) {
                $del_list->measurement_name = $mes->name;
            }
            if ($unit) {
                $del_list->unit_name = $unit->name;
            }
            if ($pin) {
                $del_list->pincode = $pin->pincode;
            }
            if ($area) {
                $del_list->area = $area->name;
            }
            if ($del_line) {
                $dl = Delivery_lines::where('id', $del_line->delivery_line_id)->first();
                $del_list->delivery_line_id = $dl->name;
            }
        }

        $giftproduct = giftproduct::where('customer_id', $id)->orderBy('created_at', 'desc')->get();
        foreach ($giftproduct as $wall) {
            $cus = Customer::find($wall->customer_id);
            $product = Product::find($wall->product_id);
            $quan = Quantity::find($wall->quantity);
            $meas = Measurement::find($wall->measurment);
            if ($cus) {
                $wall->customer_id = $cus->name . " " . $cus->mobile;
            }
            if ($product) {
                $wall->product_id = $product->name;
                $wall->price = $product->price;
                $category = Category::where('id', $product->category_id)->first();
                $wall->category = $category->name;
            }
            if ($quan) {
                $wall->quantity_id = $quan->name;
            }
            if ($meas) {
                $wall->measurment = $meas->name;
            }
        }

        $giftamount = giftamount::where('customer_id', $id)->orderBy('created_at', 'desc')->get();
        foreach ($giftamount as &$wall) {
            $cus = Wallet::where('id', $wall->customer_id)->first();
            if ($cus) {
                $cuss = Customer::find($cus->customers_id);
                if ($cuss) {
                    $wall->customer_id = $cuss->name . " " . $cuss->mobile;
                }
            }
        }

        return view(
            'customer.list_view',
            compact(
                'subscriber_user',
                'wallet',
                'orderCount',
                'delivery_line',
                'pincode',
                'area',
                'subscriber_user_count',
                'id',
                'delivery_list',
                'giftproduct',
                'giftamount'
            )
        );
    }


    public function client_detail(Request $request)
    {
        $id = $request->id;
        $customer = Customer::where('id', $id)->first();

        if (!$customer) {
            return 'Customer not found';
        }

        $pincode = Pincode::where('id', $customer->pincode_id)->first();
        $area = Area::where('id', $customer->area_id)->first();
        $delivery_line = Delivery_lines::where('id', $customer->deliverylines_id)->first();

        $sel = '';
        $sel .= '
        <table id="example" class="table table-bordered dt-responsive nowrap w-100 mt-3">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Name</th>
                    <th>Pin code</th>
                    <th>Area</th>
                    <th>Delivery line</th>
                    <th>Lat & Long</th>
                    <th>Profile</th>
                    <th>Home</th>
                    <th>Phone number</th>
                    <th>Address</th>
                </tr>
            </thead>
            <tbody id="tab_values">
                <tr>
                    <td>1</td>
                    <td>' . htmlspecialchars($customer->name) . '</td>
                    <td>' . ($pincode ? htmlspecialchars($pincode->pincode) : 'N/A') . '</td>
                    <td>' . ($area ? htmlspecialchars($area->name) : 'N/A') . '</td>
                    <td>' . ($delivery_line ? htmlspecialchars($delivery_line->name) : 'N/A') . '</td>
                    <td><a href="' . url('cust_loc/' . $customer->id) . '" target="_blank">' . htmlspecialchars($customer->latlon) . '</a></td>
        ';
        
        // Profile Image
        if ($customer->profile_pic) {
            $sel .= '<td onclick="profileimage(\'' . env('ASSET_LINK_URL') . $customer->profile_pic . '\')"><img src="' . asset($customer->profile_pic) . '" alt="Image" style="width: 100px; height: 100px;"></td>';
        } else {
            $sel .= '<td>No Image</td>';
        }
        
        // Home Image
        if ($customer->home_img) {
            $sel .= '<td onclick="viewimage(\'' . env('ASSET_LINK_URL') . $customer->home_img . '\')"><img src="' . asset($customer->home_img) . '" alt="Image" style="width: 100px; height: 100px;"></td>';

        } else {
            $sel .= '<td>No Image</td>';
        }
        
        // Close the row and table
        $sel .= '
                    <td>' . htmlspecialchars($customer->mobile) . '</td>
                    <td>' . htmlspecialchars($customer->address) . '</td>
                </tr>
            </tbody>
        </table>
        ';

        return response()->json(['success' => true, 'message' => 'check out successfully', 'tabel' => $sel]);
    }
    public function wallet_balance(Request $request)
    {
        $id = $request->id;
        $wallet = Wallet::where('customers_id', $id)->first();
        // dd($wallet);
        if (!$wallet) {
            return response()->json(['success' => false, 'message' => 'Wallet not found']);
        }

        $cus = Customer::find($wallet->customers_id);

        if (!$cus) {
            return response()->json(['success' => false, 'message' => 'Customer not found']);
        }

        $clientName = $cus->name . " " . $cus->mobile;
        $history = Customerwallethistory::where('customer_wallet_id', $wallet->id)->get();
        $history = Customerwallethistory::select(
            'customer_wallet_history.*',
            'customer_payments_history.order_id as order_id',
            'customer_payments_history.transaction_id as transaction_id',
            'customer_payments_history.payment_status as payment_status'
        )
            ->leftJoin('customer_payments_history', 'customer_wallet_history.payment_history_id', '=', 'customer_payments_history.id')
            ->where('customer_wallet_history.customer_id', $id)
            ->get();
        // dd($history);
        $i = 1;
        $sel = '<h4 class="text-center mt-3">Wallet History</h4>
        <table id="example" class="table table-bordered dt-responsive nowrap w-100 mt-3">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Status</th>
                    <th>Amount</th>
                    <th>Order Id</th>
                    <th>Transaction Id</th>
                    <th>Note</th>

                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="tab_values">';
        // dd($history);
        foreach ($history as $his) {
            $sel .= '<tr>
                <td>' . $i . '</td>
                <td>' . $his->debit_credit_status . '</td>
                <td>' . $his->amount . '</td>
                <td>' . $his->order_id . '</td>
                <td>' . $his->transaction_id . '</td>
                <td>' . $his->notes . '</td>

                <td>' . $his->payment_status . '</td>
            </tr>';
            $i++;
        }

        $sel .= '</tbody>
        </table>';

        return response()->json(['success' => true, 'message' => 'Check out successfully', 'tabel' => $sel]);
    }
    public function subscription(Request $request)
    {
        $id = $request->id;
        $wallet = CustomerPlansTrack::where('customers_id', $id)->get();


        $i = 1;
        $sel = '<h4 class="text-center mt-3">Subscription History</h4>
        <table id="example" class="table table-bordered dt-responsive nowrap w-100 mt-3">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Plan</th>
                    <th>Offer% </th>
                    <th>Subscription payment date </th>
                    <th>Start date</th>
                    <th>End Date</th>

                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="tab_values">';
        // dd($history);
        foreach ($wallet as $his) {
            $plan = Subscriptionplan::where('id', $his->plan_id)->first();
            $sel .= '<tr>
                <td>' . $i . '</td>
                <td>' . $plan->name . '</td>
                <td>' . $his->discount . '</td>
                <td>' . Carbon::parse($his->created_at)->format('d-m-Y') . '</td>
                <td>' . Carbon::parse($his->start_date)->format('d-m-Y') . '</td>
                <td>' . Carbon::parse($his->end_date)->format('d-m-Y') . '</td>

                <td><button class="btn btn-success"onclick="sub_plans(' . $his->id . ')"><i class="fa fa-eye"></i></button></td>
            </tr>';
            $i++;
        }

        $sel .= '</tbody>
        </table>';

        return response()->json(['success' => true, 'message' => 'Check out successfully', 'tabel' => $sel]);
    }
    public function order_count(Request $request)
    {
        $id = $request->id;

        $history = Order::where('customer_id', $id)->get();

        $i = 1;
        $sel = '<h4 class="text-center mt-3">Order History</h4>
        <table id="example" class="table table-bordered dt-responsive nowrap w-100 mt-3">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Order ID</th>
                    <th>Total value </th>
                    <th>Ordered at </th>
                    <th>Scheduled date </th>
                    <th>Delivered at </th>
                    <th>Order Status</th>

                </tr>
            </thead>
            <tbody id="tab_values">';

        foreach ($history as $his) {
            if ($his->delivery_status == 'Delivered') {
                $del_sts = $his->delivery_at;
            } else {
                $del_sts = "";
            }
            $sel .= '<tr>
                <td>' . $i . '</td>
                <td>' . $his->order_id . '</td>
                <td>' . $his->price . '</td>

                <td>' . Carbon::parse($his->created_at)->format('d-m-Y') . '</td>
                <td>' . Carbon::parse($his->delivery_date)->format('d-m-Y') . '</td>
                <td>' . $del_sts . '</td>
                <td>' . $his->delivery_status . '</td>


            </tr>';
            $i++;
        }

        $sel .= '</tbody>
        </table>';

        return response()->json(['success' => true, 'message' => 'Check out successfully', 'tabel' => $sel]);
    }
    public function order_count_dash(Request $request)
    {
        $id = $request->id;

    //     $history = Order::where('customer_id', $id)
    // ->where('price', '>', 0)
    // ->distinct('order_id') // This will give you distinct order_ids
    // ->get();
    // $history = Order::where('customer_id', $id)
    // ->where('price', '>', 0)
    // ->groupBy('order_id')
    // ->selectRaw('order_id, SUM(price) as price')  // Aggregate by summing prices
    // ->get();
    $history = Order::where('customer_id', $id)
    ->where('price', '>', 0)
    ->groupBy('order_id') // Group by order_id
    ->selectRaw('order_id, 
                 SUM(price) as price, 
                 MIN(delivery_status) as delivery_status, 
                 MIN(delivery_at) as delivery_at,
                 MIN(created_at) as created_at,
                 MIN(delivery_date) as delivery_date', 
                 )
    ->get();


        $giftproduct = giftproduct::where('customer_id', $id)->orderBy('created_at', 'desc')->get();
        foreach ($giftproduct as $wall) {
            $cus = Customer::find($wall->customer_id);

            $product = Product::select(
                'products.*',
                'units.name as quantity_name',
                'measurements.name as measurement_name',
                'product_names.name as product_name',
            )
                ->join('units', 'products.quantity_id', '=', 'units.id')
                ->join('measurements', 'products.measurement_id', '=', 'measurements.id')
                ->join('product_names', 'products.product_id', '=', 'product_names.id')
                ->where('products.id', $wall->product_id)
                ->first();


            // $product = Product::find($wall->product_id);
            // $quan = Quantity::find($product->quantity_id);
            // $meas = Measurement::find($product->measurement_id);
            if ($cus) {
                // $wall->customer_id = $cus->name . " " . $cus->mobile;
                $wall->customer_id = $cus->name;
            }
            if ($product) {
                $wall->product_id_name = $product->product_name;
                $wall->measurement_name = $product->measurement_name;
                $wall->quantity_name = $product->quantity_name;
                $wall->price = $wall->price;
            }
            // if ($quan) {
            //     $wall->quantity_id = $quan->name;
            // }
            // if ($meas) {
            //     $wall->measurment = $meas->name;
            // }
        }

        // dd($giftproduct);
        return view('customer.order_count_dash', compact('history', 'giftproduct'));
    }
    public function order_walet_dash(Request $request)
    {
        $id = $request->id;
        $wallet = Wallet::where('customers_id', $id)->first();

        if (!$wallet) {
            return view('customer.order_walet_dash', [
                'id' => $id,
                'success' => false,
                'success_message' => 'Wallet not found'
            ]);
        }


        $cus = Customer::find($wallet->customers_id);

        // if (!$cus) {
        //     return back()->with(['success' => false, 'success_message' => 'Customer not found']);
        // }

        $clientName = $cus->name . " " . $cus->mobile;
        $history = Customerwallethistory::where('customer_wallet_id', $wallet->id)->get();
        $history = Customerwallethistory::select(
            'customer_wallet_history.*',
            'customer_payments_history.order_id as order_id',
            'customer_payments_history.transaction_id as transaction_id',
            'customer_payments_history.payment_status as payment_status'
        )
            ->leftJoin('customer_payments_history', 'customer_wallet_history.payment_history_id', '=', 'customer_payments_history.id')
            ->where('customer_wallet_history.customer_id', $id)
            ->orderBy('customer_wallet_history.created_at', 'desc') // Ordering by created_at in descending order
            ->get();

        // dd($history);
        // $giftamount = giftamount::where('customer_id', $id)->orderBy('created_at', 'desc')->get();
        // foreach ($giftamount as &$wall) {

        //     $cus = Wallet::where('id', $wall->customer_id)->first();
        //     if ($cus) {
        //         $cuss = Customer::where('id', $cus->customers_id)->first();
        //         if ($cuss) {
        //             $wall->customer_id = $cuss->name . " " . $cuss->mobile;
        //         }
        //     }

        // }
        $giftamount = Giftamount::where('customer_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($giftamount as &$wall) {
            // Get the Wallet associated with the gift amount
            $cus = Wallet::where('id', $wall->customer_id)->first();

            if ($cus) {
                // Get the Customer associated with the Wallet
                $cuss = Customer::where('id', $cus->customers_id)->first();

                if ($cuss) {
                    // Assign name and mobile directly to the $wall object
                    $wall->name = $cuss->name;          // Add 'name' field
                    $wall->mobile = $cuss->mobile;      // Add 'mobile' field
                }
            }
        }
        // dd($giftamount);
        return view('customer.order_walet_dash', compact('history', 'wallet', 'giftamount'));
    }
    public function order_subscription_dash(Request $request,string $id)
    {
// dd($id);
        $product = Product::select(
            'products.*',
            'units.name as quantity_name',
            'measurements.name as measurement_name',
            'product_names.name as product_name',
        )
            ->join('units', 'products.quantity_id', '=', 'units.id')
            ->join('measurements', 'products.measurement_id', '=', 'measurements.id')
            ->join('product_names', 'products.product_id', '=', 'product_names.id')
            ->where('products.subscription', 'Y')
            ->orderBy('product_id')
            // ->where('products.id', $wall->product_id)
            ->get();

        $subscription_plans = Subscriptionplan::where('status', 'Active')
            ->select('id', 'name', 'discount', 'days_count') // Specify the columns you want to retrieve
            ->get();




        $id = $request->id;
        $wallet = CustomerPlansTrack::where('customers_id', $id)->get();


        // dd($history);
        foreach ($wallet as &$his) {
            $plan = Subscriptionplan::where('id', $his->plan_id)->first();
            $his->plan_id = $plan->name;
        }



        return view('customer.order_subscription_dash', compact('wallet','product','subscription_plans','id'));
    }
    public function vendor_buyer(Request $request)
    {
        $user = vendor::get();
        foreach ($user as &$usr) {
            if ($usr->type == 1) {
                $usr->type = 'Vendor';
            } else {
                $usr->type = 'Buyer';
            }
        }
        return view('vendor.index', compact('user'));
    }
    public function add_vendor_buyer(Request $request)
    {
        $user = vendor::get();
        return view('vendor.add_form', compact('user'));
    }
    public function add_vendor_save(Request $request)
    {
        // $user=vendor::get();
        // return view('vendor.add_form',compact('user'));
        // dd($request);

        $order = vendor::create([
            'name' => $request->name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'type' => $request->type,
            'address' => $request->address,
            'status' => $request->status,
        ]);
        if ($order->save()) {
            return redirect('vendor_buyer')->with('success_message', 'Vendor Added Successfully!');
        } else {
            return redirect('add_vendor_buyer')->with('error_message', 'Failed to update user.');
        }
    }
    public function update_vendor(string $id, Request $request)
    {
        $user_details = vendor::where('id', $id)->first();
        // dd($user_details);
        return view('vendor.update_form', compact('user_details'));
        // dd($request);


    }
    public function update_vendor_update(Request $request)
    {
        // dd($request);
        $user_details = vendor::find($request->id);

        $user_details->name = $request->input('name');
        $user_details->email = $request->input('email');
        $user_details->mobile = $request->input('mobile');
        $user_details->type = $request->input('type');
        $user_details->status = $request->input('status');
        $user_details->address = $request->input('address');

        if ($user_details->save()) {
            return redirect('vendor_buyer')->with('success_message', 'User Updated Successfully!');
        } else {
            return redirect('update_vendor/' . $request->id)->with('error_message', 'Failed to update user.');
        }
    }

    public function pro_sts(Request $request)
    {
        $id = $request->id;
        $del_list = Customersubscription::where('id', $id)->first();
        $cus = Customer::find($del_list->subscription_customer_id);
        $pro = Product::find($del_list->subscription_products_id);
        $cart = Cart::find($del_list->order_id);
        $del_very = Deliveryperson::find($del_list->deliveryperson_id);
        $mes = Measurement::find($pro->measurement_id);
        $unit = Unit::find($pro->quantity_id);
        $pin = Pincode::find($del_list->pincode);
        $area = Area::find($del_list->area);
        $del_line = delivery_line_mapping::find($del_list->delivery_line_id);

        if ($cus) {
            $del_list->subscription_customer_id = $cus->name . " " . $cus->mobile;
        }
        if ($pro) {
            $del_list->subscription_products_id = $pro->name;
        }
        if ($del_very) {
            $del_list->deliveryperson_id = $del_very->name;
        }
        if ($mes) {
            $del_list->measurement_name = $mes->name;
        }
        if ($unit) {
            $del_list->unit_name = $unit->name;
        }
        if ($pin) {
            $del_list->pincode = $pin->pincode;
        }
        if ($area) {
            $del_list->area = $area->name;
        }
        if ($unit) {
            $del_list->unit = $unit->name;
        }
        if ($del_line) {
            $dl = Delivery_lines::where('id', $del_line->delivery_line_id)->first();
            $del_list->delivery_line_id = $dl->name;
        }
        // dd($del_list);
        $i = 1;
        $sel = "";
        $sel .= '<table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>

                <th scope="col">Product</th>
                <th scope="col">Quantity</th>
                <th scope="col">Measurement</th>
                <th scope="col">Unit</th>

                <th scope="col">Price</th>
            </tr>
            </thead>
            <tbody>';
        $sel .= '<tr>
            <td scope="row">' . $i . '</td>
            <td>' . $del_list->subscription_products_id . '</td>
            <td>' . $del_list->subscription_quantity . '</td>
            <td>' . $del_list->unit . '</td>
            <td>' . $del_list->measurement_name . '</td>
            <td>' . $del_list->subscription_quantity * $pro->price . '</td>
            </tr>';


        $sel .= ' </tbody>
                        </table>';
        return response()->json(['success' => true, 'message' => 'check out successfully', 'table' => $sel]);
    }
    public function add_on(Request $request)
    {
        $id = $request->id;
        $del_list = Customersubscription::where('id', $id)->first();

        $history = Order::where('customer_id', $del_list->subscription_customer_id )->where('delivery_date',$del_list->date)->get();

        $i = 1;
        $sel = '<h4 class="text-center mt-3">Order History</h4>
    <table id="example" class="table table-bordered dt-responsive nowrap w-100 mt-3">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Order ID</th>
                <th>Total value </th>
                <th>Ordered at </th>
                <th>Scheduled date </th>
                <th>Delivered at </th>
                <th>Order Status</th>

            </tr>
        </thead>
        <tbody id="tab_values">';

        foreach ($history as $his) {
            if ($his->delivery_status == 'Delivered') {
                $del_sts = $his->delivery_at;
            } else {
                $del_sts = "";
            }
            $sel .= '<tr>
            <td>' . $i . '</td>
            <td>' . $his->order_id . '</td>
            <td>' . $his->price . '</td>

            <td>' . Carbon::parse($his->created_at)->format('d-m-Y') . '</td>
            <td>' . Carbon::parse($his->delivery_date)->format('d-m-Y') . '</td>
            <td>' . $del_sts . '</td>
            <td>' . $his->delivery_status . '</td>


        </tr>';
            $i++;
        }

        $sel .= '</tbody>
    </table>';

        return response()->json(['success' => true, 'message' => 'Check out successfully', 'tabel' => $sel]);
    }
    public function rating_star(Request $request)
    {
        $id = $request->id;
        $del_list = Customersubscription::where('id', $id)->first();

        // Ensure that the pic field is not null
        $image_url = $del_list->pic ? asset($del_list->pic) : '';

        $sel = '<div class="row">
                <div class="col">
                    <img src="' . $image_url . '" class="img-fluid">
                </div>
            </div>
            <div class="row mt-2">
                <div class="col">
                    <p>' . htmlspecialchars($del_list->comments, ENT_QUOTES, 'UTF-8') . '</p>
                </div>
            </div>';

        return response()->json(['success' => true, 'message' => 'Check out successfully', 'tabel' => $sel]);
    }
    public function sub_plans(Request $request)
    {
        $id = $request->id;
        $wallet = CustomerPlansTrack::where('id', $id)->get();

        // dd($wallet);
        $i = 1;
        $sel = '<h4 class="text-center mt-3">Subscription History</h4>
        <table id="example" class="table table-bordered dt-responsive nowrap w-100 mt-3">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Actual price</th>
                    <th>Offer Price</th>
                    <th>Discounted Price</th>

                </tr>
            </thead>
            <tbody id="tab_values">';
        // dd($history);
        foreach ($wallet as $his) {
            $plan = Subscriptionplan::where('id', $his->plan_id)->first();
            $sel .= '<tr>
                <td>' . $i . '</td>
                <td> ₹' . round($his->total_amount) . '</td>
                <td> ₹' .  ($his->total_amount * round($his->discount)) / 100 . '</td>

                <td> ₹' . round($his->final_price) . '</td>

                  </tr>';
            $i++;
        }

        $sel .= '</tbody>
        </table>';

        return response()->json(['success' => true, 'message' => 'Check out successfully', 'tabel' => $sel]);
    }


    public function wallet_history(Request $request)
    {
        // Check if from_date and to_date are provided, otherwise set defaults
        $from_date = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : today()->subDays(7)->startOfDay();
        $to_date = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : today()->endOfDay();
    
        // Fetch wallet data within the specified date range
        $wallet_data = Customerwallethistory::whereBetween('created_at', [$from_date, $to_date])
            ->orderBy('created_at', 'desc')
            ->get();
    
        // Add customer name or mobile to each wallet data entry
        foreach ($wallet_data as $d) {
            $cus = Customer::find($d->customer_id);
            $d->cus_name = $cus ? ($cus->name ?? $cus->mobile) : 'Unknown';
        }
    
        // Return the wallet history view with data
        return view('wallet.wallet-history', compact('wallet_data'));
    }
    
    public function cust_loc(string $id,Request $request)
    {
    //    dd($id);
        $customer=Customer::findOrFail($id);
        // dd($customer);
        return view('customer.location', compact('customer'));
    }
    public function transcation_list(Request $request)
    {
        // Check if from_date and to_date are provided, otherwise set defaults
        $from_date = $request->from_date ? Carbon::parse($request->from_date)->startOfDay() : today()->subDays(7)->startOfDay();
        $to_date = $request->to_date ? Carbon::parse($request->to_date)->endOfDay() : today()->endOfDay();
    
        // Fetch wallet data within the specified date range
        $wallet_data = Customerwallethistory::whereBetween('created_at', [$from_date, $to_date])
            ->where('debit_credit_status','debited')
            ->orderBy('created_at', 'desc')
            ->get();
    
        // Add customer name or mobile to each wallet data entry
        foreach ($wallet_data as $d) {
            $cus = Customer::find($d->customer_id);
            $d->cus_name = $cus ? ($cus->name ?? $cus->mobile) : 'Unknown';
        }
    
        // Return the wallet history view with data
        return view('wallet.tanscation-history', compact('wallet_data'));
    }
}
