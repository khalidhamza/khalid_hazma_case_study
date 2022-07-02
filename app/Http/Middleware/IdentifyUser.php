<?php

namespace App\Http\Middleware;

use App\Http\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class IdentifyUser
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $userIdentified = false;
        $message        = null;

        if(! $request->has('session_id')){
            $bearerToken    = request()->bearerToken();
            if($bearerToken != null){
                $token  = PersonalAccessToken::findToken($bearerToken);
                $user   = isset($token) ? $token->tokenable : null;
                if($user != null){
                    $userIdentified = true;
                }else{
                    $message = 'wrong_access_token';
                }
            }else{
                $message = 'session_id_is_required';
            }
        }else{
            $userIdentified = true;
        }
        
        if (! $userIdentified) {
            return $this->apiResults(0, $message);
        }
        
        return $next($request);
    }
}
