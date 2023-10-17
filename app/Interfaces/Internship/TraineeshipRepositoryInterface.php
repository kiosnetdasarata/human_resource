<?php 

namespace App\Interfaces\Internship;

use App\Models\Traineeship;

interface TraineeshipRepositoryInterface
{
    public function getAll();
    public function find($slug);
    public function findWithTrashes($slug);
    public function create($request);
    public function update(Traineeship $traineeship, $request);
    public function delete(Traineeship $traineeship);
}

?>