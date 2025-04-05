<?php
namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait JsonResponseTrait
{
    /**
     * Return a success JSON response
     *
     * @param mixed $data
     * @param string $message
     * @param int $statusCode
     * @param array $headers
     * @return JsonResponse
     */
    protected function successResponse($data = [], string $message = 'Operation successful', int $statusCode = 200, array $headers = []): JsonResponse
    {
        return response()->json([
            'status'  => true,
            'message' => $message,
            'data'    => $data,
        ], $statusCode, $headers);
    }

    /**
     * Return an error JSON response
     *
     * @param string $message
     * @param int $statusCode
     * @param array $data
     * @param array $headers
     * @return JsonResponse
     */
    protected function errorResponse(string $message = 'Operation failed', int $statusCode = 400, array $data = [], array $headers = []): JsonResponse
    {
        return response()->json([
            'status'  => false,
            'message' => $message,
            'data'    => $data,
        ], $statusCode, $headers);
    }

    /**
     * Return a validation error JSON response
     *
     * @param array $errors
     * @param string $message
     * @param int $statusCode
     * @param array $headers
     * @return JsonResponse
     */
    protected function validationErrorResponse(array $errors, string $message = 'Validation error', int $statusCode = 422, array $headers = []): JsonResponse
    {
        return response()->json([
            'status'  => false,
            'message' => $message,
            'errors'  => $errors,
        ], $statusCode, $headers);
    }

    /**
     * Return a not found JSON response
     *
     * @param string $message
     * @param array $data
     * @param array $headers
     * @return JsonResponse
     */
    protected function notFoundResponse(string $message = 'Resource not found', array $data = [], array $headers = []): JsonResponse
    {
        return response()->json([
            'status'  => false,
            'message' => $message,
            'data'    => $data,
        ], 404, $headers);
    }

    /**
     * Return an unauthorized JSON response
     *
     * @param string $message
     * @param array $headers
     * @return JsonResponse
     */
    protected function unauthorizedResponse(string $message = 'Unauthorized access', array $headers = []): JsonResponse
    {
        return response()->json([
            'status'  => false,
            'message' => $message,
        ], 401, $headers);
    }

    /**
     * Return a forbidden JSON response
     *
     * @param string $message
     * @param array $headers
     * @return JsonResponse
     */
    protected function forbiddenResponse(string $message = 'Access forbidden', array $headers = []): JsonResponse
    {
        return response()->json([
            'status'  => false,
            'message' => $message,
        ], 403, $headers);
    }

    /**
     * Return a custom JSON response
     *
     * @param mixed $data
     * @param string $message
     * @param bool $status
     * @param int $statusCode
     * @param array $headers
     * @return JsonResponse
     */
    protected function customResponse($data = [], string $message = '', bool $status = true, int $statusCode = 200, array $headers = []): JsonResponse
    {
        return response()->json([
            'status'  => $status,
            'message' => $message,
            'data'    => $data,
        ], $statusCode, $headers);
    }
}
