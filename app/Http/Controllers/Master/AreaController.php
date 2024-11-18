<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Area;
use App\Models\Pincode;
use Illuminate\Validation\Rule;
use App\Models\RouteAssign;
use App\Models\Delivery_lines;

class AreaController extends Controller
{


    public function index()
    {

        $data = Area::get();


        foreach ($data as $d) {
            $pincode = Pincode::where('id', $d->pincode)->first();
            $d->pincode = $pincode->pincode;
        }
        return view('Master.area.list', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pincode = Pincode::where('status', 'Active')->get();
        return view('Master.area.add', ['pincode' => $pincode]);
    }

    public function getarea(Request $request)
    {
        $area = Area::where('pincode', $request->pincode)->get();

        foreach ($area as $d) {
            $assign_line = RouteAssign::where('area_id', $d->id)->where('del', 0)->first();

            if ($assign_line) {
                $delvery_lines = Delivery_lines::where('id', $assign_line->delivery_line_id)->first();
                if ($delvery_lines) {
                    $d->assign_delivery_lines = $delvery_lines->name;
                } else {
                    $d->assign_delivery_lines = '--'; // Handle if delivery line is not found
                }
            } else {
                $d->assign_delivery_lines = '--'; // Handle if route assignment is not found
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Areas retrieved successfully.',
            'data' => $area,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);
        $id = $request->id ?? null;

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('areas')->ignore($id)],
            'pincode' => 'required|numeric',
            'status' => 'required',
        ]);

        if ($id) {
            $data = Area::findOrFail($id);
            $data->name = $request['name'];
            $data->status = $request['status'];
            $data->pincode = $request['pincode'];
            $data->save();
        } else {
            $data = Area::create($data);
        }

        if ($data) {
            $action = $request->id ? 'Update' : 'Request';
            return redirect()->route('area.index')->with('success_message', 'Your Record ' . $action . ' Created Successfully!');
        } else {
            return redirect()->route('area.index')->with('error_message', 'Something went wrong!');
        }
    }



    public function edit(string $id)
    {
        $data = Area::where('id', $id)->first();
        $pincode = Pincode::where('status', 'Active')->get();

        return view('Master.area.add', ['data' => $data, 'pincode' => $pincode]);
    }

}
