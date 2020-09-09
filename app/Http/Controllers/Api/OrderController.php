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

    public function index(Request $request)
    {
        return Order::all();
    }

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
        $productArr = $request->productArr;
        $validation = [
            'cash_given' => 'required|numeric',
            'cash_return' => 'required|numeric',
            'total_price' => 'required|numeric',
            'date_create' => 'required|date',  
            'discount' => "required|numeric" 
        ];
        $validator = $this->validation($request,$validation);
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
                    $product_id = $product['product_id'];
                    $quantity = $product['quantity'];
                    $price = $product['price'];
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

    public function show($id)
    {
        $order = Order::find($id);
        if($order)
            return $this->responseSuccess($order);
        else
            return $this->responseError($order,"Order not found",404);
    }

    public function getOrderDetail($id)
    {
        $order = Order::find($id);
        if($order)
        {
            $orderdetail = $order->getOrderDetail();
            return $this->responseSuccess($orderdetail);
        }
        else    
            return $this->responseError($order,"Order not found",404);
    }

    public function update(Request $request,$id)
    {
        $order = Order::find($id);
        if(!$order)
        {
            return $this->responseError($order);
        }
        else
        {
            $validation = [
                'cash_given' => 'required|numeric',
                'cash_return' => 'required|numeric',
                'total_price' => 'required|numeric',
                'date_create' => 'required|date',  
                'discount' => "required|numeric" 
            ];
            $validator = $this->validation($request,$validation);
            if($validator->fails())
            {
                $error = $validator->messages();
                return $this->responseValidate($error);
            }
            else
            {
                $request->user_id = $order->user_id;
                $data = $order->update($request->all());
                return $this->responseSuccess($data);
            }
        }
    }

    public function destroy($id)
    {
        $data = Order::find($id);
        if($data)
        {
            $data = $data->delete();
            return $this->responseSuccess($data);
        }
        else
        {
            return $this->responseError($data,"Order not found",404);
        }
    }

    public function validation($request, $data)
    {
        $validator = Validator::make($request->all(),$data);
        return $validator;
    }
}
