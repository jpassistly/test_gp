<?php

namespace App\Http\Controllers\web\pages;

use App\Models\Customersubscription;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customerpayment;
use Carbon\Carbon;
use App\Models\Customer;
use App\Models\Subscriptionproduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Subscriptionplan;
use App\Models\CustomerPlansTrack;
use App\Models\Product;
use App\Models\Wallet;
use App\Models\wallet_balance;
use Illuminate\Support\Facades\Auth;
use App\Models\Customerwallethistory;
// use cust
class subscriptionListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $payment = Customersubscription::select('subscription_customer_id', 'subscription_products_id', DB::raw('count(date) as date_count'))
            ->where('date', '>', Carbon::now())
            ->groupBy('subscription_customer_id', 'subscription_products_id')
            ->get();
        foreach ($payment as $pay) {
            $cus = Customer::find($pay->subscription_customer_id);
            $pro = Subscriptionproduct::find($pay->subscription_products_id); // Use the correct model to find the product

            if ($cus) {
                $pay->subscription_customer_id = $cus->name . " - " . $cus->mobile;
            }
            if ($pro) {
                $pay->subscription_products_id = $pro->name;
            }


        }
        // dd($payment);
        return view('subscriber_list.index', compact('payment'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $product_list = Subscriptionplan::get();
        return view('plans.index', compact('product_list'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        return view('plans.add_form');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        // dd($request);
        $validatedData = $request->validate([
            'id' => 'required|exists:subscriptionplans,id',
            'name' => 'required|string|max:255',
            'discount' => 'required|numeric|min:0',
            'days_count' => 'required|numeric|min:0',
            'status' => 'required|string|in:Active,Inactive',
        ]);

        // Retrieve the subscription plan by ID
        $subscriptionPlan = Subscriptionplan::find($request->id);

        // Update the subscription plan's properties
        $subscriptionPlan->name = $request->name;
        $subscriptionPlan->discount = $request->discount;
        $subscriptionPlan->days_count = $request->days_count;
        $subscriptionPlan->status = $request->status;

        // Save the updated subscription plan to the database
        $subscriptionPlan->save();

        // Redirect to the subscription plans list with a success message
        return redirect()->to('supscription_plans')->with('success_message', 'Subscription plan updated successfully!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product_list = Subscriptionplan::where('id', $id)->first();
        // dd($product_list);
        return view('plans.update_form', compact('product_list'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function save_ratings(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer|exists:customers_subscription,id',
        ]);

        // Find the Customersubscription record by its ID
        $subscription = Customersubscription::find($request->id);

        if ($subscription) {
            // Update the fields
            $subscription->rating = $request->rating;
            $subscription->comments = $request->comments;
            $subscription->updated_at = now(); // Set the rating_date to the current timestamp

            // Handle file upload
            if ($request->hasFile('pic')) {
                // Get the uploaded file
                $file = $request->file('pic');

                // Define the destination path in the public directory
                $destinationPath = 'assets/images/review_img';

                // Generate a unique file name
                $fileName = time() . '_' . $file->getClientOriginalName();

                // Move the file to the destination path
                $file->move(public_path($destinationPath), $fileName);

                // Save the relative path to the database
                $subscription->pic = $destinationPath . '/' . $fileName;
            }
            // Save the updated record
            $subscription->save();

            return response()->json(['message' => 'Rating updated successfully.'], 200);
        }

        return response()->json(['message' => 'Subscription not found.'], 404);
    }

    public function add_plans_value(Request $request)
    {
        $validatedData = $request->validate([

            'name' => 'required|string|max:255',
            'discount' => 'required|numeric|min:0',
            'status' => 'required|string|in:Active,Inactive',
            'days_count'=>'required|numeric|min:0'
        ]);

        // Create a new subscription plan with the provided data
        $newSubscriptionPlan = new Subscriptionplan();
        $newSubscriptionPlan->name = $request->name;
        $newSubscriptionPlan->discount = $request->discount;
        $newSubscriptionPlan->days_count = $request->days_count;
        $newSubscriptionPlan->status = $request->status;

        // Save the new subscription plan to the database
        $newSubscriptionPlan->save();

        // Redirect to the subscription plans list with a success message
        return redirect()->to('supscription_plans')->with('success_message', 'Subscription plan created successfully!');
    }
    public function subscription_list(Request $request) {
        $plans = Subscriptionplan::where('status', 'Active')->get();

        if ($request->isMethod('post')) {
            $from_date = $request->from_date . ' 00:00:00';
            $to_date = $request->to_date . ' 23:59:59';
            $selectedPlan = $request->plan;

            $paymentQuery = CustomerPlansTrack::whereBetween('created_at', [$from_date, $to_date]);

            if ($selectedPlan != null) {
                $paymentQuery->where('plan_id', $selectedPlan);
            }

            $payment = $paymentQuery->get();
        } else {
            $payment = CustomerPlansTrack::orderBy('created_at', 'desc')->get();
        }

        foreach ($payment as &$pay) {
            $cus = Customer::find($pay->customers_id);
            $plan = Subscriptionplan::find($pay->plan_id);

            if ($cus) {
                $pay->customers_id = $cus->name . " - " . $cus->mobile;
            }
            if ($plan) {
                $pay->plan_id = $plan->name;
            }
        }

        return view('subscripion_cust.index', compact('payment', 'plans'));
    }



    public function payment_plan(Request $request){
        $id = $request->id;
        $payment = CustomerPlansTrack::where('id', $id)->first();

        // Assuming the product_ids and quantities are stored as comma-separated strings
        $product_ids = explode(',', $payment->product_ids); // Adjust 'product_ids' with the actual column name
        $quantities = explode(',', $payment->quantity); // Adjust 'quantities' with the actual column name

        // Ensure that product_ids and quantities arrays have the same length
        if (count($product_ids) !== count($quantities)) {
            return response()->json(['success' => false, 'message' => 'Mismatched product IDs and quantities']);
        }

        // Create the initial part of the table structure
        $sel = '
        <table id="example" class="table table-bordered dt-responsive nowrap w-100 mt-3">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody id="tab_values">';

        // Initialize counters
        $i = 1;

        // Iterate through the product IDs and quantities
        foreach ($product_ids as $index => $product_id) {
            $product = Product::select(
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
                ->where('products.id', $product_id)
                ->first();

            if (!$product) {
                // Handle case where product is not found
                $sel .= '<tr>
                    <td>' . $i . '</td>
                    <td colspan="4">Product not found (ID: ' . $product_id . ')</td>
                </tr>';
            } else {
                // Add a table row for each product
                $sel .= '<tr>
                    <td>' . $i . '</td>
                    <td>' . $product->product_name . '</td>
                    <td>' . $quantities[$index] . '</td>
                    <td>' .$product->quantity_value  . " " . $product->measurement_name . '</td>
                    <td>' . $quantities[$index] * $product->product_price . '</td>
                </tr>';
            }

            // Increment counter
            $i++;
        }

        // Close the table structure
        $sel .= '</tbody></table>';

        return response()->json(['success' => true, 'data' => $sel]);
    }
    public function get_to_date(Request $request) {
        //.. "sub_id" => "1"
        // "fromdate" => "2024-10-24"
        $id = $request->sub_id;
        $fromdate = $request->fromdate;
        
        // Retrieve the subscription plan
        $plans = Subscriptionplan::where('id', $id)->first();
        
        // Check if the plan exists
        if (!$plans) {
            return response()->json(['error' => 'Subscription plan not found.'], 404);
        }
        
        $days = $plans->days_count;
        
        // Create a Carbon instance from the fromdate and add days
        $toDate = Carbon::parse($fromdate)->addDays($days);
        
        return response()->json(['to_date' => $toDate->toDateString(),'discount'=>$plans->discount]); // Return the result as a JSON response
    }
    public function add_wallet(Request $request,string $id) {
        //.. "sub_id" => "1"
        // "fromdate" => "2024-10-24"
        $cus=Customer::findOrFail($id);
        return view('customer.add_wallet',['id'=>$id,'cus'=>$cus]);
       
    }
    public function save_wallet(Request $request) {
        $uid = Auth::user()->id;

        // Validate the request data
        $validatedData = $request->validate([
            'id' => 'required|integer|exists:customers,id',
            'amount' => 'required|numeric|min:0',
        ]);
        
        // Find the customer by ID
        $customer = Customer::findOrFail($validatedData['id']);
        
        // Retrieve or create a new wallet record for the customer
        $wallet = Wallet::firstOrNew(['customers_id' => $customer->id]);
        
        // Update wallet fields
        $wallet->current_amount = ($wallet->current_amount ?? 0) + $validatedData['amount'];
        $wallet->customers_id = $customer->id;
        $wallet->status = 'Active';
        $wallet->created_by = $uid;
        
        // Save the wallet record
        $wallet->save();
        
        // Create a new wallet history record
        $cwhis = Customerwallethistory::create([
            'customer_wallet_id' => $wallet->id, // Use wallet id instead of customers_id
            'debit_credit_status' => 'credited',
            'amount' => $validatedData['amount'], // Log only the transaction amount
            'notes' => 'Add Cash',
            'customer_id' => $validatedData['id']
        ]);
        
        // Return with a success message
        return redirect('cust_list_view2/'.$validatedData['id'])->with([
            'success' => true,
            'success_message' => 'Wallet updated successfully!'
        ]);
        
    }
    

public function calculate(Request $request) {
    // Initialize variables for storing product data and total cost
    $productQuantities = [];
    $totalCost = 0;
    $sub_id=$request->subscription_id;
    $cid=$request->customer_id;
    $discount=$request->discount/100;
   
    $plans = Subscriptionplan::where('id', $sub_id)->first();
    $wallet = Wallet::where('customers_id', $cid)->first();
    $days = $plans->days_count;
    if(!$wallet){
        return response()->json(['wallet' => 0,'message'=>'first create the wallet']);  
    }
    // Loop through all the request inputs
    foreach ($request->all() as $key => $value) {
        // Check if the key starts with 'product_id_' and has a non-null value
        if (strpos($key, 'product_id_') === 0 && $value) {
            // Extract the product ID number from the key
            $productId = str_replace('product_id_', '', $key);
            $quantity = (int)$value;

            // Find the product by its ID and get the cost
            $product = Product::find($productId);
            if ($product) {
                $productCost = $product->price * $quantity;
                $totalCost += $productCost;

                // Store the product ID and quantity for reference
                $productQuantities[$productId] = $quantity;
            }
        }
    }
    $totalpric2=($totalCost*$days);  
$discount=($totalpric2*$discount);
// dd($discount);
$finalprice=$totalpric2-$discount;
    // Return the calculated total cost and product quantities
    return response()->json([
        'product_quantities' => $productQuantities,
        'total_cost' => $totalCost*$days,
        'wallet'=>1,
        'wallet_balance'=>$wallet->current_amount,
        'final_price'=>$finalprice
    ]);
}
public function grceperiod(Request $request) {
    
    
}

}
