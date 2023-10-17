<?php 

namespace App\Interfaces;

use Illuminate\Http\Request;

interface RoleRepositoryInterface
{
    public function getAll();
    public function find($uuid);
    public function create($request);
    public function update($jobTitle, $request);
    public function delete($id);

}

?>