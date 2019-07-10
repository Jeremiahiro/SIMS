<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Mail\PasswordRecoveryMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class VerificationController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
	public function verify(Request $request)
    {
        $this->validate($request, [
            'token' => 'required|max:6'
        ]);

        $token = $request->input('token');
        $checkCode = User::where('token', $token)->exists();

        if ($checkCode) {
        $user = User::where('token', $token)->first();

            if ($user->email_verified_at == null){
                $user->email_verified_at = date("Y-m-d H:i:s");
                $user->token = null;
                $user->save();
                
                $msg["success"] = "Account verified succefully.";
                $msg['verified'] = True;
                return response()->json($msg, 200);

            } else {
                $msg["status"] = "Oops! Account verified already. Please Login";
                $msg['verified'] = True;

                return response()->json($msg, 200);
             }
        } else{
            $msg["message"] = "Account with code does not exist!";
            return response()->json($msg, 409);
        }    
    }
    
    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function recovery(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);

        $pass = substr(md5(time()), 0, 6);

        $email = $request->input('email');
        $user = User::where('email', $email)->first();

        if (!$user){
            return response()->json(['data' =>['success' => false, 'message' => 'User Not Found']], 404);
        }

        DB::beginTransaction();

            try{
                $user->token = $pass;
                $user->save();

                Mail::to($user->email)->send(new PasswordRecoveryMail($user));
    
                $res['success'] = true;
                $res['message'] = "A Password recovery code has been sent to $user->email";

                DB::commit();

                return response()->json($res, 201);

            }catch(\Exception $e) {
                //if any operation fails, Thanos snaps finger - user was not created
                DB::rollBack();
                $res['error'] = "Oops! Something went wrong, Try Again!";
                return response()->json($res, 422);   
            }
    }

    public function reset(Request $request)
    {

        $this->validate($request, [
        	'token' => 'required|max:6',
        	'password' => 'required|confirmed|min:8',
        ]);

        $token = $request->input('token');
        $password = Hash::make($request->get('password'));
        $user = User::where('token', $token)->first();

        if ($user) {

            try{

                $user->password = $password;
                $user->token = null;
                $user->save();

                $res['success'] = true;
                $res['message'] = "Password Updated Successfully!";

                return response()->json($res, 201);
                
            } catch (Exception $e) {

                $res['success'] = false;
                $res['message'] = "Oops! Something went wrong. Please try again";

                return response()->json($res, 201);
            }

        } else{
            $msg["message"] = "Invalid Recovery Code!";
            return response()->json($msg, 409);
        }   
    }
}
