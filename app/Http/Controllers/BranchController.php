<?php

namespace App\Http\Controllers;

use App\Interfaces\BranchCompanyRepositoryInterface;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function __construct(private BranchCompanyRepositoryInterface $branchCompanyRepositoryInterface)
    {
        
    }
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        $branch = $this->branchCompanyRepositoryInterface->getAll()->map(function ($item) {
            return $item->only(['id','kode_branch','nama_branch']);
        });
        return response()->json([
            'status' => 'success',
            'data' => $branch,
        ]);
    }
}
