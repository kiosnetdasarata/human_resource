<?php 

namespace App\Repositories;

use App\Models\Sales;
use App\Interfaces\SalesRepositoryInterface;

class SalesRepository implements SalesRepositoryInterface
{

    public function __construct(private Sales $sales)
    {
    }

    public function getAll()
    {
        return $this->sales->get();
    }

    public function find($uuid)
    {
        return $this->sales->findOrFail($uuid);
    }
    
    public function create($request)
    {
        return $this->sales->create($request);
    }
    
    public function update($sales, $request)
    {
        return $sales->update($request);
    }
    
    public function delete($sales)
    {
        return $sales->delete();
    }
    
}

?>