<?php

namespace App\Http\Controllers;

use App\EnumsAndConsts\HttpStatus;
use App\Http\Resources\DiseaseOutbreakCollection;
use App\Http\Resources\DiseaseOutbreakResource;
use App\Models\DiseaseOutbreak;
use Illuminate\Http\Request;

class DiseaseOutbreakController extends Controller
{
    /**
     * @param  Request  $request
     *
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DiseaseOutbreak::query();
        $query->latest();

        $diseases = $query->paginate($request->get('limit', '15'));

        return (new DiseaseOutbreakCollection($diseases))->additional([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ])->response()->setStatusCode(HttpStatus::OK);
    }

    /**
     * Display the specified resource.
     */
    public function show(DiseaseOutbreak $disease)
    {
        return (new DiseaseOutbreakResource($disease))->additional([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ])->response()->setStatusCode(HttpStatus::OK);
    }
}
