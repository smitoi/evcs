<?php

namespace App\Policies;

use App\Models\Station;
use App\Policies\Definitions\BasePolicy;

class StationPolicy extends BasePolicy
{
    protected function getClassName(): string
    {
        return Station::class;
    }
}
