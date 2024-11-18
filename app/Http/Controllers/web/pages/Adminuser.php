<?php

namespace App\Http\Controllers\web\pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class Adminuser extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::get();
        return view('user.index', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('user.add_form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = $request->validate([
            'name' => 'required|string|max:50',
            'email' => 'required|string|email|max:50|unique:users',
            'password' => 'required|string|min:6',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required|string',
        ]);


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'email_verified_at' => now(),
            'password' => Hash::make($request->password),
            'status' => $request->status,
            'remember_token' => Str::random(60),
        ]);

        if ($request->hasFile('avatar')) {
            // Get the uploaded file
            $file = $request->file('avatar');

            // Define the destination path in the public directory
            $destinationPath = 'assets/images/admin_img';

            // Generate a unique file name
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Move the file to the destination path
            $file->move(public_path($destinationPath), $fileName);

            // Save the relative path to the database
            $user->avatar = $destinationPath . '/' . $fileName;
            $user->save();
        }

        return redirect()->to('/user_reg')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user_details = User::where('id', $id)->first();
        return view('user.update_form', compact('user_details'));
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
    public function update(Request $request)
    {
        // dd($request->all());
        // Validate the incoming request
        $validator = $request->validate([
            'name' => 'required|string|max:50',
            'email' => 'required|string|email|max:50|unique:users,email,' . $request->id, // Exclude current user's email from uniqueness check
            'password' => 'nullable|string|min:6', // Password is optional
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Avatar upload is optional
            'status' => 'required|string',
        ]);

        // Find the user by ID
        $user = User::findOrFail($request->id); // Assuming you're using a User model

        // Update user fields
        $user->name = $request->name;
        $user->email = $request->email;

        // Update the password only if it is provided
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password); // Hash the password before saving
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Remove old avatar if necessary


            // Move the uploaded file to the desired directory
            $file = $request->file('avatar');
            $destinationPath = 'assets/images/admin_img';

            // Generate a unique file name
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Move the file to the destination path
            $file->move(public_path($destinationPath), $fileName);

            // Save the relative path to the database
            $user->avatar = $destinationPath . '/' . $fileName;
        }

        // Update the status
        $user->status = $request->status;

        // Save the updated user
        $user->save();


        if (Str::contains(url()->previous(), 'contacts-profile')) {
            return redirect('contacts-profile')->with('success_message', 'User updated successfully.');
        }


        // Redirect back with a success message
        return redirect()->to('/user_reg')->with('success_message', 'User updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
