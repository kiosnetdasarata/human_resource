<?php 

namespace App\Interfaces\Internship;

use App\Models\FilePartnership;


interface FilePartnershipRepositoryInterface
{
    public function getAll();
    public function find($mitraid);
    public function create($request);
    public function update(FilePartnership $filePartnership, $request);
    public function delete(FilePartnership $filePartnership);
}

?>