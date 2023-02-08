<?php

namespace App\Http\Requests;

use App\Enums\PermissionEnum;
use App\Models\Station;
use App\Services\PermissionService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use ReflectionException;

/**
 * @OA\RequestBody(
 *     request="UpdateStationRequest",
 *     description="Request to update an existing station",
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
class UpdateStationRequest extends FormRequest
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
            PermissionService::getPermissionForModel(Station::class, PermissionEnum::PERMISSION_UPDATE)
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
            'name' => [
                'required',
                'min:3',
                Rule::unique('stations', 'name')->ignore($this->route('uuid'), 'uuid')
            ],
            'company_uuid' => 'required|exists:companies,uuid',
            'address' => 'required|min:3',
            'latitude' => 'numeric|min:-90|max:90',
            'longitude' => 'numeric|min:-180|max:180'
        ];
    }
}
