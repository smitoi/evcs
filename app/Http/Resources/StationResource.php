<?php

namespace App\Http\Resources;

use App\Models\Station;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * Class StationResource
 * @package App\Http\Resources
 * @OA\Schema(
 *      @OA\Property(
 *          property="uuid",
 *          description="Identifier of the station",
 *          type="string",
 *      ),
 *      @OA\Property(
 *          property="name",
 *          description="Name of the station",
 *          type="string",
 *      ),
 *      @OA\Property(
 *          property="address",
 *          description="Address of the station",
 *          type="string",
 *      )
 * )
 *
 */
class StationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        /** @var Station $this */
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'address' => $this->address,
            'company_uuid' => $this->company->uuid,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }
}
