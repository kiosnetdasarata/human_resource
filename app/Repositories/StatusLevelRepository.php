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
    
    public function getLevels()
    {
        return $this->level->get();
    }

    public function getLevelByCommission($level)
    {
        return $this->commission->where('level_id', $level);
    }
}

?>