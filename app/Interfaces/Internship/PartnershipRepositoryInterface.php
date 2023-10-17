<?php 

namespace App\Interfaces\Internship;

use App\Models\Partnership;

interface PartnershipRepositoryInterface
{
    public function getAll();
    public function find($id);
    public function create($request);
    public function update(Partnership $partnership, $request);
    public function delete(Partnership $partnership);
}

?>