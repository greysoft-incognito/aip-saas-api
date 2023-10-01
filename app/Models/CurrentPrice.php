<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrentPrice extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'available_qty' => 'integer',
        'price' => 'float',
        'price_tons' => 'float',
    ];

    /**
     * Get the available quantity
     *
     * @return string
     */
    protected function quantity(): Attribute
    {
        return Attribute::make(
            get: fn () => MarketItem::isAvailable()->whereGrade($this->item)->sum('quantity'),
        );
    }

    /**
     * Get the available quantity in tons
     *
     * @return string
     */
    protected function quantityTons(): Attribute
    {
        return Attribute::make(
            get: fn () => MarketItem::isAvailable()->whereGrade($this->item)->sum('quantity_tons'),
        );
    }
}