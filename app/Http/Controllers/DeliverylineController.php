<?php

namespace App\Http\Controllers;

use App\Models\Delivery_lines;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\RouteAssign;
use App\Models\Pincode;

class DeliverylineController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list()
    {
        $delivery_lines_list = Delivery_lines::orderBy('id', 'desc')->get();

        foreach ($delivery_lines_list as $d) {

            $co=RouteAssign::where('delivery_line_id',$d->id)->where('del', '0')->count();

            $d->area_count=$co;
            if ($d->pincode_id != '') {
                $pin = Pincode::where('id', $d->pincode_id)->first();
                $d->pincode = $pin->pincode;
            }
        }
        return view('delivery-line/index')->with(compact('delivery_lines_list'));
    }

    public function add()
    {
        $pin = Pincode::where('status', 'Active')->get();
        return view('delivery-line/add_form', ['pincode' => $pin]);
    }

    public function update($id = null)
    {
        $pin = Pincode::where('status', 'Active')->get();
        $delivery_lines_details = Delivery_lines::find($id);
        return view('delivery-line/update_form', ['pincode' => $pin, 'delivery_lines_details' => $delivery_lines_details]);

    }

    public function store(Request $request)
    {
        // dd($request);
        $user = auth()->user();

        $data = $request->only([
            'name',
            'status',
            'pincode_id',
            'color_code'
        ]);
        $media = new Delivery_lines(array_merge($data, [
            'created_by' => $user->id,
            'updated_by' => 0
        ]));

        if ($media->save()) {
            return redirect('delivery-line')->with('success_message', 'Delivery Line Created Sucessfully!..');
        } else {
            return redirect('delivery-line')->with('success_message', 'Something Wrong!..');
        }
    }

    public function update_store(Request $request)
    {
        $user = auth()->user();

        $data = $request->only([
            'id',
            'name',
            'pincode_id',
            'status',
            'color_code'
        ]);
        $media = new Delivery_lines(array_merge($data, [
            'created_by' => $user->id,
            'updated_by' => $user->id
        ]));

        $media = Delivery_lines::find($request->id);

        if ($media->update($request->all())) {
            return redirect('delivery-line')->with('success_message', 'Delivery Line Updated Sucessfully!..');
        } else {
            return redirect('delivery-line')->with('success_message', 'Something Wrong!..');
        }
    }
}
