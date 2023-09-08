<?php 

namespace App\Interfaces;

use Illuminate\Http\Request;

interface BranchCompanyRepositoryInterface
{
    public function getAll();
    public function find($uuid);
    public function create($request);
    public function update($division, $request);
    public function delete($id);
}

?>