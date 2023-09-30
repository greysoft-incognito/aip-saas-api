<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Closure;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $redirectToRoute
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|null
     */
    public function handle($request, Closure $next, $redirectToRoute = null)
    {
        if (! $request->user() || $request->user()->role === 'user') {
            return (new Controller())->buildResponse(
                [
                    'data' => UserResource::make($request->user()),
                    'status' => 'error',
                    'message' => 'You do not have permision to complete this action.',
                    'response_code' => 403,
                ],
                [
                    'response' => [],
                ]
            );
        }

        return $next($request);
    }
}
