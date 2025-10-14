<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class Handler extends ExceptionHandler
{
    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e): JsonResponse
    {
        // If it's a validation exception, return formatted JSON
        if ($e instanceof ValidationException) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
                'message' => $e->getMessage() ?: 'Validation failed'
            ], 422);
        }

        $status = 500;
        $message = 'Server Error';

        if ($e instanceof HttpExceptionInterface) {
            $status = $e->getStatusCode();
            $message = $e->getMessage() ?: JsonResponse::HTTP_STATUS_TEXT[$status] ?? 'Error';
        }

        // For API routes, always send JSON
        return response()->json([
            'success' => false,
            'error' => $message,
            'exception' => config('app.debug') ? class_basename($e) : null
        ], $status);
    }
}
