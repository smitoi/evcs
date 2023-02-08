<?php

namespace App\Http\Requests;

use App\Enums\PermissionEnum;
use App\Models\Station;
use App\Services\PermissionService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use ReflectionException;

/**
 * @OA\RequestBody(
 *     request="StoreStationRequest",
 *     description="Request to create a new station",
 *     @OA\MediaType(
 *         mediaType="application/json",
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="name",
 *                 description="Name of the station - required, should be unique in the database",
 *                 type="string"
 *             ),
 *             @OA\Property(
 *                 property="address",
 *                 description="Address of the station - required",
 *                 type="string"
 *             ),
 *             @OA\Property(
 *                 property="company_uuid",
 *                 description="UUID of the company for the station - required",
 *                 type="string"
 *             ),
 *             @OA\Property(
 *                 property="latitude",
 *                 description="Latitude on the map - required, should between -90 and 90",
 *                 type="string"
 *             ),
 *             @OA\Property(
 *                 property="longitude",
 *                 description="Latitude on the map - required, should between -90 and 90",
 *                 type="string"
 *             )
 *         )
 *     )
 * )
 */
class StoreStationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     * @throws ReflectionException
     */
    public function authorize(): bool
    {
        return Auth::user()?->hasPermissionTo(
            PermissionService::getPermissionForModel(Station::class, PermissionEnum::PERMISSION_CREATE)
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|min:3|unique:stations',
            'company_uuid' => 'required|exists:companies,uuid',
            'address' => 'required|min:3',
            'latitude' => 'numeric|min:-90|max:90',
            'longitude' => 'numeric|min:-180|max:180'
        ];
    }
}
