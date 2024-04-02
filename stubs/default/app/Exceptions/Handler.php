<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        })->stop();
        $this->renderable(function (Throwable $e, Request $request) {
            $request->merge([
                'error_class' => get_class($e),
                'error_message' => $e->getMessage(),
                'stack_trace' => json_decode(json_encode($e->getTrace())),
            ]);
        });
    }
}
