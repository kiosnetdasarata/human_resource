<?php 

namespace App\Interfaces;

use Illuminate\Http\Request;

interface TechnicianRepositoryInterface
{
    public function getAll();
    public function find($uuid);
    public function create($request);
    public function update($technician, $request);
    public function delete($id);

}

?>