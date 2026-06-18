<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\LocationResource;
use App\Services\LocationService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    use ResponseTrait;
    public function __construct(
        private LocationService $locationServer
    ) {}

    public function index()
    {
        $locations = $this->locationServer->index();
        return LocationResource::collection($locations);
    }
}
