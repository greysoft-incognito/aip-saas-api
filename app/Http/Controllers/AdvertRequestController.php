<?php

namespace App\Http\Controllers;

use App\EnumsAndConsts\HttpStatus;
use App\Http\Resources\AdvertRequestCollection;
use App\Http\Resources\AdvertRequestResource;
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
        /** @var \App\Models\User $user */
        $user = $request->user();

        $query = $user->advertRequests();
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
        $adv->hide_texts = $request->hide_texts;
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
    public function show(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $adv = $user->advertRequests()->findOrFail($id);

        return (new AdvertRequestResource($adv))->additional([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ])->response()->setStatusCode(HttpStatus::OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $adv = $user->advertRequests()->findOrFail($id);

        $this->validate($request, [
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif',
            'title' => 'required|string|min:3|max:255',
            'line1' => 'nullable|string|min:3|max:255',
            'line2' => 'nullable|string|min:3|max:255',
            'line3' => 'nullable|string|min:3|max:255',
            'duration' => 'nullable|numeric|min:1',
            'hide_texts' => 'nullable|boolean',
        ]);

        $adv->title = $request->title ?? $adv->title;
        $adv->line1 = $request->line1 ?? $adv->line1;
        $adv->line2 = $request->line2 ?? $adv->line2;
        $adv->line3 = $request->line3 ?? $adv->line3;
        $adv->hide_texts = $request->hide_texts ?? $adv->hide_texts;
        $adv->duration = $request->duration ?? $adv->duration;

        if ($request->send_now) {
            $adv->status = 'pending';
        } else {
            $adv->status = 'draft';
        }
        $adv->save();

        return (new AdvertRequestResource($adv))->additional([
            'message' => __('Advert Request ":0" has been :1 successfully:2', [
                $adv->title,
                $request->send_now ? 'sent' : 'updated',
                $request->send_now ? ', we would reach out to you with further processing details.' : '.'
            ]),
            'status' => 'success',
            'status_code' => HttpStatus::ACCEPTED,
        ])->response()->setStatusCode(HttpStatus::ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $adv = $user->advertRequests()->findOrFail($id);
        $adv->delete();

        return (new AdvertRequestResource($adv))->additional([
            'message' => __('Advert Request ":0" has been deleted successfully', [$adv->title]),
            'status' => 'success',
            'status_code' => HttpStatus::ACCEPTED,
        ])->response()->setStatusCode(HttpStatus::ACCEPTED);
    }
}