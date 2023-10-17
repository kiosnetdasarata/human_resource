<?php 

namespace App\Interfaces;


interface DivisionRepositoryInterface
{
    public function getAll();
    public function find($uuid);
    public function create($request);
    public function update($division, $request);
    public function delete($id);
}

?>