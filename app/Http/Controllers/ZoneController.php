<?php

namespace App\Http\Controllers;

use App\Interfaces\StatusLevelRepositoryInterface;
use App\Interfaces\ZoneRepositoryInterface;

class ZoneController extends Controller
{
    public function __construct(
        private ZoneRepositoryInterface $zoneRepositoryInterface,
    )
    {  
    }

    public function getProvinces()
    {
        return response()->json([
            'status' => 'success',
            'provinces' => $this->zoneRepositoryInterface->getProvinces(),
        ]);
    }

    public function getRegencies($province)
    {
        return response()->json([
            'status' => 'success',
            'regencies' => $this->zoneRepositoryInterface->getRegencies($province),
        ]);
    }

    public function getDistricts($regency)
    {
        return response()->json([
            'status' => 'success',
            'districts' => $this->zoneRepositoryInterface->getDistricts($regency),
        ]);
    }

    public function getVillages($district)
    {
        return response()->json([
            'status' => 'success',
            'villages' => $this->zoneRepositoryInterface->getVillages($district),
        ]);
    }
}
