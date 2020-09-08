<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Validator;

class ProductController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Product::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validation = [
            'name' => 'required|unique:product',
            'groupcategory_id' => 'required',
            'price' => 'required|numeric',
            'image' => 'required|image'
        ];
        $validator = $this->validation($request,$validation);
        if($validator->fails())
        {
            $error = $validator->messages();
            return $this->responseValidate($error);
        }
        else
        {
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $path = Storage::putFileAs('images',$file,$filename);
            $data = Product::create([
                'name' => $request->name,
                'price' => $request->price,
                'groupcategory_id' => $request->groupcategory_id,
                'image' => $path,
            ]);
            return $this->responseSuccess($data, "Success",201);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Product::find($id);
        if($data)
            return $this->responseSuccess($data);
        else
            return $this->responseError($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if(!$product)
        {
            return $this->responseError($product);
        }
        else
        {
            $validation = [
                'name' => 'required',
                'groupcategory_id' => 'required',
                'price' => 'required|numeric',
                'image' => 'required|image'
            ];
            $validator = $this->validation($request,$validation);
            if($validator->fails())
            {
                $error = $validator->messages();
                return $this->responseValidate($error);
            }
            else
            {
                $file = $request->file('image');
                $filename = $file->getClientOriginalName();
                $path = Storage::putFileAs('images',$file,$filename);
                Storage::delete($product->image);
                $data = $product->update([
                    'name' => $request->name,
                    'price' => $request->price,
                    'groupcategory_id' => $request->groupcategory_id,
                    'image' => $path,
                ]);
                return $this->responseSuccess($data);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Product::find($id);
        if($data)
        {
            $data = $data->delete();
            return $this->responseSuccess($data);
        }
        else
        {
            return $this->responseError($data);
        }
    }

    public function loadData(Request $request)
    {
        $id = $request->id;
        if(!empty($id) || $id == 0)
        {
            if($request->id > 0)
            {
                $data = Product::where('id','<',$id)->orderBy('id','DESC')->limit(5)->get();
                return $this->responseSuccess($data);
            }
            else
            {
                $data = Product::orderBy('id','DESC')->limit(5)->get();
                return $this->responseSuccess($data);
            }
        }
        return $this->responseSuccess(null);
    }

    public function validation($request,$data)
    {
        $validator = Validator::make($request->all(),$data);
        return $validator;
    }

}
