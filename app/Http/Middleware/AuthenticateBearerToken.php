<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DB;

class AuthenticateBearerToken
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
        $token = $request->bearerToken();
        if (!$token) {
            $response = [
                'status' =>false,
                'statusCode' => 401,
                'message' => 'Missing BearerToken',
                'errors'=>['Unauthorized'=>['Bearer token missing in the request header']]
            ];
            return response()->json($response, 401);
        }
        if($token){
            $token_parts = explode('.', $token);
            $token_header = (!empty($token_parts[1]))?$token_parts[1]:$token_parts[0];
            $token_header_json = base64_decode($token_header);
            $token_header_array = json_decode($token_header_json, true);
            if(!isset($token_header_array['jti'])){
                $response = [
                    'status' =>false,
                    'statusCode' => 401,
                    'message' => 'Invalid BearerToken',
                    'errors'=>['Unauthorized'=>['Invalid bearer token passed in the request header']]
                ];
                return response()->json($response, 401);
            }
            $user_token = $token_header_array['jti'];
            $user = DB::table('oauth_access_tokens')->where('id', $user_token)->first();
            if(empty($user) || !isset($user->user_id))
            {
                $response = [
                    'status' =>false,
                    'statusCode' => 401,
                    'message' => 'Invalid BearerToken',
                    'errors'=>['Unauthorized'=>['Invalid bearer token passed in the request header']]
                ];
                return response()->json($response, 401);
            }
        }

        return $next($request);
    }
}
