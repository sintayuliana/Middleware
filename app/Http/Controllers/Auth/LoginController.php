<?php

namespace App\Http\Controllers\Auth;

use JWTAuth;
use App\Http\Controllers\Controller;
use App\Models\XUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;

class LoginController extends Controller
{
    public function authenticate(Request $request)
    {
        $credentials = $request->only('username', 'password');

        $validator = Validator::make($credentials, [
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        $params['username'] = $credentials['username'];
        $params['password'] = $credentials['password'];
        if ($validator->fails()) {
            $response['status'] = 0;
            $response['message'] = $validator->messages();
            $code = 400;
        } else {
        }
        $token = 'JWTAuth'::attempt($credentials);
        $response = array();
        try {
            if ($token == false) {

                $response['status'] = 0;
                $response['message'] = 'User invalid!';
                $code = 406;
            } else {
                XUsers::where('username', $params['username'])
                    ->update(['token' => $token]);
                $userInfo = XUsers::select('user_id', 'username', 'full_name', 'token')->where('username', $request->username)->first();
                session_start();
                $_SESSION['token_app'] = $token;

                $response['status'] = 1;
                $response['message'] = 'Logged in!';
                $response['data'] = $userInfo;
                $code = 200;
            }
        } catch (JWTException $e) {
            $response['status'] = 0;
            $response['message'] = 'Server error!';
            $code = 500;
        }

        return response()->json($response, $code);
    }

    public static function getUserInfo($token){
        $token = substr($token, 7);
        $q = XUsers::select('user_id', 'username', 'token')
                    ->where('token', $token)
                    ->get()
                    ->toArray();
        $xuser = $q;
        return $xuser;
    }


    public function logout(Request $request)
    {
        $validator = Validator::make($request->only('token'), [
            'token' => 'required'
        ]);

        if ($validator->fails()) {
            $response['status'] = 0;
            $response['message'] = $validator->messages();
            $code = 400;
        }

        try {
            'JWTAuth'::invalidate($request->token);

            $response['status'] = 1;
            $response['message'] = 'Logged out!';
            $code = 200;
        } catch (JWTException $exception) {
            $response['status'] = 0;
            $response['message'] = 'Server error!';
            $code = 500;
        }

        return response()->json($response, $code);
    }
}