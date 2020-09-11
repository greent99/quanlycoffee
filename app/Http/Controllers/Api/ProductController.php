<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Validator;
use Image;

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
            'name' => 'required|unique:product|min:3|max:100',
            'groupcategory_id' => 'required|numeric',
            'price' => 'required|numeric|between:1000,10000000',
            'image' => 'required|image'
        ];
        $validator = $this->validation($request, $validation);
        if ($validator->fails()) {
            $error = $validator->messages();
            return $this->responseValidate($error);
        } else {
            $uploadFolder = 'product';
            $image = $request->file('image');

            $image_uploaded_path = $image->store($uploadFolder, 'public');

            $uploadedImageResponse = [
                "image_name" => basename($image_uploaded_path),
                "image_url" => Storage::disk('public')->url($image_uploaded_path),
                "mime" => $image->getClientMimeType()
            ];

            $data = Product::create([
                'name' => $request->name,
                'price' => $request->price,
                'groupcategory_id' => $request->groupcategory_id,
                'image' => $uploadedImageResponse['image_url'],
            ]);
            return $this->responseSuccess($data, "Success", 201);
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
        if ($data) {
            return $this->responseSuccess($data);
        } else {
            return $this->responseError($data, "Product not found", 404);
        }
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
        if (!$product) {
            return $this->responseError($product, "Product not found", 404);
        } else {
            $validation = [
                'name' => 'required|min:3|max:100',
                'groupcategory_id' => 'required',
                'price' => 'required|numeric|between:1000,10000000',
            ];
            $validator = $this->validation($request, $validation);
            if ($validator->fails()) {
                $error = $validator->messages();
                return $this->responseValidate($error);
            } else {
                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    $filename = $file->getClientOriginalName();
                    $status = Storage::putFileAs('public', $file, $filename);
                    $path = public_path().'/storage/'.$filename;
                    Storage::delete($product->image);
                } else {
                    $path = $product->image;
                }
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
        if ($data) {
            $data = $data->delete();
            return $this->responseSuccess($data, "Success", 204);
        } else {
            return $this->responseError($data, "Product not found", 404);
        }
    }

    public function loadData(Request $request)
    {
        $id = $request->id;
        if (!empty($id) || $id == 0) {
            if ($request->id > 0) {
                $data = Product::where('id', '<', $id)->orderBy('id', 'DESC')->limit(5)->get();
                return $this->responseSuccess($data);
            } else {
                $data = Product::orderBy('id', 'DESC')->limit(5)->get();
                return $this->responseSuccess($data);
            }
        }
        return $this->responseSuccess(null);
    }

    public function validation(Request $request, $data)
    {
        $validator = Validator::make($request->all(), $data);
        return $validator;
    }
}
