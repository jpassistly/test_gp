<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unit;
use Illuminate\Validation\Rule;

class UnitnameController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data = Unit::get();
        return view('Master.unit-name.list', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // $data = Unit::where('status', 'Y')->get();
        return view('Master.unit-name.add', ['' => '']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);
        $id = $request->id ?? null;

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('units', 'name')->ignore($id),
            ],
            'status' => 'required',
        ]);

        if ($id) {
            $data = Unit::findOrFail($id);
            $data->name = $request['name'];
            $data->status = $request['status'];
            $data->updated_by = auth()->user()->id;
            $data->save();
        } else {
            $data['created_by']=auth()->user()->id;
            $data = Unit::create($data);
        }

        if ($data) {
            $action = $request->id ? 'Update' : 'Insert';
            return redirect()->route('unit.index')->with('success_message', 'Your Record ' . $action . ' Successfully!');
        } else {
            return redirect()->route('unit.index')->with('error_message', 'Something went wrong!');
        }
    }



    public function edit(string $id)
    {
        $data = Unit::where('id', $id)->first();
        return view('Master.unit-name.add', ['data' => $data]);
    }

}
