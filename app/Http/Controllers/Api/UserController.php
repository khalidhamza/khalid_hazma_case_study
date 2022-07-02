<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends ApiController
{
    /**
     * register
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $rules      = [            
            'name'              => ['required'],            
            'email'             => ['required', 'email', 'unique:users,email'],
            'password'          => ['required'],
            'confirm_password'  => ['required', 'same:password']
        ];

        $validate   = Validator::make($request->all(), $rules);
        if($validate->fails()){
            $errors     = $validate->errors()->toArray();
            return $this->apiValidationErrors($errors);
        }else{            
            $requestData                = $request->except('password');
            $requestData['password']    = bcrypt($request->password);
            $user       = User::create($requestData);
            $token      = $user->createToken('tokens')->plainTextToken;
            $userInfo   = $this->getUserInfo($token, $user);
            return $this->apiResults(200, 'success', $userInfo);
        }
    }

    /**
     * login
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $rules      = [                        
            'email'     => ['required', 'email'],            
            'password'  => ['required'],            
        ];
        $validate   = Validator::make($request->all(), $rules);
        if($validate->fails()){
            $errors     = $validate->errors()->toArray();
            return $this->returnValidationMsgs($errors);
        }else{
            $credentials    = $request->all(['email', 'password']);
            if (!Auth::attempt($credentials)) {
                return $this->returnErrorMsg(155, 'failed');
            }else{
                $user       = auth()->user();
                $token      = $user->createToken('tokens')->plainTextToken;
                $userInfo   = $this->getUserInfo($token, $user);
                return $this->apiResults(200, 'success', $userInfo);       
            }
        }
    }


    /***** HELPERS *****/
    /**
     * Get User Info
     * 
     * @param string $token
     * @param collection$user
     * 
     * @return array 
     */
    private function getUserInfo($token, $user)
    {
        return [
            'name'          => $user->name,
            'email'         => $user->email,
            'token'         => $token,
            'token_type'    => "Bearer"
        ];
    }
}
