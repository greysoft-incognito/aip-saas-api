<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MatanYadaev\EloquentSpatial\SpatialBuilder;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Objects\Polygon;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;

/**
 * @property Point $location
 * @property Polygon $area
 * @method static SpatialBuilder query()
 */
class UserLocation extends Model
{
    use HasFactory;
    use HasSpatial;

    protected $fillable = [
        'name',
        'area',
        'user_id',
        'location',
    ];

    protected $casts = [
        'location' => Point::class,
        'area' => Polygon::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}