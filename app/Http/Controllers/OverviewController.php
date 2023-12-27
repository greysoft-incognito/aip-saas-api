<?php

namespace App\Http\Controllers;

use App\EnumsAndConsts\HttpStatus;
use App\Http\Resources\CurrentPriceCollection;
use App\Http\Resources\UserLocationCollection;
use App\Models\CurrentPrice;
use App\Models\DiseaseOutbreak;
use App\Models\SoilRequirement;
use App\Models\User;
use App\Models\UserLocation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use MatanYadaev\EloquentSpatial\Objects\Geometry;
use MatanYadaev\EloquentSpatial\Objects\LineString;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Objects\Polygon;
use MatanYadaev\EloquentSpatial\SpatialBuilder;

class OverviewController extends Controller
{
    public function index(Request $request)
    {
        $stats = str($request->get('visible', join(',', [
            'farmers',
            'aggregators',
            'suppliers',
            'processors',
            'offtakers',
            'logistics',
            'researchers',
            'disease_outbreaks',
            'soil_requirements',
            'current_prices'
        ])))->explode(',')->mapWithKeys(function ($key) use ($request) {
            if ($request->group) {
                return [$key => self::counter($key, $request->group)];
            }

            return [$key => self::counter($request->group, $key)];
        });

        return $this->buildResponse([
            'message' => 'OK',
            'status' => 'success',
            'status_code' => HttpStatus::OK,
            'data' => [
                'stats' => $stats
            ],
        ]);
    }

    private static function counter(?string $type, ?string $group = null): int|ResourceCollection
    {
        if ($group === 'disease_outbreaks') {
            return DiseaseOutbreak::whereActive(true)->count();
        } elseif ($group === 'soil_requirements') {
            return SoilRequirement::query()->count();
        } elseif ($group === 'current_prices') {
            return new CurrentPriceCollection(CurrentPrice::orderBy('item')->get());
        }
        return User::query()
        ->when($group, function (Builder $q) use ($group) {
            $q->where('group', $group);
        })
        ->when($type, function (Builder $q) use ($type) {
            $q->where('type', $type);
        })->count();
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