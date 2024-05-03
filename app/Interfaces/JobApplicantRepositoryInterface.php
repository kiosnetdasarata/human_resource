<?php

namespace App\Interfaces;

interface JobApplicantRepositoryInterface
{
    public function getAll();
    public function find($id);
    public function findSlug($slug);
    public function create($request);
    public function search($key, $value);
    public function update($jobApplicant, $request);
    public function delete($jobApplicant);
}
