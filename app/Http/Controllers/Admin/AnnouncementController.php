<?php

namespace App\Http\Controllers\Admin;

use App\EnumsAndConsts\HttpStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\AnnouncementCollection;
use App\Http\Resources\AnnouncementResource;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /**
     * @param  Request  $request
     *
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Announcement::query();
        $query->latest();

        $announcements = $query->paginate($request->get('limit', '15'));

        return (new AnnouncementCollection($announcements))->additional([
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
            'title' => 'required|string|min:3|max:255',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif',
            'content' => 'required|string|min:3|max:520',
            'active' => 'nullable|boolean',
        ]);

        $announcement = new Announcement();
        $announcement->title = $request->title;
        $announcement->active = $request->active ?? true;
        $announcement->content = $request->content;
        $announcement->save();

        return (new AnnouncementResource($announcement))->additional([
            'message' => __('Announcement ":0" has been created successfully', [$announcement->title]),
            'status' => 'success',
            'status_code' => HttpStatus::CREATED,
        ])->response()->setStatusCode(HttpStatus::CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Announcement $announcement)
    {
        return (new AnnouncementResource($announcement))->additional([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ])->response()->setStatusCode(HttpStatus::OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Announcement $announcement)
    {
        $this->validate($request, [
            'title' => 'required|string|min:3|max:255',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif',
            'content' => 'required|string|min:3|max:520',
            'active' => 'nullable|boolean',
        ]);

        $announcement->title = $request->title ?? $announcement->title;
        $announcement->active = $request->active ?? $announcement->active;
        $announcement->content = $request->content ?? $announcement->content;
        $announcement->save();

        return (new AnnouncementResource($announcement))->additional([
            'message' => __('Announcement ":0" has been updated successfully', [$announcement->title]),
            'status' => 'success',
            'status_code' => HttpStatus::ACCEPTED,
        ])->response()->setStatusCode(HttpStatus::ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return (new AnnouncementResource($announcement))->additional([
            'message' => __('Announcement ":0" has been deleted successfully', [$announcement->title]),
            'status' => 'success',
            'status_code' => HttpStatus::ACCEPTED,
        ])->response()->setStatusCode(HttpStatus::ACCEPTED);
    }
}
