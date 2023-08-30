<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Validator::extend('iunique', function ($attribute, $value, $parameters, $validator) {
            $query = DB::table($parameters[0]);
            $column = $query->getGrammar()->wrap($parameters[1]);
            $exceptId = isset($parameters[2]) ? $parameters[2] : null;
            $exceptId = null;
            if (isset($parameters[2])) {
                $exceptId = $parameters[2];
                return $query->where('id', '<>', $exceptId);
            }
            return ! $query->whereRaw("lower({$column}) = lower(?)", [$value])->count();
        });
    }
}
