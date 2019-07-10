<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;

class AuthController extends Controller
{
    /**
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $jwt;

    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required|email|max:255',
            'password' => 'required',
        ]);

        try {

            if (! $token = $this->jwt->attempt($request->only('email', 'password'))) {
                return response()->json(['user not found'], 404);
            }

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token expired'], 500);

        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token invalid'], 500);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token absent' => $e->getMessage()], 500);

        }
        $user = Auth::guard('api')->user();
        if ($user->email_verified_at != null) {

            $msg['verified'] = true;
            $msg['message'] = "You are Logged in!";
            $msg['user'] = $user;
            $msg['token'] = $token;

            
            return response()->json($msg, 200);

         }else{

            $msg['verified'] = false;
            $msg['message'] = "Please Verify Your Account";
            
            return response()->json($msg, 401);
         }    
    }

}