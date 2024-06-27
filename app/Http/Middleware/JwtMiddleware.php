<?php

namespace App\Http\Middleware;

use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use JWTAuth;
use Exception;
use Closure;

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
            
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return errorMsgResponse('User not found');
            }
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return errorMsgResponse('Token Expired');

        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return errorMsgResponse('Invalid Token');

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return errorMsgResponse($e->getMessage());
        }catch (Exception $e) {
            
            return errorMsgResponse($e->getMessage());
        }
        return $next($request);
    }
}
