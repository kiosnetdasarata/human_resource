<?php 

namespace App\Repositories\Internship;

use App\Models\Internship;
use App\Interfaces\Internship\InternshipRepositoryInterface;

class InternshipRepository implements InternshipRepositoryInterface
{
    public function __construct(private Internship $internship) 
    {
    }

    public function getAll()
    {
        return $this->internship->get();
    }

    public function findBySlug($slug)
    {
        return $this->internship->where('slug', 'LIKE','%'. $slug.'%')->get();
    }

    public function find($uuid)
    {
        return $this->internship->findOrFail($uuid);
    }

    
    public function findId($id)
    {
        return $this->internship->where('id', $id)->firstOrFail();
    }

    public function create($request)
    {
        return $this->internship->create($request);
    }

    public function update($internship, $request)
    {
        return $internship->update($request);
    }

    public function delete($internship)
    {
        return $internship->delete();
    }

}

?>