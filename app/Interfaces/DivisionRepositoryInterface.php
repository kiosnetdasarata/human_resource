<?php 

namespace App\Interfaces;

use Illuminate\Http\Request;

interface DivisionRepositoryInterface
{
    public function getAll();
    public function find($uuid);
    public function create(Request $request);
    public function update($division, Request $request);
    public function delete($id);
}

?>