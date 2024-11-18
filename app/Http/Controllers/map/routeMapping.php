<?php

namespace App\Http\Controllers\map;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Delivery_lines;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customersubscription;

class routeMapping extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(request $request)
    {
        $delivery_line = Delivery_lines::where('status', 'Active')->get();
        // $customer = Customer::whereNotNull('latlon')->where('status','Active')->get();
        $customer = Customer::select('customers.*', 'delivery_lines.color_code')
    ->leftJoin('delivery_lines', 'customers.deliverylines_id', '=', 'delivery_lines.id')
    ->whereNotNull('customers.latlon')
    ->where('customers.status', 'Active')
    ->get();
// dd($customer);
        $active = Customer::whereNotNull('latlon')->where('status','Active')->count();
        $inactive = Customer::whereNotNull('latlon')->where('status','Inactive')->count();

        // dd($customer);

        return view('map.add',['active'=>$active,'inactive'=>$inactive,'delivery_line'=>$delivery_line,'customer'=>$customer]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // "category_id" => "1"
        $id=$request->category_id;
        if($id !=""){
           
    $customer = Customer::select('customers.*', 'delivery_lines.color_code')
    ->leftJoin('delivery_lines', 'customers.deliverylines_id', '=', 'delivery_lines.id')
    ->whereNotNull('customers.latlon')
    ->where('customers.deliverylines_id',$id)
    ->where('customers.status', 'Active')
    ->get();
        }else{
            // $customer = Customer::whereNotNull('latlon')->where('status','Active')->where('deliverylines_id',$id)->get();
            $customer = Customer::select('customers.*', 'delivery_lines.color_code')
            ->leftJoin('delivery_lines', 'customers.deliverylines_id', '=', 'delivery_lines.id')
            ->whereNotNull('customers.latlon')
            ->where('customers.status', 'Active')
            ->get(); 
        }
        $delivery_line = Delivery_lines::where('status', 'Active')->get();
        // $customer = Customer::whereNotNull('latlon')->where('status','Active')->where('deliverylines_id',$id)->get();
        
        $active = Customer::whereNotNull('latlon')->where('status','Active')->count();
        $inactive = Customer::whereNotNull('latlon')->where('status','Inactive')->count();

        // dd($customer);

        return view('map.add',['active'=>$active,'inactive'=>$inactive,'delivery_line'=>$delivery_line,'customer'=>$customer]);
   
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request, string $id)
    {
       
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
