<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Mail\VerificationMail;
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
            'full_name' => $request->input('full_name'),
            'password' => Hash::make($request->get('password')),
        ]);
        
			Mail::to($user->email)->send(
                new VerificationMail($user)
            );
 
            $res['success'] = true;
            $res['message'] = "Registration Successful! A Verification Mail has been Sent to $user->email";
            $res['data'] = $user;            

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
		];
		$messages = [
			'required' => ':attribute is required',
			'email' => 'wrong :attribute format',
	];
		$this->validate($request, $rules, $messages);
		}
}
