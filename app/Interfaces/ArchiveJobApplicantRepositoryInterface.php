<?php 
namespace App\Interfaces;

interface ArchiveJobApplicantRepositoryInterface
{
    public function getAllJobApplicant();
    public function getAllTranieeship();
    public function find($id);
    public function create($request);
} 