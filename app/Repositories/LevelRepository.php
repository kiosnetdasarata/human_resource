<?php 

namespace App\Repositories;

use App\Models\Level;
use App\Interfaces\LevelRepositoryInterface;

class LevelRepository implements LevelRepositoryInterface
{

    public function __construct(private Level $level)
    {
    }

    public function getAll()
    {
        return $this->level->get();
    }

    public function find($kodeJabatan)
    {
        return $this->level->where('divisi_id', $kodeJabatan)->firstOrFail();
    }
    
    public function create($request)
    {
        return $this->level->create($request);
    }
    
    public function update($level, $request)
    {
        return $level->update($request);
    }
    
    public function delete($level)
    {
        return $level->delete();
    }
    
}

?>