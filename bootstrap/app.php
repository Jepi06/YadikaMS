<?php

// FILE: bootstrap/app.php
// Tidak ada perubahan — sudah benar sesuai file yang dikirim.
// Dicantumkan di sini sebagai referensi konfirmasi.

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth.pkl'  => \App\Http\Middleware\AuthenticatePkl::class,
            'auth.ppdb' => \App\Http\Middleware\AuthenticatePpdb::class,
            'role'      => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
