<?php

use Hero\Application;
use Illuminate\Support\Str;

if (!function_exists('app')) {
    /**
     * Get application instance or create an implementation of a binding.
     *
     * @param string $name
     * @param array  $parameters
     *
     * @return mixed
     */
    function app($name = null, array $parameters = [])
    {
        if ($name === null) {
            return Application::getInstance();
        }

        return Application::make($name, $parameters);
    }
}

if (!function_exists('env')) {
    /**
     * Gets the value of an environment variable. Supports boolean, empty and null.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    function env($key, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            return value($default);
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;

            case 'false':
            case '(false)':
                return false;

            case 'empty':
            case '(empty)':
                return '';

            case 'null':
            case '(null)':
                return;
        }

        if (Str::startsWith($value, '"') && Str::endsWith($value, '"')) {
            return substr($value, 1, -1);
        }

        return $value;
    }
}
