<?php
namespace App\Http\Middleware;
use Closure;
use Exception;
use App\User;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
class JwtMiddleware
{
    public function handle($request, Closure $next, $guard = null)
    {
        $api_token = $request->header('x-auth-token');
        
        if(!$api_token) {
            $data =[
                    "error"=> true,
                    "message"=> "Token not provided.",
                    "data"=> [],
                    "code"=> 404
                ];
            return $data;

        }
        try {
             $credentials = JWT::decode($api_token, env('JWT_SECRET'), ['HS256']);
             $user = User::find($credentials->sub);
            if (count($user) > 0)
            {
                $data =[
                    "error"=> false,
                    "message"=> "Success",
                    "data"=> $user,
                    "code"=> 200
                ];
            }else{
                $data =[
                    "error"=> true,
                    "message"=> "UN Success",
                    "data"=> [],
                    "code"=> 404
                ];
            }
            return $data;

        } catch(ExpiredException $e) {

             $data =[
                    "error"=> true,
                    "message"=> "Provided token is expired.",
                    "data"=> [],
                    "code"=> 404
                ];
            return $data;
        } catch(Exception $e) {

             $data =[
                    "error"=> true,
                    "message"=> "Error",
                    "data"=> [],
                    "code"=> 404
                ];
            return $data;
        }     

    }
}
