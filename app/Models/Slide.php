<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use ToneflixCode\LaravelFileable\Traits\Fileable;

class Slide extends Model
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
        'active' => 'boolean',
        'expires_at' => 'datetime'
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
            $slug = $model->title . '-' . rand(100, 999);
            $model->slug = str($model->slug ?? (Slide::whereSlug($slug)->exists()
                ? $slug . '-' . rand(100, 999)
                : $slug
            ))->slug();
        });
    }

    /**
     * Get the slides's avatar
     *
     * @return string
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->media_file,
        );
    }

    public function scopeIsAdv($query, $isAdv = true, $checkExpiry = false)
    {
        if ($isAdv) {
            $query->whereNotNull('expires_at');
            if ($checkExpiry) {
                $query->where('expires_at', '>', now());
            }
        } else {
            $query->whereNull('expires_at');
        }
    }

    public function advertRequest(): BelongsTo
    {
        return $this->belongsTo(AdvertRequest::class);
    }
}