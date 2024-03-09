<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class UserActivityLog
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response)
    {
        $path = $request->path();
        $requestMethod = $request->method();
        $user = Auth::user();
        $ipClient = $request->ip();
        $userAgent = $request->header('User-Agent');
        $statusCode = $response->getStatusCode();
        $statusName = Response::$statusTexts[$statusCode];

        $logData = [
            'path' => $path,
            'method' => $requestMethod,
            'status_code' => $statusCode,
            'status_name' => $statusName,
            'user_id' => $user?->user_id,
            'name' => $user?->name,
            'ip_client' => $ipClient,
            'user_agent' => $userAgent,
        ];

        if($response->isSuccessful()){
            Log::info(json_encode($logData));
        }else{
            if ($request->has('errors')) {
                $errors = $request->get('errors');
                $logData['errors'] = [
                    'message' => $errors->getMessage(),
                    'stack_trace' => $errors->getTrace(),
                ];
                Log::error(json_encode($logData));
            }else{
                Log::error(json_encode($logData));
            }
        }
    }
}
