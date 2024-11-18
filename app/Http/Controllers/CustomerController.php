<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Delivery_lines;
use App\Models\Pincode;
use App\Models\Customersubscription;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\Area;
use Carbon\Carbon;
use App\Models\citytable;
use Illuminate\Support\Str;

class CustomerController extends Controller
{


    public function list()
    {
        $user_list = Customer::select(
            'customers.id',
            'customers.name',
            'customers.mobile',
            'customers.loc_status',
            'customers.area_id',
            'customers.status',
            'customers.type',
            'delivery_lines.name AS line_name',
            'pincodes.pincode AS pincode_name',
            'areas.name AS area_name',



        )
            ->leftJoin('delivery_lines', 'customers.deliverylines_id', '=', 'delivery_lines.id')
            ->leftJoin('pincodes', 'customers.pincode_id', '=', 'pincodes.id')
            ->leftJoin('areas', 'customers.area_id', '=', 'areas.id')

            ->orderBy('customers.id', 'DESC')->get();

        // dd($user_list);

        return view('customer/index')->with(compact('user_list'));
    }

    /* public function list()
    {
        $user_list = Customer::orderBy('id', 'desc')->get()->toArray();
        return view('customer/index')->with(compact('user_list'));
    } */



    public function add()
    {
        $area = Area::get();
        $pincode = Pincode::get();
        $delivery = Delivery_lines::get()->toArray();
        $city=citytable::where('del',0)->where('status','Y')->get();
        return view(
            'customer/add_form',
            ['area' => $area, 'pincode' => $pincode, 'delivery' => $delivery,'city'=>$city,'user' => [],]
        );
    }

    // public function customer_create(request $request)
    // {
    // //    dd($request);
    //     if($request->id){
    //         $request->validate([
    //             'name' => 'required|string|max:30',
    //             'gender' => 'required',
    //             'mobile' => 'required|numeric|digits:10|unique:customers,mobile,'.$request->id,
    //             'address' => 'required|string|max:255',
    //             'pincode_id' => 'required',
    //             'area_id' => 'required',
    //             'latlon' => 'required|string',
    //             // 'sub_status' => 'required',
    //             'loc_status' => 'required',
    //             'status' => 'required',
    //             // 'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    //             'home_img' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    //             'city'=>'required',
                
    //         ]);
    //     }else{
    //         $request->validate([
    //             'name' => 'required|string|max:30',
    //             'gender' => 'required',
    //             'mobile' => 'required|numeric|digits:10|unique:customers,mobile',
    //             'address' => 'required|string|max:255',
    //             'pincode_id' => 'required',
    //             'area_id' => 'required',
    //             'latlon' => 'required|string',
    //             // 'sub_status' => 'required',
    //             'loc_status' => 'required',
    //             'status' => 'required',
    //             // 'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    //             'home_img' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    //             'city'=>'required',
    //         ]);
    //     }


    //     $addressParts = [
    //         $request->door_no,
    //         $request->floor_no,
    //         $request->flat_no,
    //         $request->street,
    //         $request->land_mark,
    //     ];
        
    //     // Filter out any null or empty values
    //     $filteredAddressParts = array_filter($addressParts, function ($value) {
    //         return !is_null($value) && $value !== '';
    //     });
        
    //     // Join the filtered parts with a comma
    //     $add = implode(',', $filteredAddressParts);
        
    //     // Create or update the customer
    //     $customer = $request->id ? Customer::find($request->id) : new Customer;

    //     $customer->name = $request->name;
    //     $customer->gender = $request->gender;
    //     $customer->mobile = $request->mobile;
    //     $customer->address = $add;
    //     $customer->deliverylines_id = $request->deliverylines_id;
    //     $customer->pincode_id = $request->pincode_id;
    //     $customer->area_id = $request->area_id;
    //     $customer->latlon = $request->latlon;
    //     $customer->door_no = $request->door_no;
    //     $customer->street = $request->street;
    //     $customer->land_mark = $request->land_mark;
    //     $customer->flat_no = $request->flat_no;
    //     $customer->city = $request->city;
       

    //     // $customer->sub_status = $request->sub_status;
    //     $customer->loc_status = $request->loc_status;
    //     $customer->status = $request->status;


    //     if($request->id){

    //         $today = date('Y/m/d');

    //         $sub = Customersubscription::where('subscription_customer_id', $request->id)
    //             ->where('date', '>=', $today)
    //             ->get();

    //         if ($sub) {

    //             foreach ($sub as $subscription) {
    //                 $subscription->pincode = $request->pincode_id;
    //                 $subscription->area = $request->area_id;
    //                 $subscription->cus_lat_lon = $request->latlon;
    //                 $subscription->save();
    //             }
    //         }



    //         $order = Order::where('customer_id', $request->id)
    //             ->where('delivery_date', '>=', $today)
    //             ->get();

    //         if ($order) {
    //             foreach ($order as $odr) {
    //                 $odr->pincode =  $request->pincode_id;
    //                 $odr->area = $request->area_id;
    //                 $odr->cus_lat_lon = $request->latlon;
    //                 $odr->save();
    //             }
    //         }
    //     }
    //     // Handle file uploads (Profile picture and Home image)
    //     if ($request->hasFile('profile_pic')) {
    //         $profilePicPath = $request->file('profile_pic')->store('profile_pics', 'public');
    //         $customer->profile_pic = $profilePicPath;
    //     }

    //     if ($request->hasFile('home_img')) {
    //         $file = $request->file('home_img');
    //         $fileName = time() . '_' . $file->getClientOriginalName(); // Generate a unique file name
    //         $filePath = 'customer_home/' . $fileName;

    //         // Move the uploaded file to the desired folder
    //         $file->move(public_path('customer_home'), $fileName); // Moves the file to the public/customer_home folder

    //         $customer['home_img'] = $filePath; // Save the path in the database
    //     }

    //     // Save customer
    //     $customer->save();

    //     return redirect()->route('list_customer')->with('success', 'Customer saved successfully.');
    // }

    public function customer_create(request $request)
    {
        // Check if the request is for updating an existing customer
        if ($request->id) {
            $request->validate([
                'name' => 'required|string|max:30',
                'gender' => 'required',
                'mobile' => 'required|numeric|digits:10|unique:customers,mobile,' . $request->id,
                'address' => 'required|string|max:255',
                'pincode_id' => 'required',
                'area_id' => 'required',
                'latlon' => 'required|string',
                'loc_status' => 'required',
                'status' => 'required',
                'home_img' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'city' => 'required',
            ]);
        } else {
            $request->validate([
                'name' => 'required|string|max:30',
                'gender' => 'required',
                'mobile' => 'required|numeric|digits:10|unique:customers,mobile',
                'address' => 'required|string|max:255',
                'pincode_id' => 'required',
                'area_id' => 'required',
                'latlon' => 'required|string',
                'loc_status' => 'required',
                'status' => 'required',
                'home_img' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'city' => 'required',
            ]);
        }
    
        // Combine address parts
        $addressParts = [
            $request->door_no,
            $request->floor_no,
            $request->flat_no,
            $request->street,
            $request->land_mark,
        ];
        $filteredAddressParts = array_filter($addressParts, function ($value) {
            return !is_null($value) && $value !== '';
        });
        $add = implode(',', $filteredAddressParts);
    
        // Find existing or create a new customer
        $customer = $request->id ? Customer::find($request->id) : new Customer;
    
        $customer->name = $request->name;
        $customer->gender = $request->gender;
        $customer->mobile = $request->mobile;
        $customer->address = $add;
        $customer->pincode_id = $request->pincode_id;
        $customer->area_id = $request->area_id;
        $customer->latlon = $request->latlon;
        $customer->door_no = $request->door_no;
        $customer->street = $request->street;
        $customer->land_mark = $request->land_mark;
        $customer->flat_no = $request->flat_no;
        $customer->city = $request->city;
        $customer->loc_status = $request->loc_status;
        $customer->status = $request->status;
    
        // Update fields specific to edits
        if ($request->id) {
            $customer->edited_at = now();
            $customer->edited_by = auth()->id(); // Assuming user is logged in
            $customer->deliverylines_id=null;
            $customer->temp_deliverylines_id = $request->deliverylines_id;
        } else {
            $customer->deliverylines_id = $request->deliverylines_id;
        }
    
        // Handle file uploads
        if ($request->hasFile('profile_pic')) {
            $profilePicPath = $request->file('profile_pic')->store('profile_pics', 'public');
            $customer->profile_pic = $profilePicPath;
        }
    
        if ($request->hasFile('home_img')) {
            $file = $request->file('home_img');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = 'customer_home/' . $fileName;
            $file->move(public_path('customer_home'), $fileName);
            $customer->home_img = $filePath;
        }
    
        // Save customer
        $customer->save();
    
        // Update related subscriptions and orders if necessary
        if ($request->id) {
            $today = date('Y/m/d');
    
            $sub = Customersubscription::where('subscription_customer_id', $request->id)
                ->where('date', '>=', $today)
                ->get();
    
            if ($sub) {
                foreach ($sub as $subscription) {
                    $subscription->pincode = $request->pincode_id;
                    $subscription->area = $request->area_id;
                    $subscription->cus_lat_lon = $request->latlon;
                    $subscription->save();
                }
            }
    
            $order = Order::where('customer_id', $request->id)
                ->where('delivery_date', '>=', $today)
                ->get();
    
            if ($order) {
                foreach ($order as $odr) {
                    $odr->pincode = $request->pincode_id;
                    $odr->area = $request->area_id;
                    $odr->cus_lat_lon = $request->latlon;
                    $odr->save();
                }
            }
        }
    
        return redirect()->route('list_customer')->with('success', 'Customer saved successfully.');
    }
    
    public function getcustomer(Request $request)
    {
        // Fetch concatenated customer IDs
        $result = DB::table('customers_subscription')
            ->select(DB::raw("GROUP_CONCAT(DISTINCT subscription_customer_id) as customer_ids"))
            ->where('delivery_status', '=', 'yet to deliver')
            ->first();

        // Check if $result is not null and extract customer IDs
        if ($result && $result->customer_ids) {
            $customerIds = explode(',', $result->customer_ids);

            // Retrieve customers based on the extracted IDs
            $cus_list = Customer::where('area_id', $request->area)->whereIn('id', $customerIds)->get();

            foreach ($cus_list as $d) {
                $area = Area::where('id', $d->area_id)->first();
                $pincode = Pincode::where('id', $d->pincode_id)->first();

                $d->area = $area->name;
                $d->pincode = $pincode->pincode;
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Customers retrieved successfully.',
                'data' => $cus_list,
            ], 200);
        } else {
            // Handle case where no customers are found
            return response()->json([
                'status' => 'error',
                'message' => 'No customers found with the specified criteria.',
                'data' => [],
            ], 404);
        }
    }


    public function store(Request $request)
    {
        $user = auth()->user();
        $password = Hash::make($request->input('password'));

        $data = $request->only([
            'name',
            'mobile',
            'aadhar_number',
            'status',
        ]);
        $users = new Customer(array_merge($data, [
            'password' => $password,
            'created_by' => $user->id,
            'updated_by' => 0
        ]));

        if ($users->save()) {
            return redirect('delivery-person')->with('success_message', 'User Created Sucessfully!..');
        } else {
            return redirect('delivery-person')->with('success_message', 'Something Wrong!..');
        }
    }

    public function update($id = null)
    {
        $area = Area::get();
        $pincode = Pincode::get();
        $delivery = Delivery_lines::get()->toArray();
        $city=citytable::where('del',0)->where('status','Y')->get();
        $user = Customer::find($id);

        // dd($user);
        return view('customer/add_form')->with(compact('user', 'pincode', 'delivery', 'area','city'));
    }

    public function update_store(Request $request)
    {
        $user = auth()->user();

        $data = $request->only([
            'id',
            'name',
            'gender',
            'mobile',
            'address',
            'pincode_id',
            'latlon',
            'deliverylines_id',
            'address_status',
            'status',
            'area_id'
        ]);
        /*  $media = new Delivery_lines(array_merge($data, [
             'created_by' => $user->id,
             'updated_by' => $user->id
         ])); */

        $data = Customer::find($request->id);

        if ($data->update($request->all())) {
            return redirect('list_customer')->with('success_message', 'Customer Updated Sucessfully!..');
        } else {
            return redirect('list_customer')->with('success_message', 'Something Wrong!..');
        }
    }
    public function new_customer(Request $request)
{
    // Check if start_date and end_date are present in the request
    $start_date = $request->from_date ?? Carbon::now()->subDays(7)->format('Y-m-d');
    $end_date = $request->to_date ?? Carbon::now()->format('Y-m-d');

    // Convert the start_date and end_date to Carbon instances
    $start_date = Carbon::createFromFormat('Y-m-d', $start_date)->startOfDay();
    $end_date = Carbon::createFromFormat('Y-m-d', $end_date)->endOfDay();

    // Query with date conditions
    

$user_list_query = Customer::select(
        'customers.id',
        'customers.name',
        'customers.mobile',
        'customers.loc_status',
        'customers.area_id',
        'customers.status',
        'delivery_lines.name AS line_name',
        'pincodes.pincode AS pincode_name',
        'areas.name AS area_name',
        DB::raw("CASE 
                    WHEN EXISTS (
                        SELECT 1 
                        FROM customers_subscription 
                        WHERE customers_subscription.subscription_customer_id = customers.id
                    ) 
                    THEN 'Yes' 
                    ELSE 'No' 
                 END AS subscription")
    )
    ->leftJoin('delivery_lines', 'customers.deliverylines_id', '=', 'delivery_lines.id')
    ->leftJoin('pincodes', 'customers.pincode_id', '=', 'pincodes.id')
    ->leftJoin('areas', 'customers.area_id', '=', 'areas.id')
    ->whereBetween('customers.created_at', [$start_date, $end_date])
    ->orderBy('customers.id', 'DESC');


    // Output raw SQL with bindings
    $rawSql = Str::replaceArray(
        '?',
        $user_list_query->getBindings(),
        $user_list_query->toSql()
    );

    // Execute the query
    $user_list = $user_list_query->get();
// dd($user_list);
    // Return the view with the user list
    return view('customer/new_client')->with(compact('user_list'));
}
public function edited_customer()
{
    $user_list = Customer::select(
        'customers.id',
        'customers.name',
        'customers.mobile',
        'customers.loc_status',
        'customers.area_id',
        'customers.status',
        'delivery_lines.name AS line_name',
        'pincodes.pincode AS pincode_name',
        'areas.name AS area_name',



    )
        ->leftJoin('delivery_lines', 'customers.deliverylines_id', '=', 'delivery_lines.id')
        ->leftJoin('pincodes', 'customers.pincode_id', '=', 'pincodes.id')
        ->leftJoin('areas', 'customers.area_id', '=', 'areas.id')
        ->whereNotNull('edited_at')
        ->orderBy('customers.id', 'DESC')->get();

    // dd($user_list);

    return view('customer/edited')->with(compact('user_list'));
}
    

}
