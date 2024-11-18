<?php

namespace App\Http\Controllers;

use App\Models\User;
//use App\Models\Role;
//use App\Models\Stage;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list()
    {
        $user_list = User::select(
            'users.id',
            'users.role_id',
            'users.name',
            'users.email',
            'roles.name AS role_name'
        )
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->whereNotIn('users.id',[1])
            ->orderBy( 'users.id', 'DESC')->get();

        return view('user/index')->with(compact('user_list'));
    }

    public function add()
    {
        $role_list = Role::get()->toArray();
       // $stage_list = Stage::whereNotIn('id',[1,2])->where('status','=','Active')->orderBy('id')->get()->toArray();
        return view('user/add_form')->with(compact('role_list'));
    }

    public function store(Request $request)
     {
            $user = auth()->user();
            $stages = $request->input('stages');
            $stages_value = implode(',', $stages);
            $password = Hash::make($request->input('password'));

              $data = $request->only([
                'role_id','name','email','dob','active',
            ]);
             $users = new User(array_merge($data, [
                 'password' => $password,
                 'stages' => $stages_value,
                 'avatar' => 'images/user-dummy.jpg',
                 'created_by' => $user->id,
                 'updated_by' => 0
             ]));

             if ($users->save()) {
                    return redirect ('user')->with('success_message','User Created Sucessfully!..');
             } else {
                    return redirect ('user')->with('success_message','Something Wrong!..');
             }
     }

     public function update($id=null)
     {
        $user_details = User::find($id);
        $stages = $user_details->stages;
        $stages_select = explode(',', $stages);

        $role_list = Role::get()->toArray();
        $stage_list = Stage::orderBy('id')->get()->toArray();

         return view('user/update_form')->with(compact('user_details','role_list','stage_list','stages_select'));

     }

     public function update_store(Request $request)
     {
            $user = auth()->user();
            $user_details = User::find($request->id);
            $old_password = $user_details->password;

            $stages_value = $request->input('stages');
            $stages = implode(',', $stages_value);

            $password = $request->input('password');
            if ($password!=""){
                 $password = Hash::make($request->input('password'));
            }else{
                $password = $old_password;
            }

            $request->merge([
                'role_id' =>  $request->input('role_id'),
                'name' =>  $request->input('name'),
                'email' => $request->input('email'),
                'password' => $password,
                'stages' => $stages,
                'updated_by' => $user->id,
            ]);

             $order = User::find($request->id);

             if ($user_details->update($request->all())) {
                    return redirect ('user')->with('success_message','User Updated Sucessfully!..');
             } else {
                    return redirect ('user')->with('success_message','Something Wrong!..');
             }
     }
}
