<?php 

namespace App\Repositories;

use App\Models\StatusLevel;
use App\Interfaces\StatusLevelRepositoryInterface;
use App\Models\Commission;
use App\Models\Level;

class StatusLevelRepository implements StatusLevelRepositoryInterface
{
    public function __construct(
        private StatusLevel $statusLevel,
        private Level $level,
        private Commission $commission,
    )
    {}

    public function getAll()
    {
        return $this->statusLevel->get();
    }

    public function find($slug)
    {
        return $this->statusLevel->where('slug', $slug)->firstOrFail();
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
    
    public function getLevels()
    {
        return $this->level->get();
    }

    public function getCommission($commission)
    {
        return $this->commission->findOrFail($commission);
    }
}

?>