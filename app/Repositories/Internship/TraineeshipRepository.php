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

    public function findBySlug($slug) 
    {
        return $this->traineeship->with('interviewPoint')
                    ->where(function ($query) use ($slug) {
                        $query->where('slug', $slug)
                            ->orWhere('slug', 'REGEXP', '^'.$slug.'_[0-9]+$');
                    })->withTrashed()->get();        
    }


    public function find($id)
    {
        return $this->traineeship->with('interviewPoint')->where('id', $id)->firstOrFail();
    }

    public function findWithTrashes($id)
    {
        return $this->traineeship->with('interviewPoint')->where('id', $id)->withTrashed()->firstOrFail();
    }

    public function findByJobVacancy($vacancyId)
    {
        return $this->traineeship->where('vacancy_id', $vacancyId)->get();
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