<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Resources\CompanyDetailResource;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Services\CompanyService;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CompanyController extends ApiController
{
    public const PER_PAGE = 10;

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
     * @throws AuthorizationException
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Company::class);

        return $this->jsonSuccess(
            data: CompanyResource::collection(
                Company::with('parent')->paginate(self::PER_PAGE),
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
     * @throws AuthorizationException
     */
    public function store(StoreCompanyRequest $request): JsonResponse
    {
        $this->authorize('create', Company::class);

        try {
            DB::beginTransaction();
            $validated = $request->validated();

            /** @var Company $parent */
            if ($parentUuid = Arr::pull($validated, 'parent_uuid')) {
                $parent = Company::query()->where('uuid', '=', $parentUuid)->firstOrFail();
                Arr::set($validated, 'parent_id', $parent->id);
            }

            /** @var Company $company */
            $company = Company::query()->create($validated);
            CompanyService::addParentHierarchy($company);
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
     * @throws AuthorizationException
     */
    public function show(Company $company): JsonResponse
    {
        $this->authorize('view', $company);

        $company->load('ancestors', 'successors');

        return $this->jsonSuccess(
            data: CompanyDetailResource::make($company),
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
     * @throws AuthorizationException
     */
    public function update(UpdateCompanyRequest $request, Company $company): JsonResponse
    {
        $this->authorize('update', $company);

        try {
            DB::beginTransaction();
            $validated = $request->validated();

            /** @var Company $parent */
            if ($parentUuid = Arr::pull($validated, 'parent_uuid')) {
                $parent = Company::query()->where('uuid', '=', $parentUuid)->firstOrFail();
                Arr::set($validated, 'parent_id', $parent->id);
            }

            $company->update($validated);
            CompanyService::addParentHierarchy($company);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->jsonError(
                message: $exception->getMessage(),
            );
        }

        DB::commit();

        return $this->jsonSuccess(
            CompanyResource::make($company)
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
     * @throws AuthorizationException
     */
    public function destroy(Company $company): JsonResponse
    {
        $this->authorize('delete', $company);

        $company->delete();

        return $this->jsonSuccess(
            code: 204,
        );
    }
}
