<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\XUsers;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class RegisterController extends Controller
{
    public function create(Request $request)
    {
        $body = $request->only(
            'username',
            'password',
            'full_name',
            'email',
            'phone_number'
        );

        $response = array();
        $validator = Validator::make($body, [
            'username' => 'required|string|min:6|max:18',
            'password' => 'required|string|min:6|max:50',
            'full_name' => 'required|string',
            'email' => 'required|email',
            'phone_number' => 'required'
        ]);

        if ($validator->fails()) {
            $response['status'] = 0;
            $response['message'] = 'Please fill the form!';
            $code = 400;
        } else {
            DB::beginTransaction();
            $params['user_id'] = Uuid::uuid4()->toString();
            $params['username'] = $body['username'];
            $params['password'] = bcrypt($body['password']);
            $params['full_name'] = $body['full_name'];
            $params['email'] = $body['email'];
            $params['phone_number'] = $body['phone_number'];
            $params['token'] = null;
            $params['is_login'] = false;

            $qSelectZ = XUsers::select('username')->where('username', $params['username'])->get();
            if ($qSelectZ->count() > 0) {
                $response['status'] = 0;
                $response['message'] = 'Username is already exists!';
                $code = 400;
            } else {
                try {
                    XUsers::create($params);
                    $response['status'] = 1;
                    $response['message'] = 'User created!';
                    $code = 200;
                } catch (Exception $e) {
                    $response['status'] = 0;
                    $response['message'] = 'Server Error!';
                    $code = 500;
                }
            }
        }

        if($response['status'] == 1){
            DB::commit();
        }else{
            DB::rollBack();
        }

        return response()->json($response, $code);

    }
}