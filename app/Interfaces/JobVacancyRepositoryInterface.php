<?php 

namespace App\Interfaces;

interface JobVacancyRepositoryInterface
{
    public function getAll();
    public function getRole();
    public function find($uuid);
    public function findByRole($uuid);
    public function create($request);
    public function update($employee, $request);
    public function delete($id);

}

?>