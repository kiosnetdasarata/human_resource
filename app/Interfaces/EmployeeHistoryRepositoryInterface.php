<?php 

namespace App\Interfaces;

use Illuminate\Http\Request;

interface EmployeeHistoryRepositoryInterface
{
    public function getAll();
    public function find($uuid);
    public function create($request);
    public function update($employeeHistory, $request);
    public function delete($id);

}

?>