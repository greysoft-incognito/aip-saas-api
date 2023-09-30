<?php

namespace App\Http\Controllers\Admin;

use App\EnumsAndConsts\HttpStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\CurrentPriceCollection;
use App\Http\Resources\CurrentPriceResource;
use App\Models\CurrentPrice;
use Illuminate\Http\Request;

class CurrentPriceController extends Controller
{
    /**
     * @param  Request  $request
     *
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CurrentPrice::query();
        $query->latest();

        $announcements = $query->paginate($request->get('limit', '15'));

        return (new CurrentPriceCollection($announcements))->additional([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ])->response()->setStatusCode(HttpStatus::OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'item' => 'required|string|min:3|max:255',
            'unit' => 'required|string|min:1|max:25',
            'icon' => 'nullable|string|min:1',
            'price' => 'required|numeric',
            'available_qty' => 'numeric',
        ]);

        $price = new CurrentPrice();
        $price->item = $request->item;
        $price->unit = $request->unit;
        $price->icon = $request->icon;
        $price->price = $request->price;
        $price->available_qty = $request->available_qty;
        $price->save();

        return (new CurrentPriceResource($price))->additional([
            'message' => __('Item ":0" has been created successfully', [$price->item]),
            'status' => 'success',
            'status_code' => HttpStatus::CREATED,
        ])->response()->setStatusCode(HttpStatus::CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(CurrentPrice $price)
    {
        return (new CurrentPriceResource($price))->additional([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ])->response()->setStatusCode(HttpStatus::OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CurrentPrice $price)
    {
        $this->validate($request, [
            'item' => 'required|string|min:3|max:255',
            'unit' => 'required|string|min:1|max:25',
            'icon' => 'nullable|string|min:1',
            'price' => 'required|numeric',
            'available_qty' => 'numeric',
        ]);

        $price->item = $request->item ?? $price->item;
        $price->unit = $request->unit ?? $price->unit;
        $price->icon = $request->icon ?? $price->icon;
        $price->price = $request->price ?? $price->price;
        $price->available_qty = $request->available_qty ?? $price->available_qty;
        $price->save();

        return (new CurrentPriceResource($price))->additional([
            'message' => __('Item ":0" has been updated successfully', [$price->item]),
            'status' => 'success',
            'status_code' => HttpStatus::ACCEPTED,
        ])->response()->setStatusCode(HttpStatus::ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CurrentPrice $price)
    {
        $price->delete();

        return (new CurrentPriceResource($price))->additional([
            'message' => __('Item ":0" has been deleted successfully', [$price->item]),
            'status' => 'success',
            'status_code' => HttpStatus::ACCEPTED,
        ])->response()->setStatusCode(HttpStatus::ACCEPTED);
    }
}