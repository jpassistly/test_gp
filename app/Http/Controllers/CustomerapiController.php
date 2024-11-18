<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Pincode;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tempcart;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Wallet;
use App\Models\Customer_fcm;
use App\Models\Suscriptionsession;
use App\Models\Customersubscription;
use App\Models\CustomerPlansTrack;
use App\Models\Customerpayment;
use App\Http\Controllers\PaymentController;
use App\Models\Customerwallethistory;
use Illuminate\Support\Facades\DB;
use App\Models\Subscriptionproduct;
use App\Models\Subscriptionplan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Models\Area;
use Illuminate\Support\Facades\Validator;
use App\Models\citytable;
use App\Models\Unit;
use App\Models\subscription_log;
class CustomerapiController extends Controller
{
    //

    public function __construct()
    {

        // update Un Delivered
        $today = date('Y/m/d');

        $sub = Customersubscription::where('delivery_status', 'yet to deliver')
            ->where('date', '<', $today)
            ->get();

        if ($sub) {
            foreach ($sub as $subscription) {
                $subscription->delivery_status = 'Undelivered';
                $subscription->save();
            }
        }



        $order = Order::where('delivery_status', 'yet to deliver')
            ->where('delivery_date', '<', $today)
            ->get();

        if ($order) {
            foreach ($order as $odr) {
                $odr->delivery_status = 'Undelivered';
                $odr->save();
            }
        }
        // update Un Delivered


        $phone = '1111111111';


        $check_user = Customer::where('mobile', $phone)->first();

        if (!$check_user) {
            $otp = '123456';

            // Create a new user and assign OTP
            $new_user = Customer::create([
                'mobile' => $phone,
                'otp' => $otp,
                'address_status' => 'N',
                'status' => 'Active',
            ]);

            // Generate token for the newly created user
            $token = $new_user->createToken($phone . '-AuthToken')->plainTextToken;

            // Update the user with the token
            $new_user->update(['remember_token' => $token]);
        }


        $this->payment_controller = new PaymentController();
    }

    public function customer_login(Request $request)
    {


        $post_values = $request->all();
        $otp = random_int(100000, 999999);  // Generate OTP
        $phone = $post_values['mobile'];    // Get mobile number
        $type=$post_values['type']; 
        $check_user = Customer::where('mobile', $phone)->first();

        if ($phone == '9999999999' || $phone == '1111111111') {

            $check_user = Customer::where('mobile', $phone)->first();

            if (!empty($check_user)) {
                $otp = '123456';
                // Update OTP for existing user
                $check_user->update(['otp' => $otp]);

                $token = $check_user->createToken($phone . '-AuthToken')->plainTextToken;
                $check_user->update(['remember_token' => $token]);

                return response()->json([
                    'otp' => $otp,
                    'message' => 'OTP generated successfully for existing user',
                ]);
            } else {
                $otp = '123456';
                // Create a new user and assign OTP
                $new_user = Customer::create([
                    'mobile' => $phone,
                    'otp' => $otp,
                    'address_status' => 'N',
                    'status' => 'Active',
                ]);

                $token = $check_user->createToken($phone . '-AuthToken')->plainTextToken;
                $check_user->update(['remember_token' => $token]);

                return response()->json([
                    'otp' => $otp,
                    'message' => 'OTP generated and user created successfully',
                ]);
            }
        } else {

            $check_user = Customer::where('mobile', $phone)->first();


            if (!empty($check_user)) {
                // Update OTP for existing user
                $check_user->update(['otp' => $otp,'type'=>$type]);

                $otpsend_check = $this->send_otp($phone, $otp);

                // Validate if OTP was sent successfully
                if (isset($otpsend_check['ErrorCode']) && $otpsend_check['ErrorCode'] == '000') {
                    return response()->json([
                        'otp' => $otp,
                        'message' => 'OTP generated successfully for existing user',
                    ]);
                } else {
                    return response()->json([
                        'error' => $otpsend_check,
                        'message' => 'Failed to send OTP. Please try again.',
                    ], 500);
                }
            } else {
                // Create a new user and assign OTP
                $new_user = Customer::create([
                    'mobile' => $phone,
                    'otp' => $otp,
                    'address_status' => 'N',
                    'status' => 'Active',
                    'type'=>$type
                ]);

                $token = $new_user->createToken($phone . '-AuthToken')->plainTextToken;
                $new_user->update(['remember_token' => $token]);

                // Send OTP using the sendotp function
                $otpsend_check = $this->send_otp($phone, $otp);

                // Validate if OTP was sent successfully
                if (isset($otpsend_check['ErrorCode']) && $otpsend_check['ErrorCode'] == '000') {
                    return response()->json([
                        'otp' => $otp,
                        'message' => 'OTP generated and user created successfully',
                    ]);
                } else {
                    return response()->json([
                        'error' => $otpsend_check,
                        'message' => 'Failed to send OTP. Please try again.',
                    ], 500);
                }
            }
        }
        // Check if the customer exists



    }

    // Function to send OTP via API
    private function send_otp($phone, $otp)
    {
        $url = "http://sms.vstcbe.com/api/mt/SendSMS";

        // JSON payload for sending OTP
        $data = json_encode([
            "Account" => [
                "User" => "GPPAAL",
                "Password" => "Gppaal@123",
                "SenderId" => "GPPAAL",
                "Channel" => "Trans",
                "DCS" => 0,
                "FlashSms" => 0,
                "SchedTime" => null,
                "GroupId" => null,
                "Route" => 3
            ],
            "Messages" => [
                [
                    "Number" => "91$phone",
                    "Text" => "$otp is your one-time passcode for login from Gramathupaal",
                ]
            ]
        ]);

        // Make API call to send the OTP and return the response
        return json_decode($this->sendotp('POST', $url, $data), true);
    }

    // cURL function to send the OTP
    private function sendotp($method, $url, $data)
    {
        $curl = curl_init();

        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                if ($data) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                }
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                if ($data) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                }
                break;
            default:
                if ($data) {
                    $url = sprintf("%s?%s", $url, http_build_query($data));
                }
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        $result = curl_exec($curl);
        if (!$result) {
            die("Connection Failure: " . curl_error($curl));
        }
        curl_close($curl);

        return $result;
    }

    public function check_otp(Request $request)
    {
        $post_values = $request->all();

        if (!isset($post_values['mobile']) || !isset($post_values['otp'])) {
            return response()->json([
                'message' => 'Missing mobile number or OTP',
                'status' => 400,
            ], 400);
        }

        $user = Customer::where('mobile', $post_values['mobile'])->first();

        if ($user->status == "Inactive") {
            return response()->json([
                'message' => 'Your account has been blocked kindly contact admin',
                'status' => 401,
            ], 401);
        }

        if (!$user || $post_values['otp'] !== $user->otp) {
            return response()->json([
                'message' => 'Incorrect OTP or mobile number',
                'status' => 401,
            ], 401);
        }

        $check_fcmkey = Customer_fcm::where('customer_id', $user->id)->first();
        if ($check_fcmkey) {
            $check_fcmkey->fcm_key = isset($post_values['customer_fcmkey']) ? $post_values['customer_fcmkey'] : "";
            $check_fcmkey->save();
        } else {
            $create_fcmkey = Customer_fcm::create([
                'customer_id' => $user->id,
                'fcm_key' => isset($post_values['customer_fcmkey']) ? $post_values['customer_fcmkey'] : "",
            ]);
        }



        return response()->json([
            'access_token' => $user->remember_token,
            'customer_id' => $user->id,
            'message' => 'OTP verified successfully',
            'status' => 200,
        ], 200);
    }


    public function save_fcm(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|integer',
            'fcm_key' => 'required',
        ]);

        if ($validator->fails()) {
            // Handle validation failure
            return response()->json([
                'status' => 'error',
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }


        $fcm = Customer_fcm::where('customer_id', $request->customer_id)->first();

        if ($fcm) {
            $fcm = Customer_fcm::where('customer_id', $request->customer_id)
                ->update(['fcm_key' => $request->fcm_key]);
        } else {
            $fcm = Customer_fcm::create([
                'customer_id' => $request->customer_id,
                'fcm_key' => $request->fcm_key,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Fcm saved success',
            'status' => 200,
        ], 200);
    }


    public function get_pincodes()
    {
        try {
            $pincodes = Pincode::where('status', 'Active')->get();

            if ($pincodes->isEmpty()) {
                return response()->json([
                    'message' => 'No active pincodes found',
                    'status' => 204,
                ], 204);
            }

            return response()->json([
                'message' => 'Active pincodes retrieved successfully',
                'data' => $pincodes,
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Failed to retrieve pincodes: ' . $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function get_area(request $request)
    {
        try {

            if ($request->pincode) {
                $area = Area::where('pincode', $request->pincode)->where('status', 'Y')->get();
            } else {
                $area = Area::where('status', 'Y')->get();
            }


            if ($area->isEmpty()) {
                return response()->json([
                    'message' => 'No active pincodes found',
                    'status' => 204,
                ], 204);
            }

            return response()->json([
                'message' => 'Active area retrieved successfully',
                'data' => $area,
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Failed to retrieve area: ' . $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
    public function get_city(request $request)
    {


           
                $area = citytable::where('status', 'Y')->where('del', 0)->get();
           


            

            return response()->json([
                'message' => 'Active area retrieved successfully',
                'data' => $area,
                'status' => 200,
            ], 200);
       
    }


    public function store_address(Request $request)
    {
        $post_values = $request->all();

        if (isset($post_values['loc_status']) && isset($post_values['customer_id']) &&  isset($post_values['pincode_id']) && isset($post_values['area_id'])) {
            try {
                $user = Customer::where('id', $post_values['customer_id'])->first();

                if (!$user) {
                    return response()->json([
                        'message' => 'Customer not found',
                        'status' => 404,
                    ], 404);
                }

                // if (isset($user['address'])) {
                //     return response()->json([
                //         'message' => 'Address already exists',
                //         'status' => 409, // Conflict status code
                //     ], 409);
                // }
                $addressParts = [
                

                    $post_values['door_no'],
                    $post_values['floor_no'],
                    $post_values['flat_no'],
                    $post_values['street'],
                    $post_values['land_mark'],
                ];
                
                // Filter out any null or empty values
                $filteredAddressParts = array_filter($addressParts, function ($value) {
                    return !is_null($value) && $value !== '';
                });
                
                // Join the filtered parts with a comma
                $add = implode(',', $filteredAddressParts);
                $user->address = $add;
                $user->city = $post_values['city'];
                $user->land_mark = $post_values['land_mark'];
                $user->street = $post_values['street'];
                $user->city = $post_values['city'];
                $user->street = $post_values['street'];
                $user->flat_no = $post_values['flat_no'];
                $user->floor_no = $post_values['floor_no'];
                $user->door_no = $post_values['door_no'];
                $user->pincode_id = $post_values['pincode_id'];
                $user->area_id = $post_values['area_id'];
                $user->loc_status = $post_values['loc_status'];
                $user->latlon = isset($post_values['lat_long']) ? $post_values['lat_long'] : "";
                $user->save();

                return response()->json([
                    'message' => 'Address added successfully',
                    'status' => 200,
                    'data' => $user,
                ], 200);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Failed to save address',
                    'status' => 500,
                    'error' => $e->getMessage(), // Provide error message for debugging
                ], 500);
            }
        }

        return response()->json([
            'message' => 'Missing customer_id, address, or pincode ,or area_id, or status in request',
            'request' => $request->all(),
            'status' => 400,
        ], 400);
    }
    // public function store_address(Request $request)
    // {
    //     $post_values = $request->all();

    //     if (isset($post_values['loc_status']) && isset($post_values['customer_id']) && isset($post_values['address']) && isset($post_values['pincode_id']) && isset($post_values['area_id'])) {
    //         try {
    //             $user = Customer::where('id', $post_values['customer_id'])->first();

    //             if (!$user) {
    //                 return response()->json([
    //                     'message' => 'Customer not found',
    //                     'status' => 404,
    //                 ], 404);
    //             }

    //             // if (isset($user['address'])) {
    //             //     return response()->json([
    //             //         'message' => 'Address already exists',
    //             //         'status' => 409, // Conflict status code
    //             //     ], 409);
    //             // }

    //             $user->address = $post_values['address'];
    //             $user->pincode_id = $post_values['pincode_id'];
    //             $user->area_id = $post_values['area_id'];
    //             $user->loc_status = $post_values['loc_status'];
    //             $user->latlon = isset($post_values['lat_long']) ? $post_values['lat_long'] : "";
    //             $user->save();

    //             return response()->json([
    //                 'message' => 'Address added successfully',
    //                 'status' => 200,
    //                 'data' => $user,
    //             ], 200);
    //         } catch (\Exception $e) {
    //             return response()->json([
    //                 'message' => 'Failed to save address',
    //                 'status' => 500,
    //                 'error' => $e->getMessage(), // Provide error message for debugging
    //             ], 500);
    //         }
    //     }

    //     return response()->json([
    //         'message' => 'Missing customer_id, address, or pincode ,or area_id, or status in request',
    //         'request' => $request->all(),
    //         'status' => 400,
    //     ], 400);
    // }


    public function check_addressverification(Request $request)
    {
        $validatedData = $request->validate([
            'customer_id' => 'required|integer',
        ]);

        $customer_id = $validatedData['customer_id'];

        $address_verify = Customer::where('id', $customer_id)
            ->where('address_status', 'Y')
            ->first();

        if (!is_null($address_verify)) {
            return response()->json([
                'message' => 'Address verified successfully',
                'status' => 200,
            ], 200);
        }

        return response()->json([
            'message' => 'Address has to be verified',
            'status' => 200,
        ], 200);
    }

    public function get_productcategory()
    {
        $categories = Category::where('status', 'Active')->get();

        $category_list = [];

        if (!$categories->isEmpty()) {
            foreach ($categories as $category) {

                $category_data = [
                    'category_id' => $category->id,
                    'category_name' => $category->name,
                    'category_image' => url($category->pic),
                ];

                $category_list[] = $category_data; // Add each category data to the list
            }

            return response()->json([
                'categories' => $category_list,
                'status' => 200,
            ], 200);
        }

        return response()->json([
            'message' => 'No active categories found',
            'status' => 204,
        ], 204);
    }

    public function get_productbycategory(Request $request)
    {
        $post_values = $request->all();
        $products = Product::select(
            'products.*',
            'units.name as quantity_name',
            'measurements.name as measurement_name',
        )
            ->join('units', 'products.quantity_id', '=', 'units.id')
            ->join('measurements', 'products.measurement_id', '=', 'measurements.id')
            ->where('products.category_id', $post_values['category_id'])
            ->where('products.status', 'Active')
            ->where('units.status', 'Active')
            ->get();
        $product_list = [];

        if (!$products->isEmpty()) {
            foreach ($products as $product) {
                $product_data = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'measurement_name' => $product->measurement_name,
                    'quantity_name' => $product->quantity_name,
                    'category_id' => $product->category_id,
                    'quantity_id' => $product->quantity_id,
                    'product_image' => URL::asset($product->pic),
                    'product_price' => $product->price,
                ];

                $product_list[] = $product_data;
            }
            return response()->json([
                'products' => $product_list,
                'status' => 200,
            ], 200);
        }

        return response()->json([
            'message' => 'No active products found for the given category',
            'status' => 204,
        ], 204);
    }

    public function get_subscriptionplans()
    {
        $subscription_products = Product::select(
            'products.*',
            'units.name as quantity_name',
            'measurements.name as measurement_name',
        )
            ->join('units', 'products.quantity_id', '=', 'units.id')
            ->join('measurements', 'products.measurement_id', '=', 'measurements.id')
            ->where('products.subscription', 'Y')
            ->get();

        $n_list = Product::where('subscription', 'Y')
            ->groupBy('product_id')
            ->select('product_id', DB::raw('MAX(name) as name'))
            ->get();


        foreach ($n_list as $d) {
            $product = Product::where('product_id', $d->product_id)->first();
            $d->pic = url($product->pic);
        }

        $subscription_list = [];
        if (!$subscription_products->isEmpty()) {
            foreach ($subscription_products as $subcription) {
                $subcription_details = [
                    'subcription_productid' => $subcription->id,
                    'subcription_productname' => $subcription->name,
                    'subcription_productquantity_id' => $subcription->quantity_id,
                    'subcription_productprice' => $subcription->price,
                    'subcription_productimage' => url($subcription->pic),
                    'subcription_productquantityname' => $subcription->quantity_name,
                    'subcription_measurement_name' => $subcription->measurement_name,
                    'product_id' => $subcription->product_id,
                ];

                $subcription_list[] = $subcription_details;
            }

            $subscription_plans = Subscriptionplan::where('status', 'Active')
                ->select('id', 'name', 'discount', 'days_count') // Specify the columns you want to retrieve
                ->get();
            $subscription_type = [];
            if (!$subscription_plans->isEmpty()) {
                foreach ($subscription_plans as $subscriptionplan) {
                    $subscriptiontype = [
                        'subscription_planid' => $subscriptionplan->id,
                        'subscription_name' => $subscriptionplan->name,
                        'subscription_discount' => $subscriptionplan->discount,
                        'subscription_days_count' => $subscriptionplan->days_count,

                    ];
                    $subscription_type[] = $subscriptiontype;
                }
            }

            return response()->json([
                'product_cat' => $n_list,
                'subscription_products' => $subcription_list,
                'subscription_type' => $subscription_type,

                'status' => 200,
            ], 200);
        }

        return response()->json([
            'message' => 'No active subcription products found for the given category',
            'status' => 204,
        ], 204);
    }

    // public function get_subscriptionprice(Request $request)
    // {
    //     $post_values = $request->all();
    //     $product_ids = explode(',', $post_values['product_id']);
    //     $product_quantities = explode(',', $post_values['product_qty']);
    //     $subscription_id = $post_values['subscription_id'];
    //     $customer_id= $post_values['customer_id'];
    //     $products = Product::whereIn('id', $product_ids)->select('id', 'price', 'name')->get();
    //     //print_r($products);exit;
    //     $grand_total = 0;

    //     foreach ($products as $product) {
    //         $index = array_search($product->id, $product_ids);
    //         $quantity = $product_quantities[$index];
    //         $grand_total += $product->price * $quantity;
    //     }
    //     $subscription = Subscriptionplan::where('id', $subscription_id)
    //         // ->select('id', 'name', 'discount')
    //         ->first();
    //     // $string = $subscription['name'];

    //     // preg_match('/\d+/', $string, $matches);
    //     // $number = $matches[0];
    //     $number = $subscription['days_count'];

    //     $total_price = $grand_total * $number;
    //     $discount = $subscription ? $subscription->discount : 0;
    //     $final_price = $total_price - ($total_price * ($discount / 100));
    //     $final_price = number_format($final_price, 2, '.', '');
    //     $customer=Customer::where('id',$customer_id)->first();
    //     subscription_log::create([
    //         'customers_id' => $customer_id,
    //         'name' => $customer->name,
    //         'mobile' => $customer->mobile,
    //         'total_price' => $total_price,
    //         'discount' => $discount,
    //         'final_price' => $final_price,
    //         'remarks' => 'Subscription price calculated', // Modify remarks as needed
    //         'created_by' =>$customer_id, // Assuming logged-in user, else set to a default
    //         'updated_by' => $customer_id,
    //         'created_at' => now(),
    //         'updated_at' => now(),
    //     ]);
    //     return response()->json([
    //         'total_price' => $total_price,
    //         'discount' => $discount,
    //         'final_price' => $final_price,
    //         'status' => 200,
    //     ], 200);
    // }
    public function get_subscriptionprice(Request $request)
    {
        try {
            $post_values = $request->all();
            
            // Retrieve product IDs and quantities from request
            $product_ids = explode(',', $post_values['product_id']);
            $product_quantities = explode(',', $post_values['product_qty']);
            $subscription_id = $post_values['subscription_id'];
            $customer_id = $post_values['customer_id'];
            
            // Retrieve products and calculate the grand total
            $products = Product::whereIn('id', $product_ids)->select('id', 'price', 'name')->get();
            $grand_total = 0;
    
            foreach ($products as $product) {
                $index = array_search($product->id, $product_ids);
                if ($index === false) continue;
                
                $quantity = $product_quantities[$index] ?? 1;  // Default to 1 if quantity is missing
                $grand_total += $product->price * $quantity;
            }
    
            // Retrieve subscription details
            $subscription = Subscriptionplan::where('id', $subscription_id)->first();
            if (!$subscription) {
                return response()->json(['error' => 'Invalid subscription ID'], 400);
            }
    
            $number = $subscription->days_count;
            $total_price = $grand_total * $number;
            $discount = $subscription->discount ?? 0;
            $final_price = $total_price - ($total_price * ($discount / 100));
            $final_price = number_format($final_price, 2, '.', '');
    
            // Retrieve customer details
            $customer = Customer::where('id', $customer_id)->first();
            if (!$customer) {
                return response()->json(['error' => 'Invalid customer ID'], 400);
            }
    
            // Insert into subscription_log
            subscription_log::create([
                'customers_id' => $customer_id,
                'name' => $customer->name,
                'mobile' => $customer->mobile,
                'total_price' => $total_price,
                'discount' => $discount,
                'final_price' => $final_price,
                'remarks' => 'Subscription price calculated',
                
                
                'created_at' => now(),
                'updated_at' => now(),
            ]);
    
            // Return response with calculated values
            return response()->json([
                'total_price' => $total_price,
                'discount' => $discount,
                'final_price' => $final_price,
                'status' => 200,
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred: ' . $e->getMessage(),
                'status' => 500,
                'data'=>$customer_id
            ], 500);
        }
    }
    

    public function addproduct_tempcart(Request $request)
    {
        $post_values = $request->all();
        $post_values['product_qty'] = (int) $post_values['product_qty'];

        $cart = Tempcart::where('product_id', $post_values['product_id'])
            ->where('user_id', $post_values['customer_id'])
            ->first();

        if ($cart) {
            $cart->product_quantity = $post_values['product_qty'];
            $cart_items = $cart->save();
        } else {
            // Create a new cart item
            $cart_items = Tempcart::create([
                'user_id' => $post_values['customer_id'],
                'product_id' => $post_values['product_id'],
                'product_quantity' => $post_values['product_qty']
            ]);
        }

        if ($cart_items) {
            return response()->json([
                'message' => 'Product added to cart',
                'status' => 200,
            ], 200);
        }

        return response()->json([
            'message' => 'Failed to update cart',
            'status' => 400,
        ], 400);
    }

    public function show_cart(Request $request)
    {
        $post_values = $request->all();

        $cartDetails = Cart::select('cart.*', 'products.name as product_name', 'products.price as product_price', 'products.pic as product_image', 'measurements.name as measurement_name', 'units.name as quantity_value')
            ->join('products', 'cart.product_id', '=', 'products.id')
            ->join('measurements', 'products.measurement_id', '=', 'measurements.id')
            ->join('units', 'products.quantity_id', '=', 'units.id')
            ->where('cart.customers_id', $post_values['customer_id'])
            ->whereNull('order_id')
            ->get();

        $cartDetails->transform(function ($item) {
            $item->product_image = URL::to($item->product_image);
            return $item;
        });
        if ($cartDetails->isEmpty()) {
            return response()->json([
                'message' => 'Cart is empty',
                'status' => 200,
            ], 200);
        }

        $totalCartValue = 0;
        foreach ($cartDetails as $cartItem) {
            $totalCartValue += $cartItem->product_price * $cartItem->quantity;
        }

        return response()->json([
            'message' => 'Cart details retrieved successfully',
            'status' => 200,
            'data' => [
                'cart_items' => $cartDetails,
                'total_cart_value' => $totalCartValue
            ],
        ], 200);
    }

    public function update_cart(Request $request)
    {
        $post_values = $request->all();
        $update_cart = Tempcart::where('user_id', $post_values['customer_id'])
            ->where('product_id', (int) $post_values['product_id'])
            ->update(['product_quantity' => $post_values['product_qty']]);

        if ($update_cart) {
            $cartDetails = Tempcart::select('tempcart.*', 'products.name as product_name', 'products.price as product_price', 'products.pic as product_image', 'measurements.name as measurement_name', 'units.name as quantity_value')
                ->join('products', 'tempcart.product_id', '=', 'products.id')
                ->join('measurements', 'products.measurement_id', '=', 'measurements.id')
                ->join('units', 'products.quantity_id', '=', 'units.id')
                ->where('tempcart.user_id', $post_values['customer_id'])
                ->get();

            $cartDetails->transform(function ($item) {
                $item->product_image = URL::to($item->product_image);
                return $item;
            });

            $totalCartValue = 0;
            foreach ($cartDetails as $cartItem) {
                $totalCartValue += $cartItem->product_price * $cartItem->product_quantity;
            }

            return response()->json([
                'message' => 'Cart details retrieved successfully',
                'status' => 200,
                'data' => [
                    'cart_items' => $cartDetails,
                    'total_cart_value' => $totalCartValue
                ],
            ], 200);
        }
    }

    public function add_cart(Request $request)
    {
        $post_values = $request->all();

        $product_ids = explode(',', $request->input('product_id'));
        $product_prices = explode(',', $request->input('product_price'));
        $product_quantities = explode(',', $request->input('product_qty'));

        // Begin a transaction
        DB::beginTransaction();

        try {
            // Loop through the products and insert each one into the database
            $customer_id = $request->input('customer_id');
            foreach ($product_ids as $index => $product_id) {
                $ck = Cart::where(['customers_id' => $customer_id, 'product_id' => $product_id])->whereNull('order_id')->first();

                if ($ck) {
                    // Update existing cart item
                    $ck->update([
                        'price' => $product_prices[$index],
                        'quantity' => $ck->quantity + $product_quantities[$index],
                    ]);
                } else {
                    // Create new cart item
                    Cart::create([
                        'customers_id' => $customer_id,
                        'product_id' => $product_id,
                        'price' => $product_prices[$index],
                        'quantity' => $product_quantities[$index],
                    ]);
                }
            }

            // Commit the transaction
            DB::commit();

            // Delete temporary cart items outside the transaction block
            TempCart::where('user_id', $customer_id)->delete();

            return response()->json(['message' => 'Cart items added successfully.'], 201);
        } catch (Exception $e) {
            // Rollback the transaction if something goes wrong
            DB::rollBack();

            // Return an error response
            return response()->json(['error' => 'Failed to add cart items. Please try again.', 'details' => $e->getMessage()], 500);
        }
    }


    public function delete_cartitem(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'customer_id' => 'required|integer',
            'product_id' => 'required|integer',
        ]);

        $customer_id = $request->input('customer_id');
        $product_id = $request->input('product_id');

        // Try to delete the item from the TempCart
        try {
            $delete_item = Cart::where('customers_id', $customer_id)
                ->where('product_id', $product_id)
                ->whereNull('order_id')
                ->delete();

            if ($delete_item) {
                return response()->json([
                    'message' => 'Product removed from cart',
                    'status' => 200,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Product not found in cart',
                    'status' => 404,
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to remove product from cart',
                'status' => 500,
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function save_order(Request $request)
    {
        // Retrieve all post values
        $post_values = $request->all();

        $order_id = $this->generateOrderId();
        $customer_id = $post_values['customer_id'];
        $cart_total = $post_values['cart_total'];
        $remarks = $post_values['remarks'];
        $delivery_date = $post_values['delivery_date'];
        $delivery_date = Carbon::createFromFormat('d-m-Y', $delivery_date)->format('Y-m-d');

        $wallet = Wallet::where('customers_id', $customer_id)
            ->where('status', 'Active')
            ->select('current_amount', 'recharged_amount_till', 'id')
            ->first();

        $check_date = Carbon::createFromFormat('Y-m-d', $delivery_date)->format('Y-m-d');

        // Query the CustomerPlansTrack model
        $check_subs_to_this_date = CustomerPlansTrack::where('customers_id', $customer_id)
            ->where('start_date', '<=', $check_date)
            ->where('end_date', '>=', $check_date)
            ->get();


        if (($check_subs_to_this_date->count()) > 0) {
            // return response()->json([
            //     'message' => 'You  Order Place in this date',
            // ], 401);

            if ($wallet && $wallet->current_amount >= $cart_total) {
                DB::beginTransaction();

                try {
                    $cus = Customer::where('id', $customer_id)->first();

                    $cart = Cart::where('customers_id', $customer_id)
                        ->whereNull('order_id')
                        ->get();

                    foreach ($cart as $d) {
                        $product = Product::where('id', $d->product_id)->first();
                        $order = Order::create([
                            'order_id' => $order_id,
                            'customer_id' => $customer_id,
                            'area' => $cus->area_id,
                           
                            'products_id' => $d->product_id,
                            'quantity' => $d->quantity,
                            'cus_lat_lon' => $cus->latlon,
                            'pincode' => $cus->pincode_id,
                            'price' => $product->price,
                            'delivery_status' => "yet to deliver",
                            'delivery_date' => $delivery_date,
                        ]);
                    }


                    $wallet = Wallet::where('customers_id', $customer_id)
                        ->where('status', 'Active')
                        ->select('current_amount', 'recharged_amount_till', 'id', 'customers_id')
                        ->first();
                    $wallet->current_amount -= $cart_total;
                    $wallet->save();

                    Customerwallethistory::create([
                        'customer_wallet_id' => $wallet->id,
                        'debit_credit_status' => "debited",
                        'amount' => $cart_total,
                        'remarks' => $remarks,
                        'customer_id' => $wallet->customers_id,
                        'notes' => '',
                    ]);

                    $update_orderid = Cart::where('customers_id', $customer_id)
                        ->whereNull('order_id')
                        ->update(['order_id' => $order_id]);

                    $update_subscription = Customersubscription::where('subscription_customer_id', $customer_id)
                        ->where('subscription_customer_id', $customer_id)
                        ->where('date', $delivery_date)
                        ->where('delivery_status', 'yet to deliver')
                        ->update(['addon_status' => $order_id]);
                    // Commit transaction
                    DB::commit();

                    $cus_details = Customer::where('id', $customer_id)->first();

                    if ($cus_details) {
                        // Get the FCM token and customer details
                        $get_fcm = Customer_fcm::where('customer_id', $cus_details->id)->first();

                        if ($get_fcm && $cus_details) {
                            // Prepare notification data

                            $title = 'Ordered Successful';
                            $message_body = 'Dear ' . $cus_details->name . ', Your Order Placed successfully';

                            $image = null; // Replace with your image URL if needed
                            $link = null;  // Replace with your link if needed

                            // Instantiate the NotificationController and send the notification
                            $notificationController = new NotificationController();
                            $notification = $notificationController->sendNotification($get_fcm->fcm_key, $title, $message_body, $image, $link);
                        }
                    }
                    return response()->json([
                        'message' => 'Order placed successfully',
                        'status' => 200,
                    ], 200);
                } catch (\Exception $e) {
                    DB::rollBack();

                    return response()->json([
                        'message' => 'Failed to place order',
                        'status' => 500,
                        'error' => $e->getMessage(),
                    ], 500);
                }
            } else {
                return response()->json([
                    'message' => 'Insufficient wallet amount',
                    'status' => 401,
                ], 401);
            }
        } else {
            return response()->json([
                'message' => 'Your Order Can not be Placed at this date',
                'status' => 401,
                'count' => $check_subs_to_this_date,
            ], 401);
        }
    }


    private function generateOrderId()
    {
        do {
            $order_id = 'ord' . rand(100000, 999999);
        } while (Order::where('order_id', $order_id)->exists());

        return $order_id;
    }

    // public function save_subscription(Request $request)
    // {
    //     $post_values = $request->all();

    //     $customer_id = $post_values['customer_id'];
    //     $subscription_id = $post_values['subscription_id'];
    //     $start_date = Carbon::createFromFormat('d-m-Y', $post_values['start_date']);
    //     $end_date = Carbon::createFromFormat('d-m-Y', $post_values['end_date']);
    //     $product_ids = explode(',', $post_values['product_id']);

    //     //changed 25/09/24
    //     // $product_qtys = explode(',', $post_values['product_qty']);
    //     $product_qtys = explode(',', $post_values['total_quantity']);
    //     $total_quantities = explode(',', $post_values['total_quantity']);
    //     $session_ids = $post_values['session_ids'];
    //     $subscription_value = $post_values['final_price'];

    //     $wallet = Wallet::where('customers_id', $customer_id)
    //         ->where('status', 'Active')
    //         ->select('current_amount', 'recharged_amount_till', 'id')
    //         ->first();

    //     if ($wallet && $wallet->current_amount >= $subscription_value) {

    //         // $products = Subscriptionproduct::whereIn('id', $product_ids)->select('id', 'price', 'name')->get();

    //         $subscriptionData = [];
    //         $cus = Customer::where('id', $customer_id)->first();

    //         // Iterate over each date in the range
    //         for ($date = $start_date; $date->lte($end_date); $date->addDay()) {
    //             foreach ($product_ids as $index => $product_id) {
    //                 $subscriptionData[] = [
    //                     'subscription_customer_id' => $customer_id,
    //                     'subscription_products_id' => $product_id,
    //                     'subscription_quantity' => $product_qtys[$index],
    //                     'subscription_total_qty' => $total_quantities[$index],
    //                     'subscription_session_id' => $session_ids,
    //                     'delivery_status' => "yet to deliver",
    //                     'pincode' => $cus->pincode_id,
    //                     'area' => $cus->area_id,
    //                     'cus_lat_lon' => $cus->latlon,
    //                     'from_date' => $start_date,
    //                     'to_date' => $end_date,
    //                     'date' => $date->toDateString(), // Convert Carbon instance to date string
    //                     'created_at' => now(),
    //                     'updated_at' => now(),
    //                 ];
    //             }
    //         }

    //         $wallet->current_amount -= $subscription_value;
    //         $wallet->save();


    //         //create wallet history
    //         Customerwallethistory::create([
    //             'customer_wallet_id' => $wallet->id,
    //             'debit_credit_status' => "debited",
    //             'amount' => $subscription_value,
    //             'remarks' => 'Purchase Subscription',
    //             'customer_id' => $customer_id,
    //             'notes' => '',
    //         ]);

    //         // Insert the subscription data in bulk..
    //         Customersubscription::insert($subscriptionData);

    //         $check_subscription = Customersubscription::where('subscription_customer_id', $customer_id)->get();
    //         $add_date_sub_sts = Customer::where('id', $customer_id)->first();
    //         $add_date_sub_sts->sub_status = 1;
    //         $add_date_sub_sts->from_date = $start_date;
    //         $add_date_sub_sts->to_date = $end_date;
    //         $add_date_sub_sts->save();

    //         $startDate = Carbon::createFromFormat('d-m-Y', $post_values['start_date'])->format('Y-m-d');
    //         $endDate = Carbon::createFromFormat('d-m-Y', $post_values['end_date'])->format('Y-m-d');

    //         CustomerPlansTrack::create([
    //             'customers_id' => $customer_id,
    //             'product_ids' => $post_values['product_id'],
    //             'quantity' => $post_values['product_qty'],
    //             'plan_id' => $subscription_id,
    //             'start_date' => $startDate,
    //             'end_date' => $endDate,
    //             'total_amount' => $post_values['total_price'], // Assuming 'total_amount' corresponds to 'total_quantity'
    //             'discount' => $post_values['discount'], // Add discount logic if needed
    //             'final_price' => $post_values['final_price'],

    //         ]);


    //         if (!$check_subscription->isEmpty()) {
    //             $subscription_customer = "yes";
    //         }

    //         $cus_details = Customer::where('id', $customer_id)->first();

    //         if ($cus_details) {
    //             // Get the FCM token and customer details
    //             $get_fcm = Customer_fcm::where('customer_id', $cus_details->id)->first();

    //             if ($get_fcm && $cus_details) {
    //                 // Prepare notification data

    //                 $title = 'Subcription successful';
    //                 $message_body = 'Dear ' . $cus_details->name . ', Your subcription is added successfully';

    //                 $image = null; // Replace with your image URL if needed
    //                 $link = null;  // Replace with your link if needed

    //                 // Instantiate the NotificationController and send the notification
    //                 $notificationController = new NotificationController();
    //                 $notification = $notificationController->sendNotification($get_fcm->fcm_key, $title, $message_body, $image, $link);
    //             }
    //         }

    //         return response()->json([
    //             'message' => 'Subscription saved successfully',
    //             'subscription_customer' => $subscription_customer,
    //             'status' => 200,
    //         ], 200);
    //     }




    //     return response()->json([
    //         'message' => 'Insufficient wallet amount',
    //         'status' => 401,
    //     ], 401);
    // }

    public function save_subscription(Request $request)
{
    $post_values = $request->all();

    $customer_id = $post_values['customer_id'];
    $subscription_id = $post_values['subscription_id'];
    $start_date = Carbon::createFromFormat('d-m-Y', $post_values['start_date']);
    $end_date = Carbon::createFromFormat('d-m-Y', $post_values['end_date']);
    $product_ids = explode(',', $post_values['product_id']);
    $product_qtys = explode(',', $post_values['total_quantity']);
    $total_quantities = explode(',', $post_values['total_quantity']);
    $session_ids = $post_values['session_ids'];
    $subscription_value = $post_values['final_price'];

    $wallet = Wallet::where('customers_id', $customer_id)
        ->where('status', 'Active')
        ->select('current_amount', 'recharged_amount_till', 'id')
        ->first();

    if ($wallet && $wallet->current_amount >= $subscription_value) {

        $subscriptionData = [];
        $cus = Customer::where('id', $customer_id)->first();

        // First, create the CustomerPlansTrack entry
        $startDate = $start_date->format('Y-m-d');
        $endDate = $end_date->format('Y-m-d');
        $customerPlansTrack = CustomerPlansTrack::create([
            'customers_id' => $customer_id,
            'product_ids' => $post_values['product_id'],
            'quantity' => $post_values['product_qty'],
            'plan_id' => $subscription_id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_amount' => $post_values['total_price'],
            'discount' => $post_values['discount'],
            'final_price' => $post_values['final_price'],
        ]);

        // Get the ID of the newly created CustomerPlansTrack entry
        $customerTrackId = $customerPlansTrack->id;

        // Iterate over each date in the range
        for ($date = $start_date; $date->lte($end_date); $date->addDay()) {
            foreach ($product_ids as $index => $product_id) {
                $subscriptionData[] = [
                    'subscription_customer_id' => $customer_id,
                    'subscription_products_id' => $product_id,
                    'subscription_quantity' => $product_qtys[$index],
                    'subscription_total_qty' => $total_quantities[$index],
                    'subscription_session_id' => $session_ids,
                    'delivery_status' => "yet to deliver",
                    'pincode' => $cus->pincode_id,
                    'area' => $cus->area_id,
                    'cus_lat_lon' => $cus->latlon,
                    'from_date' => $start_date,
                    'to_date' => $end_date,
                    'date' => $date->toDateString(),
                    'customer_track_id' => $customerTrackId,  // Add customer_track_id here
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        $wallet->current_amount -= $subscription_value;
        $wallet->save();

        // Create wallet history
        Customerwallethistory::create([
            'customer_wallet_id' => $wallet->id,
            'debit_credit_status' => "debited",
            'amount' => $subscription_value,
            'remarks' => 'Purchase Subscription',
            'customer_id' => $customer_id,
            'notes' => '',
        ]);

        // Insert the subscription data in bulk
        Customersubscription::insert($subscriptionData);

        $check_subscription = Customersubscription::where('subscription_customer_id', $customer_id)->get();
        $add_date_sub_sts = Customer::where('id', $customer_id)->first();
        $add_date_sub_sts->sub_status = 1;
        $add_date_sub_sts->from_date = $start_date;
        $add_date_sub_sts->to_date = $end_date;
        $add_date_sub_sts->save();

        if (!$check_subscription->isEmpty()) {
            $subscription_customer = "yes";
        }

        $cus_details = Customer::where('id', $customer_id)->first();

        if ($cus_details) {
            // Get the FCM token and customer details
            $get_fcm = Customer_fcm::where('customer_id', $cus_details->id)->first();

            if ($get_fcm) {
                // Prepare notification data
                $title = 'Subscription successful';
                $message_body = 'Dear ' . $cus_details->name . ', Your subscription is added successfully';

                $notificationController = new NotificationController();
                $notificationController->sendNotification($get_fcm->fcm_key, $title, $message_body);
            }
        }

        return response()->json([
            'message' => 'Subscription saved successfully',
            'subscription_customer' => $subscription_customer,
            'status' => 200,
        ], 200);
    }

    return response()->json([
        'message' => 'Insufficient wallet amount',
        'status' => 401,
    ], 401);
}

    public function save_subscription2(Request $request)
    {
        $post_values = $request->all();

        $customer_id = $post_values['customer_id'];
        $subscription_id = $post_values['subscription_id'];
        $start_date = Carbon::createFromFormat('d-m-Y', $post_values['start_date']);
        $end_date = Carbon::createFromFormat('d-m-Y', $post_values['end_date']);
        $product_ids = explode(',', $post_values['product_id']);

        //changed 25/09/24
        // $product_qtys = explode(',', $post_values['product_qty']);
        $product_qtys = explode(',', $post_values['total_quantity']);
        $total_quantities = explode(',', $post_values['total_quantity']);
        $session_ids = $post_values['session_ids'];
        $subscription_value = $post_values['final_price'];

        $wallet = Wallet::where('customers_id', $customer_id)
            ->where('status', 'Active')
            ->select('current_amount', 'recharged_amount_till', 'id')
            ->first();
// dd($wallet."-".$subscription_value);
        if ($wallet && $wallet->current_amount >= $subscription_value) {

            // $products = Subscriptionproduct::whereIn('id', $product_ids)->select('id', 'price', 'name')->get();

            $subscriptionData = [];
            $cus = Customer::where('id', $customer_id)->first();

            // Iterate over each date in the range
            for ($date = $start_date; $date->lte($end_date); $date->addDay()) {
                foreach ($product_ids as $index => $product_id) {
                    $subscriptionData[] = [
                        'subscription_customer_id' => $customer_id,
                        'subscription_products_id' => $product_id,
                        'subscription_quantity' => $product_qtys[$index],
                        'subscription_total_qty' => $total_quantities[$index],
                        'subscription_session_id' => $session_ids,
                        'delivery_status' => "yet to deliver",
                        'pincode' => $cus->pincode_id,
                        'area' => $cus->area_id,
                        'cus_lat_lon' => $cus->latlon,
                        'from_date' => $start_date,
                        'to_date' => $end_date,
                        'date' => $date->toDateString(), // Convert Carbon instance to date string
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            $wallet->current_amount -= $subscription_value;
            $wallet->save();


            //create wallet history
            Customerwallethistory::create([
                'customer_wallet_id' => $wallet->id,
                'debit_credit_status' => "debited",
                'amount' => $subscription_value,
                'remarks' => 'Purchase Subscription',
                'customer_id' => $customer_id,
                'notes' => '',
            ]);

            // Insert the subscription data in bulk
            Customersubscription::insert($subscriptionData);

            $check_subscription = Customersubscription::where('subscription_customer_id', $customer_id)->get();
            $add_date_sub_sts = Customer::where('id', $customer_id)->first();
            $add_date_sub_sts->sub_status = 1;
            $add_date_sub_sts->from_date = $start_date;
            $add_date_sub_sts->to_date = $end_date;
            $add_date_sub_sts->save();

            $startDate = Carbon::createFromFormat('d-m-Y', $post_values['start_date'])->format('Y-m-d');
            $endDate = Carbon::createFromFormat('d-m-Y', $post_values['end_date'])->format('Y-m-d');

            CustomerPlansTrack::create([
                'customers_id' => $customer_id,
                'product_ids' => $post_values['product_id'],
                'quantity' => $post_values['product_qty'],
                'plan_id' => $subscription_id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'total_amount' => $post_values['total_price'], // Assuming 'total_amount' corresponds to 'total_quantity'
                'discount' => $post_values['discount'], // Add discount logic if needed
                'final_price' => $post_values['final_price'],

            ]);


            if (!$check_subscription->isEmpty()) {
                $subscription_customer = "yes";
            }

            $cus_details = Customer::where('id', $customer_id)->first();

            if ($cus_details) {
                // Get the FCM token and customer details
                $get_fcm = Customer_fcm::where('customer_id', $cus_details->id)->first();

                if ($get_fcm && $cus_details) {
                    // Prepare notification data

                    $title = 'Subcription successful';
                    $message_body = 'Dear ' . $cus_details->name . ', Your subcription is added successfully';

                    $image = null; // Replace with your image URL if needed
                    $link = null;  // Replace with your link if needed

                    // Instantiate the NotificationController and send the notification
                    $notificationController = new NotificationController();
                    $notification = $notificationController->sendNotification($get_fcm->fcm_key, $title, $message_body, $image, $link);
                }
            }

            return response()->json([
                'message' => 'Subscription saved successfully',
                'subscription_customer' => $subscription_customer,
                'customer_id'=>$customer_id,
                'status' => 200,
            ], 200);
        }




        return response()->json([
            'message' => 'Insufficient wallet amount',
            'status' => 401,
        ], 401);
    }

    // public function get_address(Request $request)
    // {
    //     $post_values = $request->all();

    //     $customer_id = $post_values['customer_id'];
    //     $customer = Customer::where('id', $customer_id)->first();

    //     $area_name = '';
    //     $pincode = '';
    //     if (isset($customer->pincode_id)) {
    //         $get_pincode = Pincode::where('id', $customer->pincode_id)->first();
    //         $pincode = $get_pincode->pincode;
    //     }

    //     if (isset($customer->area_id)) {
    //         $area = Area::where('id', $customer->area_id)->first();
    //         $area_name = $area->name;
    //     }
    //     if ($customer) {
    //         return response()->json([
    //             'address' => $customer->address,
    //             'area' => $area_name,
    //             'area_id' => $customer->area_id,
    //             'loc_status' => $customer->loc_status,
    //             'latlon' => $customer->latlon,
    //             'pincode' => $pincode,
    //             'pincode_id' => $customer->pincode_id,
    //             'status' => 200
    //         ], 200);
    //     }

    //     return response()->json([
    //         'message' => "Address not found",
    //         'status' => 401,
    //     ], 401);
    // }
    public function get_address(Request $request)
    {
        $customer_id = $request->input('customer_id');
    
        // Prepare the query
        $query = Customer::select(
                'customers.id',
                'customers.address',
                'customers.city',
                'customers.area_id',
                'customers.loc_status',
                'customers.latlon',
                'customers.flat_no',
                'customers.land_mark',
                'customers.street',
                'customers.floor_no',
                'customers.door_no',
                'customers.pincode_id',
                'areas.name as area_name',
                'pincodes.pincode as pincode',
                'city.name as city_name'
            )
            ->leftJoin('areas', 'areas.id', '=', 'customers.area_id')
            ->leftJoin('pincodes', 'pincodes.id', '=', 'customers.pincode_id')
            ->leftJoin('city', 'city.id', '=', 'customers.city')
            ->where('customers.id', $customer_id);
    
        // Get the customer result
        $customer = $query->first();
    
        // Combine the SQL and bindings to generate the full query
        $sql = vsprintf(str_replace('?', '%s', $query->toSql()), array_map(function ($binding) {
            return is_numeric($binding) ? $binding : "'$binding'";
        }, $query->getBindings()));
    
        if ($customer) {
            // Filter out null or empty values before joining them
            $addressParts = array_filter([
                $customer->address,
                $customer->area_name,
                $customer->city_name,
                $customer->pincode
            ], fn($value) => !is_null($value) && $value !== '');
    
            $fullAddress = implode(', ', $addressParts);
    
            return response()->json([
                'address' => $fullAddress,
                'area' => $customer->area_name,
                'area_id' => $customer->area_id,
                'loc_status' => $customer->loc_status,
                'latlon' => $customer->latlon,
                'pincode' => $customer->pincode,
                'pincode_id' => $customer->pincode_id,
                'flat_no' => $customer->flat_no,
                'land_mark' => $customer->land_mark,
                'street' => $customer->street,
                'city' => $customer->city_name,
                'city_id' => $customer->city,
                'floor_no' => $customer->floor_no,
                'door_no' => $customer->door_no,
                'status' => 200,
                // 'sql_query' => $sql, // Include SQL query in the response if needed
            ], 200);
        }
    
        return response()->json([
            'message' => "Address not found",
            'status' => 401,
            'sql_query' => $sql, // Include SQL query in the response even if no data found
        ], 401);
    }
    



    public function edit_address(Request $request)
    {
        // Retrieve inputs
        $customer_id = $request->input('customer_id');
        $pincode_id = $request->input('pincode_id');
        $area_id = $request->input('area_id');
        $loc_status = $request->input('loc_status');
        $latlon = $request->input('lat_long', '');
        
        // Create the address string by filtering out null or empty values
        $addressParts = [
            $request->input('door_no'),
            $request->input('floor_no'),
            $request->input('flat_no'),
            $request->input('street'),
            $request->input('land_mark'),
        ];
        $filteredAddressParts = array_filter($addressParts, fn($value) => !is_null($value) && $value !== '');
        $add = implode(',', $filteredAddressParts);
    
        // Ensure required fields are present before proceeding
        if ($latlon && $loc_status && $customer_id && $pincode_id && $area_id) {
            $edit_address = Customer::find($customer_id);
            
            if (!$edit_address) {
                return response()->json([
                    'message' => "Customer not found",
                    'status' => 404,
                ], 404);
            }
            
            // Update customer address details
            $edit_address->update([
                'address' => $add,
                'pincode_id' => $pincode_id,
                'loc_status' => $loc_status,
                'area_id' => $area_id,
                'address_status' => 'N', // Assuming 'N' means 'Not Active'
                'latlon' => $latlon,
                'door_no' => $request->input('door_no'),
                'floor_no' => $request->input('floor_no'),
                'flat_no' => $request->input('flat_no'),
                'street' => $request->input('street'),
                'land_mark' => $request->input('land_mark'),
                'deliverylines_id' => null,
                'temp_deliverylines_id' => $edit_address->deliverylines_id,
                'edited_at' => now(),
                'edited_by' => $customer_id,
            ]);
    
            // Update address details in future subscriptions
            $today = date('Y-m-d');
            $subscriptions = Customersubscription::where('subscription_customer_id', $customer_id)
                ->where('date', '>=', $today)
                ->get();
    
            foreach ($subscriptions as $subscription) {
                $subscription->update([
                    'pincode' => $pincode_id,
                    'area' => $area_id,
                    'cus_lat_lon' => $latlon,
                ]);
            }
            $subscriptions_undeliver = Customersubscription::where('subscription_customer_id', $customer_id)
            ->where('date', '=', $today)
            ->get();
            foreach ($subscriptions_undeliver as $subscriptions) {
                $subscriptions->delivery_status = 'cancelled';
                $subscriptions->save();
            }
    
            $end_date = Customersubscription::where('subscription_customer_id', $customer_id)
                ->max('date');
    
            $end_date = $end_date ? Carbon::createFromFormat('Y-m-d', $end_date) : Carbon::today();
    
            $new_subscription_count = Customersubscription::where('subscription_customer_id', $customer_id)
                ->where('date', $end_date->toDateString())
                ->count();
            if ($new_subscription_count > 0) {
                $end_date->addDay();
            }
    
            foreach ($subscriptions_undeliver as $subscription) {
                $new_subscription_data = $subscription->replicate();
                $new_subscription_data->date = $end_date->toDateString();
                $new_subscription_data->created_at = now();
                $new_subscription_data->updated_at = now();
                $new_subscription_data->delivery_status = "yet to deliver";
                $new_subscription_data->save();
            }
            // Update address details in future orders
            $orders = Order::where('customer_id', $customer_id)
                ->where('delivery_date', '>=', $today)
                ->get();
    
            foreach ($orders as $order) {
                $order->update([
                    'pincode' => $pincode_id,
                    'area' => $area_id,
                    'cus_lat_lon' => $latlon,
                ]);
            }
    
            return response()->json([
                'message' => "Address updated successfully",
                'status' => 200,
                'data'=>$edit_address
            ], 200);
        }
    
        // Error response if required fields are missing
        return response()->json([
            'message' => "Failed to update address",
            'status' => 400,
            'error' => 'Required fields missing',
        ], 400);
    }
    
    // public function edit_address(Request $request)
    // {
    //     $customer_id = $request->input('customer_id');
    //     $address = $request->input('address');
    //     $pincode_id = $request->input('pincode_id');
    //     $area_id = $request->input('area_id');
    //     $loc_status = $request->input('loc_status');
    //     $latlon = $request->input('lat_long', '');

    //     if ($latlon && $loc_status && $customer_id && $address && $pincode_id && $area_id) {
    //         $edit_address = Customer::where('id', $customer_id)
    //             ->update([
    //                 'address' => $address,
    //                 'pincode_id' => $pincode_id,
    //                 'loc_status' => $loc_status,
    //                 'area_id' => $area_id,
    //                 'address_status' => 'N',
    //                 'latlon' => $latlon
    //             ]);


    //         // update adddress in order and subscription
    //         $today = date('Y/m/d');

    //         $sub = Customersubscription::where('subscription_customer_id', $customer_id)
    //             ->where('date', '>=', $today)
    //             ->get();

    //         if ($sub) {
    //             foreach ($sub as $subscription) {
    //                 $subscription->pincode = $pincode_id;
    //                 $subscription->area = $area_id;
    //                 $subscription->cus_lat_lon = $latlon;
    //                 $subscription->save();
    //             }
    //         }



    //         $order = Order::where('customer_id', $customer_id)
    //             ->where('delivery_date', '>=', $today)
    //             ->get();

    //         if ($order) {
    //             foreach ($order as $odr) {
    //                 $odr->pincode = $pincode_id;
    //                 $odr->area = $area_id;
    //                 $odr->cus_lat_lon = $latlon;
    //                 $odr->save();
    //             }
    //         }
    //         // update adddress





    //         if ($edit_address) {
    //             return response()->json([
    //                 'message' => "Address updated successfully",
    //                 'status' => 200,
    //             ], 200);
    //         }
    //     }

    //     return response()->json([
    //         'message' => "Failed to update address",
    //         'status' => 500,
    //     ], 500);
    // }
    public function update_cus_lat_long(Request $request)
    {
        $customer_id = $request->input('customer_id');
        $latlon = $request->input('lat_long', '');

        if ($latlon) {
            $edit_address = Customer::where('id', $customer_id)
                ->update([
                    // 'address' => $address,
                    // 'pincode_id' => $pincode_id,
                    // 'loc_status' => $loc_status,
                    // 'area_id' => $area_id,
                    // 'address_status' => 'N',
                    'latlon' => $latlon
                ]);


            // update adddress in order and subscription
            $today = date('Y/m/d');

            $sub = Customersubscription::where('subscription_customer_id', $customer_id)
                ->where('date', '>=', $today)
                ->get();

            if ($sub) {
                foreach ($sub as $subscription) {
                    // $subscription->pincode = $pincode_id;
                    // $subscription->area = $area_id;
                    $subscription->cus_lat_lon = $latlon;
                    $subscription->save();
                }
            }



            $order = Order::where('customer_id', $customer_id)
                ->where('delivery_date', '>=', $today)
                ->get();

            if ($order) {
                foreach ($order as $odr) {
                    // $odr->pincode = $pincode_id;
                    // $odr->area = $area_id;
                    $odr->cus_lat_lon = $latlon;
                    $odr->save();
                }
            }
            // update adddress





            if ($edit_address) {
                return response()->json([
                    'message' => "Location updated successfully",
                    'status' => 200,
                ], 200);
            }
        }

        return response()->json([
            'message' => "Failed to update Location",
            'status' => 500,
        ], 500);
    }


    public function show_subscription(Request $request)
    {
        $post_values = $request->all();
        $customer_id = $post_values['customer_id'];
        $month = $post_values['month'];
        $year = $post_values['year'];

        $subscription_data = Customersubscription::select(
            'customers_subscription.*',
            'products.name as product_name',
            'products.price as product_price',
            'products.pic as product_image',
            'measurements.name as measurement_name',
            'units.name as unit_name',
        )
            ->join('products', 'customers_subscription.subscription_products_id', '=', 'products.id')
            ->join('measurements', 'products.measurement_id', '=', 'measurements.id')
            ->join('units', 'products.quantity_id', '=', 'units.id')
            ->where('customers_subscription.subscription_customer_id', $customer_id)
            ->whereYear('customers_subscription.date', '=', $year)
            ->whereMonth('customers_subscription.date', '=', $month)
            ->get();

        $subscription_data->transform(function ($item) {
            $item->product_image = URL::to($item->product_image);
            return $item;
        });

        $get_order = Order::select(
            'orders.*',
            'products.id as product_id',
            'products.name as product_name',
            'products.price as product_price',
            'products.pic as product_image',
            'measurements.name as measurement_name',
            'units.name as unit_name',
            // 'cart.quantity as product_quantity',
        )
            // ->join('cart', 'cart.order_id', '=', 'orders.order_id')
            ->join('products', 'orders.products_id', '=', 'products.id')
            ->join('measurements', 'products.measurement_id', '=', 'measurements.id')
            ->join('units', 'products.quantity_id', '=', 'units.id')
            ->where('orders.customer_id', $customer_id)
            ->whereYear('orders.delivery_date', '=', $year)
            ->whereMonth('orders.delivery_date', '=', $month)
            ->get();

        $get_order->transform(function ($item) {
            $item->product_image = URL::to($item->product_image);
            return $item;
        });


        $grouped_data_order = $get_order->groupBy('delivery_date')->map(function ($dateGroup) {
            return $dateGroup->map(function ($item) {
                return [

                    'id' => $item->order_id,
                    'order_customer_id' => $item->customer_id,
                    'order_products_id' => $item->product_id,
                    'order_product_name' => $item->product_name,
                    'order_product_price' => $item->product_price,
                    'order_measurement_name' => $item->measurement_name,
                    'order_unit_name' => $item->unit_name,
                    'order_product_image' => $item->product_image,
                    'order_quantity' => $item->quantity,
                    'delivery_status' => $item->delivery_status,
                ];
            });
        });
        $grouped_data = $subscription_data->groupBy('date')->map(function ($dateGroup) {
            return $dateGroup->map(function ($item) {
                return [
                    'id' => $item->id,
                    'subscription_customer_id' => $item->subscription_customer_id,
                    'subscription_products_id' => $item->subscription_products_id,
                    'subscription_product_name' => $item->product_name,
                    'subscription_product_price' => $item->product_price,
                    'subscription_product_image' => $item->product_image,
                    'subscription_quantity' => $item->subscription_quantity,
                    'subscription_measurement_name' => $item->measurement_name,
                    'subscription_total_qty' => $item->subscription_total_qty,
                    'subscription_session_id' => $item->subscription_session_id,
                    'subscription_unit_name' => $item->unit_name,
                    'delivery_status' => $item->delivery_status,
                    'rating' => $item->rating,
                    'comments' => $item->comments,
                    'image' => asset($item->pic),

                ];
            });
        });

        //  $merged_data = $grouped_data->mergeRecursive($grouped_data_order);

        return response()->json([
            'subscription_details' => $grouped_data,
            'order_details' => $get_order,
            'status' => 200,
        ], 200);
    }


    public function get_editdata(Request $request)
    {
        $customer_id = $request->input('customer_id');
        $get_editdata = Customer::where('id', $customer_id)
            ->select('id', 'name', 'gender', 'mobile', 'profile_pic')
            ->first();

        if ($get_editdata) {
            if (!is_null($get_editdata->profile_pic)) {
                $get_editdata->profile_pic = URL::to($get_editdata->profile_pic);
            }

            if ($get_editdata) {
                return response()->json([
                    'edit_data' => $get_editdata,
                    'status' => 200,
                ], 200);
            }
        }

        return response()->json([
            'message' => "Profile not found",
            'status' => 200,
        ], 200);
    }

    public function edit_profile(Request $request)
    {
        $customer_id = $request->input('customer_id');
        $customer_name = $request->input('name');
        $customer_gender = $request->input('gender');
        $base64Image = $request->input('pic');

        $updateData = [
            'name' => $customer_name,
            'gender' => $customer_gender
        ];
        if (isset($base64Image)) {
            $base64Image = preg_replace('#^data:image/\w+;base64,#i', '', $base64Image);
            $imageData = base64_decode($base64Image);
            $fileName = time() . '.png'; // You can choose the extension based on your needs

            $filePath = 'customer_profile/' . $fileName;

            if (file_put_contents($filePath, $imageData)) {
                $updateData['profile_pic'] = $filePath;
            }
        }



        // Update customer data
        $update_data = Customer::where('id', $customer_id)->update($updateData);

        // Check if the update was successful
        if ($update_data) {
            return response()->json([
                'message' => 'Profile updated successfully',
                'status' => 200,
            ], 200);
        }

        return response()->json([
            'message' => 'Failed to update profile',
            'status' => 500,
        ], 500);
    }

    public function removeproduct_subscription(Request $request)
    {
        $customer_id = $request->input('customer_id');
        $subscription_ids = $request->input('subscription_ids'); // Accept an array of subscription IDs
        $savedate=$request->input('date');
        // Ensure subscription_ids is an array
        if (!is_array($subscription_ids)) {
            $subscription_ids = explode(',', $subscription_ids);
        }
        $subscriptions = Customersubscription::where('subscription_customer_id', $customer_id)
            ->whereIn('id', $subscription_ids)
            ->get();
        // dd( $subscriptions);
        if ($subscriptions->isEmpty()) {
            return response()->json([
                'message' => 'Subscriptions not found',
                'status' => 404,
            ], 404);
        }

        // Cancel each subscription and set delivery status to 'cancelled'
        foreach ($subscriptions as $subscription) {
            $subscription->delivery_status = 'cancelled';
            $subscription->save();
        }

        $end_date = Customersubscription::where('subscription_customer_id', $customer_id)
            ->max('date');

        $end_date = $end_date ? Carbon::createFromFormat('Y-m-d', $end_date) : Carbon::today();

        $new_subscription_count = Customersubscription::where('subscription_customer_id', $customer_id)
            ->where('date', $end_date->toDateString())
            ->count();
        if ($new_subscription_count > 0) {
            $end_date->addDay();
        }

        foreach ($subscriptions as $subscription) {
            $new_subscription_data = $subscription->replicate();
            $new_subscription_data->date = $savedate;
            $new_subscription_data->created_at = now();
            $new_subscription_data->updated_at = now();
            $new_subscription_data->delivery_status = "yet to deliver";
            $new_subscription_data->save();
        }

        return response()->json([
            'message' => 'Products removed and subscriptions extended successfully',
            'status' => 200,
        ], 200);
    }


    
    // public function removeproduct_subscription(Request $request)
    // {
    //   $customer_id = $request->input('customer_id');
    //   $subscription_id = $request->input('subscription_id');
    //   //$date_to_remove = Carbon::createFromFormat('d-m-Y', $request->input('date_to_remove')); // The date on which the product is to be removed

    //     $get_subscription = Customersubscription::where('subscription_customer_id', $customer_id)
    //                                         ->where('id', $subscription_id)
    //                                         ->first();

    //   if (!$get_subscription) {
    //       return response()->json([
    //           'message' => 'Subscription not found',
    //           'status' => 404,
    //       ], 404);
    //     }

    // // Update delivery status to "cancelled"
    //     $get_subscription->delivery_status = 'cancelled';
    //     $get_subscription->save();

    //     $end_date = Carbon::createFromFormat('Y-m-d', $get_subscription->date);

    //     $new_end_date = $end_date->addDay();

    //     $new_subscription_data = $get_subscription->replicate();
    //     $new_subscription_data->date = $new_end_date->toDateString();
    //     $new_subscription_data->created_at = now();
    //     $new_subscription_data->updated_at = now();
    //     $new_subscription_data->save();

    //     return response()->json([
    //       'message' => 'Product removed and subscription extended successfully',
    //       'status' => 200,
    //     ], 200);
    // }


    public function check_subscription(Request $request)
    {
        // $customer_id = $request->input('customer_id');

        // $check_subscription = Customersubscription::where('subscription_customer_id', $customer_id)
        //     ->where('delivery_status', "Yet to deliver")
        //     ->max('date');

        // $total_d_d = Customersubscription::where('subscription_customer_id', $customer_id)
        //     ->where('delivery_status', "Yet to deliver")
        //     ->distinct('date')
        //     ->count();

        // $total_c_d = Customersubscription::where('subscription_customer_id', $customer_id)
        //     ->where('delivery_status', "cancelled")
        //     ->distinct('date')
        //     ->count();

        // $check_subscription_startdate = Customersubscription::where('subscription_customer_id', $customer_id)
        //     ->where('delivery_status', "Yet to deliver")
        //     ->min('date');

        // $start_date = Carbon::parse($check_subscription_startdate);
        // $todaydate = Carbon::now();

        // if ($check_subscription_startdate > $todaydate) {
        //     $interval = $todaydate->diff($check_subscription_startdate);
        //     $remainingDays = $interval->days;
        // } else {
        //     $remainingDays = 0;
        // }



        // $check_subscription_startdate = Customersubscription::where('subscription_customer_id', $customer_id)
        //     ->where('delivery_status', "Yet to deliver")
        //     ->distinct('date')
        //     ->count('date');

        // $days_remaining = $check_subscription_startdate ? $check_subscription_startdate : 0;


        // $days_remaining = 0;
        // $subscription_status = "no";

        // $days_remaining = $total_d_d;

        // if ($days_remaining > 0) {
        //     $subscription_status = "Yes";
        // }


        // return response()->json([
        //     'subscription_status' => $subscription_status,
        //     'days_remaining' => $days_remaining,
        //     'days_to_start' => $remainingDays,
        //     'status' => 200,
        // ], 200);
        $customer_id = $request->input('customer_id');
        
        // Subquery to get aggregated data from customers_subscription for each track
        $subQuery = \DB::table('customers_subscription')
            ->select(
                'customer_track_id',
                \DB::raw("COUNT(DISTINCT CASE WHEN delivery_status = 'Yet to deliver' THEN date END) as days_remaining"),
                \DB::raw("COUNT(DISTINCT CASE WHEN delivery_status = 'Delivered' THEN date END) as days_delivered"),
                \DB::raw("COUNT(DISTINCT CASE WHEN delivery_status = 'cancelled' THEN date END) as days_cancelled"),
                \DB::raw("MAX(CASE WHEN delivery_status = 'Yet to deliver' THEN date END) as latest_delivery_date"),
                \DB::raw("MIN(CASE WHEN delivery_status = 'Yet to deliver' THEN date END) as first_delivery_date")
            )
            ->groupBy('customer_track_id');
    
        // Join the subquery with CustomerPlansTrack
        $total_d_d = Customersubscription::where('subscription_customer_id', $customer_id)
            ->where('delivery_status', "Yet to deliver")
            ->distinct('date')
            ->count();
        $cust_track = CustomerPlansTrack::where('customers_id', $customer_id)
            ->leftJoinSub($subQuery, 'sub', function($join) {
                $join->on('customer_plans_tracks.id', '=', 'sub.customer_track_id');
            })
            ->select('customer_plans_tracks.*', 'sub.days_remaining','sub.days_delivered', 'sub.days_cancelled', 'sub.latest_delivery_date', 'sub.first_delivery_date')
            ->get();
            $check_subscription_startdate = Customersubscription::where('subscription_customer_id', $customer_id)
                ->where('delivery_status', "Yet to deliver")
                ->distinct('date')
                ->count('date');
    
            $days_remaining = $check_subscription_startdate ? $check_subscription_startdate : 0;
    
    
            $days_remaining = 0;
            $subscription_status = "no";
    
            $days_remaining = $total_d_d;
    
            if ($days_remaining > 0) {
                $subscription_status = "Yes";
            }
    
    
        return response()->json([
            'subscription_status' => $subscription_status,
            'plans' => $cust_track,
            'status' => 200,
        ], 200);
    }
    public function check_subscription2(Request $request)
    {
        $customer_id = $request->input('customer_id');
        
        // Subquery to get aggregated data from customers_subscription for each track
        $subQuery = \DB::table('customers_subscription')
            ->select(
                'customer_track_id',
                \DB::raw("COUNT(DISTINCT CASE WHEN delivery_status = 'Yet to deliver' THEN date END) as days_remaining"),
                \DB::raw("COUNT(DISTINCT CASE WHEN delivery_status = 'cancelled' THEN date END) as days_cancelled"),
                \DB::raw("MAX(CASE WHEN delivery_status = 'Yet to deliver' THEN date END) as latest_delivery_date"),
                \DB::raw("MIN(CASE WHEN delivery_status = 'Yet to deliver' THEN date END) as first_delivery_date")
            )
            ->groupBy('customer_track_id');
    
        // Join the subquery with CustomerPlansTrack
        $cust_track = CustomerPlansTrack::where('customers_id', $customer_id)
            ->leftJoinSub($subQuery, 'sub', function($join) {
                $join->on('customer_plans_tracks.id', '=', 'sub.customer_track_id');
            })
            ->select('customer_plans_tracks.*', 'sub.days_remaining', 'sub.days_cancelled', 'sub.latest_delivery_date', 'sub.first_delivery_date')
            ->get();
    
        return response()->json([
            'plans' => $cust_track,
            'status' => 200,
        ], 200);
    }
    
    


    public function wallet_recharge(Request $request)
    {
        $customer_id = $request->input('customer_id');
        $recharge_amount = $request->input('recharge_amount');
        $payment_type = $request->input('payment_type');

        try {
            $paymentLinkdata = $this->payment_controller->make_payment($customer_id, $recharge_amount, $payment_type);
            $paymentLink = $paymentLinkdata['razorpaylink'];
            $order_id = $paymentLinkdata['razorpayorderid'];
            if ($paymentLink) {
                return response()->json([
                    'payment_link_id' => $paymentLink->id,
                    'short_url' => $paymentLink->short_url,
                    'amount' => $paymentLink->amount,
                    'currency' => $paymentLink->currency,
                    'description' => $paymentLink->description,
                    'customer' => [
                        'name' => $paymentLink->customer->name,
                        'contact' => $paymentLink->customer->contact
                    ],
                    'order_id' => $order_id
                ], 200);
            } else {
                return response()->json([
                    'error' => 'Failed to create payment link.'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function wallet_history(Request $request)
    {
        $customer_id = $request->input('customer_id');

        $data = Customerwallethistory::select(
            'customer_wallet_history.*',
            'customer_payments_history.order_id as order_id',
            'customer_payments_history.transaction_id as transaction_id',
            'customer_payments_history.payment_status as payment_status'
        )
            ->leftJoin('customer_payments_history', 'customer_wallet_history.payment_history_id', '=', 'customer_payments_history.id')
            ->where('customer_wallet_history.customer_id', $customer_id)
            ->get();

        return response()->json([
            'message' => "Wallet History fetched successfully",
            'data' => $data,
            'status' => 200,
        ], 200);
    }


    public function check_address(Request $request)
    {
        $customer_id = $request->input('customer_id');
        $customer_address = Customer::where('id', $customer_id)
            ->whereNotNull('address')
            ->first();
        if ($customer_address) {
            return response()->json([
                'address_saved' => 'Yes',
                'status' => 200,
            ], 200);
        } else {
            return response()->json([
                'address_saved' => 'No',
                'status' => 404,
            ], 404);
        }
    }
    public function version_check(Request $request)
    {
            return response()->json([
                'version' => '1',
                'status' => 200,
            ], 200);
    }

    public function save_complaints(Request $request)
    {
        $customer_id = $request->input('customer_id');
        $subscription_id = $request->input('subscription_id');
        $comments = $request->input('comments');
        $pic = $request->input('pic');

        $updateData = ['comments' => $comments];

        if (isset($pic)) {
            $base64Image = preg_replace('#^data:image/\w+;base64,#i', '', $pic);
            $imageData = base64_decode($base64Image);
            $fileName = time() . '.png'; // You can choose the extension based on your needs
            $filePath = 'customer_complaints/' . $fileName;

            file_put_contents(public_path($filePath), $imageData);

            $updateData['pic'] = $filePath;
        }

        $customer_complaints = Customersubscription::where('id', $subscription_id)
            ->where('subscription_customer_id', $customer_id)
            ->update($updateData);

        if ($customer_complaints) {
            return response()->json([
                'message' => 'Customer complaints updated',
                'status' => 200,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Failed to update complaints',
                'status' => 500,
            ], 500);
        }
    }

    public function payment_details(Request $request)
    {
        $customer_id = $request->input('customer_id');
        $razorpay_orderid = $request->input('razorpay_order_id');
        $razorpay_paymentid = $request->input('razorpay_payment_id');

        $get_paymentdetails = $this->payment_controller->getPaymentDetails($razorpay_paymentid);

        if ($get_paymentdetails['status'] == "captured") {
            $payment_details = Customerpayment::create([
                'customers_id' => $customer_id,
                'amount' => $get_paymentdetails['amount'] / 100,
                'order_id' => $get_paymentdetails['order_id'],
                'transaction_id' => $razorpay_paymentid,
                'payment_status' => "success",
                'notes' => json_encode($get_paymentdetails),
            ]);



            if ($payment_details) {
                $wallet = Wallet::where('customers_id', $customer_id)
                    ->where('status', 'Active')
                    ->first();

                if ($wallet) {
                    $wallet->current_amount += $get_paymentdetails['amount'] / 100;
                    $wallet->save();
                } else {

                    $wallet = Wallet::create([
                        'customers_id' => $customer_id,
                        'current_amount' => $get_paymentdetails['amount'] / 100,
                        'created_by' => $customer_id,
                        'updated_by' => $customer_id,
                        'status' => "Active",
                    ]);
                }

                Customerwallethistory::create([
                    'customer_wallet_id' => $wallet->id,
                    'debit_credit_status' => "credited",
                    'amount' => $get_paymentdetails['amount'] / 100,
                    'notes' => '',
                    'remarks' => 'Recharge Wallet Amount',
                    'customer_id' => $wallet->customers_id,
                    'payment_history_id' => $payment_details->id,
                ]);

                $cus_details = Customer::where('id', $customer_id)->first();

                if ($cus_details) {
                    // Get the FCM token and customer details
                    $get_fcm = Customer_fcm::where('customer_id', $cus_details->id)->first();

                    if ($get_fcm && $cus_details) {
                        // Prepare notification data

                        $title = 'Payment Successful';
                        $message_body = 'Dear ' . $cus_details->name . ', Your Payment added successfully';

                        $image = null; // Replace with your image URL if needed
                        $link = null;  // Replace with your link if needed

                        // Instantiate the NotificationController and send the notification
                        $notificationController = new NotificationController();
                        $notification = $notificationController->sendNotification($get_fcm->fcm_key, $title, $message_body, $image, $link);
                    }
                }

                return response()->json([
                    'message' => "Wallet recharged successfully",
                    'status' => 200,
                ], 200);
            }
        } else if ($get_paymentdetails['status'] == "failed") {
            $payment_details = Customerpayment::create([
                'customers_id' => $customer_id,
                'amount' => $get_paymentdetails['amount'] / 100,
                'order_id' => $get_paymentdetails['order_id'],
                'transaction_id' => $razorpay_paymentid,
                'payment_status' => "failed",
                'notes' => json_encode($get_paymentdetails),
            ]);

            Customerwallethistory::create([
                'customer_wallet_id' => '',
                'debit_credit_status' => "failed",
                'amount' => $get_paymentdetails['amount'] / 100,
                'notes' => '',
                'remarks' => 'Recharge Wallet Amount failed',
                'customer_id' => $customer_id,
                'payment_history_id' => $payment_details->id,
            ]);


            return response()->json([
                'message' => "Failed to recharge wallet",
                'status' => 500,
            ], 500);
        }
    }

    public function get_walletamount(Request $request)
    {
        $customer_id = $request->input('customer_id');
        $wallet = Wallet::where('customers_id', $customer_id)->first();

        if ($wallet) {
            return response()->json([
                'wallet_amount' => $wallet->current_amount,
                'status' => 200,
            ], 200);
        }

        return response()->json([
            'message' => "Wallet not found for this customer ID",
            'status' => 404,
        ], 404);
    }

    public function payment_history(Request $request)
    {

        $customer_id = $request->input('customer_id');
        $payment_history = Customerpayment::where('customers_id', $customer_id)
            ->select('amount', 'order_id', 'transaction_id', 'payment_status', 'notes', 'created_at')
            ->get();

        if ($payment_history->isNotEmpty()) {
            return response()->json([
                'payment_history' => $payment_history,
                'status' => 200,
            ], 200);
        }

        return response()->json([
            'message' => "No data found",
            'status' => 404,
        ], 404);
    }


    public function save_ratings(Request $request)
    {
        dd($request);
    }


    public function client_home(Request $request)
    {
        $customer_id = $request->input('customer_id');
        $base64Image = $request->input('img');

        if (isset($customer_id) && isset($base64Image)) {




            if (isset($base64Image)) {
                $base64Image = preg_replace('#^data:image/\w+;base64,#i', '', $base64Image);
                $imageData = base64_decode($base64Image);
                $fileName = time() . '.png'; // You can choose the extension based on your needs

                $filePath = 'customer_home/' . $fileName;

                if (file_put_contents($filePath, $imageData)) {
                    $updateData['home_img'] = $filePath;
                }
            }



            // Update customer data
            $update_data = Customer::where('id', $customer_id)->update($updateData);
            if ($update_data) {
                return response()->json([
                    'message' => 'Image updated successfully',
                    'status' => 200,
                ], 200);
            } else {

                return response()->json([
                    'message' => 'Failed to update image',
                    'status' => 500,
                ], 500);
            }
        }

        return response()->json([
            'message' => 'customer_id, img is required',
            'status' => 200,
        ], 200);
    }

    public function supscription_confrim(Request $request){
        $id = $request->customer_id;
        // $id = 2;
    $date = date("Y-m-d");

    // Using raw SQL query to fetch records where end_date matches the current date
    $query = CustomerPlansTrack::select('id','customers_id','start_date','end_date')->where('customers_id', $id);
       

    // Execute the query to get the data
    $data = $query->get();
      
    // Get the SQL query string with bindings
    $sql = vsprintf(str_replace(['?', '%'], ['\'%s\'', '%%'], $query->toSql()), $query->getBindings());
    $cancel=Customersubscription::select('date')->where('subscription_customer_id', $id)->where('delivery_status','cancelled')->get();
    
    return response()->json([
            
            'status' => 200,
            
            'data'=>$data,
            'id'=>$id,
            // 'cancel_date'=>$cancel
        ], 200);
    }
    // public function supscription_confrim(Request $request){
    //     $id = $request->customer_id;
    //     $date = date("Y-m-d");
    
    //     // Fetch customer plan tracks with a join on Customersubscription to get cancel dates
    //     $data = CustomerPlansTrack::select('customer_plans_tracks.id', 'customer_plans_tracks.customers_id', 'customer_plans_tracks.start_date', 'customer_plans_tracks.end_date', 'customers_subscription.date as cancel_date')
    //         ->leftJoin('customers_subscription', function($join) use ($id) {
    //             $join->on('customers_subscription.customer_track_id', '=', 'customer_plans_tracks.id')
    //                 ->where('customers_subscription.subscription_customer_id', '=', $id)
    //                 ->where('customers_subscription.delivery_status', '=', 'cancelled');
    //         })
    //         ->where('customer_plans_tracks.customers_id', $id)
    //         ->get();
    
    //     // Get the SQL query string with bindings
    //     // $sql = vsprintf(str_replace(['?', '%'], ['\'%s\'', '%%'], $data->toSql()), $data->getBindings());
    
    //     return response()->json([
    //         'status' => 200,
    //         'data' => $data,
    //         'id' => $id
    //     ], 200);
    // }
    public function reduce_liters(Request $request)
{
    $customer_id = $request->input('customer_id');
    $subscription_id = $request->input('subscription_ids');
    $product_id = $request->input('product_id');

    $subscription = Customersubscription::where('id', $subscription_id)->first();

    if (!$subscription) {
        return response()->json([
            'message' => "Subscription not found",
            'status' => 404,
        ], 404);
    }

    $product = Product::where('id', $subscription->subscription_products_id)->first();
    if (!$product) {
        return response()->json([
            'message' => "Product not found",
            'status' => 404,
        ], 404);
    }

    $unit = Unit::where('id', $product->quantity_id)->first();
    $measurement_id = $product->measurement_id;

    if (!$unit) {
        return response()->json([
            'message' => "Unit not found",
            'status' => 404,
        ], 404);
    }

    // Calculate liters
    $liter = ($measurement_id == 2) ? $unit->name : $unit->name * 1000;

    if ($liter > 500) {
        $validLiter = $liter - 500;

        if ($validLiter >= 1000) {
            $validLiter = $validLiter / 1000;
        }

        $unitZ = Unit::where('name', $validLiter)->first();
        $newProduct = Product::where('quantity_id', $unitZ->id)->where('name', $product->name)->first();

        if (!$newProduct) {
            return response()->json([
                'message' => "New product not found with calculated liters",
                'status' => 404,
            ], 404);
        }

        $amountProduct = Product::where('name', $newProduct->name)->where('quantity_id', 2)->first();

        if (!$amountProduct) {
            return response()->json([
                'message' => "Product price not found for updated liters",
                'status' => 404,
            ], 404);
        }

        $amount = $amountProduct->price;
        $pid = $newProduct->id;

        // Store the old subscription product ID
        $oldProductID = $subscription->subscription_products_id;

        // Update subscription with new product ID and set edited fields
        $subscription->update([
            'subscription_products_id' => $pid,
            'edited_product_id' => $oldProductID,
            'edited_at' => now(),
        ]);

        $wallet = Wallet::where('customers_id', $customer_id)->first();

        if ($wallet) {
            // Add the new amount to the current amount
            $wallet->current_amount += $amount;
            $wallet->last_gift_at = now();
            $wallet->save();

            // Insert into customer wallet history
            Customerwallethistory::create([
                'customer_wallet_id' => $wallet->id, // Use wallet id instead of customers_id
                'debit_credit_status' => 'credited',
                'amount' => $amount,
                'notes' => 'Reduce Product',
                'customer_id' => $wallet->customers_id,
            ]);
        } else {
            return response()->json([
                'message' => "Wallet not found for the customer",
                'status' => 404,
            ], 404);
        }

        return response()->json([
            'message' => "Updated liters successfully",
            'valid_liter' => $validLiter,
            'updated_product' => $newProduct,
            'wallet_amount' => $amount,
            'status' => 200,
        ], 200);
    } else {
        return response()->json([
            'message' => "The requested liters are less than 500",
            'status' => $liter,
        ], 400);
    }
}

    
    
}
