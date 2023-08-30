<?php 

namespace App\Repositories;

use App\Models\Technician;
use App\Interfaces\TechnicianRepositoryInterface;

class TechnicianRepository implements TechnicianRepositoryInterface
{

    public function __construct(private Technician $technician)
    {
    }

    public function getAll()
    {
        return $this->technician->get();
    }

    public function find($id)
    {
        return $this->technician->find($id);
    }
    
    public function create($request)
    {
        return $this->technician->create($request);
    }
    
    public function update($technician, $request)
    {
        return $technician->update($request);
    }
    
    public function delete($technician)
    {
        return $technician->delete();
    }
}

?>