<?php

namespace App\Http\Middleware;

use App\Models\Admin\LanguageManagement;
use App\Models\API\Authentication;
use Closure;

class CheckAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $language = $request->header('Accept-Language');
        if ($request->header('Authorization')) {
            $token = $request->header('Authorization');
            $authenticatedUser = Authentication::where('access_token', $token)->get()->first();
            if ($authenticatedUser != null) {
                if ($authenticatedUser->type == 1) {
                    $request->request->add(['user_id' => $authenticatedUser["user_id"]]);
                } else {
                    $request->request->add(['company_id' => $authenticatedUser["user_id"]]);
                }

                return $next($request);
            } else {
                return response()->json([
                    'error' => LanguageManagement::getLabel('text_unauthorized', $language),
                ], 403);
            }

        } else {
            return response()->json([
                'error' => LanguageManagement::getLabel('invalid_api_request', $language),
            ], 400);
        }
    }
}
