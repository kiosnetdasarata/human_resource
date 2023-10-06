<?php 

namespace App\Interfaces;


interface UserRepositoryInterface
{
    public function getAll();
    public function find($id);
    public function findByNIP($nip);
    public function setIsactive($user, $status);
    public function create($request);
    public function update($user, $slug);

}

?>