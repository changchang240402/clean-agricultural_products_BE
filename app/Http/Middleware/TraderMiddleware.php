<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TraderMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $status = [1,3];
        $role = User::where('id', auth()->id())
                    ->whereIn('status', $status)
                    ->value('role');
        if ($role === config('constants.ROLE')['trader']) {
            return $next($request);
        };

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
