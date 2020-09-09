<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Models\GroupCategory;
use Validator;

class GroupCategoryController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return GroupCategory::all();
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
            $data = GroupCategory::create([
                'name' => $request->name,
                'category_id' => $request->category_id
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
    public function show(Request $request, $id)
    {
        $groupcategory = GroupCategory::find($id);
        if($groupcategory)
        {
            $data = $groupcategory->getProduct()->get();
            return $this->responseSuccess($data);
        }
        return $this->responseError(null,'Group not found',404);  
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = GroupCategory::find($id);
        if($data)
        {
            $data = $data->delete();
            return $this->responseSuccess($data);
        }
        else
        {
            return $this->responseError($data,"Group not found",404);
        }
    }

    public function validation($request, $data)
    {
        $validator = Validator::make($request->all(),$data);
        return $validator;
    }
}

