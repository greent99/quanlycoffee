<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterFormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use App\User;
use Validator;

class AuthController extends BaseController
{
    public function register(Request $request)
    {
        
        $validator = $this->validation($request);
        if($validator->fails())
        {
            $error = $validator->messages();
            return $this->responseValidate($error);
        }
        $data = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
        ]);
        $token = JWTAuth::fromUser($data);
        $data->token = $token;
        return $this->responseSuccess($data, "Success",201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if($validator->fails())
        {
            $error = $validator->messages();
            return $this->responseValidate($error);
        }
       
        $credentials = $request->only('email', 'password');

        if (!($token = $this->guard()->attempt($credentials))) {
            return response()->json([
                'status' => false,
                'msg' => 'Email or password is not correct!!',
            ], Response::HTTP_BAD_REQUEST);
        }
        $email = $request->email;
        $role = User::where('email',$email)->get('role_id');
        $expires_at = $this->guard()->factory()->getTTL() * 60;
        $data = [
            'email' => $email,
            'role' => $role,
            'expires_at' => $expires_at
        ];
        return response()->json(['token' => $token, 'data'=> $data], Response::HTTP_OK);
    }

    public function user(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            return response($user, Response::HTTP_OK);
        }
        return response(null, Response::HTTP_BAD_REQUEST);
    }

    /**
     * Log out
     * Invalidate the token, so user cannot use it anymore
     * They have to relogin to get a new token
     *
     * @param Request $request
     */
    public function logout(Request $request) {
        $this->guard()->logout();
        return $this->responseSuccess(null,'You have successfully logged out.',200);
    }

    public function refresh()
    {
        return response(JWTAuth::getToken(), Response::HTTP_OK);
    }

    public function validation(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|unique:users|min:3|max:100',
            'email' => 'required|email|unique:users|min:8|max:100',
            'password' => 'required|min:8|max:100'
        ]);
        return $validator;
    }

    public function guard()
    {
        return Auth::guard();
    }
}
