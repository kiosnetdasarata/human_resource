<?php 

namespace App\Interfaces;

use Illuminate\Http\Request;

interface StatusLevelRepositoryInterface
{
    public function getAll();
    public function find($uuid);
    public function create($request);
    public function update($statusLevel, $request);
    public function delete($id);
    public function getLevels();
    public function getCommission($level);
}

?>