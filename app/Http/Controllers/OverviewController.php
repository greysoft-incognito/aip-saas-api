<?php

namespace App\Http\Controllers;

use App\EnumsAndConsts\HttpStatus;
use App\Http\Resources\UserLocationCollection;
use App\Models\CurrentPrice;
use App\Models\DiseaseOutbreak;
use App\Models\SoilRequirement;
use App\Models\User;
use App\Models\UserLocation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use MatanYadaev\EloquentSpatial\Objects\Geometry;
use MatanYadaev\EloquentSpatial\Objects\LineString;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Objects\Polygon;
use MatanYadaev\EloquentSpatial\SpatialBuilder;

class OverviewController extends Controller
{
    public function index()
    {
        return $this->buildResponse([
            'message' => 'OK',
            'status' => 'success',
            'status_code' => HttpStatus::OK,
            'data' => [
                'stats' => [
                    'farmers' => User::whereType('farmer')->count(),
                    'marketers' => User::whereType('marketer')->count(),
                    'offtakers' => User::whereType('offtaker')->count(),
                    'processsors' => User::whereType('processsor')->count(),
                    'researchers' => User::whereType('researcher')->count(),
                    'transporters' => User::whereType('transporter')->count(),
                    'disease_outbreaks' => DiseaseOutbreak::whereActive(true)->count(),
                    'soil_requirements' => SoilRequirement::query()->count(),
                    'current_prices' => CurrentPrice::get(),
                ]
            ],
        ]);
    }

    /**
     * @param  Request  $request
     *
     * Display a listing of the resource.
     */
    public function locations(Request $request)
    {
        $query = UserLocation::query();
        $query->when($request->has('bounds'), function (SpatialBuilder | Builder $q) use ($request) {
            $ne_lat = $request->bounds["ne"]["lat"];
            $ne_lon = $request->bounds["ne"]["lng"];
            $sw_lat = $request->bounds["sw"]["lat"];
            $sw_lon = $request->bounds["sw"]["lng"];

            $area = new Polygon([
                new LineString([
                    new Point($sw_lon, $sw_lat),
                    new Point($ne_lon, $sw_lon),
                    new Point($ne_lon, $ne_lat),
                    new Point($sw_lon, $ne_lat),
                    new Point($sw_lon, $sw_lat),
                ]),
            ]);
            $q->whereWithin('location', $area);
        });

        $locations = $query->paginate($request->get('limit', 15));

        return (new UserLocationCollection($locations))->additional([
            'message' => 'OK',
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ]);
    }
}
