<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class GroupedStationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return $this->resource->mapWithKeys(fn($value, $lat) => $value->map(fn($value, $long) => StationResource::collection($value)))->toArray();
    }
}
