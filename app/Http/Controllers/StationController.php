<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStationRequest;
use App\Http\Requests\UpdateStationRequest;
use App\Http\Resources\StationResource;
use App\Models\Company;
use App\Models\Station;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class StationController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/api/station/",
     *     security={{"bearerAuth":{}}},
     *     tags={"station"},
     *     description="List",
     *     summary="List all stations",
     *     operationId="stationList",
     *     @OA\Response(
     *         response="200",
     *         description="Success with the stations",
     *         @OA\JsonContent(ref="#/components/schemas/StationResource"),
     *     )
     * )
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Station::class);

        return $this->jsonSuccess(
            data: StationResource::collection(
                Station::all(),
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
