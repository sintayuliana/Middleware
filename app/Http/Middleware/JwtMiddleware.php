<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Exception;
use JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $user = 'JWTAuth'::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                $response['status'] = 0;
                $response['message'] = 'Token is invalid!';
                $code = 401;
                return response()->json($response, $code);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                $response['status'] = 0;
                $response['message'] = 'Token hasn been expired!';
                $code = 401;
                return response()->json($response, $code);
            } else {
                $response['status'] = 0;
                $response['message'] = 'Authorization not found!';
                $code = 401;
                return response()->json($response, $code);
            }
        }
        return $next($request);
    }
}