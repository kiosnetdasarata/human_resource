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
        return $this->partnership->with(['filePartnership' => function ($query) {
            $query->latest();
        }])->where('id', $id)->firstOrFail();
    }

    public function getInternship($id, $status)
    {
        $partnership =  $this->partnership->with(['internship' => function($query) use ($status){
            $query->where('status_internship', $status);
        }])->where('id', $id)->firstOrFail();
        
        return $partnership->internship;
    }

    public function getInternshipArchive($id, $status)
    {
        $partnership = $this->partnership->with(['internship' => function($query) use ($status){
            $query->where('status_internship', $status)->withTrashed();
        }])->where('id', $id)->firstOrFail();
        return $partnership->internship;
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