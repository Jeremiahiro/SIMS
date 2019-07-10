<?php

namespace App\Http\Controllers;

use App\User;
use App\Mail\VerificationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
     /**
     * Register new user
     *
     * @param $request Request
     */
    public function register(Request $request)
    {
        $this->validateRequest($request);
 
        $token = (str_random(6));
        $default_avater = 'https://res.cloudinary.com/iro/image/upload/v1552487696/Backtick/noimage.png';
		//start temporay transaction
        DB::beginTransaction();

        try {
        
        $user = User::create([
            'token' => $token,
            'avater' => $default_avater,
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'gender' => $request->input('gender'),
            'full_name' => $request->input('full_name'),
            'password' => Hash::make($request->get('password')),
        ]);
        
			Mail::to($user->email)->send(
                new VerificationMail($user)
            );
 
            $res['success'] = true;
            $res['data'] = $user;
			$res['message'] = "Registration Successful! A Verification Mail has been Sent to $user->email";

            DB::commit();

            return response()->json($res, 201);

        }catch(\Exception $e) {
            //if any operation fails, Thanos snaps finger - user was not created
            DB::rollBack();

            $msg['error'] = "Oops! Something went wrong, Try Again!";

            return response()->json($msg, 422);   
        }
    }

    public function validateRequest(Request $request){
		$rules = [
			'full_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'phone' => 'required|phone:AUTO,US',
            'gender' => 'required'
		];
		$messages = [
			'required' => ':attribute is required',
			'email' => 'wrong :attribute format',
			'phone' => 'invalid :attribute number',
	];
		$this->validate($request, $rules, $messages);
		}
}
