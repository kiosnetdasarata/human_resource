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

    public function getRegencies($province = null)
    {        
        return $this->regency->where('province_id',$province)->select('id','name')->get();
    }

    public function getDistricts($regency = null)
    {
        return $this->district->where('regency_id',$regency)->select('id','name')->get();
    }

    public function getVillages($district = null)
    {
        return $this->village->where('district_id',$district)->select('id','name')->get();
    }

    
}

?>