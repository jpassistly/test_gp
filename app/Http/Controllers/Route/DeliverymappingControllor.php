<?php

namespace App\Http\Controllers\Route;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pincode;
use App\Models\Delivery_lines;
use App\Models\Deliveryperson;
use App\Models\DeliveryLineMapping;
use App\Models\Customersubscription;
use App\Models\RouteAssign;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Auth;

class DeliverymappingControllor extends Controller
{
    /**
     * Display a listing of the resource..
     */
    public function index()
    {
        // $data = DeliveryLineMapping::select('date', DB::raw('count(*) as total'))
        //     ->where('del', '0')
        //     ->groupBy('date')
        //     ->get();
        $data = DeliveryLineMapping::select('date', DB::raw('count(*) as total'))
            ->where('del', '0')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();


        // foreach ($data as $d) {
        //     // Get the delivery line name
        //     $delivery_line = Delivery_lines::find($d->delivery_line_id);
        //     $d->delivery_line_name = $delivery_line ? $delivery_line->name : 'N/A';

        //     // Get the delivery staff name
        //     $delivery_staff = Deliveryperson::find($d->delivery_staff_id);
        //     $d->delivery_boy_name = $delivery_staff ? $delivery_staff->name : 'N/A';
        // }

        return view('Master.delivery.list', [
            'data' => $data,
        ]);
    }


    public function get_old_datas(Request $request)
    {
        // Validate the date parameter
        $request->validate([
            'date' => 'required|date',
        ]);

        $requestedDate = Carbon::parse($request->date);
        $yesterday = $requestedDate->copy()->subDay();

        // Fetch delivery lines for the requested date
        $list = DeliveryLineMapping::where('date', $requestedDate->format('Y-m-d'))->get();

        if ($list->count() > 0) {
            return response()->json([
                'success' => true,
                'status' => 'success',
                'message' => 'Already some delivery lines are scheduled this date.',
                'data' => $list,
            ], 200);
        } else {
            // Fetch delivery lines for the previous day
            $list = DeliveryLineMapping::where('date', $yesterday->format('Y-m-d'))->get();

            if ($list->count() > 0) {
                $msg = 'No delivery lines scheduled for this date, so displaying yesterday\'s schedule data.';
            }else{
                $msg = 'No delivery lines scheduled for this date ';
            }
            return response()->json([
                'success' => true,
                'status' => 'success',
                'message' => $msg,
                'data' => $list,
            ], 200);
        }
    }
    /**
     * Show the form for creating a new resource.
     */

    public function create()
    {
        $pincodes = Pincode::where('status', 'Active')->get();
        $delivery_lines = Delivery_lines::where('status', 'Active')->get();
        $delivery_staff = Deliveryperson::where('status', 'Active')->get();
        return view('Master.delivery.add', [
            'pincodes' => $pincodes,
            'delivery_lines' => $delivery_lines,
            'delivery_staff' => $delivery_staff,
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */

     public function store(Request $request)
     {
         // Get the current authenticated user
         $user = Auth::user();
     
         // Get the input data
         $ids = $request->delivery_line_ids;
         $all_staff = $request->delivery_staff_id;
         $from_date = Carbon::parse($request->fromdate); // From date
         $to_date = Carbon::parse($request->to_date); // To date
     
         // Initialize the success message variable
         $message = 'Delivery Line Mapping Created Successfully!';
     
         // Loop through the dates from 'from_date' to 'to_date'
         $dateRange = Carbon::parse($from_date)->daysUntil($to_date); // Get all the days between the two dates
     
         foreach ($ids as $index => $d) {
             // Get the current delivery line ID and staff ID for this index
             $new_delivery_line_id = $d;
             $new_staff = $all_staff[$index];
     
             foreach ($dateRange as $date) {
                 $new_date = $date->format('Y-m-d'); // Format the date to 'Y-m-d'
     
                 // Check if a record already exists for the given date and delivery line ID
                 $existingMapping = DeliveryLineMapping::where('date', $new_date)
                     ->where('delivery_line_id', $new_delivery_line_id)
                     ->first();
     
                 if (!$existingMapping) {
                     // Create a new mapping if it doesn't exist
                     DeliveryLineMapping::create([
                         'date' => $new_date,
                         'delivery_line_id' => $new_delivery_line_id,
                         'delivery_staff_id' => $new_staff,
                         'created_by' => $user->id,
                         'updated_by' => $user->id,
                         'del' => 0, // Assuming 'del' is a boolean or a flag to indicate deletion status
                     ]);
     
                     // Update the message after creating a record
                     $message = 'Delivery Line Mapping Created Successfully for ' . $new_date;
     
                     // Get the area IDs for the current delivery line
                     $area_ids = RouteAssign::where('delivery_line_id', $new_delivery_line_id)
                         ->where('del', '0')
                         ->pluck('area_id');
     
                     // Update customer subscriptions for the given areas and date
                     Customersubscription::whereIn('area', $area_ids)
                         ->where('date', $new_date)
                         ->update(['deliveryperson_id' => $new_staff, 'delivery_line_id' => $new_delivery_line_id]);
     
                     // Update orders for the given areas and date
                     Order::whereIn('area', $area_ids)
                         ->where('delivery_date', $new_date)
                         ->update(['delivered_by' => $new_staff, 'delivery_line_id' => $new_delivery_line_id]);
                 } else {
                     // If already exists, you can either modify the message or just keep it as is
                     $message = 'Delivery Line Mapping already exists for ' . $new_date;
                 }
             }
         }
     
         // Redirect back with the success message
         return redirect('delivery-lins-mapping')->with('success_message', $message);
     }
     

     
     

    public function update(Request $request)
    {
        // $validatedData = $request->validate([
        //     'date' => 'required|date',
        //     'delivery_line_id' => 'required|exists:delivery_lines,id',
        //     'delivery_staff_id' => 'required|exists:deliverypersons,id',
        // ]);

        // dd($request->all());
        $user = Auth::user();

        $ids = $request->delivery_line_ids;
        $all_staff = $request->delivery_staff_id;
        $new_date = $request->date;
        // dd($request->all());

        foreach ($ids as $index => $d) {
            //     echo ',' . $index . 'dd' . $d;
            $new_delivery_line_id = $d;
            $new_staff = $all_staff[$index];

            $ch = DeliveryLineMapping::where('date', $new_date)->where('delivery_line_id', $new_delivery_line_id)->first();
            if ($ch) {


                $ch->update([
                    'date' => $new_date,
                    'delivery_line_id' => $new_delivery_line_id,
                    'delivery_staff_id' => $new_staff,
                    'updated_by' => $user->id,
                ]);
                $message = 'Delivery Line Mapping Updated Successfully!';
            }

            $area_ids = RouteAssign::where('delivery_line_id', $new_delivery_line_id)->where('del', '0')->pluck('area_id');

            if ($ch) {

                //null delivery_line_id deliveryperson_id
                $up_main = Customersubscription::whereIn('area', $area_ids)
                    ->where('delivery_line_id', $new_delivery_line_id)
                    ->update(['deliveryperson_id' => null, 'delivery_line_id' => null]);

                $order = Order::whereIn('area', $area_ids)
                    ->where('delivery_line_id', $new_delivery_line_id)
                    ->update(['delivered_by' => null, 'delivery_line_id' => null]);




                $up_main = Customersubscription::whereIn('area', $area_ids)
                    ->where('date', $new_date)
                    ->update(['deliveryperson_id' => $new_staff, 'delivery_line_id' => $new_delivery_line_id]);

                $order = Order::whereIn('area', $area_ids)
                    ->where('delivery_date', $new_date)
                    ->update(['delivered_by' => $new_staff, 'delivery_line_id' => $new_delivery_line_id]);

            }
        }
        return redirect('delivery-lins-mapping')->with('success_message', $message);

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

    public function edit(string $date)
    {

        // Current date in Y-m-d format
        $currentDate = Carbon::now()->format('Y-m-d');

        // Convert input date to Carbon instance for comparison
        $inputDate = Carbon::createFromFormat('Y-m-d', $date)->format('Y-m-d');

        // if ($inputDate < $currentDate) {
        //     return redirect()->route('delivery-lins-mapping.index')->with('success_message', 'Cannot Edit Old Dates');
        // }


        $data = DeliveryLineMapping::where('date', $date)->get();
        $pincodes = Pincode::where('status', 'Active')->get();
        $delivery_lines = Delivery_lines::where('status', 'Active')->get();
        $delivery_staff = Deliveryperson::where('status', 'Active')->get();

        if ($inputDate < $currentDate) {
            return view('Master.delivery.view', [
                'date' => $date,
                'pincodes' => $pincodes,
                'delivery_lines' => $delivery_lines,
                'delivery_staff' => $delivery_staff,
                'data' => $data,
            ]);
            }

        return view('Master.delivery.edit', [
            'date' => $date,
            'pincodes' => $pincodes,
            'delivery_lines' => $delivery_lines,
            'delivery_staff' => $delivery_staff,
            'data' => $data,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function old_values(Request $request)
    {
        //
    }
}
