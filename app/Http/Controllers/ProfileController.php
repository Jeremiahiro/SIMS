<?php

namespace App\Http\Controllers;

use App\User;
use Cloudder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller

{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function index(User $user)
    {
        $user = Auth::user();
             
        $res['success'] = true;
        $res['data'] = $user;

        return response()->json($res, 200);
    }

    public function uploadImage(Request $request)
    {
        $this->validate($request, [
            'avater' => 'image|max:4000|required',
            ]);
        $user = Auth::user();
        if ($request->hasFile('avater') && $request->file('avater')->isValid()){
            if ($user->avater != "noimage.jpg") {
                $oldImage = pathinfo($user->avater, PATHINFO_FILENAME);
                try {
                    $delete_old_image = Cloudder::destroyImage($oldImage);
                } catch (Exception $e) {
                    $mes['error'] = "Try Again";
                    return back()->with($mes);
                }
            }
            $user = $request->file('avater');
            $filename = $request->file('avater')->getClientOriginalName();
            $avater = $request->file('avater')->getRealPath();
            Cloudder::upload($avater, null);
            list($width, $height) = getimagesize($avater);
            $avater = Cloudder::show(Cloudder::getPublicId(), ["width" => $width, "height"=>$height]);
            $this->saveImages($request, $avater);

        $res['message'] = "Upload Successful!";  
        $res['image'] = $avater;          
        return response()->json($res, 200); 
        }
    }
    
    public function saveImages(Request $request, $avater)
    {
        $user = Auth::user();
        $user->avater = $avater;
        $user->save();
    }

    public function update(Request $request)
    {
        $user = Auth::user();

    
        $this->validateRequest($request);
        $user->pob = $request->input('pob');
        $user->dob = $request->input('dob');
        $user->lga = $request->input('lga');
        $user->level = $request->input('level');
        $user->state = $request->input('state');
        $user->phone = $request->input('phone');
        $user->gender = $request->input('gender');
        $user->address = $request->input('address');
        if(!empty($request->input('full_name')))
        {
            $user->full_name = $request->input('full_name');
        }
        $user->occupation = $request->input('occupation');
        $user->nationality = $request->input('nationality');
        $user->marital_status = $request->input('marital_status');

        if(!empty($request->input('password')))
        {
            $user->password = Hash::make($request->input('password'));
        }
       
        $user->save();

		$res['message'] = "Kudos! Profile updated successfully!";        
        $res['data'] = $user;

        return response()->json($res, 200); 
    }

    public function validateRequest(Request $request)
    {
       $id = Auth::id();
       $rules = [
        'dob' => 'date',
        'pob' => 'string',
        'lga' => 'string',
        'state' => 'string',
        'level' => 'string',
        'gender' => 'string',
        'address' => 'string',
        'nationality' => 'string',
        'occupation' => 'string',
        'marital_status' => 'string',
        'full_name' => 'sometimes|string',
        'phone' => 'required|phone:AUTO,US',
        'password' => 'nullable|min:6|different:current_password|confirmed',
        ];
        $messages = [
            'required' => ':attribute is required',
            'phone' => ':attribute number is invalid'
        ];
        
        $this->validate($request, $rules);
    }
}