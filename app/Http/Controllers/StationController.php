<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStationRequest;
use App\Http\Requests\UpdateStationRequest;
use App\Http\Resources\GroupedStationResource;
use App\Http\Resources\StationResource;
use App\Models\Company;
use App\Models\Station;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Meilisearch\Endpoints\Indexes;

class StationController extends ApiController
{
    public const PER_PAGE = 10;

    /**
     * @OA\Get(
     *     path="/api/station/",
     *     security={{"bearerAuth":{}}},
     *     tags={"station"},
     *     description="List",
     *     summary="List all stations",
     *     operationId="stationList",
     *     @OA\Parameter(
     *        description="Longitude of the user",
     *        name="lat",
     *        in="query",
     *        required=false,
     *        example="42.27",
     *          @OA\Schema(
     *              type="number",
     *          )
     *     ),
     *     @OA\Parameter(
     *        description="Latitude of the user",
     *        name="long",
     *        in="query",
     *        required=false,
     *        example="27.42",
     *          @OA\Schema(
     *              type="number",
     *          )
     *     ),
     *     @OA\Parameter(
     *        description="Maximum distance of the stations in kilometers",
     *        name="max_distance",
     *        in="query",
     *        required=false,
     *        example="3.14",
     *          @OA\Schema(
     *              type="number",
     *          )
     *     ),
     *     @OA\Parameter(
     *        description="Company that owns the station - will include 'child' companies",
     *        name="company_uuid",
     *        in="query",
     *        required=false,
     *        example="valid-uuid-value",
     *          @OA\Schema(
     *              type="string",
     *          )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Success with the stations as a paginated resource",
     *         @OA\JsonContent(ref="#/components/schemas/StationResource"),
     *     )
     * )
     *
     * @param Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'long' => 'sometimes|required_with:lat|numeric|min:-90|max:90',
            'lat' => 'sometimes|required_with:long|min:-180|max:180',
            'max_distance' => 'sometimes|required_with:lat|required_with:long|min:0',
            'company_uuid' => 'sometimes|exists:companies,uuid',
        ]);

        $this->authorize('viewAny', Station::class);

        if (($long = Arr::get($validated, 'long')) &&
            ($lat = Arr::get($validated, 'lat'))) {
            $maxDistance = Arr::get($validated, 'max_distance') * 1000;

            $query = Station::search(
                callback: static function (Indexes $meilisearch, string $query, array $options) use ($long, $lat, $maxDistance) {
                    if ($maxDistance) {
                        $options['filter'] = "_geoRadius($lat, $long, $maxDistance)";
                    }

                    $options['sort'] = ["_geoPoint($lat,$long):asc"];

                    return $meilisearch->rawSearch(
                        query: $query,
                        searchParams: $options,
                    );
                },
            );

            if ($companyId = Arr::get($validated, 'company_uuid')) {
                /** @var Company $company */
                $company = Company::where('uuid', $companyId)->with('successors')->first();
                $query = $query->query(function ($query) use ($company) {
                    $query->whereIn('company_id', [$company->id, ...$company->successors->pluck('id')]);
                });
            }
        } else {
            $query = Station::query();

            if ($companyId = Arr::get($validated, 'company_uuid')) {
                /** @var Company $company */
                $company = Company::where('uuid', $companyId)->with('successors')->first();
                $query = $query->whereIn('company_id', [$company->id, ...$company->successors->pluck('id')]);
            }
        }

        return $this->jsonSuccess(
            data: GroupedStationResource::make(
                $query->get()->groupBy(['latitude', 'longitude'])->paginate(self::PER_PAGE),
            ),
        );
    }

    /**
     * @OA\Post(
     *     path="/api/station/",
     *     security={{"bearerAuth":{}}},
     *     tags={"station"},
     *     description="Store",
     *     summary="Stores a new station",
     *     operationId="stationStore",
     *     requestBody={"$ref": "#/components/requestBodies/StoreStationRequest"},
     *     @OA\Response(
     *         response="200",
     *         description="Success with the new station's data",
     *         @OA\JsonContent(ref="#/components/schemas/StationResource"),
     *     )
     * )
     *
     * @param StoreStationRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(StoreStationRequest $request): JsonResponse
    {
        $this->authorize('create', Station::class);

        try {
            DB::beginTransaction();
            $validated = $request->validated();

            /** @var Company $company */
            $company = Company::query()->where('uuid', '=', Arr::pull($validated, 'company_uuid'))->firstOrFail();
            Arr::set($validated, 'company_id', $company->id);

            $station = Station::query()->create($validated);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->jsonError(
                message: $exception->getMessage(),
            );
        }

        DB::commit();

        return $this->jsonSuccess(
            StationResource::make($station),
            code: 201
        );
    }

    /**
     * @OA\Get(
     *     path="/api/station/{id}",
     *     @OA\Parameter(
     *        in="path",
     *        name="id",
     *        parameter="id"
     *     ),
     *     security={{"bearerAuth":{}}},
     *     tags={"station"},
     *     description="Show",
     *     summary="Gets the details for a station",
     *     operationId="stationShow",
     *     @OA\Response(
     *         response="200",
     *         description="Success with the selected station",
     *         @OA\JsonContent(ref="#/components/schemas/StationResource"),
     *     )
     * )
     *
     * @param Station $station
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(Station $station): JsonResponse
    {
        $this->authorize('view', $station);

        return $this->jsonSuccess(
            data: StationResource::make($station),
        );
    }

    /**
     * @OA\Put(
     *     path="/api/station/{id}",
     *     @OA\Parameter(
     *        in="path",
     *        name="id",
     *        parameter="id"
     *     ),
     *     security={{"bearerAuth":{}}},
     *     tags={"station"},
     *     description="Update",
     *     summary="Updated an existing station",
     *     operationId="stationUpdate",
     *     requestBody={"$ref": "#/components/requestBodies/UpdateStationRequest"},
     *     @OA\Response(
     *         response="200",
     *         description="Success with the station's updated data",
     *         @OA\JsonContent(ref="#/components/schemas/StationResource"),
     *     )
     * )
     *
     * @param UpdateStationRequest $request
     * @param Station $station
     * @return JsonResponse
     *
     * @throws AuthorizationException
     */
    public function update(UpdateStationRequest $request, Station $station): JsonResponse
    {
        $this->authorize('update', $station);

        try {
            DB::beginTransaction();
            $validated = $request->validated();

            /** @var Company $company */
            $company = Company::query()->where('uuid', '=', Arr::pull($validated, 'company_uuid'))->firstOrFail();
            Arr::set($validated, 'company_id', $company->id);

            $station->update($validated);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->jsonError(
                message: $exception->getMessage(),
            );
        }

        DB::commit();

        return $this->jsonSuccess(
            StationResource::make($station)
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/station/{id}",
     *     @OA\Parameter(
     *        in="path",
     *        name="id",
     *        parameter="id"
     *     ),
     *     security={{"bearerAuth":{}}},
     *     tags={"station"},
     *     description="Destroy",
     *     summary="Removes a station",
     *     operationId="stationDelete",
     *     @OA\Response(
     *         response="204",
     *         description="Success, empty response"
     *     )
     * )
     *
     * @param Station $station
     * @return JsonResponse
     *
     * @throws AuthorizationException
     */
    public function destroy(Station $station): JsonResponse
    {
        $this->authorize('delete', $station);

        $station->delete();

        return $this->jsonSuccess(
            code: 204,
        );
    }
}
