<?php 
namespace App\Interfaces;

interface ArchiveJobApplicantRepositoryInterface
{
    public function getAllJobApplicant();
    public function getAllTranieeship();
    public function getJobApplicantByJobVacancy($vacancyId);
    public function getTraineeshipByJobVacancy($vacancyId);
    public function find($id);
    public function create($request);
} 