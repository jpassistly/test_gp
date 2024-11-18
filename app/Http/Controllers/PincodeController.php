<?php

namespace App\Http\Controllers;

use App\Models\Pincode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\inventerytable;
use App\Models\Area;

class PincodeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list()
    {
        $farm = inventerytable::where('id', 1)->first();

        $pincode_list = Pincode::orderBy('id', 'desc')->get();

        foreach ($pincode_list as $d) {
            $area_count = Area::where('pincode', $d->id)->count();
            $d->area_count = $area_count;
        }
        return view('pincode/index')->with(compact('pincode_list', 'farm'));
    }

    public function add()
    {
        $farm = inventerytable::where('id', 1)->first();
        return view('pincode/add_form')->with(compact('farm'));
    }

    public function update($id = null)
    {
        $farm = inventerytable::where('id', 1)->first();
        $pincode = Pincode::find($id);
        return view('pincode/add_form')->with(compact('pincode', 'farm'));

    }

    public function store(Request $request)
    {
        $user = auth()->user();

        // Validation rules
        $request->validate([
            'pincode' => 'required|numeric|digits:6|unique:pincodes,pincode,' . $request->id,
            'location' => 'required|string|max:255',
            'radius' => 'required|numeric|min:1',
            'address' => 'required|string',
            'status' => 'required|in:Active,Inactive',
        ]);

        // Check if we are updating an existing record
        if ($request->id) {
            $pincode = Pincode::find($request->id);
            if ($pincode) {
                $pincode->update([
                    'pincode' => $request->pincode,
                    'location' => $request->location,
                    'radius' => $request->radius,
                    'address' => $request->address,
                    'status' => $request->status,
                    'updated_by' => $user->id, // Optional: store the ID of the user who updated the record
                ]);

                return redirect('pincode')->with('success_message', 'Pincode Updated Successfully!');
            } else {
                return redirect('pincode')->with('error_message', 'Pincode not found!');
            }
        } else {
            // Creating a new record
            $data = Pincode::create([
                'pincode' => $request->pincode,
                'location' => $request->location,
                'radius' => $request->radius,
                'address' => $request->address,
                'status' => $request->status,
                'created_by' => $user->id, // Store only the user ID
            ]);

            if ($data) {
                return redirect('pincode')->with('success_message', 'Pincode Created Successfully!');
            } else {
                return redirect('pincode')->with('error_message', 'Something went wrong!');
            }
        }
    }

    public function update_store(Request $request)
    {
        $user = auth()->user();

        $data = $request->only([
            'id',
            'name',
            'status'
        ]);
        $media = new Pincode(array_merge($data, [
            'created_by' => $user->id,
            'updated_by' => $user->id
        ]));

        $media = Pincode::find($request->id);

        if ($media->update($request->all())) {
            return redirect('pincode')->with('success_message', 'Pincode Updated Sucessfully!..');
        } else {
            return redirect('pincode')->with('success_message', 'Something Wrong!..');
        }
    }
}
