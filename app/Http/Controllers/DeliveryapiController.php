<?php

namespace App\Http\Controllers;

use App\Models\Deliveryperson;

use App\Models\Deliverypersons_fcm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;


use Illuminate\Http\Request;

class DeliveryapiController extends Controller
{
    //

    public function delivery_login(Request $request)
    {
        $post_values = $request->all();
        $user = Deliveryperson::where('mobile', $post_values['phone'])->where('status', 'Active')->first();


        if ($user) {
            if (!$user || ($post_values['password']!=$user->password)) {
                return response()->json([
                    'message' => 'Invalid Credentials'
                ], 401);
            }

            $token = $user->createToken($user->name . '-AuthToken')->plainTextToken;
            //$token = Str::random(60);
            $save_token = $user->update(['remember_token' => $token]);

            if ($save_token) {
                return response()->json([
                    'access_token' => $token,
                    'delivery_personid' => $user->id,
                    'message' => 'Login Successful',
                ]);
            }
        }

        return response()->json([
            'message' => 'Something went wrong'
        ], 500);



    }

    public function save_fcm(Request $request)
    {
        $fcm = Deliverypersons_fcm::where('deliveryperson_id', $request->deliveryperson_id)->first();

        if ($fcm) {
            $fcm = Deliverypersons_fcm::where('deliveryperson_id', $request->deliveryperson_id)
                ->update(['fcm_key' => $request->fcm_key]);
        } else {
            $fcm = Deliverypersons_fcm::create([
                'deliveryperson_id' => $request->deliveryperson_id,
                'fcm_key' => $request->fcm_key,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Fcm saved success',
            'status' => 200,
        ], 200);
    }

    public function get_profile(Request $request)
    {
        $delivery_id = $request->input('delivery_id');
        $user = Deliveryperson::where('id', $delivery_id)
            ->select('name', 'mobile', 'aadhar_number', 'pic')
            ->first();

        if ($user->pic) {
            $user->pic = URL::to($user->pic);
        }

        if ($user) {
            return response()->json([
                'profile_details' => $user,
                'status' => 200,
            ], 200);
        }

        return response()->json([
            'message' => "No profile found",
            'status' => 404,  // Change status code to 404 to indicate "not found"
        ], 404);
    }

    public function edit_profiledata(Request $request)
    {
        $deliveryperson_id = $request->input('delivery_id');
        $name = $request->input('name');
        $pic = $request->input('pic');

        $updateData = ['name' => $name];


        if (isset($pic)) {
            $base64Image = preg_replace('#^data:image/\w+;base64,#i', '', $pic);
            $imageData = base64_decode($base64Image);
            $fileName = time() . '.png'; // You can choose the extension based on your needs
            $filePath = 'delivery_person/' . $fileName;

            file_put_contents(public_path($filePath), $imageData);

            $updateData['pic'] = $filePath;

        }

        $edit_data = Deliveryperson::where('id', $deliveryperson_id)
            ->update($updateData);

        if ($edit_data) {
            return response()->json([
                'message' => "Profile updated successfully",
                'status' => 200,
            ], 200);
        }

        return response()->json([
            'message' => "failed to update profile",
            'status' => 401,
        ], 401);

    }




}
