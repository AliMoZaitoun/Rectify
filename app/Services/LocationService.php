<?php

namespace App\Services;

use App\DAO\LocationDAO;

class LocationService
{
    public function __construct(
        private LocationDAO $locationDAO
    ) {}

    public function index()
    {
        return $this->locationDAO->index();
    }
}
