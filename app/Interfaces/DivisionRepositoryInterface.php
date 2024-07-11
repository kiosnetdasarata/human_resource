<?php

namespace App\Interfaces;


interface DivisionRepositoryInterface
{
    public function getAll();
    public function find($id);
    public function findSlug($slug);
    public function create($request);
    public function update($division, $request);
    public function delete($division);
}
