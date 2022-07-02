<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class CartController extends ApiController
{
    public function create(Request $request)
    {
        $rules      = [                        
            'product_id'    => ['required', 'integer', 'exists:products,id'],
            'quantity'      => ['required', 'integer', 'min:1'],
        ];
        $validate   = Validator::make($request->all(), $rules);
        if($validate->fails()){
            $errors     = $validate->errors()->toArray();
            return $this->apiValidationErrors($errors);
        }else{
            
            $cart   = Cart::where([[$this->getUserIdentification()]])
                            ->where('product_id', $request->product_id)
                            ->first();
            if(! empty($cart)){
                $quantity   = $request->quantity + $cart->quantity;
                $cart->update(['quantity' => $quantity]);
            }else{
                $requestData = array_merge($request->all(), $this->getUserIdentification());
                Cart::create($requestData);
            }

            return $this->apiResults(200, 'product_added_to_cart');
        }
    }

    
    
    public function get(Request $request)
    {
        $carts  = Cart::with('product')->where([[$this->getUserIdentification()]])->get();
        return $this->apiResults(200, 'success', $carts);
    }




    public function update($id, Request $request)
    {
        $rules      = ['quantity' => ['required', 'integer', 'min:1']];
        $validate   = Validator::make($request->all(), $rules);
        if($validate->fails()){
            $errors     = $validate->errors()->toArray();
            return $this->apiValidationErrors($errors);
        }else{
            
            $cart   = Cart::find($id);
            if(! empty($cart)){
                $cart->update(['quantity' => $request->quantity]);
                return $this->apiResults(200, 'cart_updated');
            }else{
                return $this->apiResults(404, 'cart_not_found');
            }
        }
    }

    public function delete($id)
    {
        $cart   = Cart::find($id);
        if(! empty($cart)){
            $cart->delete();
            return $this->apiResults(200, 'cart_deleted');
        }else{
            return $this->apiResults(404, 'cart_not_found');
        }
    }

    
    
    
    
    /***
     * Get User Identification
     * 
     * @return array
     */
    private function getUserIdentification()
    {
        $bearerToken    = request()->bearerToken();
        $accessToken    = PersonalAccessToken::findToken($bearerToken);
        $user           = isset($accessToken) ? $accessToken->tokenable : null;
        if($user != null){
            return ['user_id' => $user->id];
        }else{
            return ['session_id' => request()->session_id];
        }
    }
}
