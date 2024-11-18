<?php

namespace App\Http\Controllers;

use App\Models\Deliveryperson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DeliverypersonController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list()
    {
        $user_list = Deliveryperson::orderBy('id', 'desc')->get()->toArray();
        return view('delivery-person/index')->with(compact('user_list'));
    }

    public function add()
    {
        return view('delivery-person/add_form');
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        // Validate the request
        // $request->validate([
        //     'name' => 'required|string|max:255',
        //     'mobile' => 'required|string|max:255',
        //     'aadhar_number' => 'required|string|max:255',
        //     'status' => 'required|string',
        //     'password' => 'required|string|min:6',
        //     'pic' => 'required|image|mimes:jpeg,jpg,png|max:2048', // Validate the image
        // ]);

        // $password = Hash::make($request->input('password'));

        // $data = $request->only([
        //     'name', 'mobile', 'aadhar_number', 'status',
        // ]);

        // // Handle the file upload
        // if ($request->hasFile('pic')) {
        //     $file = $request->file('pic');
        //     $destinationPath = 'images/delivery_persons'; // Define your destination path

        //     // Create directory if it does not exist
        //     if (!file_exists(public_path($destinationPath))) {
        //         mkdir(public_path($destinationPath), 0755, true);
        //     }

        //     $fileName = time() . '_' . $file->getClientOriginalName();
        //     $file->move(public_path($destinationPath), $fileName);

        //     // Check if file was moved
        //     if (file_exists(public_path($destinationPath) . '/' . $fileName)) {
        //         // Add the file path to the data array
        //         $data['pic'] = $destinationPath . '/' . $fileName;
        //     } else {
        //         return redirect('delivery-person')->with('error_message', 'File upload failed.');
        //     }
        // }

        // $users = new Deliveryperson(array_merge($data, [
        //     'password' => $password,
        //     'created_by' => $user->id,
        //     'updated_by' => 0,
        // ]));

        // if ($users->save()) {
        //     return redirect('delivery-person')->with('success_message', 'User Created Successfully!..');
        // } else {
        //     return redirect('delivery-person')->with('error_message', 'Something went wrong!..');
        // }

         // Validate the form inputs
         $validated = $request->validate([
            'name' => 'required|max:25',
            'mobile' => 'required|digits:10',
            'password' => 'required|min:6|max:8',
            'aadhar_number' => 'required|digits:12',
            'status' => 'required',
            'pic' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Store the image if uploaded
        $imageName = null;
        if ($request->file('pic')) {
            $imageName = time() . '.' . $request->file('pic')->extension();
            $request->file('pic')->move(public_path('assets/images/delivery_person'), $imageName);
        }

        // Create a new delivery person record
        DeliveryPerson::create([
            'name' => $request->name,
            'mobile' => $request->mobile,
            'password' => $request->password, // Use bcrypt to hash the password
            'aadhar_number' => $request->aadhar_number,
            'status' => $request->status,
            'pic' => $imageName,
            'created_by' => auth()->user()->id,
            'updated_by' => auth()->user()->id,
        ]);

        // Redirect back or to another page
        return redirect('delivery-person')->with('success_message', 'User Created Successfully!');

    }

    public function update($id = null)
    {
        $user_details = Deliveryperson::find($id);
        return view('delivery-person/update_form')->with(compact('user_details'));

    }

    public function update_store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:15',
            'aadhar_number' => 'nullable|string|max:20',
            'status' => 'required',
            'pic' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'nullable|min:6',
        ]);

        // Get the authenticated user
        $user = auth()->user();

        // Find the delivery person record
        $deliveryPerson = Deliveryperson::find($request->id);

        if (!$deliveryPerson) {
            return redirect()->back()->with('error_message', 'User not found.');
        }

        // Handle file upload if a new picture is provided
        if ($request->hasFile('pic')) {
            $file = $request->file('pic');
            $destinationPath = 'assets/images/delivery_person';
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path($destinationPath), $fileName);
        } else {
            // Keep the old picture if no new picture is uploaded
            $fileName = $deliveryPerson->pic;
        }

        // Handle password update only if a new password is provided
        if (!empty($request->input('password'))) {
            $password = $request->input('password');
        } else {
            $password = $deliveryPerson->password; // Keep the old password
        }

        // Update the delivery person data
        $deliveryPerson->update([
            'name' => $request->input('name'),
            'mobile' => $request->input('mobile'),
            'aadhar_number' => $request->input('aadhar_number'),
            'status' => $request->input('status'),
            'updated_by' => $user->id,
            'pic' => $fileName,
            'password' => $password,
        ]);

        // Return success or error message
        return redirect('delivery-person')->with('success_message', 'User Updated Successfully!');
    }

}
