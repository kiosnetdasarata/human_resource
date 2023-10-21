<?php 

namespace App\Interfaces;

interface RoleRepositoryInterface
{
    public function getAll();
    public function find($uuid);
    public function create($request);
    public function update($jobTitle, $request);
    public function delete($id);

}

?>