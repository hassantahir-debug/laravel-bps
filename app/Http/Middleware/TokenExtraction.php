<?php


namespace App\Http\Middleware;

ob_start();

use Closure;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class TokenExtraction
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->cookie('token');
        $token = Cookie::get('token');
        if (!$token) {
            return response()->json(['error' => 'Token not found'], 401);
        }

        try {
            $secretKey = env('JWT_SECRET');
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
            $request->attributes->add(['authenticated_user' => $decoded]);

            return $next($request);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Invalid or expired token',
                'message' => $e->getMessage()
            ], 401);
        }
    }
}
