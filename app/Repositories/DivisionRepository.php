<?php 

namespace App\Repositories;

use App\Models\Division;
use Illuminate\Support\Str;
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
        return $this->division->where('slug', $slug)->with('role')->firstOrFail();
    }

    public function create($request)
    {
        $division = collect($request)->merge([
            'nama_divisi' => Str::title($request['nama_divisi']),
            'slug' => Str::slug($request['nama_divisi'], '_')
        ])->all();
        return $this->division->create($division);
    }

    public function getEmployee($id)
    {
        return $this->division->with('employee')->where('id', $id)->get();
    }
    
    public function update($id, $request)
    {
        $old = $this->find($id);
        $division = collect($request)->diffAssoc($old);
        if (isset($division['nama_divisi'])) {
            $division = $division->merge([
                'nama_divisi' => Str::title($request['nama_divisi']),
                'slug' => Str::slug($request['nama_divisi'], '_')
            ]);
        }
        
        return $old->update($division->all());
    }
    
    public function delete($division)
    {
        return $division->delete();
    }
    
}

?>