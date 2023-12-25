<?php

namespace App\Http\Controllers\Orders;

use App\Exports\OrdersExport;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Controller;
use App\Models\OrderDetail;
use App\Models\Orders;
use App\Models\Products;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use Maatwebsite\Excel\Facades\Excel;

class OrdersController extends Controller
{
    public function create(Request $request)
    {
        $token = $request->header('Authorization');
        $user_info = LoginController::getUserInfo($token);
        $user_id = $user_info[0]['user_id'];

        $body = $request->only(
            'order_detail'
        );

        $validator = Validator::make($body, [
            'order_detail' => 'required',
        ]);

        if ($validator->fails()) {
            $response['status'] = 0;
            $response['message'] = $validator->messages();
            $code = 400;
        } else {
            $params['order_id'] = Uuid::uuid4()->toString();
            $countDtl = count($body['order_detail']);
            $total = 0;
            for ($i = 0; $i < $countDtl; $i++) {
                $paramsDtl[$i]['order_id'] = $params['order_id'];
                $paramsDtl[$i]['product_id'] = $body['order_detail'][$i]['product_id'];
                $paramsDtl[$i]['price'] = Products::select('price')->where('product_id', $paramsDtl[$i]['product_id'])->get()->toArray()[0]['price'];
                $paramsDtl[$i]['qty'] = $body['order_detail'][$i]['qty'];
                $paramsDtl[$i]['subtotal'] = $paramsDtl[$i]['price'] * $paramsDtl[$i]['qty'];
                $paramsDtl[$i]['created_at'] = date('Y-m-d H:i:s');
                $paramsDtl[$i]['updated_at'] = date('Y-m-d H:i:s');
                $total += $paramsDtl[$i]['subtotal'];
            }
            $params['order_date'] = date('Y-m-d H:i:s');
            $params['order_detail'] = $paramsDtl;
            $params['total'] = $total;
            $params['user_id'] = $user_id;
            $params['status'] = 0;

            DB::beginTransaction();
            try {
                Orders::create($params);
                if ($countDtl == 0) {
                    $response['status'] = 0;
                    $response['message'] = 'Please fill order detail!';
                    $code = 400;
                } else {
                    'OrderDetail'::insert($paramsDtl);
                    $response['status'] = 1;
                    $response['message'] = 'Order created!';
                    $response['order_id'] = $params['order_id'];
                    $code = 200;
                }
            } catch (Exception $e) {
                $response['status'] = 0;
                $response['message'] = 'Server Error!';
                $code = 500;
            }
        }

        if ($response['status'] == 1) {
            DB::commit();
        } else {
            DB::rollBack();
        }

        return response()->json($response, 200);
    }

    public function read(Request $request)
    {
        $token = $request->header('Authorization');
        $user_info = LoginController::getUserInfo($token);
        $user_id = $user_info[0]['user_id'];
        $response = array();
        $order_id = $request->order_id;

        try {
            $order = Orders::select('order_id', 'order_date', 'total', 'status');
            if ($order_id == null) {
                $order = $order->where('user_id', $user_id)->get()->toArray();
            } else {
                $order = $order->where('user_id', $user_id)->where('order_id', $order_id)->get()->toArray();
            }

            if (count($order) == null) {
                $response['status'] = 0;
                $response['message'] = 'Order not found!';
                $code = 200;
            } else {
                $order_detail = 'OrderDetail'::select('product_id', 'price', 'qty', 'subtotal')->get()->toArray();
                $response['status'] = 1;
                $response['message'] = 'Order found!';
                $response['data'] = $order;
                $response['data']['order_detail'] = $order_detail;
                $code = 200;
            }
        } catch (Exception $e) {
            $response['status'] = 0;
            $response['message'] = 'Server Error!';
            $code = 500;
        }


        return response()->json($response, $code);
    }

    public function delete(Request $request)
    {
        $token = $request->header('Authorization');
        $user_info = LoginController::getUserInfo($token);
        $user_id = $user_info[0]['user_id'];
        $response = array();
        $order_id = $request->order_id;

        try {
            $order = Orders::select('order_id')->get();
            if ($order->count() == null) {
                $response['status'] = 0;
                $response['message'] = 'Order not found!';
                $code = 200;
            } else {
                $params['status'] = 2;
                Orders::where('order_id', $order_id)->where('user_id', $user_id)->update($params);
                $response['status'] = 1;
                $response['message'] = 'Order cancelled!';
                $response['order_id'] = $order->toArray()[0]['order_id'];
                $code = 200;
            }
        } catch (Exception $e) {
            $response['status'] = 0;
            $response['message'] = 'Server Error!';
            $code = 500;
        }

        if ($response['status'] == 1) {
            DB::commit();
        } else {
            DB::rollBack();
        }

        return response()->json($response, $code);
    }

    public function export(Request $request)
    {
        $token = $request->header('Authorization');
        $user_info = LoginController::getUserInfo($token);
        $user_id = $user_info[0]['user_id'];
        $response = array();
        $order_id = $request->order_id;

        try {
            $order = Orders::select('order_id')->get();
            if ($order->count() == null) {
                $response['status'] = 0;
                $response['message'] = 'Order not found!';
                $code = 200;
            } else {
                $params['status'] = 2;
                Orders::where('order_id', $order_id)->where('user_id', $user_id)->update($params);
                $response['status'] = 1;
                $response['message'] = 'Order cancelled!';
                $response['order_id'] = $order->toArray()[0]['order_id'];
                $code = 200;
            }
            return collect($response);
        } catch (Exception $e) {
            $response['status'] = 0;
            $response['message'] = 'Server Error!';
            $code = 500;
        }

        return response()->json($response, $code);
    }
}