<?php 

namespace App\Repositories\Internship;

use App\Interfaces\Internship\TraineeshipRepositoryInterface;
use App\Models\Traineeship;

class TraineeshipRepository implements TraineeshipRepositoryInterface
{
    public function __construct(private Traineeship $traineeship) 
    {
    }

    public function getAll()
    {
        return $this->traineeship->with(['interviewPoint', 'jobVacancy'])->get()->map(function ($e) {
            $poin = $e->interviewPoint;
            $rata2 = 0;
            if ($poin != null)
                $rata2 = (double) ($poin->presentasi + $poin->kualitas_kerja + $poin->etika
                        + $poin->adaptif + $poin->kerja_sama + $poin->disiplin
                        + $poin->tanggung_jawab+ $poin->inovatif_kreatif 
                        + $poin->problem_solving + $poin->kemampuan_teknis + $poin->tugas) / 11;
            return [
                'Nama Lengkap' => $e->nama_lengkap,
                'Nama Jabatan' => $e->jobVacancy->role->nama_jabatan,
                'Status' => $e->status_tahap,
                'Nilai' => $rata2,
                'Ket HR' => $poin == null ? '' : $poin->keterangan_hr,
                'Ket User' => $poin == null ? '' : $poin->keterangan_user,
            ];
        });
    }

    public function find($id)
    {
        return $this->traineeship->with('interviewPoint')->where('id', $id)->get()->first();
    }

    public function findWithTrashes($id)
    {
        return $this->traineeship->withTrashed()->findOrFail($id);
    }

    public function create($request)
    {
        return $this->traineeship->create($request);
    }

    public function update($traineeship, $request)
    {
        return $traineeship->update($request);
    }

    public function delete($traineeship)
    {
        return $traineeship->delete();
    }

}

?>