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
            $data = collect($e)->put('poin', 0);
            if ($poin != null)
                $data['poin'] = (double) ($poin->presentasi + $poin->kualitas_kerja + $poin->etika
                        + $poin->adaptif + $poin->kerja_sama + $poin->disiplin
                        + $poin->tanggung_jawab + $poin->inovatif_kreatif 
                        + $poin->problem_solving + $poin->kemampuan_teknis + $poin->tugas) / 11;
            return $data->put('interview_point', '')->except(['interview_point'])->all();
        });
    }

    public function findBySlug($slug) {
        return $this->traineeship->with('interviewPoint')->where('slug', 'like', $slug.'%')->withTrashed()->get();
    }

    public function find($id)
    {
        return $this->traineeship->with('interviewPoint')->where('id', $id)->first();
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
        $traineeship->update($request);
        return $this->find($traineeship->id);
    }

    public function delete($traineeship)
    {
        return $traineeship->delete();
    }

}

?>