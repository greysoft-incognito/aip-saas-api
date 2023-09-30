<?php

namespace App\Http\Controllers\Admin;

use App\EnumsAndConsts\HttpStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\EventCollection;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * @param  Request  $request
     *
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Event::query();
        $query->latest();

        $announcements = $query->paginate($request->get('limit', '15'));

        return (new EventCollection($announcements))->additional([
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
            'days' => 'nullable|numeric|min:1|max:365',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif',
            'title' => 'required|string|min:3|max:255',
            'color' => 'nullable|string|in:red,green,blue,orange,teal,primary,secondary,info,black,grey,purple',
            'icon' => 'nullable|string',
            'date' => 'nullable|string',
            'details' => 'nullable|string|min:5|max:550',
            'active' => 'nullable|boolean',
        ]);

        $event = new Event();
        $event->days = $request->days;
        $event->title = $request->title;
        $event->color = $request->color ?? 'primary';
        $event->icon = $request->icon ?? 'info';
        $event->date = $request->date;
        $event->details = $request->details;
        $event->active = $request->active ?? true;
        $event->save();

        return (new EventResource($event))->additional([
            'message' => __('Event ":0" has been created successfully', [$event->title]),
            'status' => 'success',
            'status_code' => HttpStatus::CREATED,
        ])->response()->setStatusCode(HttpStatus::CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        return (new EventResource($event))->additional([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ])->response()->setStatusCode(HttpStatus::OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        $this->validate($request, [
            'days' => 'nullable|numeric|min:1|max:365',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif',
            'title' => 'required|string|min:3|max:255',
            'color' => 'nullable|string|in:red,green,blue,orange,teal,primary,secondary,info,black,grey,purple',
            'icon' => 'nullable|string',
            'date' => 'nullable|string',
            'details' => 'nullable|string|min:5|max:550',
            'active' => 'nullable|boolean',
        ]);

        $event->days = $request->days;
        $event->title = $request->title;
        $event->color = $request->color;
        $event->icon = $request->icon;
        $event->date = $request->date;
        $event->details = $request->details;
        $event->active = $request->active ?? $event->active ?? true;
        $event->save();

        return (new EventResource($event))->additional([
            'message' => __('Event ":0" has been updated successfully', [$event->title]),
            'status' => 'success',
            'status_code' => HttpStatus::ACCEPTED,
        ])->response()->setStatusCode(HttpStatus::ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();

        return (new EventResource($event))->additional([
            'message' => __('Event ":0" has been deleted successfully', [$event->title]),
            'status' => 'success',
            'status_code' => HttpStatus::ACCEPTED,
        ])->response()->setStatusCode(HttpStatus::ACCEPTED);
    }
}
