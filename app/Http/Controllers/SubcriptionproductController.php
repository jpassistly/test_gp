<?php

namespace App\Http\Controllers;

use App\Models\Subscriptionproduct;
use App\Models\Quantity;
use App\Models\Measurement;
use App\Models\ProductName;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;


class SubcriptionproductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
//
    public function list()
    {
        $product_list = Product::select(
            'products.id',
            'products.category_id',
            'products.name',
            'products.price',
            'products.status',
            'categories.name AS category_name',
            'units.name AS quantity_name',
             'measurements.name AS measurement_name'
        )
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('units', 'products.quantity_id', '=', 'units.id')
            ->join('measurements', 'products.measurement_id', '=', 'measurements.id')
            ->where('products.subscription', 'Y')
            ->orderBy( 'products.id', 'DESC')->get();

        return view('subscribe_product/index')->with(compact('product_list'));
    }

    public function add()
    {
        $quantity_list = Quantity::get()->toArray();
        $measurement_list = Measurement::get()->toArray();
        return view('subscribe_product/add_form')->with(compact('quantity_list','measurement_list'));
    }

    public function update($id=null)
    {
        $quantity_list = Quantity::get()->toArray();
        $measurement_list = Measurement::get()->toArray();
        $product_details = Subscriptionproduct::find($id);
        return view('subscribe_product/update_form')->with(compact('product_details','quantity_list','measurement_list'));

    }

     public function store(Request $request)
     {
            $user = auth()->user();

            if (request()->has('pic')) {
                $file = request()->file('pic');
                $fileName = time() . '.' . $file->getClientOriginalExtension();
                $filePath = public_path('/sproduct/');
                $file->move($filePath, $fileName);
            }

            $data = $request->only([
                'name', 'quantity_id','measurement_id','price','status'
            ]);
             $product_details = new Subscriptionproduct(array_merge($data, [
                 'pic' => "/sproduct/" . $fileName,
                 'created_by' => $user->id,
                 'updated_by' => 0
             ]));

             if ($product_details->save()) {
                    return redirect ('list_sproduct')->with('success_message','product Created Sucessfully!..');
             } else {
                    return redirect ('list_sproduct')->with('success_message','Something Wrong!..');
             }
     }

     public function update_store(Request $request)
     {
            $user = auth()->user();

            $product_details = Subscriptionproduct::find($request->id);

            if (request()->has('npic')) {
                $file = request()->file('npic');
                $fileName = time() . '.' . $file->getClientOriginalExtension();
                $filePath = public_path('/sproduct/');
                $file->move($filePath, $fileName);

                 $sfileName = trim("/sproduct/" . $fileName);
           }else{
                 $sfileName = $request->input('old_pic');
           }

            $product_details->name = $request->input('name');
            $product_details->quantity_id = $request->input('quantity_id');
            $product_details->measurement_id = $request->input('measurement_id');
            $product_details->price = $request->input('price');
            $product_details->status = $request->input('status');
            $product_details->pic = $sfileName;
            $product_details->created_by = $user->id;
            $product_details->updated_by = $user->id;

             if ($product_details->update()) {
                    return redirect ('list_sproduct')->with('success_message','Product Updated Sucessfully!..');
             } else {
                    return redirect ('list_sproduct')->with('success_message','Something Wrong!..');
             }
     }
}
