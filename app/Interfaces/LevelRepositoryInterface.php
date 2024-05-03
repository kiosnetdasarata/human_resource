<?php

namespace App\Interfaces;

interface LevelRepositoryInterface
{
    public function getAll();
    public function find($id);
    public function create($request);
    public function update($level, $request);
    public function delete($level);
}
