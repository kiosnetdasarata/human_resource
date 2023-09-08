<?php 

namespace App\Interfaces;

use Illuminate\Http\Request;

interface EmployeeRepositoryInterface
{
    public function getAll();
    public function find($uuid);
    public function findBySlug($uuid);
    public function create($request);
    public function update($employee, $request);
    public function delete($id);

}

?>