<?php
namespace App\Http\Middleware;
use Closure;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
class JwtMiddleware
{
    public function handle($request, Closure $next, $guard = null)
    {

// todo: move somewhere else
$authServicePublicKey = <<<EOD
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAxXI+v5vHRDrsFL1t640E
bo4vxWgfwXDzS6gp13+ANZEHwfOnjRQRMQODVF/dUjqcGL5RtNaGWvmnoPJx0Ryl
9Uys4l0TzXruQAZJ9+Fmxo0Q2+UIUmqEGqtsq0HCXwYhlgVWN6WeMbTW0rnNKps1
PuCNCnrI011btdRY1l9oYEMOZ/GK5SHRilfjRu5ji3gL2OPWfHW/K0hiy/QWA/eX
rz2TOBxosHOaxxi93UwMb2IStRDtr+0aspDO/M0DvgO0XcC3u6JZ4C25aDmq7zW7
xLcQKEo8/IgBrFnWjTzDITI4je0bY8gotK2SIn631wFuBADWxrW9IB4baKNF3F62
+wIDAQAB
-----END PUBLIC KEY-----
EOD;

				//print_r($request->headers);
				$token = str_replace('Bearer ', '', $request->header('Authorization'));
        
        if(!$token) {
            // Unauthorized response if token not there
            return response()->json([
                'error' => 'Token not provided.'
            ], 401);
        }
        try {

					$credentials = JWT::decode($token, $authServicePublicKey, array('RS256'));

        } catch(ExpiredException $e) {
            return response()->json([
                'error' => 'Provided token is expired.'
            ], 400);
        } catch(Exception $e) {
            return response()->json([
                'error' => 'An error while decoding token.'
            ], 400);
        }
        // Now let's put the user in the request class so that you can grab it from there
        $request->auth = $credentials;
        return $next($request);
    }
}
