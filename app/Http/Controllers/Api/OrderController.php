<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use JWTAuth;
use Validator;

class OrderController extends BaseController
{
    public function createOrder(Request $request)
    {
        $token = JWTAuth::parseToken();
        //Try authenticating user       
        $user = $token->authenticate();
        $user_id = $user->id;
        $cash_given = $request->cash_given;
        $cash_return = $request->cash_return;
        $total_price = $request->total_price;
        $date_create = $request->date_create;
        $discount = $request->discount;
        $productArr = json_decode($request->productArr);
        $validator = $this->validation($request);
        if($validator->fails())
        {
            $error = $validator->messages();
            return $this->responseValidate($error);
        }
        else
        {
            $data = Order::create([
                'user_id' => $user_id,
                'date_create' => $date_create,
                'total_price' => $total_price,
                'cash_given' => $cash_given,
                'cash_return' => $cash_return,
                'discount' => $discount
            ]);
            //store order detail 
            $order_id = $data->id;
            if(count($productArr) > 0)
            {
                foreach($productArr as $product)
                {
                    $product_id = $product->id;
                    $quantity = $product->id;
                    $price = $product->price;
                    OrderDetail::create([
                        'order_id' => $order_id,
                        'product_id' => $product_id,    
                        'quantity' => $quantity,
                        'price' => $price
                    ]);
                }
            }
            return $this->responseSuccess($data);          
        }
    }

    public function validation($request)
    {
        $validator = Validator::make($request->all(),[
            'cash_given' => 'required|numeric',
            'cash_return' => 'required|numeric',
            'total_price' => 'required|numeric',
            'date_create' => 'required|date',    
        ]);
        return $validator;
    }
}
