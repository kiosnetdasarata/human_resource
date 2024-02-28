<?php 

namespace App\Interfaces\Internship;

use App\Models\Traineeship;

interface TraineeshipRepositoryInterface
{
    public function getAll();
    public function find($id);
    public function findWithTrashes($id);
    public function findBySlug($slug);
    public function findByJobVacancy($vacancyId);    
    public function create($request);
    public function update(Traineeship $traineeship, $request);
    public function delete(Traineeship $traineeship);
}