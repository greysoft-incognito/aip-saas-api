<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ToneflixCode\LaravelFileable\Traits\Fileable;

class Event extends Model
{
    use HasFactory;
    use Fileable;

    public function registerFileable()
    {
        $this->fileableLoader([
            'image' => 'banner'
        ]);
    }

    protected $appends = [
        'image_url'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'days' => 'integer',
        'date' => 'datetime',
        'active' => 'boolean',
    ];

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
