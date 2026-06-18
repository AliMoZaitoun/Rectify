<?php

namespace App\DAO;

use App\Models\Location;

class LocationDAO
{
    public function index()
    {
        return Location::all();
    }
}
