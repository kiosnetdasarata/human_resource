<?php 

namespace App\Interfaces;

interface BranchCompanyRepositoryInterface
{
    public function getAll();
    public function find($uuid);
    public function create($request);
    public function update($branch, $request);
    public function delete($id);
}

?>