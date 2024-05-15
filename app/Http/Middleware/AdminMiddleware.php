<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $role = User::where('id', auth()->id())->value('role');

        if ($role === config('constants.ROLE')['admin']) {
            return $next($request);
        };

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
