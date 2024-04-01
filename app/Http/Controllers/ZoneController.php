<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Interfaces\StatusLevelRepositoryInterface;
use App\Interfaces\ZoneRepositoryInterface;

class ZoneController extends Controller
{
    public function __construct(
        private ZoneRepositoryInterface $zoneRepositoryInterface,
        private ResponseHelper $response
    )
    {  
    }

    public function getProvinces()
    {
        $data = $this->zoneRepositoryInterface->getProvinces();
        return $this->response->success($data);
    }

    public function getRegencies($province)
    {
        $data = $this->zoneRepositoryInterface->getRegencies($province);
        return $this->response->success($data);
    }

    public function getDistricts($regency)
    {
        $data = $this->zoneRepositoryInterface->getDistricts($regency);
        return $this->response->success($data);
    }

    public function getVillages($district)
    {
        $data = $this->zoneRepositoryInterface->getVillages($district);
        return $this->response->success($data);
    }
}
