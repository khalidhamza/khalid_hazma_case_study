<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use GuzzleHttp\Handler\Proxy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ProductController extends ApiController
{
    public function create(Request $request)
    {
        $rules      = [                        
            'category_id'       => ['required', 'integer', 'exists:categories,id'],
            'name'              => ['required'],
            'description'       => ['required'],
            'price'             => ['required', 'numeric'],
            'avatar'            => ['nullable', 'image'],
            'developer_email'   => ['nullable']
        ];
        $validate   = Validator::make($request->all(), $rules);
        if($validate->fails()){
            $errors     = $validate->errors()->toArray();
            return $this->apiValidationErrors($errors);
        }else{
            $path   = public_path("products");
            $this->makeDirectory($path);

            $imageName  = null;
            if($request->hasFile('avatar')){
                $avatarFile     = $request->file('avatar');
                $imageName       = $avatarFile->hashName();
                $avatarFile->move($path, $imageName);
            }

            $requestData    = $request->except('api_key');
            $requestData['avatar'] = $imageName;

            Product::create($requestData);

            return $this->apiResults(200, 'product_added');
        }
    }


    public function get()
    {
        $products = Product::with('category')->ApiSelect()->get();
        return $this->apiResults(200, 'success', $products);
    }


    public function details($id)
    {
        $product    = Product::with('category')->ApiSelect()->find($id);
        return $this->apiResults(200, 'success', $product);
    }

    public function update($id, Request $request)
    {
        $rules      = [                        
            'category_id'       => ['required', 'integer', 'exists:categories,id'],
            'name'              => ['required'],
            'description'       => ['required'],
            'price'             => ['required', 'numeric'],
            'avatar'            => ['nullable', 'image'],
            'developer_email'   => ['nullable']
        ];
        $validate   = Validator::make($request->all(), $rules);
        if($validate->fails()){
            $errors     = $validate->errors()->toArray();
            return $this->apiValidationErrors($errors);
        }else{
            $path       = public_path("products");
            $product    = Product::find($id);
            if ($product) {
                $imageName  = $product->avatar;

                // delete existing image
                if($request->hasFile('image')){
                    $delFile    = "{$path}/{$imageName}";
                    File::delete($delFile);
                    $imageName  = null;        
                }

                // update the image
                if($request->hasFile('avatar')){
                    $avatarFile     = $request->file('avatar');
                    $imageName       = $avatarFile->hashName();
                    $avatarFile->move($path, $imageName);
                }

                $requestData    = $request->except('api_key');
                $requestData['avatar'] = $imageName;

                $product->update($requestData);

                return $this->apiResults(200, 'product_updated');
            }else{
                return $this->apiResults(404, 'product_not_found');
            }
        }
    }
}
