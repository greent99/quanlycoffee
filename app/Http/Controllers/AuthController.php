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
        return $this->responseSuccess($data);
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

        if (!($token = JWTAuth::attempt($credentials))) {
            return response()->json([
                'status' => false,
                'msg' => 'Email or password is not correct!!',
            ], Response::HTTP_BAD_REQUEST);
        }
        $email = $request->email;
        $role = User::where('email',$email)->get('role_id');
        $data = [
            'email' => $email,
            'role' => $role
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
        //$this->validate($request, ['token' => 'required']);
        $validator = Validator::make($request->all(),[
            'token' => 'required'
        ]);
        try {
            JWTAuth::invalidate($request->input('token'));
            //return response()->json('', Response::HTTP_OK);
            return $this->responseSuccess(null,'You have successfully logged out.');
        } catch (JWTException $e) {
            return $this->responseError(null,'Failed to logout, please try again.');
            //return response()->json('Failed to logout, please try again.', Response::HTTP_BAD_REQUEST);
        }
    }

    public function refresh()
    {
        return response(JWTAuth::getToken(), Response::HTTP_OK);
    }

    public function validation(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);
        return $validator;
    }

    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }

}
