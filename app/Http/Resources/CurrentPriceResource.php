<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrentPriceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'item' => $this->item,
            'icon' => $this->icon,
            'unit' => $this->unit,
            'price' => $this->price,
            'price_tons' => $this->price_tons,
            'quantity' => $this->quantity,
            'quantity_tons' => $this->quantity_tons,
            'available_qty' => $this->available_qty,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}