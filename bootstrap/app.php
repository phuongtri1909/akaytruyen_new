<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn() => route('login'));

        $middleware->alias([
            'secure.file.upload' => \App\Http\Middleware\SecureFileUpload::class,
            'ban' => \App\Http\Middleware\CheckBan::class,
            'canAny' => \App\Http\Middleware\CanAnyPermission::class,
        ]);

        $middleware->web([
            \App\Http\Middleware\SecureFileUpload::class,            
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
