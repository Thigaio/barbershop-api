<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->user_type_id !== 1) {
            return response()->json([
                'message' => 'Acesso não autorizado. Apenas administradores podem acessar esta rota.'
            ], 403);
        }

        return $next($request);
    }
}
