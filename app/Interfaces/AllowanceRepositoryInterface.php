<?php

namespace App\Interfaces;

interface AllowanceRepositoryInterface
{
    public function get();
    public function find($id);
    public function getWithCondition($condition);
    public function attachLevel($allowance, $nip, $isIntern);
    public function detachLevel($allowance, $nip);

}
