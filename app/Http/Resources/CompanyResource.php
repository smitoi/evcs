<?php

namespace App\Http\Resources;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * Class CompanyResource
 * @package App\Http\Resources
 * @OA\Schema(
 *      @OA\Property(
 *          property="uuid",
 *          description="Identifier of the company",
 *          type="string",
 *      ),
 *      @OA\Property(
 *          property="name",
 *          description="Name of the company",
 *          type="string",
 *      )
 * )
 *
 */
class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var Company $this */
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'parent' => [
                'name' => $this->parent?->name,
                'uuid' => $this->parent?->uuid,
            ],
            'ancestors' => $this->whenLoaded('ancestors', self::collection($this->ancestors)),
            'descendants' => $this->whenLoaded('descendants', self::collection($this->descendants))
        ];
    }
}
