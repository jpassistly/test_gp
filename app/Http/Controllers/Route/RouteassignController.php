<?php

namespace App\Http\Controllers\Route;

use App\Http\Controllers\Controller;
use App\Models\Pincode;
use App\Models\Delivery_lines;
use Illuminate\Http\Request;
use App\Models\Deliveryperson;
use App\Models\RouteAssign;
use App\Models\Area;
class RouteassignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pincode = Pincode::where('status', 'Active')->get();
        $delivery_lines = Delivery_lines::where('status', 'Active')->get();

        $delivery_staff = Deliveryperson::where('status', 'Active')->get();
        return view('route.add', [
            'pincodes' => $pincode,
            'delivery_lines' => $delivery_lines,
            'delivery_staff' => $delivery_staff,
        ]);
    }
    public function saveroute(request $request)
    {
        // dd($request);

             $pincode = $request['pincode'];
             $deliveryLine = $request['delivery_line'];
             $area_ids = $request['area_id'];


             // Iterate over the cus_ids and ltln arrays
             foreach ($area_ids as $index => $d) {

                $up=RouteAssign::where('area_id', $d)->update(['del' => 1]);


                RouteAssign::create([
                     'delivery_line_id' => $deliveryLine,
                     'pincode_id' => $pincode,
                     'area_id' => $d,
                     'del' => false, // assuming del is false by default
                 ]);
             }
             return response()->json([
                'success'=>true,
                'message' => 'Delivery lines created successfully',
                'status' => 200,
               ], 200);

    }
    public function list(request $request)
    {
        $pincode = Pincode::where('status', 'Active')->get();
        $delivery_lines = Delivery_lines::where('status', 'Active')->get();
        $delivery_staff = Deliveryperson::where('status', 'Active')->get();
        $tabledata=[];

        // if($request->delivery_line || $request->pincode){

            $query = RouteAssign::query();

            // Apply conditional where clauses
            if (isset($request->pincode)) {
                $query->where('pincode_id', $request->pincode);
            }else{

            }
            if (isset($request->delivery_line)) {
                $query->where('delivery_line_id', $request->delivery_line);
            }else{

            }


            // Execute the query
            $tabledata = $query->where('del',0)->get();

            foreach($tabledata as $d){

                $a=Delivery_lines::where('id',$d->delivery_line_id)->first();
                $d->Delivery_line_name=$a->name;

                $b=Area::where('id',$d->area_id)->first();
                $d->area_name=$b->name;

                $c=Pincode::where('id',$d->pincode_id)->first();
                $d->pincode_name=$c->pincode;

            }
        // }


        // dd($tabledata);

        return view('route.list', [
            'pincodes' => $pincode,
            'delivery_lines' => $delivery_lines,
            'delivery_staff' => $delivery_staff,
            'tabledata' => $tabledata,
        ]);
    }

    public function getdellist(Request $request)
    {
        // Initialize the query builder
        $query = RouteAssign::query();

        // Apply conditional where clauses
        if (isset($request->pincode)) {
            $query->where('pincode_id', $request->pincode);
        }
        if (isset($request->delivery_line)) {
            $query->where('delivery_line_id', $request->delivery_line);
        }


        // Execute the query
        $list = $query->where('del',0)->get();

        foreach($list as $d){

            $a=Delivery_lines::where('id',$d->delivery_line_id)->first();
            $d->Delivery_line_name=$a->name;

            $b=Area::where('id',$d->area_id)->first();
            $d->area_name=$b->name;

            $c=Pincode::where('id',$d->pincode_id)->first();
            $d->pincode_name=$c->pincode;

        }


        return response()->json([
            'status' => 'success',
            'message' => 'Delivery list retrieved successfully.',
            'data' => $list,
        ], 200);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }


}
