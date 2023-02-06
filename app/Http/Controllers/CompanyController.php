<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CompanyController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/api/company/",
     *     security={{"bearerAuth":{}}},
     *     tags={"company"},
     *     description="List",
     *     summary="List all companies",
     *     operationId="companyList",
     *     @OA\Response(
     *         response="200",
     *         description="Success with the companies",
     *         @OA\JsonContent(ref="#/components/schemas/CompanyResource"),
     *     )
     * )
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {

        return $this->jsonSuccess(
            data: CompanyResource::collection(
                Company::all(),
            ),
        );
    }

    /**
     * @OA\Post(
     *     path="/api/company/",
     *     security={{"bearerAuth":{}}},
     *     tags={"company"},
     *     description="Store",
     *     summary="Stores a new company",
     *     operationId="companyStore",
     *     requestBody={"$ref": "#/components/requestBodies/StoreCompanyRequest"},
     *     @OA\Response(
     *         response="200",
     *         description="Success with the new company's data",
     *         @OA\JsonContent(ref="#/components/schemas/CompanyResource"),
     *     )
     * )
     *
     * @param StoreCompanyRequest $request
     * @return JsonResponse
     */
    public function store(StoreCompanyRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $validated = $request->validated();

            /** @var Company $parent */
            if ($parentUuid = Arr::pull($validated, 'parent_uuid')) {
                $parent = Company::query()->where('uuid', '=', $parentUuid)->firstOrFail();
                Arr::set($validated, 'parent_id', $parent->id);
            }

            $company = Company::query()->create($request->validated());
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->jsonError(
                message: $exception->getMessage(),
            );
        }

        DB::commit();

        return $this->jsonSuccess(
            CompanyResource::make($company),
            code: 201
        );
    }

    /**
     * @OA\Get(
     *     path="/api/company/{id}",
     *     @OA\Parameter(
     *        in="path",
     *        name="id",
     *        parameter="id"
     *     ),
     *     security={{"bearerAuth":{}}},
     *     tags={"company"},
     *     description="Show",
     *     summary="Gets the details for a company",
     *     operationId="companyShow",
     *     @OA\Response(
     *         response="200",
     *         description="Success with the selected company",
     *         @OA\JsonContent(ref="#/components/schemas/CompanyResource"),
     *     )
     * )
     *
     * @param Company $company
     * @return JsonResponse
     */
    public function show(Company $company): JsonResponse
    {
        return $this->jsonSuccess(
            data: CompanyResource::make($company),
        );
    }

    /**
     * @OA\Put(
     *     path="/api/company/{id}",
     *     @OA\Parameter(
     *        in="path",
     *        name="id",
     *        parameter="id"
     *     ),
     *     security={{"bearerAuth":{}}},
     *     tags={"company"},
     *     description="Update",
     *     summary="Updated an existing company",
     *     operationId="companyUpdate",
     *     requestBody={"$ref": "#/components/requestBodies/UpdateCompanyRequest"},
     *     @OA\Response(
     *         response="200",
     *         description="Success with the company's updated data",
     *         @OA\JsonContent(ref="#/components/schemas/CompanyResource"),
     *     )
     * )
     *
     * @param UpdateCompanyRequest $request
     * @param Company $company
     * @return JsonResponse
     *
     */
    public function update(UpdateCompanyRequest $request, Company $company): JsonResponse
    {
        try {
            DB::beginTransaction();
            $validated = $request->validated();

            /** @var Company $parent */
            if ($parentUuid = Arr::pull($validated, 'parent_uuid')) {
                $parent = Company::query()->where('uuid', '=', $parentUuid)->firstOrFail();
                Arr::set($validated, 'parent_id', $parent->id);
            }

            $company->update($request->validated());
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->jsonError(
                message: $exception->getMessage(),
            );
        }

        DB::commit();

        return $this->jsonSuccess(
            CompanyResource::make($company),
            code: 201
        );
    }

    /**
     * @OA\Delete(
     *     path="/api/company/{id}",
     *     @OA\Parameter(
     *        in="path",
     *        name="id",
     *        parameter="id"
     *     ),
     *     security={{"bearerAuth":{}}},
     *     tags={"company"},
     *     description="Destroy",
     *     summary="Removes a company and all it's data",
     *     operationId="companyDelete",
     *     @OA\Response(
     *         response="204",
     *         description="Success, empty response"
     *     )
     * )
     *
     * @param Company $company
     * @return JsonResponse
     *
     */
    public function destroy(Company $company): JsonResponse
    {
        $company->delete();

        return $this->jsonSuccess(
            code: 204,
        );
    }
}
