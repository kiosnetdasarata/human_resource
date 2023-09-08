<?php 

namespace App\Interfaces;

use Illuminate\Http\Request;

interface SalesRepositoryInterface
{
    public function getAll();
    public function find($uuid);
    public function create($request);
    public function update($sales, $request);
    public function delete($id);

}

?>