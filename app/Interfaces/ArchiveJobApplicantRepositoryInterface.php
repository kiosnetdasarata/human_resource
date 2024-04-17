<?php
namespace App\Interfaces;

interface ArchiveJobApplicantRepositoryInterface
{
    public function query($request);
    public function find($id);
    public function create($request);
}
