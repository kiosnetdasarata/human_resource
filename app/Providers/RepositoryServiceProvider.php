<?php

namespace App\Providers;

use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Repositories\ZoneRepository;
use App\Repositories\SalesRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\DivisionRepository;
use App\Repositories\JobVacancyRepository;
use App\Repositories\TechnicianRepository;
use App\Interfaces\RoleRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\ZoneRepositoryInterface;
use App\Repositories\StatusLevelRepository;
use App\Interfaces\SalesRepositoryInterface;
use App\Repositories\BranchCompanyRepository;
use App\Interfaces\DivisionRepositoryInterface;
use App\Interfaces\JobVacancyRepositoryInterface;
use App\Interfaces\TechnicianRepositoryInterface;
use App\Repositories\Employee\EmployeeRepository;
use App\Interfaces\StatusLevelRepositoryInterface;
use App\Repositories\Employee\EmployeeCIRepository;
use App\Interfaces\BranchCompanyRepositoryInterface;
use App\Repositories\Internship\InternshipRepository;
use App\Repositories\Internship\PartnershipRepository;
use App\Repositories\Internship\TraineeshipRepository;
use App\Interfaces\Employee\EmployeeRepositoryInterface;
use App\Repositories\Employee\EmployeeArchiveRepository;
use App\Repositories\Employee\EmployeeHistoryRepository;
use App\Repositories\Employee\EmployeeContractRepository;
use App\Interfaces\Employee\EmployeeCIRepositoryInterface;
use App\Repositories\Employee\EmployeeEducationRepository;
use App\Repositories\Employee\EmployeeTrainingsRepository;
use App\Repositories\Internship\FilePartnershipRepository;
use App\Interfaces\Internship\InternshipRepositoryInterface;
use App\Interfaces\Internship\PartnershipRepositoryInterface;
use App\Interfaces\Internship\TraineeshipRepositoryInterface;
use App\Repositories\Internship\InternshipContractRepository;
use App\Interfaces\Employee\EmployeeArchiveRepositoryInterface;
use App\Interfaces\Employee\EmployeeHistoryRepositoryInterface;
use App\Interfaces\Employee\EmployeeContractRepositoryInterface;
use App\Repositories\Employee\EmployeeContractHistoryRepository;
use App\Interfaces\Employee\EmployeeEducationRepositoryInterface;
use App\Interfaces\Employee\EmployeeTrainingsRepositoryInterface;
use App\Interfaces\Internship\FilePartnershipRepositoryInterface;
use App\Interfaces\Internship\InternshipContractRepositoryInterface;
use App\Interfaces\Employee\EmployeeContractHistoryRepositoryInterface;
use App\Interfaces\Internship\InterviewPointRepositoryInterface;
use App\Repositories\Internship\InterviewPointRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(EmployeeRepositoryInterface::class, EmployeeRepository::class);
        $this->app->bind(EmployeeCIRepositoryInterface::class, EmployeeCIRepository::class);
        $this->app->bind(EmployeeArchiveRepositoryInterface::class, EmployeeArchiveRepository::class);
        $this->app->bind(EmployeeContractRepositoryInterface::class, EmployeeContractRepository::class);
        $this->app->bind(EmployeeContractHistoryRepositoryInterface::class, EmployeeContractHistoryRepository::class);
        $this->app->bind(EmployeeEducationRepositoryInterface::class, EmployeeEducationRepository::class);
        $this->app->bind(EmployeeHistoryRepositoryInterface::class, EmployeeHistoryRepository::class);
        $this->app->bind(EmployeeTrainingsRepositoryInterface::class, EmployeeTrainingsRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(DivisionRepositoryInterface::class, DivisionRepository::class);
        $this->app->bind(EmployeeHistoryRepositoryInterface::class, EmployeeHistoryRepository::class);
        $this->app->bind(SalesRepositoryInterface::class, SalesRepository::class);
        $this->app->bind(StatusLevelRepositoryInterface::class, StatusLevelRepository::class);
        $this->app->bind(TechnicianRepositoryInterface::class, TechnicianRepository::class);
        $this->app->bind(ZoneRepositoryInterface::class, ZoneRepository::class);
        $this->app->bind(BranchCompanyRepositoryInterface::class, BranchCompanyRepository::class);
        $this->app->bind(TraineeshipRepositoryInterface::class, TraineeshipRepository::class);
        $this->app->bind(InternshipRepositoryInterface::class, InternshipRepository::class);
        $this->app->bind(InternshipContractRepositoryInterface::class, InternshipContractRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(JobVacancyRepositoryInterface::class, JobVacancyRepository::class);
        $this->app->bind(FilePartnershipRepositoryInterface::class, FilePartnershipRepository::class);
        $this->app->bind(PartnershipRepositoryInterface::class, PartnershipRepository::class);
        $this->app->bind(InterviewPointRepositoryInterface::class, InterviewPointRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
