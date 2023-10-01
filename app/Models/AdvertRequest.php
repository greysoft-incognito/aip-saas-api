<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use ToneflixCode\LaravelFileable\Traits\Fileable;

class AdvertRequest extends Model
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
        'duration' => 'integer',
        'hide_texts' => 'boolean',
    ];

    /**
     * The model's attributes.
     *
     * @var array<string, string>
     */
    protected $attributes = [
        'hide_texts' => false,
    ];

    public function registerFileable()
    {
        $this->fileableLoader([
            'image' => 'slide',
        ]);
    }

    public static function registerEvents()
    {
        static::creating(function ($model) {
            $model->status = $model->status ?? 'draft';
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

    public function slide(): HasOne
    {
        return $this->hasOne(Slide::class)->withDefault(function () {
            return [
                'title' => 'Pending'
            ];
        })->whereNotNull('expires_at');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}