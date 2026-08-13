<?php

namespace App\Http\Controllers\V1\Client;

use App\Http\Controllers\Controller;
use App\Services\Client\ChurnRiskService;
use App\Traits\ResponseTrait;

class ChurnRiskController extends Controller
{
    use ResponseTrait;

    public function __construct(
        private ChurnRiskService $churnRiskService
    ) {}

    public function index()
    {
        $alerts = $this->churnRiskService->getAlerts();

        return $this->successResponse($alerts);
    }
}
