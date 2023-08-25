<?php 

namespace App\Interfaces;

use Illuminate\Http\Request;

interface EmployeeRepositoryInterface
{
    public function getAll();
    public function find($uuid);
    public function findSlug($uuid);
    public function create(Request $request);
    public function update($employee, Request $request);
    public function delete($id);

}

?>