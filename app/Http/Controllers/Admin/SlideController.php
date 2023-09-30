<?php

namespace App\Http\Controllers\Admin;

use App\EnumsAndConsts\HttpStatus;
use App\Http\Controllers\Controller;
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
        $query->latest();

        $announcements = $query->paginate($request->get('limit', '15'));

        return (new SlideCollection($announcements))->additional([
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
            'title' => 'required|string|min:3|max:255',
            'line1' => 'nullable|string|min:3|max:255',
            'line2' => 'nullable|string|min:3|max:255',
            'line3' => 'nullable|string|min:3|max:255',
            'active' => 'nullable|boolean',
        ]);

        $slide = new Slide();
        $slide->title = $request->title;
        $slide->line1 = $request->line1;
        $slide->line2 = $request->line2;
        $slide->line3 = $request->line3;
        $slide->active = $request->active ?? true;
        $slide->save();

        return (new SlideResource($slide))->additional([
            'message' => __('Slide ":0" has been created successfully', [$slide->title]),
            'status' => 'success',
            'status_code' => HttpStatus::CREATED,
        ])->response()->setStatusCode(HttpStatus::CREATED);
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Slide $slide)
    {
        $this->validate($request, [
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif',
            'title' => 'required|string|min:3|max:255',
            'line1' => 'nullable|string|min:3|max:255',
            'line2' => 'nullable|string|min:3|max:255',
            'line3' => 'nullable|string|min:3|max:255',
            'active' => 'nullable|boolean',
        ]);

        $slide->title = $request->title ?? $slide->title;
        $slide->line1 = $request->line1 ?? $slide->line1;
        $slide->line2 = $request->line2 ?? $slide->line2;
        $slide->line3 = $request->line3 ?? $slide->line3;
        $slide->active = $request->active ?? $slide->active;
        $slide->save();

        return (new SlideResource($slide))->additional([
            'message' => __('Slide ":0" has been updated successfully', [$slide->title]),
            'status' => 'success',
            'status_code' => HttpStatus::ACCEPTED,
        ])->response()->setStatusCode(HttpStatus::ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Slide $slide)
    {
        $slide->delete();

        return (new SlideResource($slide))->additional([
            'message' => __('Slide ":0" has been deleted successfully', [$slide->title]),
            'status' => 'success',
            'status_code' => HttpStatus::ACCEPTED,
        ])->response()->setStatusCode(HttpStatus::ACCEPTED);
    }
}
