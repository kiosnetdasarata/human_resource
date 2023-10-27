<?php 

namespace App\Repositories\Internship;

use App\Interfaces\Internship\TraineeshipRepositoryInterface;
use App\Models\Traineeship;

class TraineeshipRepository implements TraineeshipRepositoryInterface
{
    public function __construct(private Traineeship $traineeship) 
    {
    }

    public function getAll()
    {
        return $this->traineeship->with('interviewPoint')->get();
    }

    public function find($id)
    {
        return $this->traineeship->with('interviewPoint')->where('id', $id)->get()->firstOrFail();
    }

    public function findWithTrashes($id)
    {
        return $this->traineeship->withTrashed()->findOrFail($id);
    }

    public function create($request)
    {
        return $this->traineeship->create($request);
    }

    public function update($traineeship, $request)
    {
        return $traineeship->update($request);
    }

    public function delete($traineeship)
    {
        return $traineeship->delete();
    }

}

?>