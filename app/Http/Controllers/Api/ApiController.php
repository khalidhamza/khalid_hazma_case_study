<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    use ApiResponse;

    public function fallbackRoute()
    {
        return $this->apiResults(110, 'api_not_found');
    }

    /**
     * Make Directory
     * 
     * @param string $path
     * @return void
     */
    function makeDirectory($path)
    {
        if(! file_exists($path)){
            mkdir($path, 777);
        }
    }
}
