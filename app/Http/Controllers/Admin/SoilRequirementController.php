<?php

namespace App\Http\Controllers\Admin;

use App\EnumsAndConsts\HttpStatus;
use App\Http\Controllers\Controller;
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

        $announcements = $query->paginate($request->get('limit', '15'));

        return (new SoilRequirementCollection($announcements))->additional([
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
            'crop' => 'required|string|min:3|max:255',
            'water' => 'nullable|string|min:3|max:255',
            'period' => 'nullable|string|min:3|max:255',
            'details' => 'nullable|string|min:3|max:550',
            'location' => 'nullable|string|min:3|max:255',
            'temperature' => 'nullable|string|min:3|max:255',
        ]);

        $requirement = new SoilRequirement();
        $requirement->crop = $request->crop;
        $requirement->water = $request->water;
        $requirement->period = $request->period;
        $requirement->details = $request->details;
        $requirement->location = $request->location;
        $requirement->temperature = $request->temperature;
        $requirement->save();

        return (new SoilRequirementResource($requirement))->additional([
            'message' => __('Soil Requirement for ":0" has been created successfully', [$requirement->item]),
            'status' => 'success',
            'status_code' => HttpStatus::CREATED,
        ])->response()->setStatusCode(HttpStatus::CREATED);
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SoilRequirement $requirement)
    {
        $this->validate($request, [
            'crop' => 'required|string|min:3|max:255',
            'water' => 'nullable|string|min:3|max:255',
            'period' => 'nullable|string|min:3|max:255',
            'details' => 'nullable|string|min:3|max:550',
            'location' => 'nullable|string|min:3|max:255',
            'temperature' => 'nullable|string|min:3|max:255',
        ]);

        $requirement->crop = $request->crop;
        $requirement->water = $request->water;
        $requirement->period = $request->period;
        $requirement->details = $request->details;
        $requirement->location = $request->location;
        $requirement->temperature = $request->temperature;
        $requirement->save();

        return (new SoilRequirementResource($requirement))->additional([
            'message' => __('Soil Requirement for ":0" has been updated successfully', [$requirement->item]),
            'status' => 'success',
            'status_code' => HttpStatus::ACCEPTED,
        ])->response()->setStatusCode(HttpStatus::ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SoilRequirement $requirement)
    {
        $requirement->delete();

        return (new SoilRequirementResource($requirement))->additional([
            'message' => __('Soil Requirement for ":0" has been deleted successfully', [$requirement->item]),
            'status' => 'success',
            'status_code' => HttpStatus::ACCEPTED,
        ])->response()->setStatusCode(HttpStatus::ACCEPTED);
    }
}
