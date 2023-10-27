<?php 

namespace App\Interfaces\Internship;

use App\Models\InterviewPoint;

interface InterviewPointRepositoryInterface
{
    public function find($uuid);
    public function latest();
    public function create($request);
    public function update(InterviewPoint $internship, $request);
    public function delete(InterviewPoint $internship);
    public function avg($id);
}

?>