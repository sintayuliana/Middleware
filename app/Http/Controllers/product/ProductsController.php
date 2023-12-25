<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Models\Products;
use Exception;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function create(Request $request)
    {
    }

    public function read(Request $request)
    {
        $response = array();
        $product_name = $request->product_name;
        try {
            $query = Products::select('product_id', 'product_name', 'product_type.product_type_name', 'price')
                ->join('product_type', 'products.product_type_id', 'product_type.product_type_id')
                ->where('product_name', 'like', '%' . $product_name . '%')
                ->orderBy('product_id', 'ASC')
                ->get();
            $count = $query->count();
            if ($count == 0) {
                $response['status'] = 0;
                $response['message'] = 'Products not found!';
                $response['count'] = $count;
                $code = 200;
            } else {
                $response['status'] = 0;
                $response['message'] = 'Products found!';
                $response['count'] = $count;
                $response['data'] = $query;
                $code = 200;
            }
        } catch (Exception $e) {
            $response['status'] = 0;
            $response['message'] = 'Server Error!';
            $code = 500;
        }
        return response()->json($response, $code);
    }

    public function update(Request $request)
    {
    }

    public function delete(Request $request)
    {
    }
}