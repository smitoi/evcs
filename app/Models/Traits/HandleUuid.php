<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HandleUuid
{
    protected string $uuidColumn = 'uuid';

    public function getUuidColumn(): string
    {
        return $this->uuidColumn;
    }

    public static function bootHandleUuid(): void
    {
        static::creating(function (Model $model) {
            $model->{$model->getUuidColumn()} = Str::uuid()->toString();
        });
    }
}
