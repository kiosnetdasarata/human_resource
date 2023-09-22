<?php 

namespace App\Repositories;

use App\Models\JobTitle;
use App\Interfaces\JobTitleRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class JobTitleRepository implements JobTitleRepositoryInterface
{

    public function __construct(private JobTitle $jobTitle)
    {
    }

    public function getAll($division = null)
    {
        $query = $this->jobTitle;
        if ($division != null) {
            $query = $query->where('divisions_id', $division);
        }

        $jobTitle = $query->get();

        if ($jobTitle->isEmpty()) {
            throw new ModelNotFoundException;
        }

        return $jobTitle;
    }

    public function find($id)
    {
        return $this->jobTitle->with('division')->find($id);
    }
    
    public function create($request)
    {
        return $this->jobTitle->create($request);
    }
    
    public function update($jobTitle, $request)
    {
        return $jobTitle->update($request);
    }
    
    public function delete($jobTitle)
    {
        return $jobTitle->delete();
    }
    
}

?>