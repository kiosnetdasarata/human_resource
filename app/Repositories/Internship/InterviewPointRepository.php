<?php 

namespace App\Repositories\Internship;

use App\Interfaces\Internship\InterviewPointRepositoryInterface;
use App\Models\InterviewPoint;
use App\Models\Traineeship;

class InterviewPointRepository implements InterviewPointRepositoryInterface
{
    public function __construct(private InterviewPoint $internshipPoint, private Traineeship $traineeship) 
    {
    }

    public function find($uuid)
    {
        return $this->internshipPoint->with('traineeship')->with('id', $uuid)->firstOrFail();
    }

    public function latest()
    {
        return $this->internshipPoint->latest()->first();
    }

    public function create($request)
    {
        return $this->internshipPoint->create($request);
    }

    public function update($internshipPoint, $request)
    {
        return $internshipPoint->update($request);
    }

    public function delete($internshipPoint)
    {
        return $internshipPoint->delete();
    }

    public function avg($id)
    {
        $iP = $this->find($id);
        $nilai = 0;
        foreach($iP as $key => $nilai) {
            $nilai += (int)$nilai;
        }
    }

}

?>