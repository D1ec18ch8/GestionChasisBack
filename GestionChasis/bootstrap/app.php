<?php

use App\Exceptions\ChasisNotFoundException;
use App\Exceptions\EstadoInUseException;
use App\Exceptions\EstadoNotFoundException;
use App\Exceptions\ProtectedEstadoException;
use App\Exceptions\TipoChasisNotFoundException;
use App\Exceptions\UbicacionNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ChasisNotFoundException $exception): JsonResponse {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 404);
        });

        $exceptions->render(function (TipoChasisNotFoundException $exception): JsonResponse {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 404);
        });

        $exceptions->render(function (UbicacionNotFoundException $exception): JsonResponse {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 404);
        });

        $exceptions->render(function (EstadoNotFoundException $exception): JsonResponse {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 404);
        });

        $exceptions->render(function (ProtectedEstadoException $exception): JsonResponse {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 409);
        });

        $exceptions->render(function (EstadoInUseException $exception): JsonResponse {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 409);
        });
    })->create();
