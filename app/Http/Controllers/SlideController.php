<?php

namespace App\Http\Controllers;

use App\EnumsAndConsts\HttpStatus;
use App\Http\Resources\SlideCollection;
use App\Http\Resources\SlideResource;
use App\Models\Slide;
use Illuminate\Http\Request;

class SlideController extends Controller
{
    /**
     * @param  Request  $request
     *
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Slide::query();
        $query->whereActive(true);
        $query->latest();

        $slides = $query->paginate($request->get('limit', '15'));

        return (new SlideCollection($slides))->additional([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ])->response()->setStatusCode(HttpStatus::OK);
    }

    /**
     * Display the specified resource.
     */
    public function show(Slide $slide)
    {
        return (new SlideResource($slide))->additional([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ])->response()->setStatusCode(HttpStatus::OK);
    }
}
