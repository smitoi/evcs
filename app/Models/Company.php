<?php

namespace App\Models;

use App\Models\Traits\HandleUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

/**
 * Class Company
 * @property int $id
 * @property string $name
 * @property string $uuid
 * @property int|null $parent_id
 *
 * @property Company|null $parent
 * @property Collection<Company> $children
 *
 * @property Collection<Company> $ancestors
 * @property Collection<Company> $descendants
 *
 * @package App\Models
 *
 * @mixin Builder
 */
class Company extends Model
{
    use HasFactory, HandleUuid;

    protected $fillable = [
        'name',
        'parent_id',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(__CLASS__, 'parent_id', 'id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(__CLASS__, 'parent_id', 'id');
    }

    public function ancestors(): BelongsToMany
    {
        return $this->belongsToMany(
            __CLASS__,
            'companies_hierarchy_levels',
            'ancestor_id',
            'descendant_id',
        )->withPivot('distance');
    }

    public function descendants(): BelongsToMany
    {
        return $this->belongsToMany(
            __CLASS__,
            'companies_hierarchy_levels',
            'descendant_id',
            'ancestor_id',
        )->withPivot('distance');
    }
}
