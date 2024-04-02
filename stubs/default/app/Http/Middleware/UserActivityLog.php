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
        $requestBody = $request->except(['error_message','stack_trace','error_class']);
        if(isset($requestBody['password'])){
            $requestBody['password'] = '*****';
        }

        $logData = [
            'path' => $path,
            'method' => $requestMethod,
            'request_body' => $requestBody,
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
            if ($request->has('error_message') && $request->has('stack_trace')) {
                $logData['errors'] = [
                    'error_class' => $request->get('error_class'),
                    'message' => $request->get('error_message'),
                    'stack_trace' => $request->get('stack_trace'),
                ];
                Log::error(json_encode($logData));
            }else{
                Log::error(json_encode($logData));
            }
        }
    }
}