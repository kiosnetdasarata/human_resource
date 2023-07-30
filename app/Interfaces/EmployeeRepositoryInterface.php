<?php 

namespace App\Interfaces;

use Illuminate\Http\Request;

interface EmployeeRepositoryInterface
{
    public function getAll();
    public function find($id);
    public function create(Request $request);
    public function update($employee, Request $request);
    public function delete($id);

}

?>