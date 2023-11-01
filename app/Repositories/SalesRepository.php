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
    
    public function update($id, $request)
    {
        $sales = $this->find($id);
        $data = collect($request)->diffAssoc($sales);
        return $sales->update($data->all());
    }
    
    public function delete($sales)
    {
        return $sales->delete();
    }
    
}

?>