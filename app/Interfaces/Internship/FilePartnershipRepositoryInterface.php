<?php 

namespace App\Interfaces\Internship;

use App\Models\FilePartnership;


interface FilePartnershipRepositoryInterface
{
    public function getAll($idParnership);
    public function find($idParnership);
    public function create($request);
    public function update(FilePartnership $filePartnership, $request);
    public function delete(FilePartnership $filePartnership);
}

?>