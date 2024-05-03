<?php

namespace App\Repositories;

use App\Interfaces\ZoneRepositoryInterface;
use App\Models\District;
use App\Models\Province;
use App\Models\Regency;
use App\Models\Village;

class ZoneRepository implements ZoneRepositoryInterface
{

    public function __construct(
        private Province $province,
        private Regency $regency,
        private District $district,
        private Village $village,
        )
    {
    }

    public function getProvinces()
    {
        return $this->province->select('id','name')->get();
    }

    public function getRegencies($province)
    {
        return $this->regency->select('id','name')->where('province_id',$province)->get();
    }

    public function getDistricts($regency)
    {
        return $this->district->select('id','name')->where('regency_id',$regency)->get();
    }

    public function getVillages($district)
    {
        return $this->village->select('id','name')->where('district_id',$district)->get();
    }
}
