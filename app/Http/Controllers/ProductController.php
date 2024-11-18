<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Quantity;
use App\Models\Measurement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\ProductName;
use App\Models\Unit;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list()
    {
        $product_list = Product::select(
            'products.id',
            'products.category_id',
            'products.name',
            'products.price',
            'products.status',
            'products.subscription',
            'categories.name AS category_name',
            'units.name AS quantity_name',
             'measurements.name AS measurement_name'
        )
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('units', 'products.quantity_id', '=', 'units.id')
            ->join('measurements', 'products.measurement_id', '=', 'measurements.id')
            ->orderBy( 'products.id', 'asc')->get();

        return view('product/index')->with(compact('product_list'));
    }
    public function list_e()
    {
        $product_list = Product::select(
            'products.id',
            'products.category_id',
            'products.name',
            'products.price',
            'products.status',
            'products.subscription',
            'categories.name AS category_name',
            'units.name AS quantity_name',
             'measurements.name AS measurement_name'
        )
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('units', 'products.quantity_id', '=', 'units.id')
            ->join('measurements', 'products.measurement_id', '=', 'measurements.id')
            ->orderBy( 'products.id', 'asc')->get();

        return view('product/index_2')->with(compact('product_list'));
    }

    public function add()
    {   $pro=ProductName::where('status','Y')->get();
        $category_list = Category::where('status','Active')->get();
        $quantity_list = Unit::where('status','Active')->get();
        $measurement_list = Measurement::where('status','Active')->get();
        return view('product/add_form')->with(compact('category_list','quantity_list','measurement_list','pro'));
    }
    public function add1()
    {   $pro=ProductName::where('status','Y')->get();
        $category_list = Category::where('status','Active')->get();
        $quantity_list = Unit::where('status','Active')->get();
        $measurement_list = Measurement::where('status','Active')->get();
        return view('product/add_form1')->with(compact('category_list','quantity_list','measurement_list','pro'));
    }

    public function update($id=null)
    {   $pro=ProductName::where('status','Y')->get();
        $category_list = Category::where('status','Active')->get();
        $quantity_list = Unit::where('status','Active')->get();
        $measurement_list = Measurement::where('status','Active')->get();
        $product_details = Product::find($id);
        // dd($product_details);
        return view('product/add_form')->with(compact('product_details','category_list','quantity_list','measurement_list','pro'));

    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validator = $request->validate([
            'name' => 'required|exists:product_names,id', // Ensure the name exists in the product_names table
            'quantity_id' => 'required|integer',
            'measurement_id' => 'required|integer',
            'price' => 'required|numeric',
            'pic' => 'required|image|mimes:jpeg,jpg|max:2048',
            'status' => 'required|string',
            'subscription' => 'required|string',
            'category_id' => 'required|integer',
            'description' => 'nullable|string' // Optional field
        ], [
            'pic.required' => 'This image field is mandatory', // Corrected custom message
            'pic.mimes' => 'This image size is bellow then 2 MB', // Corrected custom message
        ]);

        // if ($validator->fails()) {
        //     return response()->json(['errors' => $validator->errors()]);
        // }

        $productName = ProductName::find($request->name);

        if (!$productName) {
            return response()->json(['errors' => ['name' => 'Invalid product name.']], 400);
        }

        $product = new Product([
            'category_id' => $request->category_id,
            'name' => $productName->name,
            'product_id' => $productName->id, // Assuming this is an ID field
            'quantity_id' => $request->quantity_id,
            'measurement_id' => $request->measurement_id,
            'price' => $request->price,
            'status' => $request->status,
            'subscription' => $request->subscription,
            'created_by' => $user->id,
            'description' => $request->description,
            'updated_by' => 0,

        ]);

        if ($request->hasFile('pic')) {
            $file = $request->file('pic');
            $destinationPath = 'assets/images/product_img';
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path($destinationPath), $fileName);
            $product->pic = $destinationPath . '/' . $fileName;
        }

        if ($product->save()) {
            return redirect('list_product')->with('success_message', 'Product created successfully!');
        } else {
            return redirect('list_product')->with('error_message', 'Something went wrong.');
        }
    }

    public function saveOrUpdate(Request $request)
{
    $user = auth()->user();


    // Validation
    $validator = $request->validate([
        'name' => 'required|exists:product_names,id',
        'quantity_id' => 'required|integer',
        'measurement_id' => 'required|integer',
        'price' => 'required|numeric',
        'pic' => $request->id ? 'nullable|image|mimes:jpeg,jpg|max:2048' : 'required|image|mimes:jpeg,jpg|max:2048',
        'status' => 'required|string',
        'subscription' => 'required|string',
        'category_id' => 'required|integer',
        'description' => 'nullable|string',
    ], [
        'pic.required' => 'This image field is mandatory',
        'pic.mimes' => 'This image size must be below 2 MB',
    ]);

    // Find the product name
    $productName = ProductName::find($request->name);
    if (!$productName) {
        return redirect()->back()->withErrors(['name' => 'Invalid product name.']);
    }

    // If ID is present, find the product for updating, otherwise create a new instance
    $product = $request->id ? Product::find($request->id) : new Product();

    // Populate the product model with data
    $product->category_id = $request->category_id;
    $product->name = $productName->name;
    $product->product_id = $productName->id;
    $product->quantity_id = $request->quantity_id;
    $product->measurement_id = $request->measurement_id;
    $product->price = $request->price;
    $product->status = $request->status;
    $product->subscription = $request->subscription;
    $product->description = $request->description;
    $product->updated_by = $user->id; // Set updated_by for both cases

    if ($request->id) {
        $product->updated_by = $user->id; // For update
    } else {
        $product->created_by = $user->id; // For create
    }

    // Handle image upload if a new image is provided
    if ($request->hasFile('pic')) {
        $file = $request->file('pic');
        $destinationPath = 'assets/images/product_img';
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path($destinationPath), $fileName);
        $product->pic = $destinationPath . '/' . $fileName;
    }

    // Save or update the product
    if ($product->save()) {
        $message = $request->id ? 'Product updated successfully!' : 'Product created successfully!';
        return redirect('list_product')->with('success_message', $message);
    } else {
        return redirect('list_product')->with('error_message', 'Something went wrong.');
    }
}




     public function update_store(Request $request)
     {
            $user = auth()->user();

            // dd($request);


            $validator = $request->validate([
                'name' => 'required|exists:product_names,id', // Ensure the name exists in the product_names table
                'quantity_id' => 'required|integer',
                'measurement_id' => 'required|integer',
                'price' => 'required|numeric',
                'pic' => 'required|image|mimes:jpeg,jpg|max:2048',
                'status' => 'required|string',
                'subscription' => 'required|string',
                'category_id' => 'required|integer',
                'description' => 'nullable|string' // Optional field
            ]);


            $product_details = Product::find($request->id);

            if (request()->has('npic')) {
                $file = request()->file('npic');
                $destinationPath = 'assets/images/product_img';
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path($destinationPath), $fileName);
             $sfileName= $destinationPath . '/' . $fileName;
           }else{
                 $sfileName = $request->input('old_pic');
           }
           $productName = ProductName::find($request->name);
            $product_details->category_id = $request->input('category_id');
            $product_details->name = $productName->name;
            $product_details->quantity_id = $request->input('quantity_id');
            $product_details->measurement_id = $request->input('measurement_id');
            $product_details->price = $request->input('price');
            $product_details->description = $request->input('description');
            $product_details->status = $request->input('status');
            $product_details->subscription = $request->input('subscription');

            $product_details->pic = $sfileName;
            $product_details->product_id = $request->input('name');;
            $product_details->created_by = $user->id;
            $product_details->updated_by = $user->id;

             if ($product_details->update()) {
                    return redirect ('list_product')->with('success_message','Product Updated Sucessfully!..');
             } else {
                    return redirect ('list_product')->with('success_message','Something Wrong!..');
             }
     }
}
