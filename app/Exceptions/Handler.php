<?php

namespace App\Exceptions;

use App\Http\Response;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
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
    }

    /**
     * Render exception
     */
    public function render($request, \Throwable $e)
    {
        if ($request->wantsJson() && !($e instanceof \Illuminate\Http\JsonResponse)) { 
            return $this->handleApiException($request, $e);
        }
    
        return parent::render($request, $e);
    }

    /**
     * Handle Api Exception.
     */
    private function handleApiException($request, \Throwable $exception)
    {
        $exception = $this->prepareException($exception);

        if ($exception instanceof HttpResponseException) {
            $exception = $exception->getResponse();
        }

        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            $exception = $this->unauthenticated($request, $exception);
        }

        if ($exception instanceof \Illuminate\Validation\ValidationException) {
            $exception = $this->convertValidationExceptionToResponse($exception, $request);
        }

        return $this->customApiResponse($exception);
    }

    /**
     * Handle Custom Api Response.
     */
    private function customApiResponse($exception)
    {
        if (method_exists($exception, 'getStatusCode')) {
            $statusCode = $exception->getStatusCode();
        } else {
            $statusCode = 500;
        }

        $response = Response::error($statusCode);

        switch ($statusCode) {
            case 401:
                $response->message('Ошибка авторизации');
                break;
            case 403:
                $response->message('Нет доступа');
                break;
            case 404:
                $response->message('Не найдено');
                break;
            case 405:
                $response->message('Метод не доступен');
                break;
            case 422:
                $response->message('Данные неверны');
                $response->errors($exception->original['errors']);
                break;
            default:
                if ($exception instanceof BaseAppException) {
                    $response->message($exception->getMessage());
                } else {
                    $response->message(
                        config('app.debug') ? $exception->getMessage() : 'Произошла непредвиденная ошибка. Пожалуйста, обратитесь к администратору'
                    );
                }
                break;
        }

        if (config('app.debug') !== false && method_exists($exception, 'getTrace')) {
            $trace = $exception->getTrace();
            $response->data(['trace' => $trace]);
        }

        return $response->json();
    }
}
