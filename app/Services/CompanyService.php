<?php

namespace App\Services;

use App\Models\Company;

class CompanyService
{
    public static function addParentHierarchy(Company $company): void
    {
        $company->ancestors()->detach();
        $parent = $company->parent;

        $ancestors = [];
        for ($distance = 1; $parent !== null; $distance++) {
            $ancestors += [$parent->id => ['distance' => $distance]];
            $parent = $parent->parent;
        }

        $company->ancestors()->sync($ancestors);
    }
}
