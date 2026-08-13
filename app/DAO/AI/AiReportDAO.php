<?php

namespace App\DAO\AI;

use App\Models\AI\AiReport;

class AiReportDAO
{
    public function create(array $data)
    {
        return AiReport::create($data);
    }

    public function getAllPaginated(int $perPage = 15)
    {
        return AiReport::latest()->paginate($perPage);
    }

    public function findById(int $id)
    {
        return AiReport::find($id);
    }
}
