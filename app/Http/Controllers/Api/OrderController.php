<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderDetail;
use DB;
use Carbon\Carbon;
use JWTAuth;
use Validator;

class OrderController extends BaseController
{

    public function all(Request $request)
    {
        return Order::all();
    }

    public function index(Request $request)
    {
        $order = Order::paginate(12);
        return $this->responseSuccess($order);
    }

    public function createOrder(Request $request)
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
            DB::transaction(function() use ($request,&$data){
                $token = JWTAuth::parseToken();
                //Try authenticating user       
                $user = $token->authenticate();
                $user_id = $user->id;
                $total_price = $request->total_price;
                $productArr = $request->productArr;
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
                        $item = Product::find($product_id);
                        $product_name = $item->name;
                        $quantity = $product['quantity'];
                        $price = $product['price'];
                        OrderDetail::create([
                            'order_id' => $order_id,
                            'product_name' => $product_name,    
                            'quantity' => $quantity,
                            'price' => $price
                        ]);
                    }
                }
                return $data;     
            });
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
            $orderdetails = $order->order_detail();
            return $this->responseSuccess($orderdetails);
        }
        else    
            return $this->responseError($order,"Order not found",404);
    }

    public function update(Request $request,$id)
    {
        $order = Order::find($id);
        if(empty($order))
        {
            return $this->responseError($order,"Order not found", 404);
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
