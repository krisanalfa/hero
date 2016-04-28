<?php

namespace Hero\Exceptions\Handlers;

use View;
use Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class BaseHttpExceptionHandler
{
    /**
     * Handle not found exception.
     *
     * @param  \Symfony\Component\HttpKernel\Exception\HttpException $e
     *
     * @return mixed
     */
    public function handle(HttpException $e)
    {
        $errorViewName = 'errors.'.$e->getStatusCode();

        // Here we check whether view is exist. For example if you got 404 status,
        // then we need a `errors.404` view file. If it exists, we render that view,
        // otherwise, we just throw back the exception, leaving it uncaught.
        if (View::exists($errorViewName)) {
            return Response::make(
                // You may use `$error` variable in your view file ;)
                View::make($errorViewName, ['error' => $e]),
                $e->getStatusCode()
            )->send();
        }

        throw $e;
    }
}
