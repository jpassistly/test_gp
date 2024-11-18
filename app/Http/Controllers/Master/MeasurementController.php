<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Measurement;
use Illuminate\Validation\Rule;
class MeasurementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data = Measurement::get();
        return view('Master.measurement-name.list', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // $data = Measurement::where('status', 'Y')->get();
        return view('Master.measurement-name.add', ['' =>'']);
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $id = $request->id ?? null;

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('measurements')->ignore($id),
            ],
            'status' => 'required',
        ]);

        if ($id) {
            $measurement = Measurement::findOrFail($id);
            $measurement->name = $data['name'];
            $measurement->status = $data['status'];
            $measurement->save();
        } else {
            $measurement = Measurement::create($data);
        }

        if ($measurement) {
            $action = $id ? 'Updated' : 'Created';
            return redirect()->route('measurement.index')->with('success_message', 'Your Record ' . $action . ' Successfully!');
        } else {
            return redirect()->route('measurement.index')->with('error_message', 'Something went wrong!');
        }
    }




    public function edit(string $id)
    {
        $data = Measurement::where('id', $id)->first();
        return view('Master.measurement-name.add', ['data' => $data]);
    }

}
