<?php

namespace App\Http\Controllers;

use App\EnumsAndConsts\HttpStatus;
use App\Http\Resources\CurrentPriceCollection;
use App\Http\Resources\CurrentPriceResource;
use App\Models\CurrentPrice;
use Illuminate\Database\Eloquent\Builder;
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

        $query->when($request->search, function (Builder $q) use ($request) {
            $q->where('item', 'like', "%{$request->search}%");
            if (is_numeric($request->search)) {
                $q->orWhere('price', '>=', $request->search);
                $q->orWhere('available_qty', '>=', $request->search);
            }
        })->when($request->order && is_array($request->order), function (Builder $q) use ($request) {
            foreach ($request->order as $by => $dir) {
                if (!in_array($by, ['desc', 'asc', 'null', null, ''])) {
                    $q->orderBy($by, mb_strtoupper($dir));
                }
            }
        })->latest();

        $current_prices = $query->paginate($request->get('limit', '15'));

        return (new CurrentPriceCollection($current_prices))->additional([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ])->response()->setStatusCode(HttpStatus::OK);
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
}
