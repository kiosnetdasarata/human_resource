<?php

namespace App\Providers;

use App\Repositories\UserRepository;
use App\Repositories\ZoneRepository;
use App\Repositories\SalesRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\DivisionRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\JobTitleRepository;
use App\Repositories\TechnicianRepository;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\ZoneRepositoryInterface;
use App\Repositories\StatusLevelRepository;
use App\Interfaces\SalesRepositoryInterface;
use App\Repositories\BranchCompanyRepository;
use App\Interfaces\DivisionRepositoryInterface;
use App\Interfaces\EmployeeRepositoryInterface;
use App\Interfaces\JobTitleRepositoryInterface;
use App\Repositories\EmployeeHistoryRepository;
use App\Interfaces\TechnicianRepositoryInterface;
use App\Interfaces\StatusLevelRepositoryInterface;
use App\Interfaces\BranchCompanyRepositoryInterface;
use App\Interfaces\EmployeeHistoryRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(EmployeeRepositoryInterface::class, EmployeeRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(DivisionRepositoryInterface::class, DivisionRepository::class);
        $this->app->bind(EmployeeHistoryRepositoryInterface::class, EmployeeHistoryRepository::class);
        $this->app->bind(JobTitleRepositoryInterface::class, JobTitleRepository::class);
        $this->app->bind(SalesRepositoryInterface::class, SalesRepository::class);
        $this->app->bind(StatusLevelRepositoryInterface::class, StatusLevelRepository::class);
        $this->app->bind(TechnicianRepositoryInterface::class, TechnicianRepository::class);
        $this->app->bind(ZoneRepositoryInterface::class, ZoneRepository::class);
        $this->app->bind(BranchCompanyRepositoryInterface::class, BranchCompanyRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
