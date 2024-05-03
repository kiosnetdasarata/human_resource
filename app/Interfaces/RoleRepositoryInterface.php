<?php

namespace App\Interfaces;

interface RoleRepositoryInterface
{
    public function getAll();
    public function find($uuid);
    public function create($request);
    public function update($role, $request);
    public function delete($role);
}
