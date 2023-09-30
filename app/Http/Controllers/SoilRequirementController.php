<?php

namespace App\Http\Controllers;

use App\EnumsAndConsts\HttpStatus;
use App\Http\Resources\SoilRequirementCollection;
use App\Http\Resources\SoilRequirementResource;
use App\Models\SoilRequirement;
use Illuminate\Http\Request;

class SoilRequirementController extends Controller
{
    /**
     * @param  Request  $request
     *
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SoilRequirement::query();
        $query->latest();

        $requirements = $query->paginate($request->get('limit', '15'));

        return (new SoilRequirementCollection($requirements))->additional([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ])->response()->setStatusCode(HttpStatus::OK);
    }

    /**
     * Display the specified resource.
     */
    public function show(SoilRequirement $requirement)
    {
        return (new SoilRequirementResource($requirement))->additional([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ])->response()->setStatusCode(HttpStatus::OK);
    }
}
