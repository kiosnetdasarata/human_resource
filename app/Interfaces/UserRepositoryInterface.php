<?php 

namespace App\Interfaces;


interface UserRepositoryInterface
{
    public function getAll();
    public function find($id);
    public function create($request);
    public function update($user, $request);
    public function delete($id);

}

?>