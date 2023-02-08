<?php

namespace App\Http\Requests;

use App\Enums\PermissionEnum;
use App\Models\Company;
use App\Services\PermissionService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use ReflectionException;


/**
 * @OA\RequestBody(
 *     request="StoreCompanyRequest",
 *     description="Request to create a new company",
 *     @OA\MediaType(
 *         mediaType="application/json",
 *         @OA\Schema(
 *             @OA\Property(
 *                 property="name",
 *                 description="Name of the company - required, should be unique in the database",
 *                 type="string"
 *             ),
 *             @OA\Property(
 *                 property="parent_uuid",
 *                 description="UUID of the parent for the company - optional",
 *                 type="string"
 *             )
 *         )
 *     )
 * )
 */
class StoreCompanyRequest extends FormRequest
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
            PermissionService::getPermissionForModel(Company::class, PermissionEnum::PERMISSION_CREATE)
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
            'name' => 'required|min:3|unique:companies',
            'parent_uuid' => 'sometimes|exists:companies,uuid',
        ];
    }
}
