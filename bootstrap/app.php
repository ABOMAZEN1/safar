<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api/api_routes.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        include_once __DIR__ . '/middleware.php';
    })
    ->withExceptions(function (Exceptions $exceptions) {
        include_once __DIR__ . '/exceptions.php';
    })
    ->withSchedule(function (Schedule $schedule) {
        include_once __DIR__ . '/schedule.php';
    })
    ->create();
