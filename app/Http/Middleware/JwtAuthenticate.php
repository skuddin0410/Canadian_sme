<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class JwtAuthenticate
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // Parse and validate the token
            $token = JWTAuth::parseToken();
            $token->getPayload(); // This will throw TokenExpiredException if the token is expired

            // Authenticate the user
            JWTAuth::authenticate();
        } catch (TokenExpiredException $e) {
            return $this->unauthenticatedResponse($request, 'Token has expired.');
        } catch (JWTException $e) {
            return $this->unauthenticatedResponse($request, 'Token is invalid.');
        }

        return $next($request);
    }

    /**
     * Handle unauthenticated responses.
     */
    private function unauthenticatedResponse(Request $request, $message)
    {
        if ($this->isApiRequest($request)) {
            // Return JSON response for API requests
            return response()->json([
                'success' => false,
                'message' => $message ?? 'Unauthorized',
                'data' => null,
            ], 401);
        }

        // Redirect to login for web requests
        return redirect()
            ->route('login')
            ->withErrors([
                'auth' => $message ?? 'Unauthorized',
            ]);
    }

    /**
     * Check if the request is for an API route.
     */
    private function isApiRequest(Request $request)
    {
        // For API requests, check route prefix or explicitly JSON requests
        return $request->is('api/*') || $request->expectsJson();
    }
}
