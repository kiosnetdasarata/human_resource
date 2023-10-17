<?php 

namespace App\Repositories\Internship;

use App\Models\Partnership;
use App\Interfaces\Internship\PartnershipRepositoryInterface;

class PartnershipRepository implements PartnershipRepositoryInterface
{
    public function __construct(private Partnership $partnership) 
    {
    }

    public function getAll()
    {
        return $this->partnership->get();
    }

    public function find($id)
    {
        return $this->partnership->findOrFail($id);
    }

    public function create($request)
    {
        return $this->partnership->create($request);
    }

    public function update($partnership, $request)
    {
        return $partnership->update($request);
    }

    public function delete($partnership)
    {
        return $partnership->delete();
    }

}

?>