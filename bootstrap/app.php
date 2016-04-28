<?php

require_once __DIR__.'/../vendor/autoload.php';

// We try to load `.env` file.
try {
    (new Dotenv\Dotenv(__DIR__.'/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

// Instantiate new application.
$app = new Hero\Application(dirname(__DIR__));

// Register any application provider.
$app->register('Hero\Providers\RouteServiceProvider');

return $app;
