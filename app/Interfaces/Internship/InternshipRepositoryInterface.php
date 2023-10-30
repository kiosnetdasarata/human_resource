<?php 

namespace App\Interfaces\Internship;

use App\Models\Internship;

interface InternshipRepositoryInterface
{
    public function getAll();
    public function getAllThisYear();
    public function find($uuid);
    public function findBySlug($slug);
    public function create($request);
    public function update(Internship $internship, $request);
    public function delete(Internship $internship);
}

?>