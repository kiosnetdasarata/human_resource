<?php 

namespace App\Interfaces;

use Illuminate\Http\Request;

interface JobTitleRepositoryInterface
{
    public function getAll();
    public function find($uuid);
    public function create(Request $request);
    public function update($jobTitle, Request $request);
    public function delete($id);

}

?>