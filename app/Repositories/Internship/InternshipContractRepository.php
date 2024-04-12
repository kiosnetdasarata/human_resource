<?php

namespace App\Repositories\Internship;

use App\Models\Internship;
use App\Models\InternshipContract;
use App\Interfaces\Internship\InternshipContractRepositoryInterface;


class InternshipContractRepository implements InternshipContractRepositoryInterface
{
    public function __construct(
        private InternshipContract $internshipContract,
        private Internship $internship)
    {
    }

    public function getAll($id)
    {
        $internship = $this->internship->findOrFail($id);
        return $internship->contractsHistory;
    }

    public function find($id)
    {
        $internship = $this->internship->findOrFail($id);
        return $internship->contract;
    }

    public function create($request)
    {
        return $this->internshipContract->create($request);
    }

    public function update($internshipContract, $request)
    {
        return $internshipContract->update($request);
    }

    public function delete($internshipContract)
    {
        return $internshipContract->delete();
    }

}
