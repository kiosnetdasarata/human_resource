<?php 

namespace App\Interfaces;

interface ZoneRepositoryInterface
{
    public function getProvinces();
    public function getRegencies($regency);
    public function getDistricts($province);
    public function getVillages($district);
}