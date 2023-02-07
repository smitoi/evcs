<?php

namespace App\Http\Controllers;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *      version="0.0.1",
 *      title="EVCS API",
 *      description="API for electrical vehicle station management system",
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     in="header",
 *     name="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * ),
 * @OA\SecurityScheme(
 *     securityScheme="apiKey",
 *     in="header",
 *     name="x-api-key",
 *     type="apiKey"
 * )
 */
class ApiController extends Controller
{
    /**
     * @param array|Arrayable|ArrayAccess|null $data
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    public function jsonSuccess(array|Arrayable|ArrayAccess|null $data = null, string $message = 'success', int $code = 200): JsonResponse
    {
        return response()->json(
            array_filter([
                'success' => true,
                'message' => $message,
                'data' => $data,
            ]),
            $code,
        );
    }

    /**
     * @param array|Arrayable|ArrayAccess|null $errors
     * @param string $message
     * @param integer $code
     * @return JsonResponse
     */
    public function jsonError(array|Arrayable|ArrayAccess|null $errors = null, string $message = 'error', int $code = 500): JsonResponse
    {
        return response()->json(
            array_filter([
                'success' => false,
                'message' => $message,
                'errors' => $errors,
            ]),
            $code,
        );
    }
}
