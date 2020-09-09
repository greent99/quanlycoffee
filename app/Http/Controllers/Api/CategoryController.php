<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\GroupCategory;
use Validator;

class CategoryController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         return Category::all();
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
            'name' => 'required|unique:category|min:3|max:100'
        ];
        $validator = $this->validation($request,$validation);
        if($validator->fails())
        {
            $error = $validator->messages();
            return $this->responseValidate($error);
        }
        else
        {
            $data = Category::create($request->all());
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
        if(!empty($id))
        {
            $category = Category::find($id);
            
            if(!empty($category))
            {
                $data = $category->getGroupCategory()->get();
                return $this->responseSuccess($data);
            }
            return $this->responseError($category,'Category not found',404);
        }
        return $this->responseError(null);
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
        $category = Category::find($id);
        if(!$category)
        {
            return $this->responseError($category,'Category not found',404);
        }
        else
        {
            $validation = [
                'name' => 'required|min:3|max:100'
            ];
            $validator = $this->validation($request,$validation);
            if($validator->fails())
            {
                $error = $validator->messages();
                return $this->responseValidate($error);
            }
            else
            {
                $data = $category->update($request->all());
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
        $data = Category::find($id);
        if($data)
        {
            $data = $data->delete();
            return $this->responseSuccess($data);
        }
        else
        {
            return $this->responseError($data,'Category not found',404);
        }
    }

    public function validation($request, $data)
    {
        $validator = Validator::make($request->all(),$data);
        return $validator;
    }
}
