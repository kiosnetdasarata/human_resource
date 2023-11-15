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
        return $this->division->with(['role', 'manager'])->get()->map(function ($e) {
            return[
                'id' => $e->id,
                'nama_divisi' => $e->nama_divisi,
                'kode_divisi' => $e->kode_divisi,
                'supervisor' => $e->manager->nama,
                'no_tlpn' => $e->no_tlpn,
                'jumlah_jabatan' => count($e->role),
                'created_at' => $e->created_at,
                'updated_at' => $e->updated_at
            ];
        });
    }

    public function find($slug)
    {
        return $this->division->where('slug', $slug)->with('role')->get()->firstOrFail();
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