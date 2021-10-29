<?php

namespace App\Exceptions;

use Exception;
use App\Traits\ApiResponser;
use Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    use ApiResponser;
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
        'password',
        'password_confirmation',
    ];

   

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if($exception instanceof ValidationException )
        {
            return $this->convertValidationExceptionToResponse($exception, $request);
        }

        if($exception instanceof ModelNotFoundException )
        {
            $modelName  = class_basename($exception->getModel());
            return $this->errorResponse("{$modelName} Does not exit or Can be identified", 404);
        }

        if($exception instanceof AuthenticationException )
        {
           
            return $this->unauthenticated($request, $exception);
        }

        if($exception instanceof AuthorizationException )
        {
           
            return $this->errorResponse($exception->getMessage(), 403);
        }

        if($exception instanceof NotFoundHttpException )
        {
           
            return $this->errorResponse("The specified url cannot be found", 403);
        }
        if($exception instanceof MethodNotAllowedHttpException )
        {
           
            return $this->errorResponse("The request method does not exit", 404);
        }
        if($exception instanceof HttpException )
        {
           
            return $this->errorResponse($exception->getMessage(), $exception->getStatusCode());
        }
        if($exception instanceof QueryException )
        {
            $errorCode = $exception->errorInfo[1];
            if($errorCode == 1451)
            {
                return $this->errorResponse("Cannot Perform this operation or remove such resource ", 409);
            }
            // return $this->errorResponse($exception->getMessage(), $exception->getStatusCode());
        }
        if($exception instanceof TokenMismatchException )
        {
            return redirect()->back()->withInput($request->input());
            // return $this->errorResponse($exception->getMessage(), $exception->getStatusCode());
        }
        if(config('app.debug'))
        {
            return parent::render($request, $exception);
        }
        return $this->errorResponse("Unexpected Exception, Try again later", 500);

    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if($this->isFrontend($request))
        {
            return redirect()->guest('login');
        }
        return $this->errorResponse("unauthenticated", 401);
    }


     /**
     * Create a response object from the given validation exception.
     *
     * @param  \Illuminate\Validation\ValidationException  $e
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        $errors = $e->validator->errors()->getMessages();

        if($this->isFrontend($request))
        {
            return $request->ajax() ? response()->json($errors, 422) : redirect()
                                            ->back()
                                            ->withInput($request->input())
                                            ->withErrors($errors); 
        }

        return $this->errorResponse($errors, 422);
    }

    private function isFrontend($request)
    {
        return $request->acceptsHtml() && collect($request->route()->middleware())->contains('web');
    }
}
