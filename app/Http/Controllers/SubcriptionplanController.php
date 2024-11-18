<?php

namespace App\Http\Controllers;

use App\Models\Subscriptionproduct;
use App\Models\Plan;
use App\Models\Quantity;
use App\Models\Subscriptionplan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;


class SubcriptionplanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list()
    {
        $plan_list = Subscriptionplan::select(
            'subcriptionplans.id',
            'subcriptionplans.product_id',
            'subcriptionplans.quantity_id',
            'subcriptionplans.price',
            'subcriptionplans.status',
            'subcriptionproducts.name AS product_name',
            'quantities.name AS quantity_name',
            'plans.name AS plan_name'
        )
            ->join('subcriptionproducts', 'subcriptionplans.product_id', '=', 'subcriptionproducts.id')
            ->join('quantities', 'subcriptionplans.quantity_id', '=', 'quantities.id')
            ->join('plans', 'subcriptionplans.plan_id', '=', 'plans.id')
            ->orderBy( 'subcriptionplans.id', 'DESC')->get();

        return view('subscribe_plan/index')->with(compact('plan_list'));
    }

    public function add()
    {
        $category_list = Subscriptionproduct::get()->toArray();
        return view('product/add_form')->with(compact('category_list'));
    }

    public function update($id=null)
    {
        $category_list = Subscriptionproduct::get()->toArray();
        $product_details = Subscriptionplan::find($id);
        return view('product/update_form')->with(compact('product_details','category_list'));

    }

     public function store(Request $request)
     {
            $user = auth()->user();

            if (request()->has('pic')) {
                $file = request()->file('pic');
                $fileName = time() . '.' . $file->getClientOriginalExtension();
                $filePath = public_path('/product/');
                $file->move($filePath, $fileName);
            }

            $data = $request->only([
                'category_id', 'name', 'quantity','description','status'
            ]);
             $product_details = new Subscriptionplan(array_merge($data, [
                 'pic' => "/product/" . $fileName,
                 'created_by' => $user->id,
                 'updated_by' => 0
             ]));

             if ($product_details->save()) {
                    return redirect ('list_product')->with('success_message','product Created Sucessfully!..');
             } else {
                    return redirect ('list_product')->with('success_message','Something Wrong!..');
             }
     }

     public function update_store(Request $request)
     {
            $user = auth()->user();

            $product_details = Subscriptionplan::find($request->id);

            if (request()->has('npic')) {
                $file = request()->file('npic');
                $fileName = time() . '.' . $file->getClientOriginalExtension();
                $filePath = public_path('/product/');
                $file->move($filePath, $fileName);

                 $sfileName = trim("/product/" . $fileName);
           }else{
                 $sfileName = $request->input('old_pic');
           }

            $product_details->category_id = $request->input('category_id');
            $product_details->name = $request->input('name');
            $product_details->quantity = $request->input('quantity');
            $product_details->description = $request->input('description');
            $product_details->status = $request->input('status');
            $product_details->pic = $sfileName;
            $product_details->created_by = $user->id;
            $product_details->updated_by = $user->id;

             if ($product_details->update()) {
                    return redirect ('list_product')->with('success_message','Product Updated Sucessfully!..');
             } else {
                    return redirect ('list_product')->with('success_message','Something Wrong!..');
             }
     }
}

