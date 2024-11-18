<?php

namespace App\Http\Controllers\web\pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\wallet_balance;
class Walletbalance extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $product_list=wallet_balance::get();
        return view('wallet.index',compact('product_list'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('wallet.add_form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request

        if($request->id){
            $request->validate([
                'name' => 'required|string|max:255',
                'amount' => 'required|numeric',
                'banner' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'status' => 'required|in:Active,Inactive'
            ]);
        }else{
            $request->validate([
                'name' => 'required|string|max:255',
                'amount' => 'required|numeric',
                'banner' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'status' => 'required|in:Active,Inactive'
            ]);
        }


        // Check if this is an update or a create operation
        $plan = $request->id ? wallet_balance::findOrFail($request->id) : new wallet_balance;

        // Handle file upload
        if ($request->hasFile('banner')) {
            $file = $request->file('banner');
            $fileName = date('d-m-Y') . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('banners'), $fileName);
            $bannerPath = 'banners/' . $fileName;
            $plan->banner = $bannerPath;
        }

        // Set other fields
        $plan->name = $request->name;
        $plan->amount = $request->amount;
        $plan->details = $request->details;
        $plan->status = $request->status;

        // Save the plan (both create and update)
        $plan->save();

        // Redirect with a success message
        $message = $request->id ? 'Wallet plan updated successfully' : 'Wallet plan created successfully';
        return redirect()->route('wallet.index')->with('success', $message);
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
        $plan=wallet_balance::where('id',$id)->first();
        return view('wallet.add_form',compact('plan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'name'=>'required',
            'details'=>'required',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|string|in:Active,Inactive',
        ]);

        // Retrieve the subscription plan by ID
        $subscriptionPlan = wallet_balance::find($request->id);

        // Update the subscription plan's properties
        $subscriptionPlan->amount = $request->amount;
        $subscriptionPlan->details = $request->details;
        $subscriptionPlan->name = $request->name;
        $subscriptionPlan->status = $request->status;

        // Save the updated subscription plan to the database

        if ($request->hasFile('banner')) {
            $file = $request->file('banner');
            $destinationPath = 'assets/banner';
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path($destinationPath), $fileName);
            $sfileName = $destinationPath . '/' . $fileName;
        } else {
            // If no new avatar is uploaded, retain the existing avatar URL/path
            $sfileName = $request->npic;
        }
        $subscriptionPlan->banner = $sfileName;
        $subscriptionPlan->save();

        // Redirect to the subscription plans list with a success message
        return redirect()->to('wallet_plans')->with('success_message', 'Subscription plan updated successfully!');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
