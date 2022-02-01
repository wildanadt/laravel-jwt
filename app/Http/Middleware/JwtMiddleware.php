<?php

namespace App\Http\Middleware;

use Tymon\JWTAuth\Facades\JWTAuth;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use MyHelper;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {

                return MyHelper::responseAPI(false, 'Token is Invalid', [], Response::HTTP_UNAUTHORIZED);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return MyHelper::responseAPI(false, 'Token is Expired', [], Response::HTTP_UNAUTHORIZED);
            } else {
                return MyHelper::responseAPI(false, 'Authorization Token not found', [], Response::HTTP_UNAUTHORIZED);
            }
        }
        return $next($request);
    }
}
