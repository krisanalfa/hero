<?php

namespace Hero\Exceptions;

use Exception;
use Illuminate\Support\Arr;

class Handler
{
    /**
     * Basic handler of application exception.
     *
     * @param \Exception $e
     *
     * @return mixed
     */
    public function handle(Exception $e)
    {
        list(
            $exceptionClassName,
            $handlerClassName
        ) = $this->getExceptionHandlerInfo($e);

        if (class_exists($handlerClassName)) {
            return call_user_func_array(
                [value(new $handlerClassName()), 'handle'],
                [$e]
            );
        }

        throw $e;
    }

    /**
     * Get exception handler information.
     *
     * @param \Exception $e
     *
     * @return array
     */
    protected function getExceptionHandlerInfo(Exception $e)
    {
        $exceptionClassName = Arr::last(explode('\\', get_class($e)));
        $handlerClassName = "Hero\Exceptions\Handlers\\{$exceptionClassName}Handler";

        return [
            $exceptionClassName,
            $handlerClassName,
        ];
    }
}
