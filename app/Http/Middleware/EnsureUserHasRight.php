<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRight
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $right): Response
    {
        $user = $request->user();

        if ($user === null || ! $user->hasRight($right)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
