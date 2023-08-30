<?php 

namespace App\Repositories;

use App\Models\StatusLevel;
use App\Interfaces\StatusLevelRepositoryInterface;

class StatusLevelRepository implements StatusLevelRepositoryInterface
{

    public function __construct(private StatusLevel $statusLevel)
    {
    }

    public function getAll()
    {
        return $this->statusLevel->get();
    }

    public function find($id)
    {
        return $this->statusLevel->find($id);
    }
    
    public function create($request)
    {
        return $this->statusLevel->create($request);
    }
    
    public function update($statusLevel, $request)
    {
        return $statusLevel->update($request);
    }
    
    public function delete($statusLevel)
    {
        return $statusLevel->delete();
    }
    
}

?>