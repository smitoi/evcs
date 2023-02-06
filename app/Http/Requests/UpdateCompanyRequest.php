<?php

namespace App\Http\Requests;

use App\Enums\PermissionEnum;
use App\Models\Company;
use App\Services\PermissionService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use ReflectionException;

/**
 * @OA\RequestBody(
 *     request="UpdateCompanyRequest",
 *     description="Request to update an existing company",
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
class UpdateCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool|null
     * @throws ReflectionException
     */
    public function authorize(): ?bool
    {
        return Auth::user()?->hasPermissionTo(
            PermissionService::getPermissionForModel(Company::class, PermissionEnum::PERMISSION_UPDATE)
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
                Rule::unique('companies', 'name')->ignore($this->route('id'))
            ],
            'parent_uuid' => 'sometimes|exists:companies,uuid',
        ];
    }
}
