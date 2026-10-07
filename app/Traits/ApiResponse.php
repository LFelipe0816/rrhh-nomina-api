<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * @param $data
     * @param string|null $message
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function successResponse($data, string $message = null, int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message ?? __('responses.success_operation'),
            'data'    => $data,
        ], $statusCode);
    }

    /**
     * @param $message
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function errorResponse($message, int $statusCode): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data'    => null
        ], $statusCode);
    }

    /**
     * @param $message
     * @return JsonResponse
     */
    protected function responseNotFound($message = null): JsonResponse
    {
        return $this->errorResponse($message ?? __('responses.resource_not_found'), 404);
    }

    /**
     * @param $validator
     * @return JsonResponse
     */
    protected function invalidResponse($validator): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => __('responses.invalid_data'),
            'errors' => $validator->errors()
        ], 422);
    }
}
