<?php 

namespace App\Repositories\Internship;

use App\Models\Internship;
use App\Models\InternshipContract;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
        $intern = $this->internship
                    ->with('internshipContract')
                    ->where('id', $id)
                    ->firstOrFail();
        return $intern->internshipContract;
    }

    public function find($id)
    {
        $internship = $this->internship
                            ->with(['internshipContract' => fn($query) => 
                                $query->where('is_expired', 0)
                                      ->where('date_expired', '>', now())
                            ])
                            ->where('id', $id)
                            ->firstOrFail();
        return $internship->internshipContract->first();
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