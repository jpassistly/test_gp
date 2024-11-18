<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductName;
use Illuminate\Validation\Rule;
class ProductnameController extends Controller
{
      public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data = ProductName::get();
        return view('Master.product-name.list', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // $data = ProductName::where('status', 'Y')->get();
        return view('Master.product-name.add', ['' =>'']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);
        $id = $request->id ?? null;

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('product_names')->ignore($id)],
            'status' => 'required',
        ]);

        if ($id) {
            $data = ProductName::findOrFail($id);
            $data->name = $request['name'];
            $data->status = $request['status'];
            $data->save();
        } else {
            $data = ProductName::create($data);
        }

        if ($data) {
            $action = $request->id ? 'Update' : 'Request';
            return redirect()->route('product-name.index')->with('success_message', 'Your Record ' . $action . ' Successfully!');
        } else {
            return redirect()->route('product-name.index')->with('error_message', 'Something went wrong!');
        }
    }



    public function edit(string $id)
    {
        $data = ProductName::where('id', $id)->first();
        return view('Master.product-name.add', ['data' => $data]);
    }


}
