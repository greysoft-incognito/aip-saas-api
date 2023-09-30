<?php

namespace App\Http\Controllers\Admin;

use App\EnumsAndConsts\HttpStatus;
use App\Http\Controllers\Controller;
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

        $announcements = $query->paginate($request->get('limit', '15'));

        return (new DiseaseOutbreakCollection($announcements))->additional([
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
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif',
            'name' => 'required|string|min:3|max:255',
            'active' => 'nullable|boolean',
            'reported_at' => 'nullable|string',
            'description' => 'nullable|string|min:5|max:1550',
        ]);

        $outbreak = new DiseaseOutbreak();
        $outbreak->name = $request->name;
        $outbreak->active = $request->active ?? true;
        $outbreak->reported_at = $request->reported_at ?? now();
        $outbreak->description = $request->description;
        $outbreak->save();

        return (new DiseaseOutbreakResource($outbreak))->additional([
            'message' => __('Disease Outbreak ":0" has been created successfully', [$outbreak->name]),
            'status' => 'success',
            'status_code' => HttpStatus::CREATED,
        ])->response()->setStatusCode(HttpStatus::CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(DiseaseOutbreak $outbreak)
    {
        return (new DiseaseOutbreakResource($outbreak))->additional([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ])->response()->setStatusCode(HttpStatus::OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DiseaseOutbreak $outbreak)
    {
        $this->validate($request, [
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif',
            'name' => 'required|string|min:3|max:255',
            'active' => 'nullable|boolean',
            'reported_at' => 'nullable|string',
            'description' => 'nullable|string|min:5|max:1550',
        ]);

        $outbreak->name = $request->name ?? $outbreak->name ;
        $outbreak->active = $request->active ?? $outbreak->active  ?? true;
        $outbreak->reported_at = $request->reported_at ?? $outbreak->reported_at ?? now();
        $outbreak->description = $request->description ?? $outbreak->description ;
        $outbreak->save();

        return (new DiseaseOutbreakResource($outbreak))->additional([
            'message' => __('Disease Outbreak ":0" has been updated successfully', [$outbreak->name]),
            'status' => 'success',
            'status_code' => HttpStatus::ACCEPTED,
        ])->response()->setStatusCode(HttpStatus::ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DiseaseOutbreak $outbreak)
    {
        $outbreak->delete();

        return (new DiseaseOutbreakResource($outbreak))->additional([
            'message' => __('Disease Outbreak ":0" has been deleted successfully', [$outbreak->name]),
            'status' => 'success',
            'status_code' => HttpStatus::ACCEPTED,
        ])->response()->setStatusCode(HttpStatus::ACCEPTED);
    }
}
