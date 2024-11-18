<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list()
    {
        $category_list = Category::orderBy('id', 'desc')->get()->toArray();
        return view('category/index')->with(compact('category_list'));
    }

    public function add()
    {
        return view('category/add_form');
    }


    public function edit($id = null)
    {
        $category_details = Category::find($id);
        return view('category/update_form')->with(compact('category_details'));

    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:30',
            'status' => 'required|string',
            'pic' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust the rules as needed
        ]);

        $filename = null;

        if ($request->hasFile('pic')) {
            $file = $request->file('pic');

            $filename = date('d-m-Y') . '-' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('category'), $filename);
        }

        Category::create([
            'name' => $request->input('name'),
            'status' => $request->input('status'),
            'pic' => 'category/' . $filename,
            'created_by' => auth()->user()->id,
            'updated_by' => auth()->user()->id,
        ]);

        // Return a response or redirect
        return redirect('list_category')->with('success_message', 'Category created Sucessfully!..');
    }

    public function update(Request $request)
    {

        // dd($request);
        $category = Category::findOrFail($request->id);

        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:30',
            'status' => 'required|string',
            'pic' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Allow pic to be nullable
        ]);

        // Initialize filename variable
        $filename = $category->pic; // Keep the existing picture if no new file is uploaded

        // Handle the uploaded file
        if ($request->hasFile('pic')) {
            // Delete the old file if it exists
            if ($filename && file_exists(public_path($filename))) {
                unlink(public_path($filename)); // Delete the old file
            }

            // Get the uploaded file
            $file = $request->file('pic');

            if ($filename && file_exists(public_path($filename))) {
                unlink(public_path($filename)); // Delete the old file
            }
            // Generate a new filename
            $filename = date('d-m-Y') . '-' . time() . '.' . $file->getClientOriginalExtension();

            // Move the new file to the desired location
            $file->move(public_path('category'), $filename);

            // Update the pic path
            $filename = 'category/' . $filename;
        }

        // Update the category in the database
        $category->update([
            'name' => $request->input('name'),
            'status' => $request->input('status'),
            'pic' => $filename,
            'updated_by' => auth()->user()->id,
        ]);

        // Return a response or redirect
        return redirect('list_category')->with('success_message', 'Category updated successfully!');
    }
}
