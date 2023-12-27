<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use MatanYadaev\EloquentSpatial\Objects\Point;
use ToneflixCode\LaravelFileable\Traits\Fileable;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use Fileable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    public static $userTitles = [
        "farmers" => "Farmers",
        "aggregators" => "Aggregators",
        "suppliers" => "Suppliers",
        "processors" => "Processors",
        "offtakers" => "Offtakers",
        "cooperatives" => "Cooperatives",
        "corporate" => "Corporate",
        "outgrowers" => "Outgrowers",
        "individuals" => "Small Scale (Individuals)",
        "corporate_ag" => "Large Scale (Corporate)",
        "extension" => "Extension Services",
        "mechanization" => "Mechanization",
        "seeds" => "Seeds",
        "fertilizers" => "Fertilizers",
        "herbicides" => "Herbicides",
        "small" => "Small Scale (1-3 tons monthly)",
        "medium" => "Medium Scale (3-10 tons monthly)",
        "large" => "Large Scale (Above 10 tons monthly)",
        "local" => "Local",
        "international" => "International",
        "logistics" => "Logistics",
        "researchers" => "Research Institutions",
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, array>
     */
    public static $allowedGroups = [
        "farmers" => ["cooperatives", "corporate", "outgrowers"],
        "aggregators" => ["individuals", "corporate_ag"],
        "suppliers" => [
          "extension",
          "mechanization",
          "seeds",
          "fertilizers",
          "herbicides",
        ],
        "processors" => ["small", "medium", "large"],
        "offtakers" => ["local", "international"],
        "logistics" => [],
        "researchers" => [],
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'address',
        'country',
        'state',
        'city',
        'type',
        'group',
        'email',
        'phone',
        'username',
        'password',
        'location',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'access_data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
        'access_data' => 'collection',
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
    ];

    /**
     * The model's attributes.
     *
     * @var array<string, string>
     */
    protected $attributes = [
        'role' => 'user',
    ];

    protected $appends = [
        'avatar',
        'fullname'
    ];

    public function registerFileable()
    {
        $this->fileableLoader([
            'image' => 'avatar',
        ]);
    }

    public static function registerEvents()
    {
        static::creating(function (User $model) {
            $uname = str($model->email)->explode('@');
            $model->username = $model->username ?? $uname->first(
                fn ($u) => (User::where('username', $u) ->doesntExist()),
                $uname->first() . rand(100, 999)
            );
        });

        static::retrieved(function (User $model) {
            $model->saveLocationData();
        });

        static::saved(function (User $model) {
            $model->saveLocationData();
        });
    }

    public function advertRequests(): HasMany
    {
        return $this->hasMany(AdvertRequest::class);
    }

    /**
     * save the user's location data
     *
     * @return void
     */
    protected function saveLocationData(): void
    {
        if (!$this->locationData && $this->location && $this->id) {
            $location = str($this->location)->explode(',');
            $this->locationData()->create([
                'name' => 'default',
                'location' => new Point($location[0], $location[1]),
            ]);
        }
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('id', $value)
            ->orWhere('username', $value)
            ->firstOrFail();
    }

    public function hasVerifiedPhone()
    {
        return $this->phone_verified_at !== null;
    }

    /**
     * Get the user's avatar
     *
     * @return string
     */
    protected function avatar(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->media_file,
        );
    }

    /**
     * Get the user's fullname
     *
     * @return string
     */
    protected function fullname(): Attribute
    {
        return Attribute::make(
            get: fn () => collect([$this->firstname, $this->lastname])->join(' '),
        );
    }

    /**
     * Get the user's location data
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function locationData(): HasOne
    {
        return $this->hasOne(UserLocation::class);
    }

    public function market(): HasMany
    {
        return $this->hasMany(MarketItem::class);
    }
}
