<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (TokenMismatchException $e, $request) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'session_expired',
                    'message' => 'Sesi Anda telah berakhir karena tidak ada aktivitas. Silakan login kembali.',
                    'redirect' => route('login')
                ], 419);
            }

            return redirect()->route('login')
                ->with('warning', 'Sesi Anda telah berakhir karena tidak ada aktivitas. Silakan login kembali.');
        });

        $this->renderable(function (AuthenticationException $e, $request) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'unauthenticated',
                    'message' => 'Sesi login Anda telah berakhir. Silakan login kembali.',
                    'redirect' => route('login')
                ], 401);
            }

            return redirect()->route('login')
                ->with('warning', 'Sesi login Anda telah berakhir. Silakan login kembali.');
        });
    }
}
