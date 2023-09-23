<?php 

namespace App\Repositories;

use App\Models\Division;
use App\Interfaces\DivisionRepositoryInterface;

class DivisionRepository implements DivisionRepositoryInterface
{

    public function __construct(private Division $division)
    {
    }

    public function getAll()
    {
        return $this->division->with('jobTitles')->get()->map(function ($e) {
            return[
                'id' => $e->id,
                'nama_divisi' => $e->nama_divisi,
                'jumlah_jabatan' => count($e->jobTitles),
                'created_at' => $e->created_at,
                'updated_at' => $e->updated_at
            ];
        });
    }

    public function find($slug)
    {
        return $this->division->where('slug', $slug)->firstOrFail();
    }

    public function create($request)
    {
        return $this->division->create($request);
    }
    
    public function update($division, $request)
    {
        return $division->update($request);
    }
    
    public function delete($division)
    {
        return $division->delete();
    }
    
}

?>