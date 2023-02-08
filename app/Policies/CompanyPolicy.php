<?php

namespace App\Policies;

use App\Models\Company;
use App\Policies\Definitions\BasePolicy;

class CompanyPolicy extends BasePolicy
{
    protected function getClassName(): string
    {
        return Company::class;
    }
}
