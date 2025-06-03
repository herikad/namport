<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {

        if($request->is('api/*'))
        {

            throw new HttpResponseException(response()->json(['status' => 0, 'message' => trans('validation.required', [ 'attribute' => 'Authorization'])], 401));  
            
            if(!$request->hasHeader('Authorization')){

                $response = array('status' => 0, 'message' => trans('validation.required', [ 'attribute' => 'Authorization']));
                return response()->json($response, 200);
            }
    
            throw new HttpResponseException(response()->json(['status' => 0, 'message' => trans('pages.token_expired')], 401)); 
        }

        if (! $request->expectsJson()) {
          return $request->expectsJson() ? null : route('login');
        }

    }
}
