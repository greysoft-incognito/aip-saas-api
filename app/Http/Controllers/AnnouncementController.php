<?php

namespace App\Http\Controllers;

use App\EnumsAndConsts\HttpStatus;
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
}
