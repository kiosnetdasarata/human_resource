<?php

namespace App\Repositories;

use App\Interfaces\ArchiveJobApplicantRepositoryInterface;
use App\Models\ArchiveJobApplicant;

class ArchiveJobApplicantRepository implements ArchiveJobApplicantRepositoryInterface
{
    public function __construct(private ArchiveJobApplicant $archive) { }

    public function query($request)
    {
        $query = $this->archive->query();

        $query->when($request->has('status'), function ($q) use ($request) {
            return $q->where('status', $request->query('status'));
        });

        $query->when($request->has('role'), function ($q) use ($request) {
            return $q->where('role', $request->query('role'));
        });

        $query->when($request->has('is_intern'), function ($q) use ($request) {
            return $q->where('is_intern', $request->query('is_intern'));
        });

        $query->when($request->has('vacancy_id'), function ($q) use ($request) {
            return $q->where('vacancy_id', $request->query('vacancy_id'));
        });

        return $query->get();

    }

    public function find($id)
    {
        return $this->archive->find($id);
    }

    public function create($request)
    {
        return $this->archive->create($request);
    }
}
