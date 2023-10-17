<?php 

namespace App\Repositories;

use App\Models\Branch;
use App\Interfaces\BranchCompanyRepositoryInterface;

class BranchCompanyRepository implements BranchCompanyRepositoryInterface
{

    public function __construct(private Branch $branch)
    {
    }

    public function getAll()
    {
        return $this->branch->get();
    }
    
    public function find($slug)
    {
    }

    public function create($request)
    {
    }
    
    public function update($division, $request)
    {
    }
    
    public function delete($division)
    {
    }
    
}

?>