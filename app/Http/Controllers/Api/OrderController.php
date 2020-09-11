<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderDetail;
use Carbon\Carbon;
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
            'total_price' => 'required|numeric',
        ];
        $validator = $this->validation($request,$validation);
        if($validator->fails())
        {
            $error = $validator->messages();
            return $this->responseValidate($error);
        }
        else
        {
            $date_create = Carbon::now();
            $data = Order::create([
                'user_id' => $user_id,
                'date_create' => $date_create,
                'total_price' => $total_price,
                'cash_given' => 0,
                'cash_return' => 0,
                'discount' => 0
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
            $orderdetails = $order->getOrderDetail();
            $data = [];
            foreach($orderdetails as $orderdetail)
            {
                $product_id = $orderdetail->product_id;
                $product = Product::find($product_id);
                $object = [
                    'id' => $orderdetail->id,
                    'quantity' => $orderdetail->quantity,
                    'price' => $orderdetail->price,
                    'order_id' => $orderdetail->order_id,
                    'product' => $product,
                    'created_at' => $orderdetail->created_at,
                    'updated_at' => $orderdetail->updated_at,
                ];
                array_push($data,$object);
            }
            return $this->responseSuccess($data);
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
                'total_price' => 'required|numeric',
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
                $data = $order->update([
                    'total_price' => $request->total_price
                ]);
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
            return $this->responseSuccess($data,"Success",204);
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
