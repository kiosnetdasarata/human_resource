<?php 

namespace App\Repositories\Internship;

use App\Models\InternshipContract;
use App\Interfaces\Internship\InternshipContractRepositoryInterface;


class InternshipContractRepository implements InternshipContractRepositoryInterface
{
    public function __construct(private InternshipContract $internshipContract)
    {
    }

    public function getAll()
    {
        return $this->internshipContract->with('internship')->get();
    }

    public function find($uuid)
    {
        return $this->internshipContract->with('internship')->where('id', $uuid)->get()->first();
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

?>