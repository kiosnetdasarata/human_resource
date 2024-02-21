<?php 

namespace App\Interfaces;


interface DivisionRepositoryInterface
{
    public function getAll();
    public function find($ID);
    public function findSlug($slug);
    public function getEmployee($id);
    public function getEmployeeArchive($id);
    public function create($request);
    public function update($division, $request);
    public function delete($division);
}

?>