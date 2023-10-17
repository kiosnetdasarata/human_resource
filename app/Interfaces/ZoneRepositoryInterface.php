<?php 

namespace App\Interfaces;

interface ZoneRepositoryInterface
{
    public function getProvinces();
    public function getRegencies();
    public function getDistricts();
    public function getVillages();
}

?>