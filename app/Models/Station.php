<?php

namespace App\Models;

use App\Models\Traits\HandleUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder;


/**
 * Class Station
 * @property int $id
 * @property string $uuid
 * @property string $name
 * @property string $address
 * @property int|null $company_id
 * @property double $latitude
 * @property double $longitude
 *
 * @property Company|null $company
 *
 * @package App\Models
 *
 * @mixin Builder
 */
class Station extends Model
{
    use HasFactory, HandleUuid;

    protected $fillable = [
        'name',
        'address',
        'company_id',
        'latitude',
        'longitude',
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
}
