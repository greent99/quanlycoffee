<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Enum\Result;


class BaseController extends Controller
{
    public function __construct()
    {
    }

    /**
     * function response api error 400
     * @param $errors ,$messager
     * @return array
     */
    public function responseError($data = [], $messager = 'Error', $errors = 400)
    {
        $response = [
            'code' => $errors,
            'messages' => $messager,
            'data' => $data
        ];  
        return response()->json($response);
    }

    /**
     * function response messager check params
     * @param $errors ,$messager
     * @return array
     */
    public function responseValidate($errors, $messager = 'Check params')
    {
        $response = [
            'code' => $errors,
            'messages' => $messager
        ];
        return response()->json($response, 400);
    }

    /**
     * function response api success
     * @param $data ,$statu,$messager
     * @return array
     */
    public function responseSuccess( $data = [], $messager = 'Success', $statusCode = 200)
    {
        $response = [
            
            'result_code' => $statusCode,
            'messages' => $messager,
            'data' => $data
        ];
        return response()->json($response);
    }

   
}