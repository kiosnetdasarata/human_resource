<?php

namespace App\Interfaces;

interface AllowanceRepositoryInterface
{
    public function get();
    public function find($id);
    public function getWithCondition($condition);

}
