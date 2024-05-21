<?php

namespace App\Repositories\Internship;

use App\Models\Traineeship;
use App\Interfaces\Internship\TraineeshipRepositoryInterface;

class TraineeshipRepository implements TraineeshipRepositoryInterface
{
    public function __construct(private Traineeship $traineeship)
    {
    }

    public function getAll()
    {
        return $this->traineeship->with(['jobVacancy'])->get();
    }

    public function findBySlug($slug)
    {
        return $this->traineeship
                    ->with('interviewPoint')
                    ->where(function ($query) use ($slug) {
                        $query->where('slug', $slug)
                            ->orWhere('slug', 'REGEXP', '^'.$slug.'_[0-9]+$');
                    })
                    ->withTrashed()
                    ->get();
    }


    public function find($id, $withtrashed = false)
    {
        return $this->traineeship
                    ->find($id)
                    ->when($withtrashed, function($q) {
                        $q->withTrashed();
                    })->load(['interviewPoint']);
    }

    // public function findByJobVacancy($vacancyId)
    // {
    //     return $this->traineeship->where('vacancy_id', $vacancyId)->get();
    // }

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
