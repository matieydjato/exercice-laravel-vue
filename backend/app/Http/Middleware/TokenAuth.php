<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TokenAuth
{
  public function handle(Request $request, Closure $next): Response
  {
    $token = $request->bearerToken();
    $expectedToken = env('API_TOKEN', 'test-token-latactik-2024');

    if ($token !== $expectedToken) {
      return response()->json([
        'errors' => [[
          'status' => '401',
          'title' => 'Unauthorized',
          'detail' => 'Invalid or missing API token'
        ]]
      ], 401);
    }

    return $next($request);
  }
}
