<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

use App\Events\CandidatureDeposee;
use App\Events\StatutCandidatureMis;
use App\Listeners\LogCandidatureDeposee;
use App\Listeners\LogStatutCandidatureMis;

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
        Event::listen(CandidatureDeposee::class, LogCandidatureDeposee::class);
        Event::listen(StatutCandidatureMis::class, LogStatutCandidatureMis::class);
    }
}
