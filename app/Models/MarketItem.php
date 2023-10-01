<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use ToneflixCode\LaravelFileable\Traits\Fileable;

class MarketItem extends Model
{
    use HasFactory;
    use Fileable;

    protected $appends = [
        'image_url'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'float',
        'active' => 'boolean',
        'approved' => 'boolean',
        'quantity' => 'integer',
        'quantity_tons' => 'float',
    ];

    public function registerFileable()
    {
        $this->fileableLoader([
            'image' => 'banner',
        ]);
    }

    public static function registerEvents()
    {
        static::creating(function ($model) {
            $slug = str($model->name . $model->type . '-' . rand(1000, 9999))->slug();
            $model->slug = $model->slug ?? (MarketItem::whereSlug($slug)->exists()
                ? $slug . '-' . rand(100, 999)
                : $slug
            );
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items's avatar
     *
     * @return string
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->media_file,
        );
    }

    public function scopeIsAvailable($query, $status = true)
    {
        return $query->whereApproved($status)->whereActive($status);
    }
}