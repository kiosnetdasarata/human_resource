<?php 

namespace App\Repositories;

use App\Models\Division;
use App\Interfaces\DivisionRepositoryInterface;

class DivisionRepository implements DivisionRepositoryInterface
{

    public function __construct(private Division $division)
    {
    }

    public function getAll()
    {
        return $this->division->get();
    }

    public function find($slug)
    {
        return $this->division->where('slug', $slug)->firstOrFail();
    }

    public function create($request)
    {
        return $this->division->create($request);
    }
    
    public function update($division, $request)
    {
        return $division->update($request);
    }
    
    public function delete($division)
    {
        return $division->delete();
    }
    
}

?>