<?php

namespace App\Http\Controllers\Admin;

use App\EnumsAndConsts\HttpStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\AdvertRequestCollection;
use App\Http\Resources\AdvertRequestResource;
use App\Models\AdvertRequest;
use Illuminate\Http\Request;

class AdvertRequestController extends Controller
{
    /**
     * @param  Request  $request
     *
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = AdvertRequest::query();
        $query->whereNot('status', 'draft');
        $query->latest();

        $advs = $query->paginate($request->get('limit', '15'));

        return (new AdvertRequestCollection($advs))->additional([
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
        /** @var \App\Models\User $user */
        $user = $request->user();

        $this->validate($request, [
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif',
            'title' => 'required|string|min:3|max:255',
            'line1' => 'nullable|string|min:3|max:255',
            'line2' => 'nullable|string|min:3|max:255',
            'line3' => 'nullable|string|min:3|max:255',
            'duration' => 'nullable|numeric|min:1',
            'hide_texts' => 'nullable|boolean',
        ]);

        $adv = $user->advertRequests()->make();

        $adv->title = $request->title;
        $adv->line1 = $request->line1;
        $adv->line2 = $request->line2;
        $adv->line3 = $request->line3;
        $adv->line3 = $request->line3;
        $adv->hide_texts = $request->hide_texts ?? false;
        $adv->duration = $request->duration ?? 24;
        $adv->save();

        return (new AdvertRequestResource($adv))->additional([
            'message' => __('Advert Request ":0" has been created successfully', [$adv->title]),
            'status' => 'success',
            'status_code' => HttpStatus::CREATED,
        ])->response()->setStatusCode(HttpStatus::CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(AdvertRequest $advert)
    {
        return (new AdvertRequestResource($advert))->additional([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ])->response()->setStatusCode(HttpStatus::OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AdvertRequest $advert)
    {
        $this->validate($request, [
            'image' => 'missing',
            'title' => 'required|string|min:3|max:255',
            'line1' => 'nullable|string|min:3|max:255',
            'line2' => 'nullable|string|min:3|max:255',
            'line3' => 'nullable|string|min:3|max:255',
            'duration' => 'nullable|numeric|min:1',
            'hide_texts' => 'nullable|boolean',
        ]);

        $adv = $advert;
        $adv->title = $request->title ?? $adv->title;
        $adv->line1 = $request->line1 ?? $adv->line1;
        $adv->line2 = $request->line2 ?? $adv->line2;
        $adv->line3 = $request->line3 ?? $adv->line3;
        $adv->hide_texts = $request->hide_texts ?? $adv->hide_texts;
        $adv->duration = $request->duration ?? $adv->duration;

        if ($request->approve_now) {
            $adv->status = 'approved';
            $slide = $adv->slide()->make();
            $slide->title = $adv->title;
            $slide->image = $adv->image;
            $slide->line2 = $adv->line2;
            $slide->line3 = $adv->line3;
            $slide->line3 = $adv->line3;
            $slide->hide_texts = $adv->hide_texts;
            $slide->expires_at = now()->addHours($adv->duration);
            $slide->active = true;
            $slide->save();
        } elseif ($request->reject_now) {
            $adv->status = 'rejected';
        }

        $adv->save();

        return (new AdvertRequestResource($adv))->additional([
            'message' => __('Advert Request ":0" has been :1 successfully.', [
                $adv->title,
                $request->send_now ? 'sent' : ( $request->reject_now ? 'rejected' : 'updated' ),
            ]),
            'status' => 'success',
            'status_code' => HttpStatus::ACCEPTED,
        ])->response()->setStatusCode(HttpStatus::ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AdvertRequest $advert)
    {
        $advert->slide()->delete();
        $advert->delete();

        return (new AdvertRequestResource($advert))->additional([
            'message' => __('Advert Request ":0" has been deleted successfully', [$advert->title]),
            'status' => 'success',
            'status_code' => HttpStatus::ACCEPTED,
        ])->response()->setStatusCode(HttpStatus::ACCEPTED);
    }
}