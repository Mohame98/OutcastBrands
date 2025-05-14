<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\TrustHosts;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
    //     $middleware->push(TrustHosts::class);

    //      $middleware->trustHosts(function (Request $request) {
    //         return [
    //             '*.yourdomain.com',
    //             'mytrusted.com',   
    //             '*.test', 
    //         ];
    //     });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();


    
