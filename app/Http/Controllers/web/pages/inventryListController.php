<?php

namespace App\Http\Controllers\web\pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\inventerytable;
class inventryListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $delivery_list=inventerytable::get();
        return view('inventry.index',compact('delivery_list'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $inventerytable=inventerytable::first();
        return view('inventry.add_form',compact('inventerytable'));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            "name" => "required",
            "address" => "required",
            "city"=>"required",
            "state"=>"required",
            "area"=>"required",
            "pincode"=>"required",
            "lat" => "required",
            "lon" => "required",


        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        } else {


            $d = inventerytable::create([

                'name' => $request->input('name'),
                'address' => $request->input('address'),
                'pincode'=>$request->input('pincode'),
                'city' => $request->input('city'),
                'state' => $request->input('state'),
                'lat' => $request->input('lat'),
                'lon' => $request->input('lon'),
                'area' => $request->input('area'),
                'created_by' => $user->id,
                'updated_by' => 0

            ]);
            if ($d->save()) {
                return redirect ('inventry_list')->with('success_message','Inventry Created Sucessfully!..');
         } else {
                return redirect ('inventry_add')->with('success_message','Something Wrong!..');
         }}
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
        $inventerytable=inventerytable::first();
        return view('inventry.add_form',compact('inventerytable'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        $id = $request->input('id');

        // Validate the request data
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "address" => "required",
            "city" => "required",
            "state" => "required",
            "area" => "required",
            "pincode" => "required",
            "lat" => "required",
            "lon" => "required"
        ]);

        // Return validation errors if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        // Find the inventory record by ID
        $inventory = inventerytable::findOrFail($id);

        // Update the inventory record with the new data
        $inventory->name = $request->input('name');
        $inventory->address = $request->input('address');
        $inventory->pincode = $request->input('pincode');
        $inventory->city = $request->input('city');
        $inventory->state = $request->input('state');
        $inventory->lat = $request->input('lat');
        $inventory->lon = $request->input('lon');
        $inventory->area = $request->input('area');
        // $inventory->updated_by = $user->id;

        // Save the updated inventory record
        if ($inventory->save()) {
            return response()->json(['success' => true, 'message' => 'Updated successfully']);
        } else {
            return response()->json(['success' => false, 'message' => 'Error occurred while updating']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
