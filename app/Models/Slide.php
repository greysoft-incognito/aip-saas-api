<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
            $slug = $model->title . '-' . $model->id;
            $model->slug = $model->slug ?? (Slide::whereSlug($slug)->exists()
                ? $slug . rand(100, 999)
                : $slug
            );
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
}
