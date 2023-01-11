<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Contracts\Container\BindingResolutionException;
use BadMethodCallException;

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
        // post method direct access
        $this->renderable(function (MethodNotAllowedHttpException $e, $request) {
            $msg=Controller::POST_METHOD_ACCESS_ERROR;
            return Helper::ErrorResponse($msg);
        });
        // controller does not exist
        // $this->renderable(function (BindingResolutionException $e, $request) {
        //     $msg=Controller::CONTROLLER_NOT_FOUND;
        //     return Helper::ErrorResponse($msg);
        // });
        // Method does not exist
        // $this->renderable(function (BadMethodCallException $e, $request) {
        //     $msg=Controller::METHOD_NOT_FOUND;
        //     return Helper::ErrorResponse($msg);
        // });
        $this->reportable(function (Throwable $e) {
            // 
        });
    }

}