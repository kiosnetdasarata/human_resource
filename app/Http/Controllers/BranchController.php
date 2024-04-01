<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Interfaces\BranchCompanyRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BranchController extends Controller
{
    public function __construct(
        private BranchCompanyRepositoryInterface $branchCompanyRepositoryInterface,
        private ResponseHelper $response
        )
    {
        
    }
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        try {
            $data = $this->branchCompanyRepositoryInterface->getAll();
            if (!count($data)) {
                throw new ModelNotFoundException();
            } 
            return $this->response->success($data);
        } catch (\Exception $e) {
            return $this->response->error($e);
        }

    }
}
